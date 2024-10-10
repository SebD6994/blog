<?php

require_once '../app/Models/News.php';

class NewsController {
    private $newsModel;

    public function __construct($db) {
        $this->newsModel = new News($db);
    }

    public function create() {
        // Check if the request is of type POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'],
                'content' => $_POST['content']
            ];

            try {
                // Call the create method of the News model
                $this->newsModel->create($data);
                $_SESSION['message'] = "Actualité ajoutée avec succès."; // Success message
            } catch (Exception $e) {
                $_SESSION['message'] = "Erreur lors de l'ajout de l'actualité : " . $e->getMessage(); // Error message
            }

            // Redirect to the admin page
            header('Location: index.php?page=admin');
            exit();
        }

        // If not a POST request, show the form to create news
        require '../app/Views/CreateNews.php'; // Load form view
    }

    public function update($id) {
        // Check if the request is of type POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'],
                'content' => $_POST['content']
            ];
            $this->newsModel->update($id, $data); // Call the model to update news
            
            // Redirect after the update
            header("Location: index.php?page=admin");
            exit();
        } else {
            // Fetch existing news item for pre-filling the form
            $newsItem = $this->newsModel->find($id);
            require '../app/Views/EditNews.php'; // Load edit form view
        }
    }

    public function delete($id) {
        if ($id) {
            try {
                $this->newsModel->delete($id); // Call the delete method of the News model
                $_SESSION['message'] = "Actualité supprimée avec succès."; // Success message
            } catch (Exception $e) {
                $_SESSION['message'] = "Erreur lors de la suppression de l'actualité : " . $e->getMessage(); // Error message
            }
        }

        // Redirect to the admin page
        header('Location: index.php?page=admin');
        exit();
    }
}