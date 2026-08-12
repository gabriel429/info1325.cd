<?php

ob_start();
ini_set('display_errors', '0');

function upload_actualite_parse_size(string $value): int
{
    $value = trim($value);
    if ($value === '') {
        return 0;
    }

    $unit = strtolower($value[strlen($value) - 1]);
    $size = (float)$value;

    switch ($unit) {
        case 'g':
            $size *= 1024;
            // no break
        case 'm':
            $size *= 1024;
            // no break
        case 'k':
            $size *= 1024;
            break;
    }

    return (int)$size;
}

function upload_actualite_json(int $status, array $payload): void
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/../configUrl.php';
    require_once __DIR__ . '/csrf_helper.php';
    require_once __DIR__ . '/upload_helper.php';

    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        upload_actualite_json(405, ['success' => false, 'message' => 'Méthode non autorisée.']);
    }

    $contentLength = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
    $postMaxSize = upload_actualite_parse_size((string)ini_get('post_max_size'));
    if ($postMaxSize > 0 && $contentLength > $postMaxSize) {
        upload_actualite_json(413, [
            'success' => false,
            'message' => 'Image trop lourde. Réduisez la taille du fichier puis réessayez.',
        ]);
    }

    if (!isset($_SESSION['user'])) {
        upload_actualite_json(401, ['success' => false, 'message' => 'Session expirée.']);
    }

    if (!csrf_validate(false)) {
        upload_actualite_json(403, ['success' => false, 'message' => 'Requête invalide. Rechargez la page puis réessayez.']);
    }

    $fileName = uploadFile('media', __DIR__ . '/../img/actualites/body/', [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ]);

    if (!$fileName) {
        throw new Exception('Aucun fichier reçu.');
    }

    $url = IMG_DIR . 'actualites/body/' . rawurlencode($fileName);

    upload_actualite_json(200, [
        'success' => true,
        'url' => $url,
        'location' => $url,
    ]);
} catch (Throwable $e) {
    error_log('actualite media upload error: ' . $e->getMessage());
    upload_actualite_json(400, ['success' => false, 'message' => $e->getMessage()]);
}
