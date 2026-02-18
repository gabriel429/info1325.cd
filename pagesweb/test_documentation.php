<?php
/**
 * Test script to verify documentation setup
 * Access at: http://localhost/info1325.cd/pagesweb/test_documentation.php
 */

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect;

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Documentation Test</h2>";

// Test 1: Check if documentation table exists
echo "<h3>Test 1: Check documentation table</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM documentations");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Documentation table exists with " . $result['count'] . " documents<br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
    exit;
}

// Test 2: List all documents with fichier_pdf
echo "<h3>Test 2: List all documents</h3>";
try {
    $stmt = $pdo->query("SELECT id, titreDoc, fichier_pdf FROM documentations ORDER BY id");
    $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($docs)) {
        echo "✗ No documents found in database<br>";
    } else {
        echo "Found " . count($docs) . " documents:<br>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>File</th><th>File Exists</th><th>Action</th></tr>";
        
        foreach ($docs as $doc) {
            $id = htmlspecialchars($doc['id']);
            $titre = htmlspecialchars($doc['titreDoc'] ?? 'N/A');
            $fichier = htmlspecialchars($doc['fichier_pdf'] ?? '');
            
            if (empty($doc['fichier_pdf'])) {
                $exists = "✗ NO FILE";
                $action = "N/A";
            } else {
                $filePath = __DIR__ . '/../img/documentations/' . $doc['fichier_pdf'];
                $exists = file_exists($filePath) ? "✓ YES" : "✗ NO";
                $viewUrl = BASE_URL . 'pagesweb/documentation_event.php?doc_id=' . $id . '&action=view';
                $downloadUrl = BASE_URL . 'pagesweb/documentation_event.php?doc_id=' . $id . '&action=download';
                $action = "<a href='" . htmlspecialchars($viewUrl) . "' target='_blank'>View</a> | <a href='" . htmlspecialchars($downloadUrl) . "'>Download</a>";
            }
            
            echo "<tr><td>$id</td><td>$titre</td><td>$fichier</td><td>$exists</td><td>$action</td></tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

// Test 3: Check if img/documentations directory exists
echo "<h3>Test 3: Check directory</h3>";
$docsDir = __DIR__ . '/../img/documentations/';
if (is_dir($docsDir)) {
    echo "✓ Directory exists: " . htmlspecialchars($docsDir) . "<br>";
    
    // List all PDF files in directory
    $files = glob($docsDir . '*.pdf');
    echo "Found " . count($files) . " PDF files:<br>";
    foreach ($files as $file) {
        echo "- " . htmlspecialchars(basename($file)) . " (" . filesize($file) . " bytes)<br>";
    }
} else {
    echo "✗ Directory does not exist: " . htmlspecialchars($docsDir) . "<br>";
}

echo "<h3>Test 4: Check documentation_events table</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'documentation_events'");
    if ($stmt->rowCount() > 0) {
        echo "✓ documentation_events table exists<br>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM documentation_events");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Contains " . $result['count'] . " events<br>";
    } else {
        echo "✗ documentation_events table does NOT exist<br>";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

echo "<br><p><a href='javascript:history.back()'>Back</a></p>";
?>
