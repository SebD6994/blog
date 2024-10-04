<?php

require_once '../app/Models/Appointment.php';
require_once '../app/Models/Patient.php';
require_once '../app/Models/Service.php'; // Assurez-vous d'importer le modèle Service

class AppointmentController {
    private $appointmentModel;
    private $patientModel; 
    private $serviceModel; // Modèle pour gérer les services

    public function __construct($db) {
        $this->appointmentModel = new Appointment($db);
        $this->patientModel = new Patient($db);
        $this->serviceModel = new Service($db); // Initialisation du modèle Service
    }

    public function index() {
        // Récupération des rendez-vous
        $appointments = $this->appointmentModel->getAll();
        
        // Vérifier si le patient est connecté
        $patientId = isset($_SESSION['patient_id']) ? $_SESSION['patient_id'] : null;
        $patientData = null;

        if ($patientId !== null) {
            $patientData = $this->patientModel->getById($patientId);
        }

        // Récupérer tous les services disponibles
        $services = $this->serviceModel->getAll();

        // Charger la vue des rendez-vous
        require '../app/Views/Appointment.php'; // Passer les données à la vue
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier si le patient est connecté
            $patientId = isset($_SESSION['patient_id']) ? $_SESSION['patient_id'] : null;
    
            if ($patientId === null) {
                echo "Vous devez être connecté pour créer un rendez-vous.";
                return;
            }

            // Récupérer les données du formulaire
            $appointmentDateTime = $_POST['appointment_date'] . ' ' . $_POST['appointment_time'];

            // Créer le rendez-vous dans la base de données
            $this->appointmentModel->create([
                'appointment_date' => $appointmentDateTime,
                'service_id' => $_POST['service_id'],
                'patient_id' => $patientId
            ]);

            // Rediriger vers la page de profil du patient après création
            header('Location: index.php?page=patients&action=view');
            exit();
        }

        // Si la requête n'est pas POST, afficher le formulaire d'ajout
        $this->index();
    }

    public function view() {
        // Vérifier si le patient est connecté
        $patientId = isset($_SESSION['patient_id']) ? $_SESSION['patient_id'] : null;

        if ($patientId === null) {
            echo "Vous devez être connecté pour voir votre profil.";
            return;
        }

        // Récupérer les données du patient et ses rendez-vous
        $patientData = $this->patientModel->getPatientAccountData($patientId);

        // Charger la vue du profil du patient
        require '../app/Views/patient.php'; // Passer les données à la vue
    }
}