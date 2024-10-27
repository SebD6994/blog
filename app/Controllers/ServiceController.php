<?php

require_once '../app/Models/Service.php';

class ServiceController {
    private $serviceModel;

    public function __construct($db) {
        $this->serviceModel = new Service($db);
    }

    public function index() {
        $services = $this->serviceModel->getAll();
        require '../app/Views/Service.php';
    }
}