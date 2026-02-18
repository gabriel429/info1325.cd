<?php
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect;

$stmt = $pdo->prepare('SELECT id, titreDoc, fichier_pdf FROM documentations WHERE id = 17');
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo "Document ID 17:\n";
echo "titreDoc: " . ($result['titreDoc'] ?? 'NULL') . "\n";
echo "fichier_pdf: " . ($result['fichier_pdf'] ?? 'NULL') . "\n";
echo "Type: " . gettype($result['fichier_pdf']) . "\n";

if ($result && isset($result['fichier_pdf']) && !empty($result['fichier_pdf'])) {
    $pdfPath = __DIR__ . '/../img/documentations/' . basename($result['fichier_pdf']);
    echo "Path: " . $pdfPath . "\n";
    echo "File exists: " . (is_file($pdfPath) ? 'YES' : 'NO') . "\n";
}
?>
