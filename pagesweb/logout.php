<?php
    session_start();

    require_once __DIR__ . '/../configUrl.php'; // __DIR__ = dossier racine
    require_once __DIR__ . '/../defConstLiens.php'; // __DIR__ = dossier racine
    session_destroy();
    header('Location:' . URL_AUTHENTIFICATION );
    exit;
?>
