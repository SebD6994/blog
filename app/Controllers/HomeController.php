<?php

class HomeController {
    private $dbConnection;
    private $serviceModel;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
        $this->serviceModel = new Service($dbConnection);
    }

    public function index() {
        // Récupérer tous les services via le modèle Service
        $services = $this->serviceModel->getAll();

        // Récupérer les horaires d'ouverture avec la requête SQL
        $openingHoursQuery = $this->dbConnection->query('
            SELECT day_of_week, MIN(start_time) AS opening_time, MAX(end_time) AS closing_time
            FROM opening_hours
            GROUP BY day_of_week
            ORDER BY FIELD(day_of_week, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday")
        ');
        $openingHours = $openingHoursQuery->fetchAll(PDO::FETCH_ASSOC);

        // Tableau de correspondance pour les jours de la semaine en français
        $daysTranslation = [
            'Monday' => 'Lundi',
            'Tuesday' => 'Mardi',
            'Wednesday' => 'Mercredi',
            'Thursday' => 'Jeudi',
            'Friday' => 'Vendredi',
            'Saturday' => 'Samedi',
            'Sunday' => 'Dimanche'
        ];

        // Remplacer les noms anglais par les noms français
        foreach ($openingHours as &$hour) {
            if (isset($daysTranslation[$hour['day_of_week']])) {
                $hour['day_of_week'] = $daysTranslation[$hour['day_of_week']];
            }
        }

        // Passer les services et les horaires à la vue
        require '../app/Views/home.php'; // Charger la vue de la page d'accueil
    }
}