<?php
session_start(); // Assurez-vous que la session est démarrée

require_once '../vendor/autoload.php';
require_once '../app/Config/config.php';
require_once '../app/Controllers/HomeController.php';
require_once '../app/Controllers/PatientController.php';
require_once '../app/Controllers/AppointmentController.php';
require_once '../app/Controllers/ServiceController.php';
require_once '../app/Controllers/NewsController.php';
require_once '../app/Controllers/AdminController.php'; // Ajout du contrôleur Admin

$db = getConnection();

if (!$db) {
    die("Erreur de connexion à la base de données.");
}

// Initialisation des contrôleurs
$homeController = new HomeController($db);
$patientController = new PatientController($db);
$appointmentController = new AppointmentController($db);
$serviceController = new ServiceController($db);
$newsController = new NewsController($db);
$adminController = new AdminController($db); // Initialisation du contrôleur Admin

// Vérification de la page demandée
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    case 'patients':
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        switch ($action) {
            case 'login':
                $patientController->login(); // Gérer la connexion
                break;
            case 'create':
                $patientController->create(); // Créer un nouveau patient
                break;
            case 'logout':
                $patientController->logout(); // Déconnecter le patient
                break;
            case 'update':
                $patientController->update(); // Mettre à jour un patient
                break;
            case 'delete':
                $patientController->delete($_GET['id']); // Supprimer un patient
                break;
            case 'index':
            default:
                $patientController->index(); // Afficher le tableau de bord du patient
                break;
        }
        break;

    case 'appointments':
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        switch ($action) {
            case 'view':
                $appointmentController->view($_GET['id']); // Afficher un rendez-vous spécifique
                break;
            case 'create':
                $appointmentController->create($_POST); // Créer un nouveau rendez-vous
                break;
            case 'update':
                $appointmentController->update($_POST['id'], $_POST); // Mettre à jour un rendez-vous
                break;
            case 'delete':
                $appointmentController->delete($_GET['id']); // Supprimer un rendez-vous
                break;
            case 'index':
            default:
                $appointmentController->index(); // Afficher la liste des rendez-vous
                break;
        }
        break;

    case 'services':
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        switch ($action) {
            case 'view':
                $serviceController->view($_GET['id']); // Afficher un service spécifique
                break;
            case 'create':
                $serviceController->create($_POST); // Créer un nouveau service
                break;
            case 'update':
                $serviceController->update($_POST['id'], $_POST); // Mettre à jour un service
                break;
            case 'delete':
                $serviceController->delete($_GET['id']); // Supprimer un service
                break;
            case 'index':
            default:
                $serviceController->index(); // Afficher la liste des services
                break;
        }
        break;

    case 'news':
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        switch ($action) {
            case 'view':
                $newsController->view($_GET['id']); // Afficher une news spécifique
                break;
            case 'create':
                $newsController->create($_POST); // Créer une nouvelle news
                break;
            case 'update':
                $newsController->update($_POST['id'], $_POST); // Mettre à jour une news
                break;
            case 'delete':
                $newsController->delete($_GET['id']); // Supprimer une news
                break;
            case 'index':
            default:
                $newsController->index(); // Afficher la liste des news
                break;
        }
        break;

    case 'admin':
        // Gestion du tableau de bord Admin : récupérer l'action et rediriger
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        switch ($action) {
            case 'updateAppointment':
                $adminController->updateAppointment($_POST['id'], $_POST['status']);
                break;
            case 'deleteAppointment':
                $adminController->deleteAppointment($_GET['id']);
                break;
            case 'addService':
                $adminController->addService($_POST);
                break;
            case 'updateService':
                $adminController->updateService($_POST['id'], $_POST);
                break;
            case 'deleteService':
                $adminController->deleteService($_GET['id']);
                break;
            case 'addNews':
                $adminController->addNews($_POST);
                break;
            case 'updateNews':
                $adminController->updateNews($_POST['id'], $_POST);
                break;
            case 'deleteNews':
                $adminController->deleteNews($_GET['id']);
                break;
            case 'addPatient':
                $adminController->addPatient($_POST);
                break;
            case 'updatePatient':
                $adminController->updatePatient($_POST['id'], $_POST);
                break;
            case 'deletePatient':
                $adminController->deletePatient($_GET['id']);
                break;
            case 'updateOpeningHours':
                $adminController->updateOpeningHours($_POST);
                break;
            case 'index':
            default:
                $adminController->index(); // Charger le tableau de bord Admin
                break;
        }
        break;

    case 'home':
        default:
        $homeController->index(); // Si la page n'est pas reconnue, afficher l'accueil par défaut
        break;
}