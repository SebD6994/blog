<?php

require_once '../app/Models/Home.php'; // Inclure le modèle Home

class HomeController {
    private $dbConnection;
    private $homeModel;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
        $this->homeModel = new Home($dbConnection); // Instanciation du modèle Home
    }

    public function index() {
        // Récupérer tous les services via le modèle Home
        $services = $this->homeModel->getServices();
        
        // Récupérer tous les horaires d'ouverture via le modèle Home
        $openingHours = $this->homeModel->getOpeningHours();

        // Passer les services et les horaires à la vue
        require '../app/Views/home.php'; // Charger la vue de la page d'accueil
    }
}
