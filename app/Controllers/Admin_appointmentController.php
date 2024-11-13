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

    public function getAvailableSlots() {
        
       
       
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'patient_id' => $_POST['patient_id'] ?? null,
                'service_id' => $_POST['service_id'] ?? null,
                'date' => $_POST['date'] ?? null,
                'time' => $_POST['time'] ?? null,
            ];

            if ($this->appointmentModel->createAppointment($data)) {
                header('Location: index.php?page=admin_appointment&action=create'); 
                exit();
            } else {
                die("Erreur lors de la création du rendez-vous.");
            }
        }

        require '../app/Views/admin_appointment.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['id'])) {
                $appointmentId = $_POST['id'];
                $data = [
                    'patient_id' => $_POST['patient_id'] ?? null,
                    'service_id' => $_POST['service_id'] ?? null,
                    'date' => $_POST['date'] ?? null,
                    'time' => $_POST['time'] ?? null,
                ];

                if ($this->appointmentModel->updateAppointment($appointmentId, $data)) {
                    header('Location: index.php?page=admin_appointment&action=update'); 
                    exit();
                } else {
                    die("Erreur lors de la mise à jour du rendez-vous.");
                }
            } else {
                die("ID du rendez-vous manquant.");
            }
        }

        if (isset($_GET['id'])) {
            $appointmentId = $_GET['id'];
            $appointment = $this->appointmentModel->getAppointmentById($appointmentId);
            require '../app/Views/admin_appointment.php';
        } else {
            die("ID du rendez-vous manquant.");
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $appointmentId = $_GET['id'];
            if ($this->appointmentModel->deleteAppointment($appointmentId)) {
                header('Location: index.php?page=admin_appointment&action=delete'); 
                exit();
            } else {
                die("Erreur lors de la suppression du rendez-vous.");
            }
        } else {
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