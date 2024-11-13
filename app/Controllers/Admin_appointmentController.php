<?php

require_once '../app/Models/Appointment.php';
require_once '../app/Controllers/AppointmentController.php';
require_once '../app/Models/Service.php';
require_once '../app/Models/Patient.php';
require_once '../app/Models/Home.php';

class Admin_appointmentController {
    private $db;
    private $appointmentModel;

    public function __construct($db) {
        $this->db = $db;
        $this->appointmentModel = new Appointment($db);
        $this->homeModel = new Home($db);
    }

    public function index() {
        $appointments = $this->appointmentModel->getAll();
        $serviceModel = new Service($this->db);
        $services = $serviceModel->getAll();
        $patientModel = new Patient($this->db);
        $patients = $patientModel->getAll();

        $timeSlots = $this->appointmentModel->getTimeSlots();
      
            

        require '../app/Views/admin_appointment.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier que l'utilisateur est bien connecté et que la requête est POST
            if (!$this->isLoggedIn()) {
                $_SESSION['error_message'] = "Vous devez être connecté pour créer un rendez-vous.";
                header("Location: index.php?page=appointments"); // Redirection vers la page de création de rendez-vous
                exit();
            }
    
            // Récupérer les données du formulaire
            $appointmentDate = $_POST['appointment_date'];
            $selectedTimeSlot = $_POST['appointment_time'];
            $selectedPatientId = $_POST['patient_id']; // Récupérer l'ID du patient sélectionné
            $appointmentDateTimeString = $appointmentDate . ' ' . $selectedTimeSlot;
    
            // Appel à la méthode create du AppointmentController
            $appointmentController = new AppointmentController();
    
            // Redéfinir la logique pour que l'ID du patient sélectionné soit utilisé
            $_POST['patient_id'] = $selectedPatientId; // Remplacer l'ID du patient connecté par celui du formulaire
    
            // Appeler la méthode create de AppointmentController
            $appointmentController->create();
    
            // Redirection après la création du rendez-vous
            $_SESSION['success_message'] = "Rendez-vous créé avec succès.";
            header("Location: index.php?page=appointments"); // Redirection vers la page des rendez-vous
            exit();
        }
    
        // Charger les services, patients et créneaux horaires
        $services = $this->serviceModel->getAll();
        $patients = $this->patientModel->getAll();  // Charger tous les patients
        $timeSlots = $this->appointmentModel->getTimeSlots();  // Récupérer les créneaux horaires
        $today = date('Y-m-d');
    
        // Passer les données nécessaires à la vue
        require '../app/Views/admin_appointment.php';
    }
    


    // admin_appointmentcontroller.php
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Créer une instance du AppointmentController pour utiliser sa méthode update
            $appointmentController = new AppointmentController();

            // Appeler la méthode update de AppointmentController
            $appointmentController->update();

            // Exit après la redirection pour s'assurer que le code suivant ne s'exécute pas
            header('Location: index.php?page=admin_appointment');
            exit();
        }

        // Charger la vue pour la page de modification du rendez-vous
        require '../app/Views/admin_appointment.php';
    }


    public function delete() {
        // Vérifier si l'ID est présent dans les données POST
        if (isset($_POST['appointment_id'])) {
            $appointmentId = $_POST['appointment_id'];
            
            // Appel à la méthode du modèle pour supprimer le rendez-vous
            if ($this->appointmentModel->delete($appointmentId)) {
                // Redirige après la suppression
                header('Location: index.php?page=admin_appointment'); 
                exit();
            } else {
                die("Erreur lors de la suppression du rendez-vous.");
            }
        } else {
            // Si l'ID n'est pas passé dans les données POST
            die("ID du rendez-vous manquant.");
        }
    }
    

    public function search() {
        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $searchTerm = trim($_GET['search']);
            $appointments = $this->appointmentModel->searchAppointments($searchTerm);
            require '../app/Views/admin_appointments.php';
        } else {
            header("Location: index.php?page=admin_appointment&action=search");
            exit();
        }
    }
}