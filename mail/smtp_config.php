<?php
// SMTP configuration for the site's mail server
// Fill these values with the SMTP server credentials provided by your host.
// Host/port may include a custom port (here: 2080).

if (!defined('SMTP_HOST')) define('SMTP_HOST', 'info1325.cd');
if (!defined('SMTP_USER')) define('SMTP_USER', 'contact@info1325.cd');
if (!defined('SMTP_PASS')) define('SMTP_PASS', 'Kre@teurs2513');
if (!defined('SMTP_PORT')) define('SMTP_PORT', 465);
// implicit SSL on port 465
if (!defined('SMTP_SECURE')) define('SMTP_SECURE', 'ssl');

// Optional: allow self-signed certs for local dev (not recommended in production)
// define('SMTP_OPTIONS', [
//     'ssl' => [
//         'verify_peer' => false,
//         'verify_peer_name' => false,
//         'allow_self_signed' => true
//     ]
// ]);

?>
