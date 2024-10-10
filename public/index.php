<?php
session_start();

require_once '../vendor/autoload.php';
require_once '../app/Config/config.php';

// Inclusion des contrôleurs
require_once '../app/Controllers/HomeController.php';
require_once '../app/Controllers/PatientController.php';
require_once '../app/Controllers/AppointmentController.php';
require_once '../app/Controllers/ServiceController.php';
require_once '../app/Controllers/NewsController.php';
require_once '../app/Controllers/AdminController.php';

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
$adminController = new AdminController($db);

// Vérification de la page demandée
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Routage des pages en fonction du contrôleur et de l'action
switch ($page) {
    case 'patients':
        switch ($action) {
            case 'login':
                $patientController->login();
                break;
            case 'create':
                $patientController->create();
                break;
            case 'logout':
                $patientController->logout();
                break;
            case 'update':
                $patientController->update();
                break;
            case 'delete':
                $patientController->delete($_GET['id']);
                break;
            case 'index':
            default:
                $patientController->index();
                break;
        }
        break;

    case 'appointments':
        switch ($action) {
            case 'view':
                $appointmentController->view($_GET['id']);
                break;
            case 'create':
                if ($_POST) {
                    $appointmentController->create($_POST);
                }
                break;
            case 'update':
                if ($_POST) {
                    $appointmentController->update($_POST['id'], $_POST);
                }
                break;
            case 'delete':
                $appointmentController->delete($_GET['id']);
                break;
            case 'index':
            default:
                $appointmentController->index();
                break;
        }
        break;

    case 'services':
        switch ($action) {
            case 'view':
                $serviceController->view($_GET['id']);
                break;
            case 'create':
                if ($_POST) {
                    $serviceController->create($_POST);
                }
                break;
            case 'update':
                if ($_POST) {
                    $serviceController->update($_POST['id'], $_POST);
                }
                break;
            case 'delete':
                $serviceController->delete($_GET['id']);
                break;
            case 'index':
            default:
                $serviceController->index();
                break;
        }
        break;

    case 'news':
        switch ($action) {
            case 'view':
                $newsController->view($_GET['id']);
                break;
            case 'create':
                if ($_POST) {
                    $newsController->create($_POST);
                }
                break;
            case 'update':
                if ($_POST) {
                    $newsController->update($_POST['id'], $_POST);
                }
                break;
            case 'delete':
                $newsController->delete($_GET['id']);
                break;
            case 'index':
            default:
                $newsController->index();
                break;
        }
        break;

    case 'admin':
        switch ($action) {
            case 'updateAppointment':
                if ($_POST) {
                    $adminController->updateAppointment($_POST['id'], $_POST['status']);
                }
                break;
            case 'deleteAppointment':
                $adminController->deleteAppointment($_GET['id']);
                break;
            case 'addService':
                if ($_POST) {
                    $adminController->addService($_POST);
                }
                break;
            case 'updateService':
                if ($_POST) {
                    $adminController->updateService($_POST['id'], $_POST);
                }
                break;
            case 'deleteService':
                $adminController->deleteService($_GET['id']);
                break;
            case 'addNews':
                if ($_POST) {
                    $adminController->addNews($_POST);
                }
                break;
            case 'updateNews':
                if ($_POST) {
                    $adminController->updateNews($_POST['id'], $_POST);
                }
                break;
            case 'deleteNews':
                $adminController->deleteNews($_GET['id']);
                break;
            case 'addPatient':
                if ($_POST) {
                    $adminController->addPatient($_POST);
                }
                break;
            case 'updatePatient':
                if ($_POST) {
                    $adminController->updatePatient($_POST['id'], $_POST);
                }
                break;
            case 'deletePatient':
                $adminController->deletePatient($_GET['id']);
                break;
            case 'updateOpeningHours':
                if ($_POST) {
                    $adminController->updateOpeningHours($_POST);
                }
                break;
            case 'index':
            default:
                $adminController->index();
                break;
        }
        break;

    case 'home':
    default:
        $homeController->index();
        break;
}