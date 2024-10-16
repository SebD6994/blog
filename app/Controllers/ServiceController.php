<?php

require_once '../app/Models/Service.php';

class ServiceController {
    private $serviceModel;

    public function __construct($db) {
        $this->serviceModel = new Service($db);
    }

    public function index() {
        // Retrieve all services and send them to the view
        $services = $this->serviceModel->getAll();
        require '../app/Views/Service.php'; // Load the view with the services
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['name'], $_POST['description'])) {
                try {
                    // Gérer l'upload de l'image
                    $imagePath = null;
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                        // Validation du fichier image
                        $imagePath = $this->handleImageUpload($_FILES['image']);
                    }

                    $this->serviceModel->create([
                        'name' => $_POST['name'],
                        'description' => $_POST['description'],
                        'image' => $imagePath
                    ]);

                    // Rediriger vers la page admin après la création
                    header('Location: index.php?page=admin');
                    exit();
                } catch (InvalidArgumentException $e) {
                    $_SESSION['error'] = $e->getMessage();
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Database error: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "Name and description are required.";
            }
        }

        require '../app/Views/admin.php'; // Charger la vue admin
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['name'], $_POST['description'])) {
                try {
                    // Gérer l'upload de l'image
                    $imagePath = $_POST['existing_image']; // Image existante par défaut
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                        // Supprimer l'ancienne image si une nouvelle est téléchargée
                        if (!empty($_POST['existing_image']) && file_exists($_POST['existing_image'])) {
                            unlink($_POST['existing_image']);
                        }
                        // Validation du fichier image
                        $imagePath = $this->handleImageUpload($_FILES['image']);
                    }

                    // Mettre à jour le service
                    $this->serviceModel->update($id, [
                        'name' => $_POST['name'],
                        'description' => $_POST['description'],
                        'image' => $imagePath
                    ]);

                    $_SESSION['message'] = "Service mis à jour avec succès.";
                    header('Location: index.php?page=admin');
                    exit();
                } catch (InvalidArgumentException $e) {
                    $_SESSION['error'] = $e->getMessage();
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Database error: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "Name and description are required.";
            }
        }

        // Charger le service existant pour l'édition
        $service = $this->serviceModel->find($id);
        require '../app/Views/admin.php'; // Charger la vue admin
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['id'])) {
                $id = $_POST['id']; // Get the service ID from the POST data
                
                try {
                    // Attempt to delete the service using the model
                    $this->serviceModel->delete($id);
    
                    // Set a success message
                    $_SESSION['message'] = "Service supprimé avec succès.";
                } catch (InvalidArgumentException $e) {
                    // Handle the case where the service does not exist
                    $_SESSION['error'] = $e->getMessage();
                } catch (RuntimeException $e) {
                    // Handle any runtime exceptions that occur
                    $_SESSION['error'] = $e->getMessage();
                } catch (PDOException $e) {
                    // Handle any database-related exceptions
                    $_SESSION['error'] = "Database error: " . $e->getMessage();
                } catch (Exception $e) {
                    // Handle any other exceptions that may occur
                    $_SESSION['error'] = "Erreur: " . $e->getMessage();
                }
    
                // Redirect back to the admin page after handling the delete operation
                header('Location: index.php?page=admin');
                exit();
            } else {
                // Handle the case where no ID is provided
                $_SESSION['error'] = "ID du service requis.";
                header('Location: index.php?page=admin');
                exit();
            }
        } else {
            // If not a POST request, show an error
            $_SESSION['error'] = "La suppression doit être effectuée par une requête POST.";
            header('Location: index.php?page=admin');
            exit();
        }
    }
    

    // Fonction privée pour gérer l'upload et la validation de l'image
    private function handleImageUpload($imageFile) {
        // Valider l'extension de fichier
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new InvalidArgumentException("Le format d'image n'est pas autorisé. Formats acceptés : jpg, jpeg, png, gif.");
        }

        // Valider la taille du fichier (2MB maximum)
        if ($imageFile['size'] > 2000000) { // Limite à 2MB
            throw new InvalidArgumentException("L'image est trop volumineuse. Taille maximum : 2MB.");
        }

        // Déplacer l'image vers le dossier d'uploads
        $imagePath = '../assets/images/services/' . uniqid() . '.' . $fileExtension;
        if (!move_uploaded_file($imageFile['tmp_name'], $imagePath)) {
            throw new RuntimeException("Échec du téléchargement de l'image.");
        }

        return $imagePath; // Retourner le chemin de l'image sauvegardée
    }
}