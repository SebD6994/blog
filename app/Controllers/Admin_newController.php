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
                        $imagePath = $this->handleImageUpload($_FILES['image']);
                    }

                    $this->newsModel->create([
                        'title' => $_POST['title'],
                        'content' => $_POST['content'],
                        'image_path' => $imagePath
                    ]);

                    $_SESSION['message'] = "Nouvelle actualité créée avec succès.";
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
                $_SESSION['error'] = "Le titre et le contenu sont obligatoires.";
            }
        }

        require '../app/Views/admin_new.php';
    }    
    
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['title'], $_POST['content'])) {
                try {
                    $existingNews = $this->newsModel->find($id);
                    if (!$existingNews) {
                        throw new InvalidArgumentException("Actualité introuvable.");
                    }

                    $imagePath = $existingNews['image_path'];

                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $newImagePath = $this->handleImageUpload($_FILES['image']);
                        if ($newImagePath) {
                            if (!empty($imagePath) && file_exists($imagePath)) {
                                unlink($imagePath);
                            }
                            $imagePath = $newImagePath;
                        }
                    }

                    $this->newsModel->update($id, [
                        'title' => $_POST['title'],
                        'content' => $_POST['content'],
                        'image_path' => $imagePath
                    ]);

                    $_SESSION['message'] = "Actualité mise à jour avec succès.";
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
                $_SESSION['error'] = "Le titre et le contenu sont requis.";
            }
        }

        $newsItem = $this->newsModel->find($id);
        require '../app/Views/admin_new.php';
    }    

    public function delete($id) {
        if ($id) {
            try {
                $this->newsModel->delete($id);
                $_SESSION['message'] = "Actualité supprimée avec succès.";
            } catch (Exception $e) {
                $_SESSION['message'] = "Erreur lors de la suppression de l'actualité : " . $e->getMessage();
            }
        }

        header('Location: index.php?page=admin_news');
        exit();
    }

    private function handleImageUpload($imageFile) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new InvalidArgumentException("Le format d'image n'est pas autorisé. Formats acceptés : jpg, jpeg, png, gif.");
        }

        if ($imageFile['size'] > 2000000) {
            throw new InvalidArgumentException("L'image est trop volumineuse. Taille maximum : 2MB.");
        }

        $imagePath = './assets/images/home/' . uniqid() . '.' . $fileExtension;
        if (!move_uploaded_file($imageFile['tmp_name'], $imagePath)) {
            throw new RuntimeException("Échec du téléchargement de l'image.");
        }

        return $imagePath;
    }
}