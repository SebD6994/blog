<?php

require_once '../app/Models/News.php';
require_once '../app/Controllers/NewsController.php';

class Admin_newController {
    private $db;
    private $newsModel;

    public function __construct($db) {
        $this->db = $db;
        $this->newsModel = new News($db);
    }

    public function index() {
        $news = $this->newsModel->getAll();

        require '../app/Views/admin_new.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['title'], $_POST['content'])) {
                try {
                    $imagePath = null;
    
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        // Gérer l'upload de l'image
                        $imagePath = $this->handleImageUpload($_FILES['image']);
                    }
    
                    // Créer une nouvelle actualité dans la base de données
                    $this->newsModel->create([
                        'title' => $_POST['title'],
                        'content' => $_POST['content'],
                        'image_path' => $imagePath // Le chemin de l'image (ou null)
                    ]);
    
                    $_SESSION['message'] = "Nouvelle actualité créée avec succès."; // Message de succès
                    header('Location: index.php?page=admin_news');
                    exit();
                } catch (InvalidArgumentException $e) {
                    $_SESSION['error'] = $e->getMessage();
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Database error: " . $e->getMessage();
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "Le titre et le contenu sont obligatoires."; // Message d'erreur
            }
        }
    
        // Charger la vue admin avec le formulaire de création
        require '../app/Views/admin_new.php';
    }    
    
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['title'], $_POST['content'])) {
                try {
                    // Récupérer l'actualité existante
                    $existingNews = $this->newsModel->find($id);
                    if (!$existingNews) {
                        throw new InvalidArgumentException("Actualité introuvable.");
                    }
    
                    // Commencer avec le chemin de l'image existante
                    $imagePath = $existingNews['image_path'];
    
                    // Vérifier si une nouvelle image est téléchargée
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        // Valider et télécharger la nouvelle image
                        $newImagePath = $this->handleImageUpload($_FILES['image']); // Téléchargement de la nouvelle image
                        // Si le téléchargement est réussi, supprimer l'ancienne image
                        if ($newImagePath) {
                            if (!empty($imagePath) && file_exists($imagePath)) {
                                unlink($imagePath); // Supprimer l'ancienne image
                            }
                            $imagePath = $newImagePath; // Mettre à jour le chemin de l'image avec la nouvelle image
                        }
                    }
    
                    // Mettre à jour l'article d'actualité avec les nouveaux détails
                    $this->newsModel->update($id, [
                        'title' => $_POST['title'],
                        'content' => $_POST['content'],
                        'image_path' => $imagePath // Utiliser le nouveau ou l'ancien chemin de l'image
                    ]);
    
                    $_SESSION['message'] = "Actualité mise à jour avec succès."; // Message de succès
                    header('Location: index.php?page=admin_news');
                    exit();
                } catch (InvalidArgumentException $e) {
                    $_SESSION['error'] = $e->getMessage();
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Erreur de base de données: " . $e->getMessage();
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "Le titre et le contenu sont requis."; // Message d'erreur
            }
        }
    
        // Charger l'article d'actualité existant pour l'édition
        $newsItem = $this->newsModel->find($id);
        require '../app/Views/admin_new.php'; // Charger la vue admin
    }    

    public function delete($id) {
        if ($id) {
            try {
                $this->newsModel->delete($id);
                $_SESSION['message'] = "Actualité supprimée avec succès."; // Message de succès
            } catch (Exception $e) {
                $_SESSION['message'] = "Erreur lors de la suppression de l'actualité : " . $e->getMessage(); // Message d'erreur
            }
        }

        header('Location: index.php?page=admin_news');
        exit();
    }

    private function handleImageUpload($imageFile) {
        // Validate the file extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
    
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new InvalidArgumentException("Le format d'image n'est pas autorisé. Formats acceptés : jpg, jpeg, png, gif.");
        }
    
        // Validate the file size (2MB maximum)
        if ($imageFile['size'] > 2000000) { // Limit to 2MB
            throw new InvalidArgumentException("L'image est trop volumineuse. Taille maximum : 2MB.");
        }
    
        // Move the image to the uploads directory
        $imagePath = '../assets/images/news/' . uniqid() . '.' . $fileExtension;
        if (!move_uploaded_file($imageFile['tmp_name'], $imagePath)) {
            throw new RuntimeException("Échec du téléchargement de l'image.");
        }
    
        return $imagePath; // Return the path of the uploaded image
    }
}