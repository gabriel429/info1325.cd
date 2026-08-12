<?php
session_start();
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // $pdo

if (!isset($_SESSION['user'])) {
    header('Location: ' . URL_AUTHENTIFICATION);
    exit;
}

$role = $_SESSION['role'] ?? 'user';

// ---- Stats dashboard ----
$stats = ['articles' => 0, 'docs' => 0, 'partners' => 0, 'visitors_today' => 0];

try {
    $stats['articles'] = (int)$pdo->query("SELECT COUNT(*) FROM actualites")->fetchColumn();
} catch (Exception $e) {}

try {
    $stats['docs'] = (int)$pdo->query("SELECT COUNT(*) FROM documentations")->fetchColumn();
} catch (Exception $e) {}

try {
    $stats['partners'] = (int)$pdo->query("SELECT COUNT(*) FROM partenaires")->fetchColumn();
} catch (Exception $e) {}

try {
    $stats['visitors_today'] = (int)$pdo->query(
        "SELECT COUNT(DISTINCT visitor_id) FROM visits WHERE visit_date = CURDATE()"
    )->fetchColumn();
} catch (Exception $e) {}

// ---- Layout variables ----
$pageTitle  = 'Tableau de bord';
$breadcrumb = [['label' => 'Vue d\'ensemble']];
$activePage = 'dashboard';

require_once __DIR__ . '/admin_layout_top.php';
?>

<!-- Page header -->
<div class="page-header">
    <div>
        <h1><i class="bi bi-speedometer2 me-2" style="color:var(--accent)"></i>Tableau de bord</h1>
        <p>Bienvenue, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong> &mdash;
           <?= date('l d F Y') ?></p>
    </div>
    <a href="<?= URL_ACCUEIL ?>" target="_blank" class="btn btn-admin-primary">
        <i class="bi bi-globe2 me-1"></i> Voir le site public
    </a>
</div>

<!-- ---- Stat cards ---- -->
<div class="row g-3 mb-4">

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-card-icon blue"><i class="bi bi-newspaper"></i></div>
            <div class="stat-card-info">
                <div class="stat-card-value"><?= $stats['articles'] ?></div>
                <div class="stat-card-label">Actualités</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-card-icon green"><i class="bi bi-file-earmark-text"></i></div>
            <div class="stat-card-info">
                <div class="stat-card-value"><?= $stats['docs'] ?></div>
                <div class="stat-card-label">Documents</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-card-icon orange"><i class="bi bi-handshake"></i></div>
            <div class="stat-card-info">
                <div class="stat-card-value"><?= $stats['partners'] ?></div>
                <div class="stat-card-label">Partenaires</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-card-icon purple"><i class="bi bi-person-check"></i></div>
            <div class="stat-card-info">
                <div class="stat-card-value"><?= $stats['visitors_today'] ?></div>
                <div class="stat-card-label">Visiteurs aujourd'hui</div>
            </div>
        </div>
    </div>

</div>

<!-- ---- Quick access ---- -->
<div class="admin-card mb-4">
    <div class="card-header">
        <i class="bi bi-lightning-charge me-2" style="color:var(--accent)"></i>Accès rapide
    </div>
    <div class="card-body">
        <div class="row g-3">

            <div class="col-6 col-md-3 col-xl-2">
                <a href="<?= URL_ADDACTUALITES ?>" class="quick-card">
                    <div class="quick-card-icon" style="background:#e8f4fd;color:var(--accent)">
                        <i class="bi bi-newspaper"></i>
                    </div>
                    <div class="quick-card-label">Actualités</div>
                </a>
            </div>

            <div class="col-6 col-md-3 col-xl-2">
                <a href="<?= URL_ADDDOCUMENTATIONS ?>" class="quick-card">
                    <div class="quick-card-icon" style="background:#e8f8ef;color:#28c76f">
                        <i class="bi bi-file-earmark-plus"></i>
                    </div>
                    <div class="quick-card-label">Documentation</div>
                </a>
            </div>

            <div class="col-6 col-md-3 col-xl-2">
                <a href="<?= URL_ALLDOCUMENTATIONS ?>" class="quick-card">
                    <div class="quick-card-icon" style="background:#f0eefa;color:#9b59b6">
                        <i class="bi bi-collection"></i>
                    </div>
                    <div class="quick-card-label">Toutes les docs</div>
                </a>
            </div>

            <div class="col-6 col-md-3 col-xl-2">
                <a href="<?= URL_MANAGE_SLIDER ?>" class="quick-card">
                    <div class="quick-card-icon" style="background:#fff5e6;color:#ff9f43">
                        <i class="bi bi-images"></i>
                    </div>
                    <div class="quick-card-label">Slider</div>
                </a>
            </div>

            <div class="col-6 col-md-3 col-xl-2">
                <a href="<?= URL_MANAGE_GALERIE ?>" class="quick-card">
                    <div class="quick-card-icon" style="background:#fce8ef;color:#e84393">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </div>
                    <div class="quick-card-label">Galerie</div>
                </a>
            </div>

            <div class="col-6 col-md-3 col-xl-2">
                <a href="<?= URL_MANAGE_PARTENAIRES ?>" class="quick-card">
                    <div class="quick-card-icon" style="background:#e8f4fd;color:#3290c0">
                        <i class="bi bi-handshake"></i>
                    </div>
                    <div class="quick-card-label">Partenaires</div>
                </a>
            </div>

            <div class="col-6 col-md-3 col-xl-2">
                <a href="<?= URL_MANAGE_AXES ?>" class="quick-card">
                    <div class="quick-card-icon" style="background:#e6f9f5;color:#00b09b">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                    <div class="quick-card-label">Axes</div>
                </a>
            </div>

            <div class="col-6 col-md-3 col-xl-2">
                <a href="<?= URL_MANAGE_FUNFACTS ?>" class="quick-card">
                    <div class="quick-card-icon" style="background:#fff0f0;color:#e74c3c">
                        <i class="bi bi-bar-chart-line"></i>
                    </div>
                    <div class="quick-card-label">Fun Facts</div>
                </a>
            </div>

            <?php if ($role === 'admin'): ?>
            <div class="col-6 col-md-3 col-xl-2">
                <a href="<?= URL_MANAGE_USERS ?>" class="quick-card">
                    <div class="quick-card-icon" style="background:#f0f9f0;color:#28c76f">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="quick-card-label">Utilisateurs</div>
                </a>
            </div>

            <div class="col-6 col-md-3 col-xl-2">
                <a href="<?= URL_MANAGE_SETTINGS ?>" class="quick-card">
                    <div class="quick-card-icon" style="background:#f5f5f5;color:#6c757d">
                        <i class="bi bi-gear"></i>
                    </div>
                    <div class="quick-card-label">Paramètres</div>
                </a>
            </div>
            <?php endif; ?>

            <div class="col-6 col-md-3 col-xl-2">
                <a href="<?= BASE_URL ?>pagesweb/change_password.php" class="quick-card">
                    <div class="quick-card-icon" style="background:#fdf8e1;color:#f0c05a">
                        <i class="bi bi-key"></i>
                    </div>
                    <div class="quick-card-label">Mot de passe</div>
                </a>
            </div>

        </div>
    </div>
</div>

<!-- ---- Visitor stats widget ---- -->
<div class="admin-card">
    <div class="card-header">
        <i class="bi bi-graph-up me-2" style="color:var(--accent)"></i>Statistiques de visites
    </div>
    <div class="card-body">
        <?php include __DIR__ . '/visitor_stats_widget.php'; ?>
    </div>
</div>

<?php require_once __DIR__ . '/admin_layout_bottom.php'; ?>
