<?php

require_once '../app/Models/Service.php';

class ServiceController {
    private $serviceModel;

    public function __construct($db) {
        $this->serviceModel = new Service($db);
    }

    // Méthode pour afficher tous les services
    public function index() {
        // Récupérer tous les services et les envoyer à la vue
        $services = $this->serviceModel->getAll();
        require '../app/Views/Service.php'; // Charger la vue avec les services
    }

    // Méthode pour créer un nouveau service
    public function create() {
        // Ajouter un nouveau service si la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->serviceModel->create([
                'name' => $_POST['name'],
                'description' => $_POST['description']
            ]);
            // Redirection après la création du service pour éviter la soumission multiple
            header('Location: index.php?page=services');
            exit();
        }

        // Si ce n'est pas une requête POST, afficher un formulaire de création
        require '../app/Views/CreateService.php'; // Formulaire pour créer un nouveau service
    }
}