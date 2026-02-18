<?php
/**
 * Test script to verify track_documentation_event function
 * Access at: http://localhost/info1325.cd/pagesweb/test_tracking.php
 */

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect;

define('SKIP_AUTO_TRACK', true);
require_once __DIR__ . '/track_visitor.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Documentation Tracking Test</h2>";

// Test track_documentation_event function
echo "<h3>Test: track_documentation_event()</h3>";

// First, ensure table exists
echo "Ensuring documentation_events table exists...<br>";
try {
    ensure_documentation_events_table($pdo);
    echo "✓ Table ready<br>";
} catch (Exception $e) {
    echo "✗ Error creating table: " . $e->getMessage() . "<br>";
    exit;
}

// Test 1: Track a view event
echo "<h3>Test 1: Track a view event</h3>";
try {
    $result = track_documentation_event(
        $pdo,
        1,  // doc ID
        'view',
        'http://localhost/info1325.cd/pagesweb/documentation/',
        'Test Document',
        'test.pdf'
    );
    
    if ($result) {
        echo "✓ Successfully tracked view event<br>";
    } else {
        echo "✗ Function returned false (check PHP error log)<br>";
    }
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "<br>";
}

// Test 2: Track a download event
echo "<h3>Test 2: Track a download event</h3>";
try {
    $result = track_documentation_event(
        $pdo,
        2,
        'download',
        'http://localhost/info1325.cd/pagesweb/documentation/',
        'Another Document',
        'another.pdf'
    );
    
    if ($result) {
        echo "✓ Successfully tracked download event<br>";
    } else {
        echo "✗ Function returned false (check PHP error log)<br>";
    }
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "<br>";
}

// Test 3: Get documentation statistics
echo "<h3>Test 3: Get documentation statistics</h3>";
try {
    $stats = get_documentation_stats($pdo, 30, 5);
    echo "✓ Statistics retrieved<br>";
    echo "<pre>";
    print_r($stats);
    echo "</pre>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

// Test 4: Check recent events
echo "<h3>Test 4: Check recent events in database</h3>";
try {
    $stmt = $pdo->query("SELECT * FROM documentation_events ORDER BY event_date DESC LIMIT 10");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($events)) {
        echo "✗ No events found in database<br>";
    } else {
        echo "✓ Found " . count($events) . " recent events<br>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Doc ID</th><th>Type</th><th>File</th><th>Date</th></tr>";
        foreach ($events as $event) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($event['id'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($event['documentation_id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($event['event_type'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($event['file_name'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($event['event_date'] ?? '') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

echo "<br><p><a href='javascript:history.back()'>Back</a></p>";
?>
