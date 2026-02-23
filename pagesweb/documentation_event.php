<?php
ob_start(); // Buffer output to prevent headers-already-sent issues
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
    $docTitle = '';
    
    // Determine the PDF filename
    $pdfFileName = '';
    
    if ($docId > 0) {
        $stmt = $pdo->prepare('SELECT id, titreDoc, fichier_pdf FROM documentations WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $docId]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doc) {
            error_log('documentation_event.php: Document not found for ID=' . $docId);
            http_response_code(404);
            exit('Document introuvable.');
        }
        
        $docTitle = $doc['titreDoc'] ?? '';
        // Use file parameter if provided, otherwise use fichier_pdf from DB
        $pdfFileName = ($fileParam !== '') ? $fileParam : basename((string)($doc['fichier_pdf'] ?? ''));
    } else {
        // No doc_id, must have file parameter
        $pdfFileName = $fileParam;
        $docTitle = $titleParam !== '' ? $titleParam : pathinfo($pdfFileName, PATHINFO_FILENAME);
    }
    
    if (empty($pdfFileName)) {
        error_log('documentation_event.php: No PDF filename provided (doc_id=' . $docId . ', file=' . $fileParam . ')');
        http_response_code(400);
        exit('Aucun fichier PDF spécifié.');
    }
    
    // Validate filename to prevent path traversal
    if (strpos($pdfFileName, '..') !== false || strpos($pdfFileName, '/') !== false || strpos($pdfFileName, '\\') !== false) {
        error_log('documentation_event.php: Invalid filename detected: ' . $pdfFileName);
        http_response_code(400);
        exit('Nom de fichier invalide.');
    }
    
    $pdfPath = __DIR__ . '/../img/documentations/' . $pdfFileName;

    if (!is_file($pdfPath)) {
        error_log('documentation_event.php: File not found at path: ' . $pdfPath . ' (filename: ' . $pdfFileName . ')');
        http_response_code(404);
        exit('Fichier PDF introuvable: ' . htmlspecialchars($pdfFileName));
    }

    track_documentation_event(
        $pdo,
        $docId > 0 ? $docId : null,
        $action,
        $_SERVER['HTTP_REFERER'] ?? null,
        $docTitle !== '' ? $docTitle : pathinfo($pdfFileName, PATHINFO_FILENAME),
        $pdfFileName
    );

    if ($action === 'download') {
        $downloadName = $docTitle ? preg_replace('/[\\\/:*?"<>|]+/', '_', (string)$docTitle) : pathinfo($pdfFileName, PATHINFO_FILENAME);
        if ($downloadName === '' || $downloadName === null) {
            $downloadName = pathinfo($pdfFileName, PATHINFO_FILENAME);
        }
        $downloadName .= '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . addslashes($downloadName) . '"');
        header('Content-Length: ' . filesize($pdfPath));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        ob_clean();
        flush();
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
