<?php

require_once '../app/Models/Patient.php';
require_once('../app/controllers/PatientController.php');

class Admin_patientController {
    private $patientModel;

    public function __construct($db) {
        $this->patientModel = new Patient($db);
        $this->patientController = new PatientController($db);
        
        // Démarrer la session dans le constructeur si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Afficher la liste des patients
    public function index() {
        $patients = $this->patientModel->getAll(); // Méthode pour récupérer tous les patients
        require '../app/Views/admin_patient.php'; // Inclure la vue
    }

        // Déléguer les actions des patients au PatientController
        public function create() {
            $this->patientController->create();
        }
    
        public function update() {
            $this->patientController->update();
        }
    
        public function delete() {
            if (isset($_GET['id'])) {
                $patientId = $_GET['id'];
                $this->patientController->delete($patientId);
            }
        }
    
        public function search() {
            if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
                $searchTerm = trim($_GET['search']);
                $patients = $this->adminModel->searchPatients($searchTerm);
        
                // Si la requête est AJAX, renvoyer les résultats en JSON
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    echo json_encode($patients);
                    exit();
                } else {
                    // Sinon afficher la page normalement
                    $appointments = $this->adminModel->getAppointments();
                    $services = $this->adminModel->getServices();
                    $news = $this->adminModel->getNews();
                    $openingHours = $this->homeModel->getOpeningHours();
                    require '../app/Views/admin_patient.php';
                }
            } else {
                header("Location: index.php?page=admin_patient");
                exit();
            }
        }
        
        
}