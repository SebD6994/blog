<?php

require_once '../app/Models/Admin.php';
require_once '../app/Models/Home.php';
require_once '../app/Models/Service.php';
require_once '../app/Controllers/ServiceController.php';
require_once '../app/Controllers/NewsController.php';

class AdminController {
    private $adminModel;
    private $homeModel;
    private $db;

    public function __construct($db) {
        $this->adminModel = new Admin($db);
        $this->homeModel = new Home($db);
        $this->db = $db;
    }

    public function index() {
        // Récupérer les données nécessaires pour la vue
        $appointments = $this->adminModel->getAppointments();
        $services = $this->adminModel->getServices();
        $news = $this->adminModel->getNews();
        $patients = $this->adminModel->getPatients();
        $openingHours = $this->homeModel->getOpeningHours();

        // Afficher la vue admin_dashboard
        require '../app/Views/admin.php';
    }

    // Méthodes pour gérer les actions de l'admin (ajout, suppression, mise à jour)
    public function updateAppointment($id, $status, $description) {
        if ($id && $status) {
            $stmt = $this->db->prepare("UPDATE appointments SET status = :status WHERE id = :id");
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            header("Location: index.php?page=admin");
            exit();
        } else {
            die("ID ou statut manquant lors de la mise à jour du rendez-vous.");
        }
    }
    
    public function deletePatient() {
        if (isset($_GET['id'])) {
            $patientId = $_GET['id'];

            if ($this->adminModel->deletePatient($patientId)) {
                $_SESSION['message'] = "Patient supprimé avec succès.";
            } else {
                $_SESSION['message'] = "Erreur lors de la suppression du patient.";
            }

            header('Location: index.php?page=admin');
            exit;
        }
    }

    // Delegate service actions to ServiceController
    public function createService() {
        $serviceController = new ServiceController($this->db);
        $serviceController->create(); // Delegate to ServiceController
    }

    public function updateService($id) {
        $serviceController = new ServiceController($this->db);
        $serviceController->update($id); // Delegate to ServiceController
    }

    public function deleteService($id) {
        $serviceController = new ServiceController($this->db);
        $serviceController->delete($id); // Delegate to ServiceController
    }

    // Delegate news actions to NewsController
    public function createNews() {
        $newsController = new NewsController($this->db);
        $newsController->create(); // Delegate to NewsController
    }

    public function updateNews($id) {
        $newsController = new NewsController($this->db);
        $newsController->update($id); // Delegate to NewsController
    }

    public function deleteNews($id) {
        $newsController = new NewsController($this->db);
        $newsController->delete($id); // Delegate to NewsController
    }

    // Method to update opening hours
    public function updateOpeningHours() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hours'])) {
            $this->homeModel->updateOpeningHours($_POST);
            $_SESSION['message'] = "Horaires d'ouverture mis à jour avec succès.";
            header('Location: index.php?page=admin');
            exit;
        } else {
            die("Données manquantes pour la mise à jour des horaires d'ouverture.");
        }
    }
}