<?php
// image.php — Sert une image depuis img/documentations/ de façon sécurisée
$imgName = isset($_GET['f']) ? basename(urldecode((string)$_GET['f'])) : '';

// Validation: nom vide ou tentative de path traversal
if ($imgName === '' || strpos($imgName, '..') !== false || strpos($imgName, '/') !== false || strpos($imgName, '\\') !== false) {
    http_response_code(400);
    exit('Paramètre invalide.');
}

$allowedDir = realpath(__DIR__ . '/../img/documentations/');
$fullPath   = realpath($allowedDir . '/' . $imgName);

// Vérifier que le chemin résolu reste bien dans le dossier autorisé
if ($fullPath === false || strpos($fullPath, $allowedDir) !== 0 || !is_file($fullPath)) {
    http_response_code(404);
    exit('Fichier introuvable.');
}

// Détecter le type MIME réel avec finfo (plus fiable que mime_content_type)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $fullPath);
finfo_close($finfo);

// Restreindre aux types image et PDF autorisés
$allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'application/pdf'];
if (!in_array($mime, $allowedMimes, true)) {
    http_response_code(403);
    exit('Type de fichier non autorisé.');
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($fullPath));
header('Cache-Control: public, max-age=86400');
readfile($fullPath);
exit;
