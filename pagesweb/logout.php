<?php
session_start();

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';

// Clear and destroy session
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}
session_destroy();

// Show a confirmation page then redirect to the login page
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Déconnexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta http-equiv="refresh" content="2;url=<?= URL_ACCUEIL ?>">
</head>
<body class="d-flex align-items-center" style="height:100vh;">
<div class="container text-center">
    <div class="alert alert-success">✅ Vous avez été déconnecté. Redirection…</div>
    <p class="text-muted">Si la redirection ne fonctionne pas, <a href="<?= URL_ACCUEIL ?>">cliquez ici</a>.</p>
</div>
<script>setTimeout(function(){ location.href = '<?= URL_ACCUEIL ?>'; }, 2000);</script>
</body>
</html>

<?php
exit;
?>
