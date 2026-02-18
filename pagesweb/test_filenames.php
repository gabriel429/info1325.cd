<?php
/**
 * Compare filenames between database and filesystem
 * Access at: http://localhost/info1325.cd/pagesweb/test_filenames.php
 */

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect;

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Filename Verification</h2>";

$docsDir = __DIR__ . '/../img/documentations/';
$filesystemFiles = array_map('basename', glob($docsDir . '*'));
sort($filesystemFiles);

echo "<h3>Files on Filesystem</h3>";
echo "Count: " . count($filesystemFiles) . "<br>";
echo "<ul>";
foreach ($filesystemFiles as $f) {
    echo "<li>" . htmlspecialchars($f) . "</li>";
}
echo "</ul>";

echo "<h3>Files in Database</h3>";
try {
    $stmt = $pdo->query("SELECT id, titreDoc, fichier_pdf FROM documentations WHERE fichier_pdf IS NOT NULL AND fichier_pdf != ''");
    $dbFiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Count: " . count($dbFiles) . "<br>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Title</th><th>DB Filename</th><th>Exists</th><th>Match</th></tr>";
    
    foreach ($dbFiles as $doc) {
        $id = htmlspecialchars($doc['id']);
        $titre = htmlspecialchars($doc['titreDoc'] ?? '');
        $filename = htmlspecialchars($doc['fichier_pdf'] ?? '');
        
        $filePath = $docsDir . $doc['fichier_pdf'];
        $exists = file_exists($filePath) ? "✓ YES" : "✗ NO";
        
        // Check if filename exists (exact match)
        $found = in_array((string)$doc['fichier_pdf'], $filesystemFiles, true);
        $match = $found ? "✓ YES" : "✗ NO";
        
        echo "<tr>";
        echo "<td>$id</td>";
        echo "<td>$titre</td>";
        echo "<td>$filename</td>";
        echo "<td>$exists</td>";
        echo "<td>$match</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

echo "<h3>Summary</h3>";
try {
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN fichier_pdf IS NULL OR fichier_pdf = '' THEN 1 ELSE 0 END) as no_file,
            SUM(CASE WHEN fichier_pdf IS NOT NULL AND fichier_pdf != '' THEN 1 ELSE 0 END) as with_file
        FROM documentations
    ");
    $summary = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Total documents: " . $summary['total'] . "<br>";
    echo "Without fichier_pdf: " . $summary['no_file'] . "<br>";
    echo "With fichier_pdf: " . $summary['with_file'] . "<br>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

echo "<br><p><a href='javascript:history.back()'>Back</a></p>";
?>
