<?php

require_once '../app/Models/Admin.php';
require_once '../app/Models/Home.php';
require_once '../app/Models/Service.php';
require_once '../app/Models/Appointment.php';
require_once '../app/Controllers/ServiceController.php';
require_once '../app/Controllers/NewsController.php';
require_once '../app/Controllers/PatientController.php';
require_once '../app/Controllers/AppointmentController.php';

class AdminController {
    private $db;
    private $adminModel;
    private $homeModel;
    private $appointmentModel;
    private $patientController;
    private $newsController;

    public function __construct($db) {
        $this->db = $db;
        $this->adminModel = new Admin($db);
        $this->homeModel = new Home($db);
        $this->patientController = new PatientController($db);
        $this->appointmentModel = new Appointment($db);
        $this->newsController = new NewsController($db);
    }

    public function index() {
        // Récupérer les données nécessaires pour la vue
        $appointments = $this->adminModel->getAppointments();
        $patients = $this->adminModel->getPatients();
        $services = $this->adminModel->getServices();
        $news = $this->adminModel->getNews();
        $openingHours = $this->homeModel->getOpeningHours();

        $today = date('Y-m-d');
        
        // Récupérer les créneaux horaires disponibles
        $availableSlots = $this->appointmentModel->getAvailableTimeSlots($today);

        // Vérification et structure des créneaux horaires
        $timeSlots = [
            'available' => $availableSlots['available'] ?? [], // Créneaux horaires disponibles
            'booked' => $availableSlots['booked'] ?? [] // Créneaux horaires réservés
        ];

        // Afficher la vue admin_dashboard avec les données
        require '../app/Views/admin.php';
    }

    // Délégation à AppointmentController pour mettre à jour le statut d'un rendez-vous
    public function updateAppointmentStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['id']) && isset($_POST['status'])) {
                $appointmentId = $_POST['id'];
                $status = $_POST['status'];

                // Déléguer l'appel à la méthode updateStatus du AppointmentController
                $appointmentController = new AppointmentController($this->db);
                if ($appointmentController->updateStatus($appointmentId, $status)) {
                    header('Location: index.php?page=admin'); 
                    exit();
                } else {
                    die("Erreur lors de la mise à jour du statut du rendez-vous.");
                }
            } else {
                die("ID ou statut manquant lors de la mise à jour du rendez-vous.");
            }
        } else {
            die("Aucune donnée soumise pour la mise à jour du rendez-vous.");
        }
    }

    // Délégation à AppointmentController pour obtenir les créneaux horaires disponibles
    public function getAvailableTimeSlots() {
        if (isset($_GET['date'])) {
            $date = $_GET['date'];

            // Instantiate AppointmentController
            $appointmentController = new AppointmentController($this->db);

            // Call the getTimeSlots method with the date parameter
            $appointmentController->getTimeSlots($date);
        } else {
            echo "Aucune date spécifiée.";
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