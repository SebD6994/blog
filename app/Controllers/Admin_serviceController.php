<?php

require_once '../app/Models/Service.php';
require_once '../app/Controllers/ServiceController.php';

class Admin_serviceController {
    private $db;
    private $serviceModel;

    public function __construct($db) {
        $this->db = $db;
        $this->serviceModel = new Service($this->db);
    }
    
    public function index() {
        $services = $this->serviceModel->getAll();
        require '../app/Views/admin_service.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['name'], $_POST['description'])) {
                try {
                    $imagePath = null;
                    
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                        $imagePath = $this->handleImageUpload($_FILES['image']);
                    }

                    $this->serviceModel->create([
                        'name' => $_POST['name'],
                        'description' => $_POST['description'],
                        'image' => $imagePath
                    ]);

                    $_SESSION['success'] = "Service créé avec succès.";
                } catch (InvalidArgumentException $e) {
                    $_SESSION['error'] = $e->getMessage();
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Erreur de base de données: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "Le nom et la description sont requis.";
            }
        }

        $services = $this->serviceModel->getAll();
        require '../app/Views/admin_service.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['name'], $_POST['description'])) {
                try {
                    $imagePath = $_POST['existing_image'];
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                        if (!empty($_POST['existing_image']) && file_exists($_POST['existing_image'])) {
                            unlink($_POST['existing_image']);
                        }
                        $imagePath = $this->handleImageUpload($_FILES['image']);
                    }

                    $this->serviceModel->update($id, [
                        'name' => $_POST['name'],
                        'description' => $_POST['description'],
                        'image' => $imagePath
                    ]);

                    $_SESSION['message'] = "Service mis à jour avec succès.";
                    header('Location: index.php?page=admin_services');
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

        $service = $this->serviceModel->find($id);
        require '../app/Views/admin_service.php';
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
                
                try {
                    $this->serviceModel->delete($id);
                    $_SESSION['message'] = "Service supprimé avec succès.";
                } catch (InvalidArgumentException $e) {
                    $_SESSION['error'] = $e->getMessage();
                } catch (RuntimeException $e) {
                    $_SESSION['error'] = $e->getMessage();
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Database error: " . $e->getMessage();
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur: " . $e->getMessage();
                }

                header('Location: index.php?page=admin_services');
                exit();
            } else {
                $_SESSION['error'] = "ID du service requis.";
                header('Location: index.php?page=admin_services');
                exit();
            }
        } else {
            $_SESSION['error'] = "La suppression doit être effectuée par une requête POST.";
            header('Location: index.php?page=admin_services');
            exit();
        }
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

        $imagePath = '../assets/images/services/' . uniqid() . '.' . $fileExtension;
        if (!move_uploaded_file($imageFile['tmp_name'], $imagePath)) {
            throw new RuntimeException("Échec du téléchargement de l'image.");
        }

        return $imagePath;
    }
}