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

    // Crée un patient via le modèle Patient
    public function createPatient($data) {
        return $this->patientModel->create($data);
    }

    // Supprime un patient via le modèle Patient
    public function deletePatient($id) {
        return $this->patientModel->delete($id);
    }

    // Récupère tous les services via le modèle Service
    public function getServices() {
        return $this->serviceModel->getAll();
    }

    // Crée un service via le modèle Service
    public function addService($data) {
        return $this->serviceModel->create($data);
    }

    // Met à jour un service via le modèle Service
    public function updateService($id, $data) {
        return $this->serviceModel->update($id, $data);
    }

    // Supprime un service via le modèle Service
    public function deleteService($id) {
        return $this->serviceModel->delete($id);
    }

    // Récupère toutes les actualités via le modèle News
    public function getNews() {
        return $this->newsModel->getAll();
    }

    // Ajoute une actualité via le modèle News
    public function addNews($data) {
        return $this->newsModel->create($data);
    }

    // Met à jour une actualité via le modèle News
    public function updateNews($id, $data) {
        return $this->newsModel->update($id, $data);
    }

    // Supprime une actualité via le modèle News
    public function deleteNews($id) {
        return $this->newsModel->delete($id);
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