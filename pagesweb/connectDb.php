<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

// Configuration de la base de données
$host = 'localhost';
$db   = 'mwbi6090_sn1325';
$user = 'mwbi6090_root';
$pass = 'bxAU7dh2r5KzmSS';
$charset = 'utf8mb4'; // Recommandé pour une compatibilité complète

// Options de PDO pour une connexion robuste
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    // Création de l'instance PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // En cas d'échec de la connexion, on arrête tout et on affiche une erreur générique
    // Il est déconseillé d'afficher $e->getMessage() en production pour des raisons de sécurité.
    error_log("Erreur de connexion à la BDD : " . $e->getMessage()); // Log l'erreur pour le développeur
    die("Une erreur technique est survenue. Veuillez réessayer plus tard.");
}