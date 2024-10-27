<?php

require_once '../app/Models/Home.php';
require_once '../app/Controllers/HomeController.php';

class Admin_homeController {
    private $dbConnection;
    private $homeModel;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
        $this->homeModel = new Home($dbConnection);
    }

    public function index() {
        $services = $this->homeModel->getServices();
        $openingHours = $this->homeModel->getOpeningHours();
        $bannerImagePath = $this->homeModel->getBannerImage();
        $clinicImages = $this->homeModel->getClinicImages();
        $currentApropos = $this->homeModel->getApropos();
        require '../app/Views/admin_home.php';
    }

    public function updateOpeningHours() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hours'])) {
            $this->homeModel->updateOpeningHours($_POST);
            $_SESSION['success'] = "Horaires d'ouverture mis à jour avec succès.";
            header('Location: index.php?page=admin_home');
            exit;
        } else {
            $_SESSION['error'] = "Données manquantes pour la mise à jour des horaires.";
            header('Location: index.php?page=admin_home');
            exit;
        }
    }

    public function updateBannerImage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $uploadDir = '../assets/images/home/';
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $errors = [];
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $imageId = isset($_POST['image_id']) ? (int)$_POST['image_id'] : null;

            if ($imageId) {
                if (isset($_FILES['new_banner']) && $_FILES['new_banner']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['new_banner']['tmp_name'];
                    $fileName = basename($_FILES['new_banner']['name']);
                    $targetFilePath = $uploadDir . $fileName;

                    if (in_array($_FILES['new_banner']['type'], $allowedTypes)) {
                        if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                            $this->homeModel->updateClinicImage($imageId, $targetFilePath, $description);
                            $_SESSION['success'] = "Bannière mise à jour avec succès.";
                        } else {
                            $errors[] = "Erreur lors de l'upload de l'image " . htmlspecialchars($fileName);
                        }
                    } else {
                        $errors[] = "Type de fichier non valide pour " . htmlspecialchars($fileName);
                    }
                } else {
                    $this->homeModel->updateClinicImage($imageId, null, $description);
                    $_SESSION['success'] = "Description mise à jour avec succès.";
                }
            } else {
                $errors[] = "Aucune image sélectionnée pour la mise à jour.";
            }

            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
            }

            header('Location: index.php?page=admin_home');
            exit();
        } else {
            $_SESSION['error'] = "Aucune image n'a été sélectionnée.";
            header('Location: index.php?page=admin_home');
            exit();
        }
    }

    public function updateApropos() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $newDescription = isset($_POST['description']) ? trim($_POST['description']) : '';

            if ($id > 0 && !empty($newDescription)) {
                if ($this->homeModel->updateApropos($id, $newDescription)) {
                    $_SESSION['success'] = "Description mise à jour avec succès.";
                } else {
                    $_SESSION['error'] = "Échec de la mise à jour de la description.";
                }
            } else {
                $_SESSION['error'] = "ID ou description invalide.";
            }
        } else {
            $_SESSION['error'] = "Méthode de requête non supportée.";
        }

        header('Location: index.php?page=admin_home');
        exit();
    }

    public function addClinicImage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['clinic_image'])) {
            $uploadDir = '../assets/images/home/';
            $uploadFile = $uploadDir . basename($_FILES['clinic_image']['name']);

            if (move_uploaded_file($_FILES['clinic_image']['tmp_name'], $uploadFile)) {
                $this->homeModel->addClinicImage($uploadFile, $_POST['description']);
                $_SESSION['success'] = "Image ajoutée avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors du téléchargement de l'image.";
            }
            header('Location: index.php?page=admin_home');
            exit;
        }
    }

    public function updateClinicImage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $uploadDir = '../assets/images/home/';
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $errors = [];
            $imageId = $_POST['image_id'];
            $description = isset($_POST['description']) ? $_POST['description'] : null;

            $stmt = $this->dbConnection->prepare("SELECT image_path FROM clinic_images WHERE id = :id");
            $stmt->bindParam(':id', $imageId);
            $stmt->execute();
            $currentImage = $stmt->fetchColumn();
            $newImagePath = $currentImage;

            if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['new_image']['tmp_name'];
                $fileName = basename($_FILES['new_image']['name']);
                $targetFilePath = $uploadDir . $fileName;

                if (in_array($_FILES['new_image']['type'], $allowedTypes)) {
                    if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                        $newImagePath = $targetFilePath;
                    } else {
                        $errors[] = "Erreur lors de l'upload de l'image " . htmlspecialchars($fileName);
                    }
                } else {
                    $errors[] = "Type de fichier non valide pour " . htmlspecialchars($fileName);
                }
            }

            $this->homeModel->updateClinicImage($imageId, $newImagePath, $description);

            if (empty($errors)) {
                $_SESSION['success'] = "Image du cabinet mise à jour avec succès.";
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }

            header('Location: index.php?page=admin_home');
            exit();
        } else {
            $_SESSION['error'] = "Aucune image n'a été sélectionnée.";
            header('Location: index.php?page=admin_home');
            exit();
        }
    }

    public function deleteClinicImage() {
        if (isset($_POST['image_id'])) {
            $this->homeModel->deleteClinicImage($_POST['image_id']);
            $_SESSION['success'] = "Image supprimée avec succès.";
            header('Location: index.php?page=admin_home');
            exit;
        }
    }
}