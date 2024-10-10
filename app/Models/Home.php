<?php

class Home {
    private $dbConnection;
    private $serviceModel;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
        $this->serviceModel = new Service($dbConnection); // Créer une instance du modèle Service
    }

    // Récupérer tous les services via le modèle Service
    public function getServices() {
        return $this->serviceModel->getAll(); // Appeler la méthode getAll du modèle Service
    }

    // Récupérer tous les horaires d'ouverture
    public function getOpeningHours() {
        $stmt = $this->dbConnection->prepare("SELECT * FROM opening_hours ORDER BY day_of_week ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOpeningHours($data) {
        $stmt = $this->dbConnection->prepare("UPDATE opening_hours SET start_time = ?, end_time = ? WHERE id = 1");
        $stmt->execute([$data['start_time'], $data['end_time']]);
    }    
}
