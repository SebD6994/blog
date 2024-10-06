<?php

class HomeController {
    private $serviceModel;

    public function __construct($dbConnection) {
        // Instancier le modèle Service avec la connexion à la base de données
        $this->serviceModel = new Service($dbConnection);
    }

    public function index() {
        // Récupérer tous les services
        $services = $this->serviceModel->getAll();
        
        // Passer les services à la vue
        require '../app/Views/home.php'; // Charger la vue de la page d'accueil
    }
}