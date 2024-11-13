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
                echo "Vous devez être connecté pour créer un rendez-vous.";
                return;
            }

            $appointmentDateTime = $_POST['appointment_date'] . ' ' . $_POST['appointment_time'];
            $appointmentDate = new DateTime($appointmentDateTime);
            $now = new DateTime();

            if ($appointmentDate <= $now) {
                echo "La date et l'heure du rendez-vous doivent être dans le futur.";
                return;
            }

            try {
                $this->appointmentModel->create([
                    'appointment_date' => $appointmentDateTime,
                    'service_id' => $_POST['service_id'],
                    'patient_id' => $_SESSION['patient']['id']
                ]);
            } catch (Exception $e) {
                echo $e->getMessage();
                return;
            }

            header('Location: index.php?page=patients&action=view');
            exit();
        }

        $services = $this->serviceModel->getAll();
        $today = date('Y-m-d'); 
        $timeSlotsData = $this->appointmentModel->getAvailableTimeSlots();

        require '../app/Views/Appointment.php';
    }

    public function update() {
        if (!$this->isLoggedIn()) {
            echo "Vous devez être connecté pour modifier un rendez-vous.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $appointmentId = $_POST['appointment_id'];
            $appointmentDateTime = $_POST['appointment_date'] . ' ' . $_POST['appointment_time'];
            $serviceId = $_POST['service_id'];
            $appointment = $this->appointmentModel->getById($appointmentId);
            $patientId = $_SESSION['patient']['id'];

            if ($appointment && $appointment['patient_id'] == $patientId) {
                $dateOnly = $_POST['appointment_date'];
                $timeSlotsData = $this->appointmentModel->getAvailableTimeSlots($dateOnly);
                if (!in_array($_POST['appointment_time'], $timeSlotsData['available'])) {
                    echo "Ce créneau horaire n'est pas disponible.";
                    return;
                }

                $this->appointmentModel->update($appointmentId, [
                    'appointment_date' => $appointmentDateTime,
                    'service_id' => $serviceId
                ]);

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