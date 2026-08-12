<?php
// Chargement des credentials depuis le fichier sécurisé
$_secretsFile = __DIR__ . '/../config/secrets.php';
if (!file_exists($_secretsFile)) {
    error_log('ERREUR CRITIQUE: config/secrets.php introuvable. Copier config/secrets.example.php vers config/secrets.php.');
    die('Erreur de configuration serveur. Veuillez contacter l\'administrateur.');
}
require_once $_secretsFile;

// Détection de l'environnement
$isLocal = in_array($_SERVER['SERVER_NAME'] ?? 'localhost', SECRET_LOCAL_SERVER_NAMES);

// Configuration des erreurs selon l'environnement
if ($isLocal) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}

// Sélection des credentials selon l'environnement
$host    = $isLocal ? SECRET_DB_LOCAL_HOST : SECRET_DB_PROD_HOST;
$db      = $isLocal ? SECRET_DB_LOCAL_NAME : SECRET_DB_PROD_NAME;
$user    = $isLocal ? SECRET_DB_LOCAL_USER : SECRET_DB_PROD_USER;
$pass    = $isLocal ? SECRET_DB_LOCAL_PASS : SECRET_DB_PROD_PASS;
$charset = 'utf8mb4';

ini_set('default_charset', 'UTF-8');
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
} catch (PDOException $e) {
    error_log('Erreur de connexion à la BDD : ' . $e->getMessage());
    die('Une erreur technique est survenue. Veuillez réessayer plus tard.');
}
