<?php

require_once '../app/Models/Admin.php';
require_once '../app/Models/Home.php';
require_once '../app/Models/Service.php';
require_once '../app/Controllers/ServiceController.php';
require_once '../app/Controllers/NewsController.php';
require_once '../app/Controllers/PatientController.php';

class AdminController {
    private $db;
    private $adminModel;
    private $homeModel;
    private $patientController;

    public function __construct($db) {
        $this->db = $db;
        $this->adminModel = new Admin($db);
        $this->homeModel = new Home($db);
        $this->patientController = new PatientController($db);
    }

    public function index() {
        // Récupérer les données nécessaires pour la vue
        $appointments = $this->adminModel->getAppointments();
        $patients = $this->adminModel->getPatients();
        $services = $this->adminModel->getServices();
        $news = $this->adminModel->getNews();
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
    
    // Delegate patient actions to PatientController
    public function createPatient() {
        $this->patientController->create(true); // Delegate to PatientController
    }

    public function updatePatient() {
        $this->patientController->update(); // Delegate to PatientController
    }

    public function deletePatient() {
        if (isset($_GET['id'])) {
            $patientId = $_GET['id'];
            $this->patientController->delete($patientId); // Delegate to PatientController
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