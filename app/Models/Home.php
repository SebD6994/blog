<?php

class Home {
    private $dbConnection;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    // Récupérer tous les services
    public function getServices() {
        $stmt = $this->dbConnection->prepare("SELECT * FROM services"); // Remplacez 'services' par le nom de votre table de services
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les horaires d'ouverture
    public function getOpeningHours() {
        $stmt = $this->dbConnection->prepare("SELECT * FROM opening_hours ORDER BY day_of_week ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
