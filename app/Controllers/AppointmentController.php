<?php

require_once '../app/Models/Appointment.php';
require_once '../app/Models/Patient.php';
require_once '../app/Models/Service.php';

class AppointmentController {
    private $appointmentModel;
    private $patientModel; 
    private $serviceModel;

    public function __construct($db) {
        $this->appointmentModel = new Appointment($db);
        $this->patientModel = new Patient($db);
        $this->serviceModel = new Service($db);

        // Assurez-vous que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        // Vérifier si le patient est connecté
        $patientId = $this->isLoggedIn() ? $_SESSION['patient']['id'] : null;
        $patientData = null;
        $appointments = [];

        if ($patientId !== null) {
            // Récupérer les données du patient si connecté
            $patientData = $this->patientModel->getById($patientId);
            // Récupérer les rendez-vous pour le patient connecté
            $appointments = $this->appointmentModel->getAppointmentsByPatientId($patientId);
        }

        // Récupérer tous les services disponibles
        $services = $this->serviceModel->getAll();

        // Charger la vue des rendez-vous
        require '../app/Views/Appointment.php'; // Passer les données à la vue
    }

    // Méthode pour créer un rendez-vous
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier si le patient est connecté
            if (!$this->isLoggedIn()) {
                echo "Vous devez être connecté pour créer un rendez-vous.";
                return;
            }
    
            // Rassembler les données du formulaire
            $appointmentDateTime = $_POST['appointment_date'] . ' ' . $_POST['appointment_time'];
    

            // Créer le rendez-vous dans la base de données
            $this->appointmentModel->create([
                'appointment_date' => $appointmentDateTime,
                'service_id' => $_POST['service_id'],
                'patient_id' => $_SESSION['patient']['id'] // Utiliser la structure de session
            ]);
    
            // Rediriger après la création du rendez-vous
            header('Location: index.php?page=patients&action=view');
            exit();
        }
    
        // Si ce n'est pas une requête POST, montrer le formulaire pour ajouter un rendez-vous
        // Charger les services disponibles
        $services = $this->serviceModel->getAll();
    
        // Générer des créneaux horaires pour aujourd'hui
        $today = date('Y-m-d'); // Obtenir la date d'aujourd'hui
        $timeSlots = $this->appointmentModel->getAvailableTimeSlots($today); // Obtenir les créneaux horaires disponibles
    
        // Charger la vue des rendez-vous et passer les données
        require '../app/Views/Appointment.php'; // Passer les données à la vue
    }

    public function update() {
        // Vérifier si le patient est connecté
        if (!$this->isLoggedIn()) {
            echo "Vous devez être connecté pour modifier un rendez-vous.";
            return;
        }
    
        // Vérifier si le formulaire a été soumis par la méthode POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données soumises par le formulaire
            $appointmentId = $_POST['appointment_id'];
            $appointmentDateTime = $_POST['appointment_date'] . ' ' . $_POST['appointment_time'];
            $serviceId = $_POST['service_id'];
            $status = $_POST['status']; // Récupération du statut
    
            // Récupérer le rendez-vous par son ID
            $appointment = $this->appointmentModel->getById($appointmentId);
    
            // Vérifier si le rendez-vous appartient bien au patient connecté
            $patientId = $_SESSION['patient']['id'];
    
            if ($appointment && $appointment['patient_id'] == $patientId) {
                // Vérifier si le nouveau créneau horaire est disponible
                $dateOnly = $_POST['appointment_date'];
                $timeSlots = $this->appointmentModel->getAvailableTimeSlots($dateOnly);
                if (!in_array($_POST['appointment_time'], $timeSlots)) {
                    echo "Ce créneau horaire n'est pas disponible.";
                    return;
                }

                // Mise à jour du rendez-vous avec les nouvelles données
                $this->appointmentModel->update($appointmentId, [
                    'appointment_date' => $appointmentDateTime,
                    'service_id' => $serviceId,
                    'status' => $status // Mise à jour du statut
                ]);
    
                // Rediriger vers la liste des rendez-vous après la mise à jour
                header('Location: index.php?page=appointments');
                exit();
            } else {
                echo "Vous n'êtes pas autorisé à modifier ce rendez-vous.";
            }
        }
    }    
    
    // Méthode pour supprimer un rendez-vous
    public function delete() {
        // Vérifier si le patient est connecté
        if (!$this->isLoggedIn()) {
            echo "Vous devez être connecté pour supprimer un rendez-vous.";
            return;
        }

        // Vérifier si l'ID du rendez-vous est présent dans la requête
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
            $appointmentId = $_POST['appointment_id'];
            
            // Vérifier si le rendez-vous appartient au patient connecté
            $patientId = $_SESSION['patient']['id'];
            $appointment = $this->appointmentModel->getById($appointmentId);
            
            if ($appointment && $appointment['patient_id'] == $patientId) {
                // Supprimer le rendez-vous
                $this->appointmentModel->delete($appointmentId);
                
                // Redirection après la suppression
                header('Location: index.php?page=appointments');
                exit();
            } else {
                echo "Vous n'êtes pas autorisé à supprimer ce rendez-vous.";
            }
        }
    }

    public function view() {
        // Vérifier si le patient est connecté
        if (!$this->isLoggedIn()) {
            echo "Vous devez être connecté pour voir votre profil.";
            return;
        }

        // Récupérer les données du patient et les rendez-vous
        $patientId = $_SESSION['patient']['id'];
        $patientData = $this->patientModel->getPatientAccountData($patientId);
        
        // Charger la vue du profil du patient
        require '../app/Views/patient.php'; // Passer les données à la vue
    }

    // Méthode pour obtenir les créneaux horaires disponibles pour une date donnée
    public function getTimeSlots() {
        // Vérifier si la date est fournie dans la requête
        if (isset($_GET['date'])) {
            $date = $_GET['date'];
            $timeSlots = $this->appointmentModel->getAvailableTimeSlots($date);

            // Retourner les créneaux horaires au format JSON
            header('Content-Type: application/json');
            echo json_encode(['timeSlots' => $timeSlots]);
        } else {
            // Si aucune date n'est fournie, retourner un tableau vide
            header('Content-Type: application/json');
            echo json_encode(['timeSlots' => []]);
        }
    }

    // Vérifier si le patient est connecté
    public function isLoggedIn() {
        return isset($_SESSION['patient']['id']);  // Vérifier la structure de session
    }
}