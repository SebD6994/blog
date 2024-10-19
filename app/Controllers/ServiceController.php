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
}