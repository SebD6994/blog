<?php

require_once '../app/Models/Home.php';

class HomeController {
    private $dbConnection;
    private $homeModel;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
        $this->homeModel = new Home($dbConnection);
    }

    public function index() {
        $bannerImagePath = $this->homeModel->getBannerImage();
        $services = $this->homeModel->getServices();
        $openingHours = $this->homeModel->getOpeningHours();
        $clinicImages = $this->homeModel->getClinicImages();
        $currentApropos = $this->homeModel->getApropos();

        require '../app/Views/home.php';
    }
}
