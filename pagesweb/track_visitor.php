<?php
/**
 * Visitor Tracking System
 * Tracks unique visitors and page views
 */

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/connectDb.php'; // provides $pdo

// Create visits table if not exists
function ensure_visits_table($pdo) {
    try {
        $sql = "CREATE TABLE IF NOT EXISTS visits (
            id INT AUTO_INCREMENT PRIMARY KEY,
            visitor_id VARCHAR(64) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT,
            page_url VARCHAR(512),
            referrer VARCHAR(512),
            visit_date DATE NOT NULL,
            visit_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            is_unique TINYINT(1) DEFAULT 1,
            country VARCHAR(2) DEFAULT NULL,
            INDEX idx_visitor_id (visitor_id),
            INDEX idx_visit_date (visit_date),
            INDEX idx_ip_address (ip_address)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $pdo->exec($sql);

        // Create statistics summary table for faster queries
        $sql2 = "CREATE TABLE IF NOT EXISTS visit_stats (
            id INT AUTO_INCREMENT PRIMARY KEY,
            stat_date DATE NOT NULL UNIQUE,
            total_visits INT DEFAULT 0,
            unique_visits INT DEFAULT 0,
            page_views INT DEFAULT 0,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_stat_date (stat_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $pdo->exec($sql2);

    } catch (PDOException $e) {
        error_log("Error creating visits table: " . $e->getMessage());
    }
}

ensure_visits_table($pdo);

/**
 * Get or create visitor ID
 * Uses cookie to identify returning visitors
 */
function get_visitor_id() {
    $cookie_name = 'sn1325_visitor_id';
    $cookie_lifetime = 365 * 24 * 60 * 60; // 1 year

    if (isset($_COOKIE[$cookie_name])) {
        return $_COOKIE[$cookie_name];
    }

    // Generate new visitor ID
    $visitor_id = bin2hex(random_bytes(16));

    // Set cookie
    setcookie($cookie_name, $visitor_id, time() + $cookie_lifetime, '/', '', false, true);

    return $visitor_id;
}

/**
 * Get client IP address
 */
function get_visitor_ip() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    // Check for proxy headers
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }

    return $ip;
}

/**
 * Check if this is a unique visit today
 */
function is_unique_visit_today($pdo, $visitor_id) {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM visits WHERE visitor_id = :vid AND visit_date = :date');
    $stmt->execute([':vid' => $visitor_id, ':date' => $today]);
    $count = (int)$stmt->fetchColumn();

    return $count === 0;
}

/**
 * Track a visit
 */
function track_visit() {
    global $pdo;

    // Don't track admin pages
    $current_page = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($current_page, '/pagesweb/') !== false &&
        strpos($current_page, 'authentification') === false) {
        return; // Skip tracking for admin pages
    }

    // Get visitor information
    $visitor_id = get_visitor_id();
    $ip_address = get_visitor_ip();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $page_url = $_SERVER['REQUEST_URI'] ?? '/';
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    $today = date('Y-m-d');

    // Check if unique visit today
    $is_unique = is_unique_visit_today($pdo, $visitor_id) ? 1 : 0;

    try {
        // Insert visit record
        $stmt = $pdo->prepare('
            INSERT INTO visits (visitor_id, ip_address, user_agent, page_url, referrer, visit_date, is_unique)
            VALUES (:vid, :ip, :ua, :url, :ref, :date, :unique)
        ');

        $stmt->execute([
            ':vid' => $visitor_id,
            ':ip' => $ip_address,
            ':ua' => substr($user_agent, 0, 500),
            ':url' => substr($page_url, 0, 500),
            ':ref' => substr($referrer, 0, 500),
            ':date' => $today,
            ':unique' => $is_unique
        ]);

        // Update daily statistics
        update_daily_stats($pdo, $today);

    } catch (PDOException $e) {
        error_log("Error tracking visit: " . $e->getMessage());
    }
}

