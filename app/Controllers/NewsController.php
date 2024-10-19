<?php

require_once '../app/Models/News.php';

class NewsController {
    private $newsModel;

    public function __construct($db) {
        $this->newsModel = new News($db);
    }

    public function index() {
        $newsItems = $this->newsModel->getAll();
        require '../app/Views/news.php';
    }
}