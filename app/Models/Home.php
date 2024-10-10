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

    // Mettre à jour les horaires d'ouverture
    public function updateOpeningHours($data) {
        // Boucle à travers chaque jour et met à jour les horaires
        foreach ($data['hours'] as $dayOfWeek => $hours) {
            $startTime = isset($hours['start_time']) ? $hours['start_time'] : null;
            $endTime = isset($hours['end_time']) ? $hours['end_time'] : null;

            // Préparez la requête pour mettre à jour les horaires d'ouverture pour chaque jour
            $stmt = $this->dbConnection->prepare("UPDATE opening_hours SET start_time = ?, end_time = ? WHERE day_of_week = ?");
            $stmt->execute([$startTime, $endTime, $dayOfWeek]);
        }
    }
}