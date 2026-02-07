<?php

// Test de connexion à la base de données locale
require_once __DIR__ . '/connectDb.php';

try {
    $stmt = $pdo->query('SELECT DATABASE() AS db');
    $row = $stmt->fetch();
    $dbName = $row['db'] ?? '(inconnue)';
    echo 'Connexion réussie à la base : ' . htmlspecialchars($dbName, ENT_QUOTES, 'UTF-8');
} catch (PDOException $e) {
    echo 'Échec de la connexion : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
