<?php

require_once '../app/Models/Admin.php';
require_once '../app/Models/Home.php';
require_once '../app/Models/Service.php';
require_once '../app/Models/Appointment.php';
require_once '../app/Controllers/ServiceController.php';
require_once '../app/Controllers/NewsController.php';
require_once '../app/Controllers/PatientController.php';

class AdminController {
    private $db;
    private $adminModel;
    private $homeModel;
    private $appointmentModel;
    private $patientController;
    private $newsController; // Ajout d'une propriété pour le NewsController

    public function __construct($db) {
        $this->db = $db;
        $this->adminModel = new Admin($db);
        $this->homeModel = new Home($db);
        $this->patientController = new PatientController($db);
        $this->appointmentModel = new Appointment($db);
        $this->newsController = new NewsController($db); // Instanciation du NewsController
    }

    public function index() {
        // Récupérer les données nécessaires pour la vue
        $appointments = $this->adminModel->getAppointments();
        $patients = $this->adminModel->getPatients();
        $services = $this->adminModel->getServices();
        $news = $this->adminModel->getNews();
        $openingHours = $this->homeModel->getOpeningHours();

        $today = date('Y-m-d');
        $availableSlots = $this->appointmentModel->getAvailableTimeSlots($today);

        // Afficher la vue admin_dashboard
        require '../app/Views/admin.php';
    }

    // Méthodes pour gérer les actions de l'admin (ajout, suppression, mise à jour)
    public function updateStatus($id, $status) {
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

    public function updateAppointment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['id']) && isset($_POST['status'])) {
                $appointmentId = $_POST['id'];
                $status = $_POST['status'];

                // Déléguer l'appel à la méthode update du AppointmentController
                $appointmentController = new AppointmentController($this->db);
                $appointmentController->update($appointmentId, $status);

                header('Location: index.php?page=admin'); 
                exit();
            } else {
                die("ID ou statut manquant lors de la mise à jour du rendez-vous.");
            }
        } else {
            die("Aucune donnée soumise pour la mise à jour du rendez-vous.");
        }
    }

    // Déléguer les actions des patients au PatientController
    public function createPatient() {
        $this->patientController->create(true);
    }

    public function updatePatient() {
        $this->patientController->update();
    }

    public function deletePatient() {
        if (isset($_GET['id'])) {
            $patientId = $_GET['id'];
            $this->patientController->delete($patientId);
        }
    }

    public function searchPatients() {
        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $searchTerm = trim($_GET['search']);
            $patients = $this->adminModel->searchPatients($searchTerm);
            $appointments = $this->adminModel->getAppointments();
            $services = $this->adminModel->getServices();
            $news = $this->adminModel->getNews();
            $openingHours = $this->homeModel->getOpeningHours();
            require '../app/Views/admin.php';
        } else {
            header("Location: index.php?page=admin");
            exit();
        }
    }

    // Méthodes pour les actions des services
    public function createService() {
        $serviceController = new ServiceController($this->db);
        $serviceController->create();
    }

    public function updateService($id) {
        $serviceController = new ServiceController($this->db);
        $serviceController->update($id);
    }

    public function deleteService($id) {
        $serviceController = new ServiceController($this->db);
        $serviceController->delete($id);
    }

    // Méthodes pour les actions des actualités
    public function createNews() {
        session_start(); // Début de la session pour les messages d'erreur ou de succès
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->newsController->create(); // Déléguer au NewsController
        } else {
            require '../app/Views/admin.php'; // Afficher le formulaire de création d'actualités
        }
    }

    public function updateNews($id) {
        session_start(); // Début de la session pour les messages d'erreur ou de succès
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->newsController->update($id); // Déléguer au NewsController
        } else {
            $newsItem = $this->adminModel->getNewsById($id); // Récupérer l'actualité à modifier
            require '../app/Views/admin.php'; // Afficher le formulaire de mise à jour d'actualités
        }
    }

    public function deleteNews($id) {
        $this->newsController->delete($id); // Déléguer au NewsController
    }

    // Méthode pour mettre à jour les horaires d'ouverture
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