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
require_once '../app/Controllers/Admin_serviceController.php'; // Include the Admin_serviceController

$db = getConnection();

if (!$db) {
    die("Erreur de connexion à la base de données.");
}

// Initialisation des contrôleurs
$controllers = [
    'home' => new HomeController($db),
    'patients' => new PatientController($db),
    'appointments' => new AppointmentController($db),
    'services' => new ServiceController($db),
    'news' => new NewsController($db),
    'admin_services' => new Admin_serviceController($db) // Add Admin_serviceController
];

// Vérification de la page et action demandée
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Routage des pages en fonction du contrôleur et de l'action
switch ($page) {
    case 'home':
    default:
        $controllers['home']->index();
        break;
        
    case 'patients':
        switch ($action) {
            case 'login':
                $controllers['patients']->login();
                break;
            case 'logout':
                $controllers['patients']->logout();
                break;
            case 'create':
                $controllers['patients']->create();
                break;
            case 'update':
                $controllers['patients']->update();
                break;
            case 'changePassword':
                $controllers['patients']->changePassword();
                break;
            case 'delete':
                $controllers['patients']->delete($_GET['id']);
                break;
            case 'index':
            default:
                $controllers['patients']->index();
                break;
        }
        break;

    case 'appointments':
        switch ($action) {
            case 'view':
                $controllers['appointments']->view($_GET['id']);
                break;
            case 'create':
                if ($_POST) {
                    $controllers['appointments']->create($_POST);
                }
                break;
            case 'update':
                if ($_POST) {
                    $controllers['appointments']->update($_POST['appointment_id'], $_POST);
                }
                break;
            case 'delete':
                if (isset($_POST['appointment_id'])) {
                    $controllers['appointments']->delete($_POST['appointment_id']);
                }
                break;
            case 'slots':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controllers['appointments']->createSlot($_POST);
                } else {
                    $controllers['appointments']->viewSlots();
                }
                break;
            case 'getTimeSlots':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $controllers['appointments']->getTimeSlots();
                }
                break;
            case 'index':
            default:
                $controllers['appointments']->index();
                break;
        }
        break;

    case 'services':
        switch ($action) {
            case 'view':
                $controllers['services']->view($_GET['id']);
                break;
            case 'create':
                if ($_POST) {
                    $controllers['services']->create($_POST);
                }
                break;
            case 'update':
                if ($_POST) {
                    $controllers['services']->update($_POST['id'], $_POST);
                }
                break;
            case 'delete':
                $controllers['services']->delete($_GET['id']);
                break;
            case 'index':
            default:
                $controllers['services']->index();
                break;
        }
        break;

    case 'news':
        switch ($action) {
            case 'view':
                $controllers['news']->view($_GET['id']);
                break;
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controllers['news']->create($_POST);
                } else {
                    $controllers['news']->createForm(); // Affiche le formulaire de création
                }
                break;
            case 'update':
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
                    $controllers['news']->update($_POST['id'], $_POST);
                } else {
                    $controllers['news']->editForm($_GET['id']); // Affiche le formulaire d'édition
                }
                break;
            case 'delete':
                $controllers['news']->delete($_GET['id']);
                break;
            case 'index':
            default:
                $controllers['news']->index();
                break;
        }
        break;

    case 'admin_services': // New case for Admin_serviceController
        switch ($action) {
            case 'create':
                if ($_POST) {
                    $controllers['admin_services']->create();
                }
                break;
            case 'update':
                if ($_POST) {
                    $controllers['admin_services']->update($_POST['id']);
                }
                break;
            case 'delete':
                $controllers['admin_services']->delete($_GET['id']);
                break;
            case 'index':
            default:
                $controllers['admin_services']->index();
                break;
        }
        break;
}
?>