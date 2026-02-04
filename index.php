<?php
    require_once __DIR__ . '/configUrl.php'; // __DIR__ = dossier racine
    require_once __DIR__ . '/defConstLiens.php'; // __DIR__ = dossier racine

  //require_once $dataDbConnect; 
  require_once $dateDbConnect; // Connexion à la base de données
 
?>

<!-- Composant header  page cn debut -->
    <?php require_once $headerPath;  ?>
<!-- Composant header page cn fin  -->

<!-- Composant contact  page cn debut --> 
 <?php // require_once $contactPath;  ?> 
<!-- Composant contact page cn fin  -->
<!-- Composant slider  page cn debut -->
    <?php require_once $composlidePath;  ?>
<!-- Composant slider page cn fin  -->

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
