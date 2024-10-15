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

        // Generate 20-minute time slots based on opening hours
        public function generateTimeSlots($day) {
            $stmt = $this->dbConnection->prepare("SELECT * FROM opening_hours WHERE day_of_week = :day");
            $stmt->bindParam(':day', $day);
            $stmt->execute();
            $hours = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($hours) {
                $startTime = new DateTime($hours['start_time']);
                $endTime = new DateTime($hours['end_time']);
                $timeSlots = [];
    
                // Create time slots
                while ($startTime < $endTime) {
                    $timeSlots[] = $startTime->format('H:i');
                    $startTime->modify('+20 minutes'); // Increment by 20 minutes
                }
    
                return $timeSlots;
            }
    
            return []; // Return an empty array if no hours found
        }
}