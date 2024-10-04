<?php

require_once '../app/Models/Admin.php';

class AdminController {
    private $adminModel;
    private $model;
    private $db;

    public function __construct($db) {
        $this->adminModel = new Admin($db);
        $this->model = new Admin($db);
        $this->db = $db;
    }

    public function index() {
        // Récupérer les données nécessaires pour la vue
        $appointments = $this->adminModel->getAppointments();
        $services = $this->adminModel->getServices();
        $news = $this->adminModel->getNews();
        $patients = $this->adminModel->getPatients();
        $openingHours = $this->adminModel->getOpeningHours();

        // Afficher la vue admin_dashboard
        require '../app/Views/admin.php';
    }

    // Méthodes pour gérer les actions de l'admin (ajout, suppression, mise à jour)
    public function updateAppointment($id, $status, $description) {
        if ($id && $status) {
            // Préparez la requête pour mettre à jour le statut du rendez-vous
            $stmt = $this->db->prepare("UPDATE appointments SET status = :status WHERE id = :id");
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':description', $description);
            $stmt->execute();

            // Optionnel : rediriger ou retourner une vue après la mise à jour
            header("Location: index.php?page=admin"); // Redirigez vers le tableau de bord admin après la mise à jour
            exit();
        } else {
            die("ID ou statut manquant lors de la mise à jour du rendez-vous.");
        }
    }
    
    public function deletePatient() {
        if (isset($_GET['id'])) {
            $patientId = $_GET['id'];
    
            if ($this->adminModel->deletePatient($patientId)) {
                $_SESSION['message'] = "Patient supprimé avec succès.";
            } else {
                $_SESSION['message'] = "Erreur lors de la suppression du patient.";
            }
    
            // Redirigez vers la page d'administration
            header('Location: index.php?page=admin');
            exit;
        }
    }    
}