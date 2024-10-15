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
            // Validate input data
            if (isset($_POST['name'], $_POST['description'])) {
                try {
                    $this->serviceModel->create([
                        'name' => $_POST['name'],
                        'description' => $_POST['description']
                    ]);
                    // Redirect after creating the service to avoid multiple submissions
                    header('Location: index.php?page=admin'); // Redirect back to admin page
                    exit();
                } catch (InvalidArgumentException $e) {
                    // Handle validation errors
                    $_SESSION['error'] = $e->getMessage();
                } catch (PDOException $e) {
                    // Handle database errors
                    $_SESSION['error'] = "Database error: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "Name and description are required.";
            }
        }

        // If not a POST request or validation failed, show the creation form
        require '../app/Views/admin.php'; // Load form view
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['name'], $_POST['description'])) {
                try {
                    $this->serviceModel->update($id, [
                        'name' => $_POST['name'],
                        'description' => $_POST['description']
                    ]);
                    // Success message
                    $_SESSION['message'] = "Service updated successfully.";
                    header('Location: index.php?page=admin'); // Redirect to admin page
                    exit();
                } catch (InvalidArgumentException $e) {
                    $_SESSION['error'] = $e->getMessage();
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Database error: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "Name and description are required.";
            }
        }

        // Load the existing service for editing
        $service = $this->serviceModel->find($id);
        require '../app/Views/admin.php'; // Load edit form view
    }

    public function delete($id) {
        if ($id) {
            $this->serviceModel->delete($id); // Call the delete method of the Service model
            $_SESSION['message'] = "Service deleted successfully."; // Success message
        }

        // Redirect to the admin page
        header('Location: index.php?page=admin');
        exit();
    }
}