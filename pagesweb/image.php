<?php
// image.php
$imgName = urldecode($_GET['f']); // décode le nom du fichier (%20 => espace)
$path = __DIR__ . '/img/documentations/' . $imgName;

if (file_exists($path)) {
    $mime = mime_content_type($path);
    header("Content-Type: $mime");
    readfile($path);
    exit;
} else {
    http_response_code(404);
}
