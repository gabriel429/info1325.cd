<?php
session_start();

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';

if (!isset($_SESSION['user'])) {
    header('Location: ' . URL_AUTHENTIFICATION);
    exit;
}

header('Location: ' . URL_ADDACTUALITES, true, 302);
exit;
