<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../pagesweb/connectDb.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

$email = trim($_POST['email'] ?? '');

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Adresse email invalide.']);
    exit;
}

try {
    // Crée la table si elle n'existe pas encore
    $pdo->exec("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        ip_address VARCHAR(45) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Vérifie si l'email est déjà inscrit
    $chk = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
    $chk->execute([$email]);
    if ($chk->fetch()) {
        echo json_encode(['success' => true, 'message' => 'Vous êtes déjà inscrit à notre newsletter.']);
        exit;
    }

    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
    $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email, ip_address) VALUES (?, ?)");
    $stmt->execute([$email, $ip]);

    echo json_encode(['success' => true, 'message' => 'Merci ! Vous êtes bien inscrit à notre newsletter.']);

} catch (PDOException $e) {
    error_log('Newsletter DB error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue. Veuillez réessayer.']);
}
exit;
