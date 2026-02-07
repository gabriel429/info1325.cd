<?php
session_start();
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect;
require_once __DIR__ . '/csrf.php';

// Only admins can manage settings
if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ' . URL_AUTHENTIFICATION);
    exit;
}

// Create settings table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_group VARCHAR(50) DEFAULT 'general',
        description VARCHAR(255),
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_key (setting_key),
        INDEX idx_group (setting_group)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Insert default settings if table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM site_settings");
    if ((int)$stmt->fetchColumn() === 0) {
        $defaults = [
            // General
            ['site_name', 'SN1325', 'general', 'Nom du site web'],
            ['site_tagline', 'Plateforme Nationale 1325', 'general', 'Slogan du site'],
            ['site_description', 'Plateforme Nationale de Mise en Œuvre de la Résolution 1325', 'general', 'Description du site'],
            ['site_keywords', 'résolution 1325, femmes, paix, sécurité, Sénégal', 'general', 'Mots-clés SEO'],

            // Contact
            ['contact_email', 'contact@sn1325.cd', 'contact', 'Email de contact principal'],
            ['contact_phone', '+221 XX XXX XX XX', 'contact', 'Téléphone de contact'],
            ['contact_address', 'Dakar, Sénégal', 'contact', 'Adresse postale'],

            // Social Media
            ['social_facebook', '', 'social', 'URL page Facebook'],
            ['social_twitter', '', 'social', 'URL compte Twitter/X'],
            ['social_instagram', '', 'social', 'URL compte Instagram'],
            ['social_linkedin', '', 'social', 'URL page LinkedIn'],
            ['social_youtube', '', 'social', 'URL chaîne YouTube'],

            // SEO
            ['seo_meta_title', 'SN1325 - Plateforme Nationale', 'seo', 'Titre meta par défaut'],
            ['seo_meta_description', 'Plateforme de mise en œuvre de la Résolution 1325', 'seo', 'Description meta par défaut'],
            ['seo_google_analytics', '', 'seo', 'Code Google Analytics (GA-XXXXXXX)'],

            // Features
            ['enable_comments', '1', 'features', 'Activer les commentaires sur actualités'],
            ['enable_newsletter', '1', 'features', 'Activer inscription newsletter'],
            ['maintenance_mode', '0', 'features', 'Mode maintenance (1=activé, 0=désactivé)'],
        ];

        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_group, description) VALUES (?, ?, ?, ?)");
        foreach ($defaults as $setting) {
            $stmt->execute($setting);
        }
    }
} catch (PDOException $e) {
    error_log("Error creating settings table: " . $e->getMessage());
}

// Handle form submission
$msg = '';
$msgType = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate(true);

    try {
        $stmt = $pdo->prepare("UPDATE site_settings SET setting_value = :value WHERE setting_key = :key");

        foreach ($_POST as $key => $value) {
            if ($key === 'csrf_token') continue;

            // Handle checkboxes (not present when unchecked)
            if (strpos($key, 'enable_') === 0 || strpos($key, '_mode') !== false) {
                $value = isset($_POST[$key]) ? '1' : '0';
            }

            $stmt->execute([':value' => trim($value), ':key' => $key]);
        }

        $msg = 'Paramètres enregistrés avec succès.';
        $msgType = 'success';
    } catch (PDOException $e) {
        $msg = 'Erreur lors de la sauvegarde: ' . $e->getMessage();
        $msgType = 'danger';
    }
}

