<?php

require_once '../app/Models/Home.php';
require_once '../app/Controllers/HomeController.php'; // Inclure le modèle Home

class Admin_homeController {
    private $dbConnection;
    private $homeModel;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
        $this->homeModel = new Home($dbConnection);
    }

    public function index() {
        // Récupérer tous les services via le modèle Home
        $services = $this->homeModel->getServices();
        
        // Récupérer tous les horaires d'ouverture via le modèle Home
        $openingHours = $this->homeModel->getOpeningHours();
        
        // Récupérer les chemins des images de la bannière et du cabinet
        $bannerImagePath = $this->homeModel->getBannerImage();
        $clinicImages = $this->homeModel->getClinicImages();
        
        // Récupérer le a propos
        $currentApropos = $this->homeModel->getApropos();
        
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
            $description = isset($_POST['description']) ? $_POST['description'] : '';
    
            // Vérifiez si l'ID de l'image est fourni (pour identifier l'image à mettre à jour)
            $imageId = isset($_POST['image_id']) ? (int)$_POST['image_id'] : null;
    
            if ($imageId) {
                if (isset($_FILES['new_banner']) && $_FILES['new_banner']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['new_banner']['tmp_name'];
                    $fileName = basename($_FILES['new_banner']['name']);
                    $targetFilePath = $uploadDir . $fileName;
    
                    // Vérification du type de fichier
                    if (in_array($_FILES['new_banner']['type'], $allowedTypes)) {
                        if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                            // Mise à jour de l'image et de la description dans la base de données
                            $this->homeModel->updateClinicImage($imageId, $targetFilePath, $description);
                            $_SESSION['success'] = "Bannière mise à jour avec succès.";
                        } else {
                            $errors[] = "Erreur lors de l'upload de l'image " . htmlspecialchars($fileName);
                        }
                    } else {
                        $errors[] = "Type de fichier non valide pour " . htmlspecialchars($fileName);
                    }
                } else {
                    // Si aucune nouvelle image n'est uploadée, on met juste à jour la description
                    $this->homeModel->updateClinicImage($imageId, null, $description); // Ne mettez pas à jour le chemin de l'image si aucune nouvelle image n'est uploadée.
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
    
    // Méthode pour mettre à jour la description
    public function updateApropos() {
        // Vérification de la méthode de requête (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $newDescription = isset($_POST['description']) ? trim($_POST['description']) : '';
    
            // Validation simple
            if ($id > 0 && !empty($newDescription)) {
                // Appel de la méthode de mise à jour dans le modèle
                if ($this->homeModel->updateApropos($id, $newDescription)) {
                    // Redirection ou message de succès
                    $_SESSION['success'] = "Description mise à jour avec succès.";
                } else {
                    // Gestion de l'échec de la mise à jour
                    $_SESSION['error'] = "Échec de la mise à jour de la description.";
                }
            } else {
                // Gestion de la validation des données
                $_SESSION['error'] = "ID ou description invalide.";
            }
        } else {
            // Gérer les requêtes GET ou autres méthodes
            $_SESSION['error'] = "Méthode de requête non supportée.";
        }
    
        // Redirection vers la page principale (ou toute autre page que vous souhaitez)
        header('Location: index.php?page=admin_home');
        exit();
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
    
            // Récupérer l'image actuelle depuis la base de données
            $stmt = $this->dbConnection->prepare("SELECT image_path FROM clinic_images WHERE id = :id");
            $stmt->bindParam(':id', $imageId);
            $stmt->execute();
            $currentImage = $stmt->fetchColumn();
    
            // Initialiser une variable pour le chemin de la nouvelle image
            $newImagePath = $currentImage; // Par défaut, utiliser le chemin actuel
    
            // Vérifiez si une nouvelle image a été téléchargée
            if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['new_image']['tmp_name'];
                $fileName = basename($_FILES['new_image']['name']);
                $targetFilePath = $uploadDir . $fileName;
    
                // Vérification du type de fichier
                if (in_array($_FILES['new_image']['type'], $allowedTypes)) {
                    if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                        // Si l'upload a réussi, utiliser le nouveau chemin
                        $newImagePath = $targetFilePath;
                    } else {
                        $errors[] = "Erreur lors de l'upload de l'image " . htmlspecialchars($fileName);
                    }
                } else {
                    $errors[] = "Type de fichier non valide pour " . htmlspecialchars($fileName);
                }
            }
    
            // Mettre à jour l'image et la description dans la base de données
            $this->homeModel->updateClinicImage($imageId, $newImagePath, $description);
    
            // Vérifier s'il y a des erreurs
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