/**
 * Update daily statistics
 */
function update_daily_stats($pdo, $date) {
    try {
        // Count visits for the day
        $stmt = $pdo->prepare('
            SELECT
                COUNT(*) as total_visits,
                SUM(is_unique) as unique_visits
            FROM visits
            WHERE visit_date = :date
        ');
        $stmt->execute([':date' => $date]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        // Insert or update statistics
        $stmt = $pdo->prepare('
            INSERT INTO visit_stats (stat_date, total_visits, unique_visits, page_views)
            VALUES (:date, :total, :unique, :pageviews)
            ON DUPLICATE KEY UPDATE
                total_visits = :total,
                unique_visits = :unique,
                page_views = :pageviews
        ');

        $stmt->execute([
            ':date' => $date,
            ':total' => $stats['total_visits'] ?? 0,
            ':unique' => $stats['unique_visits'] ?? 0,
            ':pageviews' => $stats['total_visits'] ?? 0
        ]);

    } catch (PDOException $e) {
        error_log("Error updating daily stats: " . $e->getMessage());
    }
}

/**
 * Get visitor statistics
 */
function get_visitor_stats($pdo) {
    $stats = [
        'total_visits' => 0,
        'unique_visitors' => 0,
        'today' => 0,
        'today_unique' => 0,
        'this_week' => 0,
        'this_month' => 0,
        'avg_daily' => 0,
        'recent_visitors' => []
    ];

    try {
        // Total visits
        $stmt = $pdo->query('SELECT COUNT(*) FROM visits');
        $stats['total_visits'] = (int)$stmt->fetchColumn();

        // Unique visitors
        $stmt = $pdo->query('SELECT COUNT(DISTINCT visitor_id) FROM visits');
        $stats['unique_visitors'] = (int)$stmt->fetchColumn();

        // Today
        $today = date('Y-m-d');
        $stmt = $pdo->prepare('SELECT COUNT(*), SUM(is_unique) FROM visits WHERE visit_date = :date');
        $stmt->execute([':date' => $today]);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stats['today'] = (int)($row[0] ?? 0);
        $stats['today_unique'] = (int)($row[1] ?? 0);

        // This week
        $week_start = date('Y-m-d', strtotime('monday this week'));
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM visits WHERE visit_date >= :date');
        $stmt->execute([':date' => $week_start]);
        $stats['this_week'] = (int)$stmt->fetchColumn();

        // This month
        $month_start = date('Y-m-01');
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM visits WHERE visit_date >= :date');
        $stmt->execute([':date' => $month_start]);
        $stats['this_month'] = (int)$stmt->fetchColumn();

        // Average daily (last 30 days)
        $stmt = $pdo->prepare('
            SELECT AVG(daily_count) as avg_daily
            FROM (
                SELECT COUNT(*) as daily_count
                FROM visits
                WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY visit_date
            ) as daily_stats
        ');
        $stmt->execute();
        $stats['avg_daily'] = round($stmt->fetchColumn() ?? 0, 1);

        // Recent visitors (last 10)
        $stmt = $pdo->query('
            SELECT ip_address, page_url, visit_time
            FROM visits
            ORDER BY visit_time DESC
            LIMIT 10
        ');
        $stats['recent_visitors'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error fetching visitor stats: " . $e->getMessage());
    }

    return $stats;
}

/**
 * Get daily stats for chart (last 30 days)
 */
function get_daily_stats_chart($pdo, $days = 30) {
    try {
        $stmt = $pdo->prepare('
            SELECT
                visit_date,
                COUNT(*) as visits,
                SUM(is_unique) as unique_visits
            FROM visits
            WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY visit_date
            ORDER BY visit_date ASC
        ');
        $stmt->execute([':days' => $days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching daily stats: " . $e->getMessage());
        return [];
    }
}

// Auto-track if this file is included
if (!defined('SKIP_AUTO_TRACK')) {
    track_visit();
}
