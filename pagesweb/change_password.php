<?php
session_start();
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // provides $pdo

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . URL_AUTHENTIFICATION);
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current'] ?? '';
    $new = $_POST['new'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($new === '' || $confirm === '' || $current === '') {
        $msg = "Veuillez remplir tous les champs.";
    } elseif ($new !== $confirm) {
        $msg = "Le nouveau mot de passe et la confirmation ne correspondent pas.";
    } else {
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || !password_verify($current, $row['password'])) {
            $msg = "Mot de passe actuel incorrect.";
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $upd = $pdo->prepare('UPDATE users SET password = :p WHERE id = :id');
            $upd->execute([':p' => $hash, ':id' => $_SESSION['user_id']]);
            $msg = "Mot de passe mis à jour avec succès.";
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Changer mot de passe</title>
  <link rel="stylesheet" href="<?= CSS_DIR ?>bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
  <a href="<?= URL_ADMINISTRATEUR ?>" class="btn btn-secondary mb-3">← Retour au dashboard</a>
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Changer le mot de passe</h5>
          <?php if ($msg): ?><div class="alert alert-info"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
          <form method="post">
            <div class="mb-3">
              <label class="form-label">Mot de passe actuel</label>
              <input type="password" name="current" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Nouveau mot de passe</label>
              <input type="password" name="new" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Confirmer le nouveau mot de passe</label>
              <input type="password" name="confirm" class="form-control" required>
            </div>
            <button class="btn btn-primary">Mettre à jour</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="<?= JS_DIR ?>bootstrap.min.js"></script>
</body>
</html>
