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
        <div class="homepage-institutional">
            <div class="institutional-copy">
                <span class="intro-label">Secrétariat National Permanent 1325</span>
                <h2>Coordonner l’action nationale autour de la Résolution 1325 en RDC</h2>
                <p>Un cadre officiel de concertation, de suivi et de capitalisation pour renforcer la participation des femmes à la paix, à la sécurité et à la prévention des conflits.</p>
            </div>

            <div class="institutional-focus" aria-label="Domaines d'action">
                <span>Coordination</span>
                <span>Suivi national</span>
                <span>Capitalisation</span>
            </div>

            <div class="institutional-actions">
                <a href="<?= URL_DOCUMENTATION ?>">Documentation</a>
                <a href="<?= URL_ACTUALITES ?>">Actualités</a>
            </div>
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

