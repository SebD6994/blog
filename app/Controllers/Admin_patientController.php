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
    
        public function update($id) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Récupération des données du formulaire
                $data = [
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'email' => $_POST['email'],
                    'phone' => $_POST['phone'],
                ];
        
                // Appel de la méthode update du modèle
                if ($this->patientModel->update($id, $data)) {
                    // Redirection ou message de succès
                    header("Location: index.php?page=admin_patient&message=update_success");
                    exit();
                } else {
                    // Gestion de l'erreur, par exemple un message d'erreur
                    $errorMessage = "Erreur lors de la mise à jour du patient. L'email pourrait déjà être utilisé.";
                }
            }
        
            // Afficher le formulaire ou autre contenu ici, si nécessaire
        }
        
    
    // Supprimer un patient
    public function delete() {
        if (isset($_POST['patient_id'])) {
            $patientId = $_POST['patient_id'];
    
            // Attempt deletion directly through patientModel
            $success = $this->patientModel->delete($patientId);
            
            if ($success) {
                // Successful deletion, redirect to the patient list
                $_SESSION['message'] = "Patient supprimé avec succès.";
                header("Location: index.php?page=admin_patient");
                exit();
            } else {
                // If deletion fails, set an error message
                $_SESSION['message'] = "Impossible de supprimer le patient.";
                header("Location: index.php?page=admin_patient");
                exit();
            }
        } else {
            echo "Patient ID not set in POST data.<br>";
        }
    }
    
    // Chercher un patient
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