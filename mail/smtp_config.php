<?php
// SMTP configuration — credentials chargés depuis config/secrets.php
$_smtpSecretsFile = __DIR__ . '/../config/secrets.php';
if (!file_exists($_smtpSecretsFile)) {
    error_log('ERREUR CRITIQUE: config/secrets.php introuvable pour SMTP.');
    die('Erreur de configuration serveur.');
}
require_once $_smtpSecretsFile;

if (!defined('SMTP_HOST'))   define('SMTP_HOST',   SECRET_SMTP_HOST);
if (!defined('SMTP_USER'))   define('SMTP_USER',   SECRET_SMTP_USER);
if (!defined('SMTP_PASS'))   define('SMTP_PASS',   SECRET_SMTP_PASS);
if (!defined('SMTP_PORT'))   define('SMTP_PORT',   SECRET_SMTP_PORT);
if (!defined('SMTP_SECURE')) define('SMTP_SECURE', SECRET_SMTP_SECURE);
