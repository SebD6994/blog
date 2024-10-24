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
        // Récupérer toutes les rendez-vous pour l'affichage
        $appointments = $this->appointmentModel->getAll();
    
        // Charger les services
        $serviceModel = new Service($this->db);
        $services = $serviceModel->getAll();
    
        // Charger les patients
        $patientModel = new Patient($this->db);
        $patients = $patientModel->getAll();
    
        // Vérifier si une date est spécifiée pour récupérer les créneaux horaires
        if (isset($_GET['date'])) {
            $date = $_GET['date'];
            $dayOfWeek = date('w', strtotime($date)); // 0 (dimanche) à 6 (samedi)
            $timeSlots = $this->appointmentModel->getTimeSlots($dayOfWeek);
        } else {
            $timeSlots = ['available' => [], 'booked' => []]; // Aucun créneau disponible par défaut
        }
    
        // Charger la vue avec les rendez-vous et les créneaux disponibles
        require '../app/Views/admin_appointment.php'; // Vue pour afficher les rendez-vous
    }
    
    

    public function getAvailableSlots() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['date'])) {
                $date = $_GET['date'];
                $dayOfWeek = date('w', strtotime($date));
                $timeSlots = $this->appointmentModel->getTimeSlots($dayOfWeek);
    
                // Définir l'en-tête pour le type de contenu HTML
                header('Content-Type: text/html');
    
                // Afficher les options
                echo $timeSlots;
            } else {
                // Si la date est manquante, renvoyer une option d'erreur
                header('Content-Type: text/html');
                echo '<option value="">La date est manquante.</option>';
            }
        } else {
            // Si la méthode HTTP n'est pas GET, renvoyer une option d'erreur
            header('Content-Type: text/html');
            echo '<option value="">Méthode non autorisée.</option>';
        }
    }
    
    
    
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traiter les données du formulaire pour créer un rendez-vous
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

        // Afficher la vue pour le formulaire de création de rendez-vous
        require '../app/Views/admin_appointment.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier les données soumises
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

        // Récupérer les données du rendez-vous pour préremplir le formulaire
        if (isset($_GET['id'])) {
            $appointmentId = $_GET['id'];
            $appointment = $this->appointmentModel->getAppointmentById($appointmentId);
            require '../app/Views/admin_appointment.php'; // Vue pour le formulaire de mise à jour
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
            require '../app/Views/admin_appointments.php'; // Vue avec les résultats de recherche
        } else {
            header("Location: index.php?page=admin_appointment&action=search");
            exit();
        }
    }
}