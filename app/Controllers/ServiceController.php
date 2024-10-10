<?php

require_once '../app/Models/Service.php';

class ServiceController {
    private $serviceModel;

    public function __construct($db) {
        $this->serviceModel = new Service($db);
    }

    public function index() {
        // Retrieve all services and send them to the view
        $services = $this->serviceModel->getAll();
        require '../app/Views/Service.php'; // Load the view with the services
    }

    public function create() {
        // Add a new service if the request is of type POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->serviceModel->create([
                'name' => $_POST['name'],
                'description' => $_POST['description']
            ]);
            // Redirect after creating the service to avoid multiple submissions
            header('Location: index.php?page=admin'); // Redirect back to admin page
            exit();
        }

        // If not a POST request, show the creation form
        require '../app/Views/admin.php'; // Load form view
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['description'])) {
            $this->serviceModel->update($id, [
                'name' => $_POST['name'],
                'description' => $_POST['description']
            ]);
            // Ajoutez un message de succès si nécessaire
            $_SESSION['message'] = "Service mis à jour avec succès.";
            header('Location: index.php?page=admin'); // Redirige vers la page admin
            exit();
        } else {
            die("Données manquantes pour la mise à jour du service.");
        }
    

        // Load the existing service for editing
        $service = $this->serviceModel->find($id);
        require '../app/Views/admin.php'; // Load edit form view
    }

    public function delete($id) {
        if ($id) {
            $this->serviceModel->delete($id); // Call the delete method of the Service model
            $_SESSION['message'] = "Service supprimé avec succès."; // Success message
        }

        // Redirect to the admin page
        header('Location: index.php?page=admin');
        exit();
    }
}