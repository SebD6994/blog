<?php

require_once '../app/Models/Appointment.php';
require_once '../app/Controllers/AppointmentController.php';
require_once '../app/Models/Service.php';
require_once '../app/Models/Patient.php';
require_once '../app/Models/Home.php';

class Admin_appointmentController {
    private $db;
    private $appointmentModel;
    private $homeModel;

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
        
        // Vérification du contenu de $_POST pour s'assurer que l'ID du patient est bien envoyé
        var_dump($_POST); // Ajoute ceci pour voir tout ce qui est envoyé dans le formulaire
        exit(); // Pour arrêter l'exécution et voir le contenu

        if (!$this->isLoggedIn()) {
            $_SESSION['error'] = "Vous devez être connecté pour créer un rendez-vous.";
            header("Location: index.php?page=appointments");
            exit();
        }

        // Récupérer les données du formulaire
        $appointmentDate = $_POST['appointment_date'];
        $selectedTimeSlot = $_POST['appointment_time'];
        $selectedPatientId = $_POST['patient_id']; // Utilisation de l'ID du patient choisi par l'administrateur
        $appointmentDateTimeString = $appointmentDate . ' ' . $selectedTimeSlot;
        
        // Création de l'objet DateTime pour vérifier la validité de la date et de l'heure
        try {
            $appointmentDateTime = new DateTime($appointmentDateTimeString);
        } catch (Exception $e) {
            $_SESSION['error'] = "Format de date ou d'heure invalide.";
            header("Location: index.php?page=appointments");
            exit();
        }

        $now = new DateTime();
        if ($appointmentDateTime <= $now) {
            $_SESSION['error'] = "La date et l'heure du rendez-vous doivent être dans le futur.";
            header("Location: index.php?page=appointments");
            exit();
        }

        try {
            // Créer le rendez-vous en utilisant l'ID du patient choisi par l'administrateur
            $this->appointmentModel->create([
                'appointment_date' => $appointmentDateTime->format('Y-m-d H:i:s'),
                'service_id' => $_POST['service_id'],
                'patient_id' => $selectedPatientId // Utilisation du patient sélectionné par l'administrateur
            ]);

            $_SESSION['success'] = "Rendez-vous créé avec succès.";
            header("Location: index.php?page=appointments");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: index.php?page=appointments");
            exit();
        }
    }

    // Charger les services, patients et créneaux horaires
    $services = $this->serviceModel->getAll();
    $patients = $this->patientModel->getAll(); 
    $timeSlotsData = $this->appointmentModel->getTimeSlots(); 
    $today = date('Y-m-d');

    require '../app/Views/Appointment.php';
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