<?php

require_once '../app/Models/Patient.php';
require_once('../app/controllers/PatientController.php');

class Admin_patientController {
    private $patientModel;

    public function __construct($db) {
        $this->patientModel = new Patient($db);
        $this->patientController = new PatientController($db);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        $patients = $this->patientModel->getAll();
        require '../app/Views/admin_patient.php';
    }

    public function create() {
        $this->patientController->create();
    }
    
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
            ];

            if ($this->patientModel->update($id, $data)) {
                header("Location: index.php?page=admin_patient&message=update_success");
                exit();
            } else {
                $errorMessage = "Erreur lors de la mise à jour du patient. L'email pourrait déjà être utilisé.";
            }
        }
    }
    
    public function delete() {
        if (isset($_POST['patient_id'])) {
            $patientId = $_POST['patient_id'];
            $success = $this->patientModel->delete($patientId);
            
            if ($success) {
                $_SESSION['message'] = "Patient supprimé avec succès.";
                header("Location: index.php?page=admin_patient");
                exit();
            } else {
                $_SESSION['message'] = "Impossible de supprimer le patient.";
                header("Location: index.php?page=admin_patient");
                exit();
            }
        } else {
            echo "Patient ID not set in POST data.<br>";
        }
    }
    
    public function search() {
        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $searchTerm = trim($_GET['search']);
            $patients = $this->adminModel->searchPatients($searchTerm);

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode($patients);
                exit();
            } else {
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