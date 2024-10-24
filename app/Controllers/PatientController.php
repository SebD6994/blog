<?php

require_once '../app/Models/Patient.php';

class PatientController {
    private $patientModel;

    public function __construct($db) {
        $this->patientModel = new Patient($db);
        
        // Démarrer la session dans le constructeur si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Afficher les informations du compte patient si connecté
    public function index($errorMessage = null, $successMessage = null) {
        $accountData = null;
        $appointments = []; // Initialiser le tableau des rendez-vous

        if ($this->isLoggedIn()) {
            $patientId = $_SESSION['patient']['id'];
            $accountData = $this->patientModel->getPatientAccountData($patientId); // Récupérer les données du compte patient

            // Récupérer les rendez-vous du patient
            $appointments = $this->patientModel->getAppointments($patientId); // Appeler la nouvelle méthode

            // On n'effectue aucune redirection même si l'utilisateur est un admin.
            // Les informations de compte (y compris le rôle) seront affichées directement.
        } else {
            // Si l'utilisateur n'est pas connecté, afficher un message d'erreur
            $errorMessage = "Veuillez vous connecter.";
        }

        // Charger la vue avec toutes les données nécessaires
        require '../app/Views/patient.php'; 
    }

    // Créer un patient (inscription)
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier si les mots de passe correspondent
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $_SESSION['message'] = "Les mots de passe ne correspondent pas.";
                header("Location: index.php?page=patients"); // Rediriger vers la page des patients
                exit();
            }

            // Inclure le rôle sélectionné dans les données du patient
            $data = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'password' => $_POST['password'],
                'role' => $_POST['role'] // Récupérer le rôle depuis le formulaire
            ];

            // Insérer les données du patient dans la base de données
            $success = $this->patientModel->create($data);

            if ($success) {
                $_SESSION['message'] = "Inscription réussie. Vous pouvez vous connecter."; // Message de succès
                header("Location: index.php?page=patients"); // Rediriger vers la page des patients
                exit();
            } else {
                $_SESSION['message'] = "Erreur lors de la création du compte."; // Message d'échec
                header("Location: index.php?page=patients"); // Rediriger vers la page des patients
                exit();
            }
        }
    }
    
    // Mettre à jour un patient
    public function update() {
        // Vérifiez que le patient est connecté
        if (!isset($_SESSION['patient']['id'])) {
            header("Location: index.php?page=patients");
            exit();
        }

        if (isset($_POST['change_password'])) {
            $this->changePassword();
            return;
        }

        $id = $_SESSION['patient']['id']; // ID du patient à partir de la session

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $data = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'role' => $_POST['role'],  // Inclure le rôle dans les données
            ];

            // Vérification de l'unicité de l'email (si nécessaire)
            $existingPatient = $this->patientModel->getByEmail($data['email']);
            if ($existingPatient && $existingPatient['id'] !== $id) {
                $errorMessage = "L'email est déjà utilisé par un autre compte.";
                $this->index($errorMessage); // Afficher la vue avec le message d'erreur
                return;
            }

            // Appeler la fonction de mise à jour du modèle
            try {
                $success = $this->patientModel->update($id, $data);
                if ($success) {
                    // Mettre à jour les informations dans la session
                    $_SESSION['patient'] = array_merge($_SESSION['patient'], $data);
                    header("Location: index.php?page=patients");
                    exit();
                } else {
                    throw new Exception("Erreur lors de la mise à jour des informations du patient.");
                }
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
                error_log("Erreur lors de la mise à jour: " . $errorMessage);
                $this->index($errorMessage); // Afficher la vue avec le message d'erreur
            }
        }
    }
    
    // Changer le mot de passe du patient
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier si l'utilisateur est connecté
            if (!isset($_SESSION['patient']['id'])) {
                header("Location: index.php?page=patients");
                exit();
            }

            $patientId = $_SESSION['patient']['id'];
            $oldPassword = $_POST['old_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            // Récupérer les données du patient
            $patient = $this->patientModel->getById($patientId);

            // Vérifier si l'ancien mot de passe est correct
            if (!$patient || !password_verify($oldPassword, $patient['password'])) {
                $errorMessage = "L'ancien mot de passe est incorrect.";
                $this->index($errorMessage);
                return;
            }

            // Vérifier si le nouveau mot de passe et la confirmation correspondent
            if ($newPassword !== $confirmPassword) {
                $errorMessage = "Les nouveaux mots de passe ne correspondent pas.";
                $this->index($errorMessage);
                return;
            }

            // Mettre à jour le mot de passe dans la base de données
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $success = $this->patientModel->updatePassword($patientId, $hashedPassword);

            if ($success) {
                $_SESSION['message'] = "Mot de passe mis à jour avec succès.";
                header("Location: index.php?page=patients");
                exit();
            } else {
                $errorMessage = "Erreur lors de la mise à jour du mot de passe.";
                $this->index($errorMessage);
            }
        }
    }

    // Supprimer un patient
    public function delete($id) {
        $success = $this->patientModel->delete($id);
        
        if ($success) {
            // Suppression réussie, redirection vers la liste des patients
            header("Location: index.php?page=patients");
            exit();
        } else {
            // Afficher une erreur si la suppression échoue
            $errorMessage = "Impossible de supprimer le patient.";
            $this->index($errorMessage); // Afficher la vue patients avec le message d'erreur
        }
    }

    // Authentification du patient
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Vérifier si l'email et le mot de passe sont corrects
            $patient = $this->patientModel->authenticate($email, $password);

            if ($patient) {
                // Stocker l'ID du patient et ses données dans la session
                $_SESSION['patient'] = [
                    'id' => $patient->id,  // Ajout de l'ID du patient
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'role' => $patient->role
                ];

                // Redirection en fonction du rôle
                if ($patient->role === 'admin') {
                    header('Location: index.php?page=admin_patient'); // Rediriger vers la page d'accueil de l'administrateur
                    exit();
                } else {
                    // Pour les patients, recharge la même page
                    $this->index(); // Charge la vue avec les nouvelles données de session
                }
            } else {
                // Si la connexion échoue, afficher un message d'erreur
                $errorMessage = "Email ou mot de passe incorrect.";
                $this->index($errorMessage); // Afficher la vue avec le message d'erreur
            }
        }
    }

    // Vérifier si un utilisateur est connecté
    public function isLoggedIn() {
        return isset($_SESSION['patient']['id']);  // Uniformiser l'accès à l'ID
    }

    // Gérer la déconnexion
    public function logout() {
        session_destroy();
        header("Location: index.php?page=patients"); // Redirection vers la page des patients
        exit();
    }
}