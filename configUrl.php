<?php

// Racine du projet (en local '/Akili/', en prod '/')

$isLocal = in_array($_SERVER['SERVER_NAME'], ['localhost', '109.234.160.5']);

if (!defined('PROJECT_ROOT_URL')) {
    // Servir le site depuis la racine '/' (production et local)
    define('PROJECT_ROOT_URL', '/');
}

// URL de base
define('BASE_URL', rtrim(PROJECT_ROOT_URL, '/') . '/');

// Dossiers publics (assets)
define('CSS_DIR', BASE_URL . 'css/');
define('JS_DIR', BASE_URL . 'js/');
define('IMG_DIR', BASE_URL . 'img/');
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

// Exemple de page
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
define('URL_STATUT', BASE_URL . 'pagesweb/compoStatut/');
define('URL_AUTHENTIFICATION', BASE_URL . 'pagesweb/authentification/');
define('URL_ADDACTUALITES', BASE_URL . 'pagesweb/add-actualites/');
define('URL_ADMINISTRATEUR', BASE_URL . 'pagesweb/admin_dashboard/');
define('URL_ADDDOCUMENTATIONS', BASE_URL . 'pagesweb/add-documentation/');
define('URL_ADDSPACEADMIN', BASE_URL . 'pagesweb/add-space/');
define('URL_MANAGE_FUNFACTS', BASE_URL . 'pagesweb/manage_funfacts/');
define('URL_MANAGE_AXES', BASE_URL . 'pagesweb/manage_axes/');
define('URL_MANAGE_SLIDER', BASE_URL . 'pagesweb/manage_slider/');
define('URL_ALLDOCUMENTATIONS', BASE_URL . 'pagesweb/all-documentations/');
define('URL_SUCCESSADDDOCUMENTATION', BASE_URL . 'pagesweb/success-add-documentation/');

define('URL_LOGOUT', BASE_URL . 'pagesweb/logout/');

