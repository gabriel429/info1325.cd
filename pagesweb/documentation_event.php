<?php
/**
 * Track documentation events (view/download) and serve/redirect PDF
 */

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect;

define('SKIP_AUTO_TRACK', true);
require_once __DIR__ . '/track_visitor.php';

$docId = isset($_GET['doc_id']) ? (int)$_GET['doc_id'] : 0;
$action = isset($_GET['action']) ? strtolower(trim((string)$_GET['action'])) : 'view';
$fileParam = isset($_GET['file']) ? basename((string)$_GET['file']) : '';
$titleParam = isset($_GET['title']) ? trim((string)$_GET['title']) : '';

if ((($docId <= 0) && $fileParam === '') || !in_array($action, ['view', 'download'], true)) {
    http_response_code(400);
    exit('Paramètres invalides.');
}

try {
    $doc = null;
    if ($docId > 0) {
        $stmt = $pdo->prepare('SELECT id, titreDoc, fichier_pdf FROM documentations WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $docId]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doc || empty($doc['fichier_pdf'])) {
            http_response_code(404);
            exit('Document introuvable.');
        }
    }

    $pdfFileName = $doc ? basename((string)$doc['fichier_pdf']) : $fileParam;
    $pdfPath = __DIR__ . '/../img/documentations/' . $pdfFileName;

    if (!is_file($pdfPath)) {
        http_response_code(404);
        exit('Fichier PDF introuvable.');
    }

    $docTitle = $doc['titreDoc'] ?? ($titleParam !== '' ? $titleParam : pathinfo($pdfFileName, PATHINFO_FILENAME));
    track_documentation_event(
        $pdo,
        $docId > 0 ? $docId : null,
        $action,
        $_SERVER['HTTP_REFERER'] ?? null,
        $docTitle,
        $pdfFileName
    );

    if ($action === 'download') {
        $downloadName = $docTitle ? preg_replace('/[\\\/:*?"<>|]+/', '_', (string)$docTitle) : pathinfo($pdfFileName, PATHINFO_FILENAME);
        if ($downloadName === '' || $downloadName === null) {
            $downloadName = pathinfo($pdfFileName, PATHINFO_FILENAME);
        }
        $downloadName .= '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $downloadName . '"; filename*=UTF-8\'\'' . rawurlencode($downloadName));
        header('Content-Length: ' . filesize($pdfPath));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        readfile($pdfPath);
        exit;
    }

    $pdfUrl = BASE_URL . 'img/documentations/' . rawurlencode($pdfFileName);
    header('Location: ' . $pdfUrl);
    exit;

} catch (PDOException $e) {
    error_log('documentation_event.php error: ' . $e->getMessage());
    http_response_code(500);
    exit('Erreur serveur.');
}
