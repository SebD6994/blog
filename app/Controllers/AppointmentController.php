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

        // Ensure the session is started
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
            // Fetch patient data if logged in
            $patientData = $this->patientModel->getById($patientId);
            // Fetch appointments for the logged-in patient
            $appointments = $this->appointmentModel->getAppointmentsByPatientId($patientId);
        }

        // Fetch all available services
        $services = $this->serviceModel->getAll();

        // Load the appointments view
        require '../app/Views/Appointment.php'; // Pass the data to the view
    }

    // Create appointment method remains unchanged
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the patient is logged in
            if (!$this->isLoggedIn()) {
                echo "Vous devez être connecté pour créer un rendez-vous.";
                return;
            }

            // Gather data from the form
            $appointmentDateTime = $_POST['appointment_date'] . ' ' . $_POST['appointment_time'];

            // Create the appointment in the database
            $this->appointmentModel->create([
                'appointment_date' => $appointmentDateTime,
                'service_id' => $_POST['service_id'],
                'patient_id' => $_SESSION['patient']['id'] // Use session structure
            ]);

            // Redirect after creating the appointment
            header('Location: index.php?page=patients&action=view');
            exit();
        }

        // If not POST, show the form to add an appointment
        $this->index();
    }

    public function view() {
        // Check if the patient is logged in
        if (!$this->isLoggedIn()) {
            echo "Vous devez être connecté pour voir votre profil.";
            return;
        }

        // Fetch patient data and appointments
        $patientId = $_SESSION['patient']['id'];
        $patientData = $this->patientModel->getPatientAccountData($patientId);
        
        // Load the patient profile view
        require '../app/Views/patient.php'; // Pass the data to the view
    }

    // Check if the patient is logged in
    public function isLoggedIn() {
        return isset($_SESSION['patient']['id']);  // Check session structure
    }

    // Other methods remain unchanged...
}