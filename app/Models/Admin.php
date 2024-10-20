<?php
class Admin {
    private $db;
    private $appointmentModel;
    private $patientModel;
    private $serviceModel;
    private $newsModel;
    private $homeModel; // Ajout du modèle Home

    public function __construct($db) {
        $this->db = $db;
        $this->appointmentModel = new Appointment($db);
        $this->patientModel = new Patient($db);
        $this->serviceModel = new Service($db);
        $this->newsModel = new News($db);
        $this->homeModel = new Home($db); // Initialisation du modèle Home
    }

    // Récupère tous les rendez-vous avec les informations du patient et du service
    public function getAppointments() {
        return $this->appointmentModel->getAll(); 
    }

    // Met à jour le statut d'un rendez-vous
    public function updateStatus($appointmentId, $status) {
        return $this->appointmentModel->updateStatus($appointmentId, $status);
    }





    
    // Récupère tous les patients via le modèle Patient
    public function getPatients() {
        return $this->patientModel->getAll();
    }
    
    public function searchPatients($searchTerm) {
        // Préparer une requête SQL pour rechercher les patients par nom, prénom, email ou téléphone
        $sql = "SELECT * FROM patients WHERE first_name LIKE :search OR last_name LIKE :search OR email LIKE :search OR phone LIKE :search";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['search' => '%' . $searchTerm . '%']);
        
        // Retourner les résultats
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crée un patient via le modèle Patient
    public function createPatient($data) {
        return $this->patientModel->create($data);
    }

    // Supprime un patient via le modèle Patient
    public function deletePatient($id) {
        return $this->patientModel->delete($id);
    }






    // Récupère les horaires d'ouverture via le modèle Home
    public function getOpeningHours() {
        return $this->homeModel->getOpeningHours();
    }

    // Mettre à jour les horaires via Home
    public function updateOpeningHours($data) {
        return $this->homeModel->updateOpeningHours($data);
    }
}