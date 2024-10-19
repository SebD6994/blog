<?php

require_once '../app/Models/Home.php';
require_once '../app/Controllers/HomeController.php'; // Inclure le modèle Home

class Admin_homeController {
    private $dbConnection;
    private $homeModel;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
        $this->homeModel = new Home($dbConnection); // Instanciation du modèle Home
    }

    public function index() {
        // Récupérer tous les services via le modèle Home
        $services = $this->homeModel->getServices();
        
        // Récupérer tous les horaires d'ouverture via le modèle Home
        $openingHours = $this->homeModel->getOpeningHours();
        
        // Récupérer les chemins des images de la bannière et du cabinet
        $bannerImagePath = $this->homeModel->getBannerImage();
        $clinicImages = $this->homeModel->getClinicImages();

        // Passer les services, les horaires, et les images à la vue
        require '../app/Views/admin_home.php'; // Charger la vue de la page d'accueil
    }

    // Méthode pour mettre à jour les horaires d'ouverture
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
            
            // Vérifiez si 'description' existe dans le tableau $_POST
            $description = isset($_POST['description']) ? $_POST['description'] : 'banner_image';
    
            if (isset($_FILES['new_banner']) && $_FILES['new_banner']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['new_banner']['tmp_name'];
                $fileName = basename($_FILES['new_banner']['name']);
                $targetFilePath = $uploadDir . $fileName;
    
                // Vérification du type de fichier
                if (in_array($_FILES['new_banner']['type'], $allowedTypes)) {
                    if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                        // Créer une nouvelle entrée pour la bannière et supprimer l'ancienne
                        $this->homeModel->updateBanner($targetFilePath, $description);
                        $_SESSION['success'] = "Bannière mise à jour avec succès.";
                    } else {
                        $errors[] = "Erreur lors de l'upload de l'image " . htmlspecialchars($fileName);
                    }
                } else {
                    $errors[] = "Type de fichier non valide pour " . htmlspecialchars($fileName);
                }
            } else {
                $errors[] = "Aucun fichier n'a été téléchargé.";
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
    
    

    // Méthode pour ajouter une image de la clinique
    public function addClinicImage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['clinic_image'])) {
            $uploadDir = '../assets/images/home/'; // Dossier de stockage des images
            $uploadFile = $uploadDir . basename($_FILES['clinic_image']['name']);
            
            // Vérifiez et déplacez le fichier téléchargé
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
    
            // Récupérer l'ID de l'image
            $imageId = $_POST['image_id'];
    
            // Vérifiez si 'description' existe dans le tableau $_POST
            $description = isset($_POST['description']) ? $_POST['description'] : null;
    
            if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['new_image']['tmp_name'];
                $fileName = basename($_FILES['new_image']['name']);
                $targetFilePath = $uploadDir . $fileName;
    
                // Vérification du type de fichier
                if (in_array($_FILES['new_image']['type'], $allowedTypes)) {
                    if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                        // Mettre à jour le chemin de l'image dans la base de données avec la description
                        $this->homeModel->updateClinicImage($imageId, $targetFilePath, $description);
                    } else {
                        $errors[] = "Erreur lors de l'upload de l'image " . htmlspecialchars($fileName);
                    }
                } else {
                    $errors[] = "Type de fichier non valide pour " . htmlspecialchars($fileName);
                }
            } else {
                $errors[] = "Aucun fichier n'a été téléchargé.";
            }
    
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
    


    // Méthode pour supprimer une image de la clinique
    public function deleteClinicImage() {
        if (isset($_POST['image_id'])) {
            $this->homeModel->deleteClinicImage($_POST['image_id']);
            $_SESSION['success'] = "Image supprimée avec succès.";
            header('Location: index.php?page=admin_home');
            exit;
        }
    }
}