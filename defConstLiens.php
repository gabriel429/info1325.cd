<?php

    $indexPath = __DIR__ . '/'; // chemin correct
        if (!file_exists($indexPath)) {
        // message d'erreur lisible pour debug
        die("Erreur : index introuvable : $indexPath");}

    $footerPath = __DIR__ . '/pagesweb/footerPage.php'; // chemin correct
        if (!file_exists($footerPath)) {
        // message d'erreur lisible pour debug
        die("Erreur : footer introuvable : $footerPath");}

    $headerPath = __DIR__ . '/pagesweb/headerPage.php'; // chemin correct
        if (!file_exists($headerPath)) {die("Erreur : header introuvable : $headerPath");}

    $contactPath = __DIR__ . '/pagesweb/contact.php'; // chemin correct
    if (!file_exists($contactPath)) {die("Erreur : contact introuvable : $contactPath");}

    $page404Path = __DIR__ . '/pagesweb/404.php'; // chemin correct
    if (!file_exists($page404Path)) {die("Erreur : 404 introuvable : $page404Path");}

    $composlidePath = __DIR__ . '/pagesweb/compoSlideArea.php'; // chemin correct
    if (!file_exists($composlidePath)) {die("Erreur : composlide introuvable : $composlidePath");}

    $actualitePath = __DIR__ . '/pagesweb/actualites.php'; // chemin correct
    if (!file_exists($actualitePath)) {die("Erreur : actualite introuvable : $actualitePath");}
    
    $documentationPath = __DIR__ . '/pagesweb/documentation.php'; // chemin correct
    if (!file_exists($documentationPath)) {die("Erreur : documentation introuvable : $documentationPath");}

    $resolutionPath = __DIR__ . '/pagesweb/resolution.php'; // chemin correct
    if (!file_exists($resolutionPath)) {die("Erreur : resolution introuvable : $resolutionPath");}

    $secretariatPath = __DIR__ . '/pagesweb/secretariat.php'; // chemin correct
    if (!file_exists($secretariatPath)) {die("Erreur : secretariat introuvable : $secretariatPath");}

    $compoAxePath = __DIR__ . '/pagesweb/compoAxe.php'; // chemin correct
    if (!file_exists($compoAxePath)) {die("Erreur : composante axe introuvable : $compoAxePath");}

    $compoActualitePath = __DIR__ . '/pagesweb/compoActualite.php'; // chemin correct
    if (!file_exists($compoActualitePath)) {die("Erreur : composante actualite introuvable : $compoActualitePath");}

    $compoGaleriPath = __DIR__ . '/pagesweb/compoGaleri.php'; // chemin correct
    if (!file_exists($compoGaleriPath)) {die("Erreur : composante galerie introuvable : $compoGaleriPath");}

    $compoPartenairePath = __DIR__ . '/pagesweb/compoPartenaires.php'; // partners band implementation
    if (!file_exists($compoPartenairePath)) {die("Erreur : composante partenaire introuvable : $compoPartenairePath");}

    $compoMinistrePath = __DIR__ . '/pagesweb/compoMinistre.php'; // chemin correct
    if (!file_exists($compoMinistrePath)) {die("Erreur : composante ministre introuvable : $compoMinistrePath");}

    $compoStatutPath = __DIR__ . '/pagesweb/compoStatut.php'; // chemin correct
    if (!file_exists($compoStatutPath)) {die("Erreur : composante statut introuvable : $compoStatutPath");}

    $compoSlideAreaPath = __DIR__ . '/pagesweb/compoSlideArea.php'; // chemin correct
    if (!file_exists($compoSlideAreaPath)) {die("Erreur : composante slide area introuvable : $compoSlideAreaPath");}
    $URL_GALERIEPath = __DIR__ . "/pagesweb/gallery.php";

    $dateDbConnect = __DIR__ . '/pagesweb/connectDb.php'; // chemin correct
    if (!file_exists($dateDbConnect)) {die("Erreur : composante date db connect introuvable : $dateDbConnect");}

    $dateDbUpdate = __DIR__ .  '/pagesweb/gallery.php'; // chemin correct
    if (!file_exists($dateDbUpdate)) {die("Erreur : composante date db update introuvable : $dateDbUpdate");}   

    $authentification = __DIR__ . '/pagesweb/authentification.php'; // chemin correct
    if (!file_exists($authentification)) {die("Erreur : composante authentification introuvable : $authentification");}