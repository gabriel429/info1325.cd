<?php
/**
 * Template de configuration sensible.
 * Copier ce fichier vers config/secrets.php et remplir les valeurs réelles.
 * NE PAS stocker config/secrets.php dans le dépôt Git.
 */

// ---------- SMTP ----------
define('SECRET_SMTP_HOST',   'votre-serveur-smtp');
define('SECRET_SMTP_USER',   'contact@votre-domaine.cd');
define('SECRET_SMTP_PASS',   'votre-mot-de-passe-smtp');
define('SECRET_SMTP_PORT',   465);
define('SECRET_SMTP_SECURE', 'ssl'); // 'ssl' ou 'tls'

// ---------- Base de données (production) ----------
define('SECRET_DB_PROD_HOST',    'localhost');
define('SECRET_DB_PROD_NAME',    'nom_base_prod');
define('SECRET_DB_PROD_USER',    'utilisateur_prod');
define('SECRET_DB_PROD_PASS',    'mot_de_passe_prod');

// ---------- Base de données (local) ----------
define('SECRET_DB_LOCAL_HOST',   'localhost');
define('SECRET_DB_LOCAL_NAME',   'nom_base_local');
define('SECRET_DB_LOCAL_USER',   'root');
define('SECRET_DB_LOCAL_PASS',   '');

// ---------- Environnement ----------
// Liste des SERVER_NAME considérés comme locaux (développement)
define('SECRET_LOCAL_SERVER_NAMES', ['localhost', '127.0.0.1']);
// IP de production (pour identification en local)
define('SECRET_PROD_IP', '0.0.0.0');
