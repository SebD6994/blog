<?php

require_once '../app/Models/Appointment.php';
require_once '../app/Models/Patient.php';
require_once '../app/Models/Service.php';
require_once '../app/Models/Home.php';

class AppointmentController {
    private $appointmentModel;
    private $patientModel; 
    private $serviceModel;
    private $homeModel;
    

    public function __construct($db) {
        $this->appointmentModel = new Appointment($db);
        $this->patientModel = new Patient($db);
        $this->serviceModel = new Service($db);
        $this->homeModel = new Home($db);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        $patientId = $this->isLoggedIn() ? $_SESSION['patient']['id'] : null;
        $patientData = null;
        $appointments = [];
        $bannerImagePath = $this->homeModel->getBannerImage();
        $timeSlots = $this->appointmentModel->getTimeSlots();

        if ($patientId !== null) {
            $patientData = $this->patientModel->getById($patientId);
            $appointments = $this->appointmentModel->getAppointmentsByPatientId($patientId);
        }

        $services = $this->serviceModel->getAll();
        require '../app/Views/Appointment.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->isLoggedIn()) {
                $_SESSION['error'] = "Vous devez être connecté pour créer un rendez-vous.";
                header("Location: index.php?page=appointments"); // Redirection vers la page de création de rendez-vous
                exit();
            }
    
            $appointmentDate = $_POST['appointment_date'];
            $selectedTimeSlot = $_POST['appointment_time'];
            $appointmentDateTimeString = $appointmentDate . ' ' . $selectedTimeSlot;
    
            try {
                $appointmentDateTime = new DateTime($appointmentDateTimeString);
            } catch (Exception $e) {
                $_SESSION['error'] = "Format de date ou d'heure invalide.";
                header("Location: index.php?page=appointments"); // Redirection vers la page de création de rendez-vous
                exit();
            }
    
            $now = new DateTime();
    
            if ($appointmentDateTime <= $now) {
                $_SESSION['error'] = "La date et l'heure du rendez-vous doivent être dans le futur.";
                header("Location: index.php?page=appointments"); // Redirection vers la page de création de rendez-vous
                exit();
            }
    
            try {
                $this->appointmentModel->create([
                    'appointment_date' => $appointmentDateTime->format('Y-m-d H:i:s'),
                    'service_id' => $_POST['service_id'],
                    'patient_id' => $_SESSION['patient']['id']
                ]);
                $_SESSION['success'] = "Rendez-vous créé avec succès.";
                header("Location: index.php?page=appointments"); // Redirection après succès
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header("Location: index.php?page=appointments"); // Redirection en cas d'erreur
                exit();
            }
        }
    
        // Charger les services et les créneaux horaires disponibles
        $services = $this->serviceModel->getAll();
        $today = date('Y-m-d');
        $timeSlotsData = $this->appointmentModel->getTimeSlots();  // Assurez-vous que ce tableau contient les heures sous forme de chaînes, ex: "10:00", "11:30"
    
        require '../app/Views/Appointment.php';
    }
    
    
    
    

    public function update() {
        if (!$this->isLoggedIn()) {
            echo "Vous devez être connecté pour modifier un rendez-vous.";
            return;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $appointmentId = $_POST['appointment_id'];
            $appointmentDate = $_POST['appointment_date']; // Récupérer la date
            $selectedTimeSlot = $_POST['time']; // Récupérer l'heure sélectionnée (créneau)
            $appointmentDateTimeString = $appointmentDate . ' ' . $selectedTimeSlot; // Combiner la date et l'heure pour une chaîne
    
            // Créer un objet DateTime pour valider et formater la date/heure
            try {
                $appointmentDateTime = new DateTime($appointmentDateTimeString);
            } catch (Exception $e) {
                echo "Format de date ou d'heure invalide.";
                return;
            }
    
            $serviceId = $_POST['service_id'];
            $appointment = $this->appointmentModel->getById($appointmentId);
            $patientId = $_SESSION['patient']['id'];
    
            if ($appointment && $appointment['patient_id'] == $patientId) {
                // Mettre à jour l'enregistrement dans la base de données
                $this->appointmentModel->update($appointmentId, [
                    'appointment_date' => $appointmentDateTime->format('Y-m-d H:i:s'), // Passer la date/heure formatée
                    'service_id' => $serviceId
                ]);
    
                // Rediriger après la mise à jour
                header('Location: index.php?page=appointments');
                exit();
            } else {
                echo "Vous n'êtes pas autorisé à modifier ce rendez-vous.";
            }
        }
    }
    

    public function delete() {
        if (!$this->isLoggedIn()) {
            echo "Vous devez être connecté pour supprimer un rendez-vous.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
            $appointmentId = $_POST['appointment_id'];
            $patientId = $_SESSION['patient']['id'];
            $appointment = $this->appointmentModel->getById($appointmentId);

            if ($appointment && $appointment['patient_id'] == $patientId) {
                $this->appointmentModel->delete($appointmentId);
                header('Location: index.php?page=appointments');
                exit();
            } else {
                echo "Vous n'êtes pas autorisé à supprimer ce rendez-vous.";
            }
        }
    }

    public function view() {
        if (!$this->isLoggedIn()) {
            echo "Vous devez être connecté pour voir votre profil.";
            return;
        }

        $patientId = $_SESSION['patient']['id'];
        $patientData = $this->patientModel->getById($patientId);
        $appointments = $this->appointmentModel->getAppointmentsByPatientId($patientId);
        
        require '../app/Views/patient.php';
    }

    public function getTimeSlots() {
        if (isset($_GET['date'])) {
            $date = $_GET['date'];
            $timeSlotsData = $this->appointmentModel->getAvailableTimeSlots($date);
            header('Content-Type: application/json');
            echo json_encode(['timeSlots' => $timeSlotsData]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['timeSlots' => []]);
        }
    }

    public function isLoggedIn() {
        return isset($_SESSION['patient']['id']);
    }
}
?>