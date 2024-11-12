<?php

require_once '../app/Models/Patient.php';
require_once '../app/Models/Home.php';

class PatientController {
    private $patientModel;
    private $homeModel;

    public function __construct($db) {
        $this->patientModel = new Patient($db);
        $this->homeModel = new Home($db);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index($errorMessage = null, $successMessage = null) {
        $accountData = null;
        $appointments = [];
        $bannerImagePath = $this->homeModel->getBannerImage();

        if ($this->isLoggedIn()) {
            $patientId = $_SESSION['patient']['id'];
            $accountData = $this->patientModel->getPatientAccountData($patientId);
            $appointments = $this->patientModel->getAppointments($patientId);
        } else {
            $errorMessage = "Veuillez vous connecter.";
        }

        require '../app/Views/patient.php'; 
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $_SESSION['message'] = "Les mots de passe ne correspondent pas.";
                header("Location: index.php?page=patients");
                exit();
            }

            $data = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'password' => $_POST['password'],
                'role' => $_POST['role']
            ];

            $success = $this->patientModel->create($data);

            if ($success) {
                $_SESSION['message'] = "Inscription réussie. Vous pouvez vous connecter.";
                header("Location: index.php?page=patients");
                exit();
            } else {
                $_SESSION['message'] = "Erreur lors de la création du compte.";
                header("Location: index.php?page=patients");
                exit();
            }
        }
    }
    
    public function update() {
        if (!isset($_SESSION['patient']['id'])) {
            header("Location: index.php?page=patients");
            exit();
        }

        $id = $_SESSION['patient']['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'role' => $_POST['role'],
            ];

            $existingPatient = $this->patientModel->getByEmail($data['email']);
            if ($existingPatient && $existingPatient['id'] !== $id) {
                $errorMessage = "L'email est déjà utilisé par un autre compte.";
                $this->index($errorMessage);
                return;
            }

            try {
                $success = $this->patientModel->update($id, $data);
                if ($success) {
                    $_SESSION['patient'] = array_merge($_SESSION['patient'], $data);
                    header("Location: index.php?page=patients");
                    exit();
                } else {
                    throw new Exception("Erreur lors de la mise à jour des informations du patient.");
                }
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
                error_log("Erreur lors de la mise à jour: " . $errorMessage);
                $this->index($errorMessage);
            }
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $patient = $this->patientModel->authenticate($email, $password);

            if ($patient) {
                $_SESSION['patient'] = [
                    'id' => $patient->id,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'role' => $patient->role
                ];

                if ($patient->role === 'admin') {
                    header('Location: index.php?page=admin_patient');
                    exit();
                } else {
                    $this->index();
                }
            } else {
                $errorMessage = "Email ou mot de passe incorrect.";
                $this->index($errorMessage);
            }
        }
    }

    public function isLoggedIn() {
        return isset($_SESSION['patient']['id']);
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?page=patients");
        exit();
    }
}