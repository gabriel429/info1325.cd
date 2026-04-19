<?php
// Chargement des credentials/config depuis le fichier sécurisé
$_cfgSecretsFile = __DIR__ . '/config/secrets.php';
if (file_exists($_cfgSecretsFile)) {
    require_once $_cfgSecretsFile;
}

// Racine du projet (en local '/info1325.cd/', en prod '/')
// Détecte si nous sommes en local (localhost ou IP locale définie dans secrets)
$_localNames = defined('SECRET_LOCAL_SERVER_NAMES') ? SECRET_LOCAL_SERVER_NAMES : ['localhost', '127.0.0.1'];
$isLocal = in_array($_SERVER['SERVER_NAME'] ?? 'localhost', $_localNames);

// En local le site est servi depuis le dossier /info1325.cd/
// En production, depuis la racine '/'
$projectRoot = $isLocal ? '/info1325.cd/' : '/';

if (!defined('PROJECT_ROOT_URL')) {
    define('PROJECT_ROOT_URL', $projectRoot);
}

// URL de base
define('BASE_URL', rtrim(PROJECT_ROOT_URL, '/') . '/');

// Dossiers publics (assets)
define('CSS_DIR', BASE_URL . 'css/');
define('JS_DIR', BASE_URL . 'js/');
define('IMG_DIR', BASE_URL . 'img/');
define('IMG_ACTUALITES_DIR', IMG_DIR . 'actualites/');
define('URL_IMG_ACTU', IMG_ACTUALITES_DIR); // Alias pour compatibilité
define('FONTS_DIR', BASE_URL . 'fonts/');
// Debug flags
if (!defined('DEBUG_ASSETS')) {
    define('DEBUG_ASSETS', false);
}

// Dossiers côté serveur
define('ROOT_DIR', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . PROJECT_ROOT_URL);
define('PAGES_DIR', ROOT_DIR . 'pagesweb/');

// Helper pour générer une URL
function url(string $path): string {
    return BASE_URL . ltrim($path, '/');
}

// Helper pour versionner les assets statiques et éviter le cache navigateur.
function asset_url(string $path): string {
    if ($path === '' || preg_match('#^(https?:)?//#i', $path)) {
        return $path;
    }

    $parts = parse_url($path);
    if ($parts === false || empty($parts['path'])) {
        return $path;
    }

    $assetPath = $parts['path'];
    $projectPrefix = trim(PROJECT_ROOT_URL, '/');
    $relativePath = ltrim($assetPath, '/');

    if ($projectPrefix !== '' && strpos($relativePath, $projectPrefix . '/') === 0) {
        $relativePath = substr($relativePath, strlen($projectPrefix) + 1);
    }

    $absolutePath = ROOT_DIR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
    if (!is_file($absolutePath)) {
        return $path;
    }

    $separator = strpos($path, '?') === false ? '?' : '&';
    return $path . $separator . 'v=' . filemtime($absolutePath);
}

// URLs des pages
define('URL_404', BASE_URL . 'pagesweb/404/');
define('URL_ACCUEIL', BASE_URL);
define('URL_ACTUALITES', BASE_URL . 'pagesweb/actualites/');
define('URL_DOCUMENTATION', BASE_URL . 'pagesweb/documentation/');
define('URL_RESOLUTION1325', BASE_URL . 'pagesweb/resolution/');
define('URL_SECRETAIRIATNATIONAL', BASE_URL . 'pagesweb/secretariat/');
define('URL_CONTACT', BASE_URL . 'pagesweb/contact/');
define('URL_MINISTRE', BASE_URL . 'pagesweb/compoMinistre/');
define('URL_PARTENAIRE', BASE_URL . 'pagesweb/compoPartenaires/');
define('URL_COMPOAXE', BASE_URL . 'pagesweb/compoAxe/');
define('URL_COMPOSLIDE', BASE_URL . 'pagesweb/compoSlideArea/');
define('URL_FOOTERPAGE', BASE_URL . 'pagesweb/footerPage/');
define('URL_HEADERPAGE', BASE_URL . 'pagesweb/headerPage/');
define('URL_GALERI', BASE_URL . 'pagesweb/compoGaleri/');
define('URL_MANAGE_GALERIE', BASE_URL . 'pagesweb/manage_gallery.php');
define('URL_STATUT', BASE_URL . 'pagesweb/compoStatut/');
define('URL_AUTHENTIFICATION', BASE_URL . 'pagesweb/authentification/');
define('URL_ADDACTUALITES', BASE_URL . 'pagesweb/add-actualites/');
define('URL_GALERIE', BASE_URL . 'pagesweb/gallery.php');
// Point to the actual admin management page for editing news
define('URL_ADMINISTRATEUR', BASE_URL . 'pagesweb/administrateur.php');
define('URL_ADDDOCUMENTATIONS', BASE_URL . 'pagesweb/add-documentation/');
define('URL_ADDSPACEADMIN', BASE_URL . 'pagesweb/add-space/');
define('URL_MANAGE_FUNFACTS', BASE_URL . 'pagesweb/manage_funfacts/');
define('URL_MANAGE_AXES', BASE_URL . 'pagesweb/manage_axes/');
define('URL_MANAGE_SLIDER', BASE_URL . 'pagesweb/manage_slider/');
define('URL_MANAGE_PARTENAIRES', BASE_URL . 'pagesweb/manage_partenaires/');
define('URL_MANAGE_USERS', BASE_URL . 'pagesweb/manage_users.php');
define('URL_MANAGE_SETTINGS', BASE_URL . 'pagesweb/manage_settings.php');
define('URL_ALLDOCUMENTATIONS', BASE_URL . 'pagesweb/all-documentations/');
define('URL_SUCCESSADDDOCUMENTATION', BASE_URL . 'pagesweb/success-add-documentation/');

define('URL_LOGOUT', BASE_URL . 'pagesweb/logout.php');
define('URL_ADMIN_DASHBOARD', BASE_URL . 'pagesweb/admin_dashboard.php');
