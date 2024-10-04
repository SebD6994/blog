<?php

require_once '../app/Models/Patient.php';

class AuthController {
    private $patientModel;

    public function __construct($db) {
        $this->patientModel = new Patient($db);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtenez le nom d'utilisateur et le mot de passe des données POST
            $username = $_POST['username'];
            $password = $_POST['password'];
    
            // Authentifiez l'utilisateur
            $patient = $this->patientModel->authenticate($username, $password);
    
            if ($patient) {
                // Démarrez la session et stockez les informations du patient
                session_start();
                $_SESSION['patient'] = [
                    'id' => $patient->id,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'role' => $patient->role // Ajout du rôle à la session
                ];
                
                // Redirigez vers la page appropriée selon le rôle
                if ($patient->role === 'admin') {
                    header('Location: index.php?page=admin'); // Redirigez vers la page admin
                } else {
                    header('Location: index.php?page=patient.php'); // Redirigez vers la page des rendez-vous
                }
                exit();
            } else {
                // Gestion de l'échec de la connexion
                $error = "Identifiant ou mot de passe invalide.";
            }
        }
        
        // Chargez la vue de connexion si c'est une requête GET ou après un échec de connexion
        require '../app/Views/patient.php'; // Votre fichier de vue de connexion
    }    

    public function logout() {
        session_start();
        session_destroy(); // Détruire la session
        header('Location: ../app/Views/patient.php'); // Redirection après déconnexion
        exit();
    }
}