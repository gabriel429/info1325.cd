<?php
require_once __DIR__ . '/configUrl.php'; // __DIR__ = dossier racine
require_once __DIR__ . '/defConstLiens.php'; // __DIR__ = dossier racine

// require_once $dataDbConnect;
require_once $dateDbConnect; // Connexion à la base de données

$pageCss = CSS_DIR . 'home.css';
$isHomePage = true;

// Track visitor
require_once __DIR__ . '/pagesweb/track_visitor.php';
require_once __DIR__ . '/pagesweb/csrf_helper.php';
?>

<?php require_once $headerPath;  ?> 

<!-- Composant contact page cn fin  -->

<!-- Composant slider  page cn debut -->

    <?php require_once $composlidePath;  ?>

<!-- Composant slider page cn fin  -->

<section class="homepage-intro section">
    <div class="container">
        <div class="homepage-intro-grid">
            <article class="intro-card highlight-card">
                <span class="intro-label">Coordination nationale</span>
                <h2>Un dispositif au service de l’agenda Femmes, Paix et Sécurité</h2>
                <p>Le portail SN1325 centralise les informations stratégiques, les ressources documentaires, les actualités et les outils de suivi liés à la mise en œuvre de la Résolution 1325 en RDC.</p>
            </article>
            <article class="intro-card stat-card">
                <span class="intro-label">Accès rapide</span>
                <strong>Documentation</strong>
                <p>Consultez les plans d’action nationaux, rapports pays, textes de référence et publications utiles à l’action institutionnelle.</p>
                <a href="<?= URL_DOCUMENTATION ?>">Ouvrir la bibliothèque</a>
            </article>
            <article class="intro-card stat-card">
                <span class="intro-label">Suivi des actions</span>
                <strong>Actualités</strong>
                <p>Retrouvez les initiatives récentes, ateliers, plaidoyers et temps forts portés par le Secrétariat National 1325.</p>
                <a href="<?= URL_ACTUALITES ?>">Voir les actualités</a>
            </article>
        </div>
    </div>
</section>



<!-- Composant slider  page cn debut -->

    <?php require_once $compoActualitePath;  ?>

<!-- Composant slider page cn fin  -->

 

<!-- Composant slider  page cn debut -->

    <?php require_once $compoStatutPath;  ?>

<!-- Composant slider page cn fin  -->



<!-- Composant slider  page cn debut -->

    <?php require_once $compoAxePath;  ?>

<!-- Composant slider page cn fin  -->



<!-- Composant slider  page cn debut -->

    <?php require_once $compoPartenairePath;  ?>

<!-- Composant slider page cn fin  -->



<!-- Composant slider  page cn debut -->

    <?php require_once $compoMinistrePath;  ?>

<!-- Composant slider page cn fin  -->



<!-- Composant slider  page cn debut -->

    <?php require_once $compoGaleriPath;  ?>

<!-- Composant slider page cn fin  -->



<!-- Composant footer  page cn debut -->

    <?php require_once $footerPath;  ?>

<!-- Composant footer page cn fin  -->

