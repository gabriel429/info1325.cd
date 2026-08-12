<?php
/**
 * admin_layout_top.php — Layout partagé pour toutes les pages d'administration.
 *
 * Variables attendues (définies AVANT l'include) :
 *   string $pageTitle   — Titre de la page (affiché dans <title> et topbar)
 *   array  $breadcrumb  — Optionnel : [['label'=>'Dashboard','url'=>URL_ADMIN_DASHBOARD], ['label'=>'Actualités']]
 *   string $activePage  — Optionnel : clé de la page active dans la sidebar (ex: 'actualites', 'dashboard')
 */

// Valeurs par défaut
$pageTitle   = $pageTitle   ?? 'Administration';
$breadcrumb  = $breadcrumb  ?? [];
$activePage  = $activePage  ?? '';

ini_set('default_charset', 'UTF-8');
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}

// Lien actif helper
function sidebarActive(string $page, string $current): string {
    return $page === $current ? ' active' : '';
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> — SN1325 Admin</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* =============================================
           VARIABLES & RESET
        ============================================= */
        :root {
            --sidebar-width:     260px;
            --sidebar-bg:        #1e2a3a;
            --sidebar-text:      #a8b9ca;
            --sidebar-hover-bg:  rgba(77,168,218,.12);
            --sidebar-active-bg: rgba(77,168,218,.18);
            --sidebar-active-cl: #4da8da;
            --sidebar-brand-bg:  #16202e;
            --topbar-height:     60px;
            --topbar-bg:         #ffffff;
            --topbar-border:     #e9ecef;
            --content-bg:        #f4f6f9;
            --accent:            #4da8da;
            --accent-dark:       #3290c0;
            --card-shadow:       0 2px 12px rgba(0,0,0,.07);
            --transition:        all .25s ease;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body.admin-body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--content-bg);
            color: #3d4d5e;
            overflow-x: hidden;
        }

        /* =============================================
           SIDEBAR
        ============================================= */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: var(--transition);
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Scrollbar sidebar */
        .admin-sidebar::-webkit-scrollbar { width: 4px; }
        .admin-sidebar::-webkit-scrollbar-track { background: transparent; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 2px; }

        /* Brand */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 20px;
            height: var(--topbar-height);
            background: var(--sidebar-brand-bg);
            text-decoration: none;
            flex-shrink: 0;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }
        .sidebar-brand-icon {
            width: 32px;
            height: 32px;
            background: var(--accent);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 16px;
            flex-shrink: 0;
        }
        .sidebar-brand-text {
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: .3px;
            line-height: 1.2;
        }
        .sidebar-brand-text span {
            display: block;
            font-size: 11px;
            font-weight: 400;
            color: var(--sidebar-text);
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        /* Navigation */
        .sidebar-nav {
            flex: 1;
            padding: 12px 0;
        }
        .sidebar-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .nav-section-title {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(168,185,202,.5);
            padding: 16px 20px 6px;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 13.5px;
            border-radius: 0;
            transition: var(--transition);
            position: relative;
        }
        .sidebar-nav a:hover {
            background: var(--sidebar-hover-bg);
            color: #fff;
        }
        .sidebar-nav a.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active-cl);
        }
        .sidebar-nav a.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--accent);
            border-radius: 0 2px 2px 0;
        }
        .sidebar-nav a i {
            font-size: 16px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 12px 0;
            border-top: 1px solid rgba(255,255,255,.07);
            flex-shrink: 0;
        }
        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 13.5px;
            transition: var(--transition);
        }
        .sidebar-footer a:hover { color: #fff; background: var(--sidebar-hover-bg); }
        .sidebar-footer a.logout-btn { color: #e87878; }
        .sidebar-footer a.logout-btn:hover { background: rgba(232,120,120,.12); color: #ff6b6b; }
        .sidebar-footer a i { font-size: 16px; width: 20px; text-align: center; }

        /* =============================================
           MAIN AREA
        ============================================= */
        .admin-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
        }

        /* Topbar */
        .admin-topbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            height: var(--topbar-height);
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--topbar-border);
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 16px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        #sidebarToggle {
            background: none;
            border: none;
            padding: 6px 8px;
            border-radius: 6px;
            color: #6c757d;
            font-size: 20px;
            cursor: pointer;
            transition: var(--transition);
            line-height: 1;
        }
        #sidebarToggle:hover { background: #f0f0f0; color: #343a40; }

        .topbar-breadcrumb {
            flex: 1;
        }
        .topbar-breadcrumb .breadcrumb {
            margin: 0;
            font-size: 13px;
        }
        .topbar-breadcrumb .breadcrumb-item a {
            color: var(--accent);
            text-decoration: none;
        }
        .topbar-breadcrumb .breadcrumb-item.active { color: #6c757d; }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            color: #495057;
        }
        .topbar-user .user-avatar {
            width: 34px;
            height: 34px;
            background: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
        }
        .topbar-user .user-name { font-weight: 500; }
        .topbar-user .user-role {
            font-size: 11px;
            color: #adb5bd;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* Page content */
        .admin-content {
            flex: 1;
            padding: 24px;
        }

        /* =============================================
           SIDEBAR COLLAPSED
        ============================================= */
        body.sidebar-collapsed .admin-sidebar {
            transform: translateX(calc(-1 * var(--sidebar-width)));
        }
        body.sidebar-collapsed .admin-main {
            margin-left: 0;
        }

        /* Overlay mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 1039;
        }
        body.sidebar-open-mobile .sidebar-overlay { display: block; }

        /* =============================================
           RESPONSIVE
        ============================================= */
        @media (max-width: 991px) {
            .admin-sidebar {
                transform: translateX(calc(-1 * var(--sidebar-width)));
            }
            .admin-main {
                margin-left: 0;
            }
            body.sidebar-open-mobile .admin-sidebar {
                transform: translateX(0);
            }
            body.sidebar-collapsed .admin-sidebar { transform: translateX(calc(-1 * var(--sidebar-width))); }
            body.sidebar-collapsed .admin-main { margin-left: 0; }
        }

        /* =============================================
           PAGE HEADER SECTION
        ============================================= */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .page-header h1 {
            font-size: 20px;
            font-weight: 700;
            color: #1e2a3a;
            margin: 0;
        }
        .page-header p {
            margin: 2px 0 0;
            font-size: 13px;
            color: #6c757d;
        }

        /* =============================================
           CARDS ADMIN
        ============================================= */
        .admin-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            border: none;
        }
        .admin-card .card-header {
            background: transparent;
            border-bottom: 1px solid #f0f2f5;
            padding: 16px 20px;
            font-weight: 600;
            font-size: 14px;
            color: #1e2a3a;
        }
        .admin-card .card-body { padding: 20px; }

        /* Stat cards */
        .stat-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: var(--transition);
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.1); }
        .stat-card-icon {
            width: 54px;
            height: 54px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #fff;
            flex-shrink: 0;
        }
        .stat-card-icon.blue  { background: linear-gradient(135deg,#4da8da,#3290c0); }
        .stat-card-icon.green { background: linear-gradient(135deg,#28c76f,#20a557); }
        .stat-card-icon.orange{ background: linear-gradient(135deg,#ff9f43,#e8822e); }
        .stat-card-icon.purple{ background: linear-gradient(135deg,#9b59b6,#7d3c98); }
        .stat-card-info { flex: 1; min-width: 0; }
        .stat-card-value {
            font-size: 26px;
            font-weight: 800;
            color: #1e2a3a;
            line-height: 1;
        }
        .stat-card-label {
            font-size: 12px;
            color: #adb5bd;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-top: 3px;
        }

        /* Quick-link cards */
        .quick-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 20px 16px;
            text-align: center;
            text-decoration: none;
            color: #3d4d5e;
            display: block;
            transition: var(--transition);
        }
        .quick-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,.12);
            color: var(--accent);
        }
        .quick-card-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin: 0 auto 12px;
        }
        .quick-card-label { font-size: 13px; font-weight: 600; }

        /* =============================================
           BUTTONS
        ============================================= */
        .btn-admin-primary {
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 8px 18px;
            font-size: 14px;
            font-weight: 500;
            transition: var(--transition);
        }
        .btn-admin-primary:hover { background: var(--accent-dark); color: #fff; }

        /* =============================================
           TABLES
        ============================================= */
        .admin-table {
            font-size: 13.5px;
        }
        .admin-table thead th {
            background: #f8f9fa;
            color: #6c757d;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            padding: 10px 14px;
        }
        .admin-table tbody td {
            padding: 10px 14px;
            vertical-align: middle;
            border-color: #f0f2f5;
        }
        .admin-table tbody tr:hover { background: #fafbfc; }

        /* =============================================
           FORM ELEMENTS
        ============================================= */
        .form-label { font-size: 13px; font-weight: 500; color: #495057; margin-bottom: 5px; }
        .form-control, .form-select {
            font-size: 13.5px;
            border-radius: 7px;
            border-color: #dee2e6;
            padding: 8px 12px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(77,168,218,.15);
        }

        /* =============================================
           ALERTS
        ============================================= */
        .alert { border-radius: 8px; font-size: 14px; }

        /* =============================================
           UTILITY
        ============================================= */
        .badge-role-admin  { background: #e8f4fd; color: var(--accent); font-size: 11px; padding: 3px 8px; border-radius: 4px; }
        .badge-role-user   { background: #f0f9f0; color: #28c76f; font-size: 11px; padding: 3px 8px; border-radius: 4px; }
        .badge-role-slider { background: #fff8f0; color: #ff9f43; font-size: 11px; padding: 3px 8px; border-radius: 4px; }

        .section-divider {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #adb5bd;
            padding-bottom: 8px;
            border-bottom: 1px solid #e9ecef;
            margin: 24px 0 16px;
        }
    </style>
</head>
<body class="admin-body">

<!-- Overlay mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ============================================================
     SIDEBAR
============================================================ -->
<aside class="admin-sidebar" id="adminSidebar">

    <!-- Brand -->
    <a href="<?= URL_ADMIN_DASHBOARD ?>" class="sidebar-brand">
        <div class="sidebar-brand-icon"><i class="bi bi-shield-check"></i></div>
        <div class="sidebar-brand-text">
            SN1325
            <span>Administration</span>
        </div>
    </a>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <ul>
            <!-- TABLEAU DE BORD -->
            <li class="nav-section-title">Tableau de bord</li>
            <li>
                <a href="<?= URL_ADMIN_DASHBOARD ?>" class="<?= sidebarActive('dashboard', $activePage) ?>">
                    <i class="bi bi-speedometer2"></i> Vue d'ensemble
                </a>
            </li>

            <!-- CONTENU -->
            <li class="nav-section-title">Contenu</li>
            <li>
                <a href="<?= URL_ADDACTUALITES ?>" class="<?= sidebarActive('actualites', $activePage) ?>">
                    <i class="bi bi-newspaper"></i> Actualités
                </a>
            </li>
            <li>
                <a href="<?= URL_ADDDOCUMENTATIONS ?>" class="<?= sidebarActive('documentation', $activePage) ?>">
                    <i class="bi bi-file-earmark-text"></i> Documentation
                </a>
            </li>
            <li>
                <a href="<?= URL_ALLDOCUMENTATIONS ?>" class="<?= sidebarActive('all-documentations', $activePage) ?>">
                    <i class="bi bi-collection"></i> Toutes les docs
                </a>
            </li>

            <!-- MÉDIAS -->
            <li class="nav-section-title">Médias</li>
            <li>
                <a href="<?= URL_MANAGE_SLIDER ?>" class="<?= sidebarActive('slider', $activePage) ?>">
                    <i class="bi bi-images"></i> Slider
                </a>
            </li>
            <li>
                <a href="<?= URL_MANAGE_GALERIE ?>" class="<?= sidebarActive('gallery', $activePage) ?>">
                    <i class="bi bi-grid-3x3-gap"></i> Galerie
                </a>
            </li>
            <li>
                <a href="<?= URL_MANAGE_PARTENAIRES ?>" class="<?= sidebarActive('partenaires', $activePage) ?>">
                    <i class="bi bi-handshake"></i> Partenaires
                </a>
            </li>

            <!-- CONFIGURATION -->
            <li class="nav-section-title">Configuration</li>
            <li>
                <a href="<?= URL_MANAGE_AXES ?>" class="<?= sidebarActive('axes', $activePage) ?>">
                    <i class="bi bi-diagram-3"></i> Axes
                </a>
            </li>
            <li>
                <a href="<?= URL_MANAGE_FUNFACTS ?>" class="<?= sidebarActive('funfacts', $activePage) ?>">
                    <i class="bi bi-bar-chart-line"></i> Fun Facts
                </a>
            </li>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <li>
                <a href="<?= URL_MANAGE_USERS ?>" class="<?= sidebarActive('users', $activePage) ?>">
                    <i class="bi bi-people"></i> Utilisateurs
                </a>
            </li>
            <li>
                <a href="<?= URL_MANAGE_SETTINGS ?>" class="<?= sidebarActive('settings', $activePage) ?>">
                    <i class="bi bi-gear"></i> Paramètres
                </a>
            </li>
            <?php endif; ?>

            <!-- SITE PUBLIC -->
            <li class="nav-section-title">Site public</li>
            <li>
                <a href="<?= URL_ACCUEIL ?>" target="_blank">
                    <i class="bi bi-globe2"></i> Voir le site
                </a>
            </li>
        </ul>
    </nav>

    <!-- Footer sidebar -->
    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>pagesweb/change_password.php" class="<?= sidebarActive('password', $activePage) ?>">
            <i class="bi bi-key"></i> Changer mot de passe
        </a>
        <a href="<?= URL_LOGOUT ?>" class="logout-btn">
            <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
    </div>

</aside>

<!-- ============================================================
     MAIN AREA
============================================================ -->
<div class="admin-main" id="adminMain">

    <!-- Topbar -->
    <header class="admin-topbar">
        <button id="sidebarToggle" title="Menu">
            <i class="bi bi-list"></i>
        </button>

        <!-- Breadcrumb -->
        <nav class="topbar-breadcrumb" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= URL_ADMIN_DASHBOARD ?>"><i class="bi bi-house-door"></i> Admin</a>
                </li>
                <?php foreach ($breadcrumb as $idx => $crumb): ?>
                    <?php if ($idx < count($breadcrumb) - 1): ?>
                        <li class="breadcrumb-item">
                            <a href="<?= htmlspecialchars($crumb['url'] ?? '#', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"><?= htmlspecialchars($crumb['label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></a>
                        </li>
                    <?php else: ?>
                        <li class="breadcrumb-item active"><?= htmlspecialchars($crumb['label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>

        <!-- User info -->
        <div class="topbar-user">
            <div class="user-avatar">
                <?= strtoupper(substr($_SESSION['user'] ?? 'A', 0, 1)) ?>
            </div>
            <div class="d-none d-sm-block">
                <div class="user-name"><?= htmlspecialchars($_SESSION['user'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
                <div class="user-role"><?= htmlspecialchars($_SESSION['role'] ?? 'user', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
            </div>
        </div>
    </header>

    <!-- Page content starts here -->
    <main class="admin-content">

<script>
(function() {
    // Sidebar toggle logic
    const toggle   = document.getElementById('sidebarToggle');
    const overlay  = document.getElementById('sidebarOverlay');
    const body     = document.body;
    const isMobile = () => window.innerWidth < 992;

    toggle.addEventListener('click', function() {
        if (isMobile()) {
            body.classList.toggle('sidebar-open-mobile');
        } else {
            body.classList.toggle('sidebar-collapsed');
        }
    });

    overlay.addEventListener('click', function() {
        body.classList.remove('sidebar-open-mobile');
    });

    // Restore desktop state from localStorage
    if (!isMobile() && localStorage.getItem('sidebarCollapsed') === 'true') {
        body.classList.add('sidebar-collapsed');
    }

    toggle.addEventListener('click', function() {
        if (!isMobile()) {
            localStorage.setItem('sidebarCollapsed', body.classList.contains('sidebar-collapsed') ? 'true' : 'false');
        }
    });

    // Close sidebar on resize to desktop
    window.addEventListener('resize', function() {
        if (!isMobile()) {
            body.classList.remove('sidebar-open-mobile');
        }
    });
})();
</script>
