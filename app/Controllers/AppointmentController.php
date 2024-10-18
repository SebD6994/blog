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

        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        // Check if the patient is logged in
        $patientId = $this->isLoggedIn() ? $_SESSION['patient']['id'] : null;
        $patientData = null;
        $appointments = [];

        if ($patientId !== null) {
            // Retrieve patient data if logged in
            $patientData = $this->patientModel->getById($patientId);
            // Get appointments for the logged-in patient
            $appointments = $this->appointmentModel->getAppointmentsByPatientId($patientId);
        }

        // Retrieve all available services
        $services = $this->serviceModel->getAll();

        // Load the appointment view
        require '../app/Views/Appointment.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the patient is logged in
            if (!$this->isLoggedIn()) {
                echo "Vous devez être connecté pour créer un rendez-vous.";
                return;
            }

            // Gather form data
            $appointmentDateTime = $_POST['appointment_date'] . ' ' . $_POST['appointment_time'];

            // Validate the appointment date and time
            $appointmentDate = new DateTime($appointmentDateTime);
            $now = new DateTime();

            if ($appointmentDate <= $now) {
                echo "La date et l'heure du rendez-vous doivent être dans le futur.";
                return; // Exit to prevent creation
            }

            // Create the appointment in the database
            try {
                $this->appointmentModel->create([
                    'appointment_date' => $appointmentDateTime,
                    'service_id' => $_POST['service_id'],
                    'patient_id' => $_SESSION['patient']['id']
                ]);
            } catch (Exception $e) {
                echo $e->getMessage();
                return; // Exit on error
            }

            // Redirect after appointment creation
            header('Location: index.php?page=patients&action=view');
            exit();
        }

        // If not a POST request, show the form for adding an appointment
        // Load available services
        $services = $this->serviceModel->getAll();

        // Generate available time slots for today
        $today = date('Y-m-d'); 
        $timeSlotsData = $this->appointmentModel->getAvailableTimeSlots($today);
        $availableSlots = $timeSlotsData['available'];
        $bookedSlots = $timeSlotsData['booked'];

        // Load the appointment view with the data
        require '../app/Views/Appointment.php';
    }

    public function update() {
        // Check if the patient is logged in
        if (!$this->isLoggedIn()) {
            echo "Vous devez être connecté pour modifier un rendez-vous.";
            return;
        }

        // Check if the form was submitted via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve submitted data
            $appointmentId = $_POST['appointment_id'];
            $appointmentDateTime = $_POST['appointment_date'] . ' ' . $_POST['appointment_time'];
            $serviceId = $_POST['service_id'];

            // Retrieve the appointment by its ID
            $appointment = $this->appointmentModel->getById($appointmentId);

            // Check if the appointment belongs to the logged-in patient
            $patientId = $_SESSION['patient']['id'];

            if ($appointment && $appointment['patient_id'] == $patientId) {
                // Check if the new time slot is available
                $dateOnly = $_POST['appointment_date'];
                $timeSlotsData = $this->appointmentModel->getAvailableTimeSlots($dateOnly);
                if (!in_array($_POST['appointment_time'], $timeSlotsData['available'])) {
                    echo "Ce créneau horaire n'est pas disponible.";
                    return;
                }

                // Update the appointment with the new data
                $this->appointmentModel->update($appointmentId, [
                    'appointment_date' => $appointmentDateTime,
                    'service_id' => $serviceId
                ]);

                // Redirect to the appointments list after update
                header('Location: index.php?page=appointments');
                exit();
            } else {
                echo "Vous n'êtes pas autorisé à modifier ce rendez-vous.";
            }
        }
    }

    public function delete() {
        // Check if the patient is logged in
        if (!$this->isLoggedIn()) {
            echo "Vous devez être connecté pour supprimer un rendez-vous.";
            return;
        }

        // Check if the appointment ID is present in the request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
            $appointmentId = $_POST['appointment_id'];

            // Check if the appointment belongs to the logged-in patient
            $patientId = $_SESSION['patient']['id'];
            $appointment = $this->appointmentModel->getById($appointmentId);

            if ($appointment && $appointment['patient_id'] == $patientId) {
                // Delete the appointment
                $this->appointmentModel->delete($appointmentId);

                // Redirect after deletion
                header('Location: index.php?page=appointments');
                exit();
            } else {
                echo "Vous n'êtes pas autorisé à supprimer ce rendez-vous.";
            }
        }
    }

    public function view() {
        // Check if the patient is logged in
        if (!$this->isLoggedIn()) {
            echo "Vous devez être connecté pour voir votre profil.";
            return;
        }

        // Retrieve patient data and appointments
        $patientId = $_SESSION['patient']['id'];
        $patientData = $this->patientModel->getById($patientId);
        $appointments = $this->appointmentModel->getAppointmentsByPatientId($patientId);
        
        // Load the patient profile view
        require '../app/Views/patient.php';
    }

    public function getTimeSlots() {
        // Check if the date is provided in the request
        if (isset($_GET['date'])) {
            $date = $_GET['date'];
            $timeSlotsData = $this->appointmentModel->getAvailableTimeSlots($date); // Get time slots

            // Return time slots in JSON format
            header('Content-Type: application/json');
            echo json_encode(['timeSlots' => $timeSlotsData]);
        } else {
            // If no date is provided, return an empty array
            header('Content-Type: application/json');
            echo json_encode(['timeSlots' => []]);
        }
    }

    // Check if the patient is logged in
    public function isLoggedIn() {
        return isset($_SESSION['patient']['id']);
    }
}
?>