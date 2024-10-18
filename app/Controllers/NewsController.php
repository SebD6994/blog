<?php

require_once '../app/Models/News.php';

class NewsController {
    private $newsModel;

    public function __construct($db) {
        $this->newsModel = new News($db);
    }

    public function index() {
        $newsItems = $this->newsModel->getAll();
        require '../app/Views/news.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['title'], $_POST['content'])) {
                try {
                    $imagePath = null;
    
                    // Vérifier si une image est uploadée
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
                    header('Location: index.php?page=admin');
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
        require '../app/Views/admin.php';
    }    
    
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['title'], $_POST['content'])) {
                try {
                    // Retrieve the existing news item
                    $existingNews = $this->newsModel->find($id);
                    if (!$existingNews) {
                        throw new InvalidArgumentException("Actualité introuvable.");
                    }
    
                    // Start with the existing image path
                    $imagePath = $existingNews['image_path'];
    
                    // Check if a new image is uploaded
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        // Validate and upload the new image
                        $newImagePath = $this->handleImageUpload($_FILES['image']); // Upload the new image
                        // If upload is successful, delete the old image
                        if ($newImagePath) {
                            if (!empty($imagePath) && file_exists($imagePath)) {
                                unlink($imagePath); // Remove the old image file
                            }
                            $imagePath = $newImagePath; // Update the image path to the new image
                        }
                    }
    
                    // Update the news article with new details
                    $this->newsModel->update($id, [
                        'title' => $_POST['title'],
                        'content' => $_POST['content'],
                        'image_path' => $imagePath // Use the new or old image path
                    ]);
    
                    $_SESSION['message'] = "Actualité mise à jour avec succès."; // Success message
                    header('Location: index.php?page=admin');
                    exit();
                } catch (InvalidArgumentException $e) {
                    $_SESSION['error'] = $e->getMessage();
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Database error: " . $e->getMessage();
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "Title and content are required."; // Error message
            }
        }
    
        // Load the existing news item for editing
        $newsItem = $this->newsModel->find($id);
        require '../app/Views/admin.php'; // Load the admin view
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
    
    
    


    public function delete($id) {
        if ($id) {
            try {
                $this->newsModel->delete($id);
                $_SESSION['message'] = "Actualité supprimée avec succès."; // Message de succès
            } catch (Exception $e) {
                $_SESSION['message'] = "Erreur lors de la suppression de l'actualité : " . $e->getMessage(); // Message d'erreur
            }
        }

        header('Location: index.php?page=admin');
        exit();
    }
}