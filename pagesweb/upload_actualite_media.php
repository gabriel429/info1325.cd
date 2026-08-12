<?php

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once __DIR__ . '/csrf_helper.php';
require_once __DIR__ . '/upload_helper.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Session expirée.']);
    exit;
}

if (!csrf_validate(false)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
    exit;
}

try {
    $fileName = uploadFile('media', __DIR__ . '/../img/actualites/body/', [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ]);

    if (!$fileName) {
        throw new Exception('Aucun fichier reçu.');
    }

    echo json_encode([
        'success' => true,
        'url' => IMG_DIR . 'actualites/body/' . rawurlencode($fileName),
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
