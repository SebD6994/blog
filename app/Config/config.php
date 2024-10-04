<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'drdupont');
define('DB_USER', 'root');
define('DB_PASS', '');

// Fonction pour établir une connexion à la base de données
function getConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        return null; // En cas d'erreur, renvoyer null
    }
}