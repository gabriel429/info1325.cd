<?php
/**
 * Site Settings Helper Functions
 * Functions to retrieve and manage site settings
 */

// Ensure database connection
if (!isset($pdo)) {
    require_once __DIR__ . '/connectDb.php';
}

/**
 * Get a setting value by key
 * @param string $key Setting key
 * @param string $default Default value if setting not found
 * @return string Setting value
 */
function get_setting($key, $default = '') {
    global $pdo;

    static $cache = [];

    // Check cache first
    if (isset($cache[$key])) {
        return $cache[$key];
    }

    try {
        $stmt = $pdo->prepare('SELECT setting_value FROM site_settings WHERE setting_key = :key LIMIT 1');
        $stmt->execute([':key' => $key]);
        $result = $stmt->fetchColumn();

        $value = $result !== false ? $result : $default;
        $cache[$key] = $value;

        return $value;
    } catch (PDOException $e) {
        error_log("Error getting setting: " . $e->getMessage());
        return $default;
    }
}

/**
 * Update a setting value
 * @param string $key Setting key
 * @param string $value New value
 * @return bool Success status
 */
function update_setting($key, $value) {
    global $pdo;

    try {
        $stmt = $pdo->prepare('UPDATE site_settings SET setting_value = :value WHERE setting_key = :key');
        return $stmt->execute([':value' => $value, ':key' => $key]);
    } catch (PDOException $e) {
        error_log("Error updating setting: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all settings as associative array
 * @return array All settings [key => value]
 */
function get_all_settings() {
    global $pdo;

    static $all_settings = null;

    if ($all_settings !== null) {
        return $all_settings;
    }

    try {
        $stmt = $pdo->query('SELECT setting_key, setting_value FROM site_settings');
        $all_settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $all_settings[$row['setting_key']] = $row['setting_value'];
        }
        return $all_settings;
    } catch (PDOException $e) {
        error_log("Error getting all settings: " . $e->getMessage());
        return [];
    }
}

/**
 * Get settings by group
 * @param string $group Setting group (general, contact, social, seo, features)
 * @return array Settings in group
 */
function get_settings_by_group($group) {
    global $pdo;

    try {
        $stmt = $pdo->prepare('SELECT setting_key, setting_value FROM site_settings WHERE setting_group = :group');
        $stmt->execute([':group' => $group]);
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    } catch (PDOException $e) {
        error_log("Error getting group settings: " . $e->getMessage());
        return [];
    }
}

/**
 * Check if a feature is enabled
 * @param string $feature_key Feature setting key (e.g., 'enable_comments')
 * @return bool True if enabled
 */
function is_feature_enabled($feature_key) {
    return get_setting($feature_key, '0') === '1';
}

/**
 * Get site name
 * @return string Site name
 */
function get_site_name() {
    return get_setting('site_name', 'SN1325');
}

/**
 * Get site tagline
 * @return string Site tagline
 */
function get_site_tagline() {
    return get_setting('site_tagline', 'Plateforme Nationale 1325');
}

/**
 * Get contact email
 * @return string Contact email
 */
function get_contact_email() {
    return get_setting('contact_email', 'contact@sn1325.cd');
}

/**
 * Get social media links
 * @return array Social media URLs
 */
function get_social_links() {
    return [
        'facebook' => get_setting('social_facebook', ''),
        'twitter' => get_setting('social_twitter', ''),
        'instagram' => get_setting('social_instagram', ''),
        'linkedin' => get_setting('social_linkedin', ''),
        'youtube' => get_setting('social_youtube', ''),
    ];
}

/**
 * Output social media icons HTML
 * @param string $class_list Additional CSS classes
 */
function display_social_icons($class_list = 'fs-4') {
    $socials = get_social_links();
    $icons = [
        'facebook' => 'facebook',
        'twitter' => 'twitter-x',
        'instagram' => 'instagram',
        'linkedin' => 'linkedin',
        'youtube' => 'youtube',
    ];

    foreach ($socials as $key => $url) {
        if (!empty($url)) {
            $icon = $icons[$key] ?? $key;
            echo '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"
                     class="' . htmlspecialchars($class_list, ENT_QUOTES, 'UTF-8') . ' me-3 text-decoration-none"
                     target="_blank" rel="noopener noreferrer">
                    <i class="bi bi-' . $icon . '"></i>
                  </a>';
        }
    }
}

/**
 * Check if site is in maintenance mode
 * @return bool True if in maintenance
 */
function is_maintenance_mode() {
    return get_setting('maintenance_mode', '0') === '1';
}
