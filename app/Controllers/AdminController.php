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

    public function __construct($db) {
        $this->db = $db;
        $this->adminModel = new Admin($db);
        $this->homeModel = new Home($db);
        $this->patientController = new PatientController($db);
        $this->appointmentModel = new Appointment($db);
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
            // Vérifiez si les données sont présentes dans $_POST
            if (isset($_POST['id']) && isset($_POST['status'])) {
                $appointmentId = $_POST['id']; // ID du rendez-vous
                $status = $_POST['status']; // Statut du rendez-vous
    
                // Déléguer l'appel à la méthode update du AppointmentController
                $appointmentController = new AppointmentController($this->db);
                $appointmentController->update(); // Appelle la méthode update dans AppointmentController
    
                // Redirigez vers la page admin ou appointments
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
        $this->patientController->create(true); // Déléguer au PatientController
    }

    public function updatePatient() {
        $this->patientController->update(); // Déléguer au PatientController
    }

    public function deletePatient() {
        if (isset($_GET['id'])) {
            $patientId = $_GET['id'];
            $this->patientController->delete($patientId); // Déléguer au PatientController
        }
    }

    public function searchPatients() {
        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $searchTerm = trim($_GET['search']);
            
            // Rechercher les patients
            $patients = $this->adminModel->searchPatients($searchTerm); // Ajoute cette ligne si la méthode existe dans Admin
    
            // Récupérer toutes les autres données nécessaires pour la vue
            $appointments = $this->adminModel->getAppointments(); // Récupérer les rendez-vous
            $services = $this->adminModel->getServices(); // Récupérer les services
            $news = $this->adminModel->getNews(); // Récupérer les actualités
            $openingHours = $this->homeModel->getOpeningHours(); // Récupérer les horaires d'ouverture
    
            // Charger la vue avec les résultats de recherche
            require '../app/Views/admin.php'; // Inclure la vue admin avec les résultats
        } else {
            // Si aucun terme de recherche n'est soumis, retourner à la page admin par défaut
            header("Location: index.php?page=admin");
            exit();
        }
    }
    

    // Méthodes pour les actions des services
    public function createService() {
        $serviceController = new ServiceController($this->db);
        $serviceController->create(); // Déléguer au ServiceController
    }

    public function updateService($id) {
        $serviceController = new ServiceController($this->db);
        $serviceController->update($id); // Déléguer au ServiceController
    }

    public function deleteService($id) {
        $serviceController = new ServiceController($this->db);
        $serviceController->delete($id); // Déléguer au ServiceController
    }

    // Méthodes pour les actions des actualités
    public function createNews() {
        $newsController = new NewsController($this->db);
        $newsController->create(); // Déléguer au NewsController
    }

    public function updateNews($id) {
        $newsController = new NewsController($this->db);
        $newsController->update($id); // Déléguer au NewsController
    }

    public function deleteNews($id) {
        $newsController = new NewsController($this->db);
        $newsController->delete($id); // Déléguer au NewsController
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