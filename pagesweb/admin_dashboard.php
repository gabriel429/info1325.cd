<?php
session_start();
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // $pdo

// Require login
if (!isset($_SESSION['user'])) {
    header('Location: ' . URL_AUTHENTIFICATION);
    exit;
}

$role = $_SESSION['role'] ?? 'user';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard administrateur - SN1325</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <span class="navbar-brand">SN1325 — Tableau de bord</span>
    <div>
        <a class="btn btn-outline-light me-2" href="<?= URL_ADDSPACEADMIN ?>">Menu Admin</a>
        <a class="btn btn-danger" href="<?= URL_LOGOUT ?>">Déconnexion</a>
    </div>
  </div>
</nav>
<div class="container py-4">
    <h3>Bienvenue, <?= htmlspecialchars($_SESSION['user']) ?></h3>
    <p>Rôle: <strong><?= htmlspecialchars($role) ?></strong></p>

    <div class="row g-3">
        <div class="col-md-4"><a class="card p-3 h-100 text-decoration-none" href="<?= URL_ADDACTUALITES ?>">📋 Ajouter une actualité</a></div>
        <div class="col-md-4"><a class="card p-3 h-100 text-decoration-none" href="<?= URL_MANAGE_FUNFACTS ?>">⚙️ Gérer Fun Facts</a></div>
        <div class="col-md-4"><a class="card p-3 h-100 text-decoration-none" href="<?= URL_MANAGE_AXES ?>">🧭 Gérer Axes</a></div>
        <div class="col-md-4"><a class="card p-3 h-100 text-decoration-none" href="<?= URL_MANAGE_SLIDER ?>">🎞️ Gérer Slider</a></div>
        <div class="col-md-4"><a class="card p-3 h-100 text-decoration-none" href="<?= BASE_URL ?>pagesweb/manage_users.php">👥 Gérer utilisateurs</a></div>
        <div class="col-md-4"><a class="card p-3 h-100 text-decoration-none" href="<?= URL_ALLDOCUMENTATIONS ?>">📚 Toutes les documentations</a></div>
        <div class="col-md-4"><a class="card p-3 h-100 text-decoration-none" href="<?= URL_ADDDOCUMENTATIONS ?>">➕ Ajouter documentation</a></div>
    </div>

    <div class="mt-4">
        <a class="btn btn-outline-primary" href="<?= BASE_URL ?>pagesweb/change_password.php">Changer mon mot de passe</a>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
