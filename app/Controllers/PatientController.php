<?php

require_once '../app/Models/Patient.php';
require_once '../app/Models/Appointment.php';

class PatientController {
    private $patientModel;
    private $appointmentModel;

    public function __construct($db) {
        $this->patientModel = new Patient($db);
        $this->appointmentModel = new Appointment($db);
        
        // Démarrer la session dans le constructeur si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Afficher les informations du compte patient si connecté
    public function index($errorMessage = null, $successMessage = null) {
        $accountData = null;

        if ($this->isLoggedIn()) {
            $patientId = $_SESSION['patient_id'];
            $accountData = $this->patientModel->getPatientAccountData($patientId); // Récupérer les données du compte patient
            
            // Récupérer les rendez-vous associés au patient
            $accountData['appointments'] = $this->appointmentModel->getAppointmentsByPatientId($patientId);

            // Vérifier le rôle et rediriger si c'est un admin
            if ($accountData['role'] === 'admin') {
                header("Location: index.php?page=admin"); // Redirection vers la vue admin
                exit();
            }
        }

        require '../app/Views/patient.php'; // Charger la vue avec toutes les données
    }

    // Créer un patient
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier si les mots de passe correspondent
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $errorMessage = "Les mots de passe ne correspondent pas.";
                $this->index($errorMessage); // Réafficher la page avec le message d'erreur
                return;
            }
            
            $data = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'password' => $_POST['password'], // Le hachage se fait dans le modèle
                'role' => $_POST['role']          // Récupérer le rôle du patient
            ];
            
            // Utilisez la méthode create() pour ajouter le patient
            if ($this->patientModel->create($data)) {
                $successMessage = "Le patient a été ajouté avec succès.";
            } else {
                $errorMessage = "Erreur lors de l'ajout du patient.";
            }

            $this->index($successMessage ?? $errorMessage); // Réafficher la page avec le message de succès ou d'erreur
        }
    }

    // Gérer la connexion
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            // Récupérer les informations du patient
            $patient = $this->patientModel->getByEmail($email);
            
            // Vérifier si le mot de passe est correct
            if ($patient && password_verify($password, $patient['password'])) {
                $_SESSION['patient_id'] = $patient['id'];
                $_SESSION['patient'] = $patient; // Stocker les données du patient dans la session
                
                // Vérifier le rôle et rediriger en conséquence
                if ($patient['role'] === 'admin') {
                    header("Location: index.php?page=admin"); // Redirection vers la vue admin
                } else {
                    header("Location: index.php?page=patients"); // Redirection vers la page des patients
                }
                exit();
            } else {
                $errorMessage = "Identifiants incorrects.";
                $this->index($errorMessage); // Affichez le formulaire de connexion avec un message d'erreur
            }
        }
    }

    // Déconnexion
    public function logout() {
        session_start(); // Assurez-vous que la session est démarrée
        session_unset(); // Effacer les données de la session
        session_destroy(); // Détruire la session
        header("Location: index.php?page=home"); // Redirection vers la page d'accueil
        exit();
    }

    // Vérifier si le patient est connecté
    public function isLoggedIn() {
        return isset($_SESSION['patient_id']);
    }
}