// Load all settings grouped
$settings = [];
$stmt = $pdo->query("SELECT * FROM site_settings ORDER BY setting_group, setting_key");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $group = $row['setting_group'];
    if (!isset($settings[$group])) {
        $settings[$group] = [];
    }
    $settings[$group][] = $row;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres du Site - SN1325</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .settings-section {
            margin-bottom: 2rem;
        }
        .setting-item {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        .setting-item:last-child {
            border-bottom: none;
        }
        .setting-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .setting-description {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm mb-4">
    <div class="container-fluid">
        <span class="navbar-brand">⚙️ Paramètres du Site</span>
        <a href="<?= URL_ADMINISTRATEUR ?>" class="btn btn-outline-light btn-sm">
            <i class="bi bi-arrow-left"></i> Retour au dashboard
        </a>
    </div>
</nav>

<div class="container-fluid py-4">

    <?php if ($msg): ?>
        <div class="alert alert-<?= $msgType ?> alert-dismissible fade show">
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <form method="post">
                <?php csrf_field(); ?>

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button">
                            <i class="bi bi-gear-fill"></i> Général
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button">
                            <i class="bi bi-envelope-fill"></i> Contact
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button">
                            <i class="bi bi-share-fill"></i> Réseaux Sociaux
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button">
                            <i class="bi bi-search"></i> SEO
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="features-tab" data-bs-toggle="tab" data-bs-target="#features" type="button">
                            <i class="bi bi-toggles"></i> Fonctionnalités
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="settingsTabsContent">

                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-gear-fill"></i> Paramètres Généraux</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach (($settings['general'] ?? []) as $setting): ?>
                                    <div class="setting-item">
                                        <label class="setting-label" for="<?= $setting['setting_key'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', str_replace('site_', '', $setting['setting_key']))) ?>
                                        </label>
                                        <?php if (strlen($setting['setting_value']) > 100): ?>
                                            <textarea
                                                name="<?= $setting['setting_key'] ?>"
                                                id="<?= $setting['setting_key'] ?>"
                                                class="form-control"
                                                rows="3"><?= htmlspecialchars($setting['setting_value']) ?></textarea>
                                        <?php else: ?>
                                            <input
                                                type="text"
                                                name="<?= $setting['setting_key'] ?>"
                                                id="<?= $setting['setting_key'] ?>"
                                                class="form-control"
                                                value="<?= htmlspecialchars($setting['setting_value']) ?>">
                                        <?php endif; ?>
                                        <?php if ($setting['description']): ?>
                                            <div class="setting-description"><?= htmlspecialchars($setting['description']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Settings -->
                    <div class="tab-pane fade" id="contact" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="bi bi-envelope-fill"></i> Informations de Contact</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach (($settings['contact'] ?? []) as $setting): ?>
                                    <div class="setting-item">
                                        <label class="setting-label" for="<?= $setting['setting_key'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', str_replace('contact_', '', $setting['setting_key']))) ?>
                                        </label>
                                        <input
                                            type="text"
                                            name="<?= $setting['setting_key'] ?>"
                                            id="<?= $setting['setting_key'] ?>"
                                            class="form-control"
                                            value="<?= htmlspecialchars($setting['setting_value']) ?>">
                                        <?php if ($setting['description']): ?>
                                            <div class="setting-description"><?= htmlspecialchars($setting['description']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Settings -->
                    <div class="tab-pane fade" id="social" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-share-fill"></i> Réseaux Sociaux</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach (($settings['social'] ?? []) as $setting): ?>
                                    <div class="setting-item">
                                        <label class="setting-label" for="<?= $setting['setting_key'] ?>">
                                            <i class="bi bi-<?= str_replace('social_', '', $setting['setting_key']) ?>"></i>
                                            <?= ucfirst(str_replace('social_', '', $setting['setting_key'])) ?>
                                        </label>
                                        <input
                                            type="url"
                                            name="<?= $setting['setting_key'] ?>"
                                            id="<?= $setting['setting_key'] ?>"
                                            class="form-control"
                                            value="<?= htmlspecialchars($setting['setting_value']) ?>"
                                            placeholder="https://...">
                                        <?php if ($setting['description']): ?>
                                            <div class="setting-description"><?= htmlspecialchars($setting['description']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Settings -->
                    <div class="tab-pane fade" id="seo" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-search"></i> Référencement (SEO)</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach (($settings['seo'] ?? []) as $setting): ?>
                                    <div class="setting-item">
                                        <label class="setting-label" for="<?= $setting['setting_key'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', str_replace('seo_', '', $setting['setting_key']))) ?>
                                        </label>
                                        <?php if (strpos($setting['setting_key'], 'description') !== false): ?>
                                            <textarea
                                                name="<?= $setting['setting_key'] ?>"
                                                id="<?= $setting['setting_key'] ?>"
                                                class="form-control"
                                                rows="3"><?= htmlspecialchars($setting['setting_value']) ?></textarea>
                                        <?php else: ?>
                                            <input
                                                type="text"
                                                name="<?= $setting['setting_key'] ?>"
                                                id="<?= $setting['setting_key'] ?>"
                                                class="form-control"
                                                value="<?= htmlspecialchars($setting['setting_value']) ?>">
                                        <?php endif; ?>
                                        <?php if ($setting['description']): ?>
                                            <div class="setting-description"><?= htmlspecialchars($setting['description']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Features Settings -->
                    <div class="tab-pane fade" id="features" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-header bg-warning">
                                <h5 class="mb-0"><i class="bi bi-toggles"></i> Fonctionnalités</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach (($settings['features'] ?? []) as $setting): ?>
                                    <div class="setting-item">
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="<?= $setting['setting_key'] ?>"
                                                id="<?= $setting['setting_key'] ?>"
                                                value="1"
                                                <?= $setting['setting_value'] == '1' ? 'checked' : '' ?>>
                                            <label class="form-check-label setting-label" for="<?= $setting['setting_key'] ?>">
                                                <?= ucfirst(str_replace('_', ' ', $setting['setting_key'])) ?>
                                            </label>
                                        </div>
                                        <?php if ($setting['description']): ?>
                                            <div class="setting-description"><?= htmlspecialchars($setting['description']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Save Button -->
                <div class="card mt-4 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i>
                                Les modifications seront appliquées immédiatement sur le site.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-lg"></i> Enregistrer les Paramètres
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
