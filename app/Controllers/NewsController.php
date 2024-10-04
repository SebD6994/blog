<?php

require_once '../app/Models/News.php';

class NewsController {
    private $newsModel;

    public function __construct($db) {
        $this->newsModel = new News($db);
    }

    public function index() {
        // Récupérer toutes les actualités et les envoyer à la vue
        $newsItems = $this->newsModel->getAll();
        require '../app/Views/News.php';
    }

    public function create() {
        // Ajouter une nouvelle actualité si la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $createdAt = date('Y-m-d H:i:s');
            $this->newsModel->create([
                'title' => $_POST['title'],
                'content' => $_POST['content'],
                'created_at' => $createdAt
            ]);
        }
        $this->index(); // Afficher la liste des actualités après la création
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->create();
        } else {
            $this->index();
        }
    }
}