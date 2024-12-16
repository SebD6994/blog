<?php

define('DB_HOST', 'mysql-detournay.alwaysdata.net');
define('DB_NAME', 'detournay_drdupont');
define('DB_USER', 'detournay');
define('DB_PASS', 'DKkfpG3A');

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