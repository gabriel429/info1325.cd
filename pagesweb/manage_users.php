<?php
session_start();
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // provides $pdo

// Only admins may manage users
if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ' . URL_AUTHENTIFICATION);
    exit;
}

$msg = '';

// Add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_user') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    if ($email === '' || $password === '') {
        $msg = 'Email et mot de passe requis.';
    } else {
        // Check unique
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :e LIMIT 1');
        $stmt->execute([':e'=>$email]);
        if ($stmt->fetch()) {
            $msg = 'Un utilisateur avec cet email existe déjà.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO users (email,password,role) VALUES (:e,:p,:r)');
            $ins->execute([':e'=>$email,':p'=>$hash,':r'=>$role]);
            $msg = 'Utilisateur créé.';
        }
    }
}

// Delete user
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // prevent deleting yourself
    if ($id === (int)($_SESSION['user_id'] ?? 0)) {
        $msg = 'Vous ne pouvez pas supprimer votre propre compte.';
    } else {
        $del = $pdo->prepare('DELETE FROM users WHERE id = :id');
        $del->execute([':id'=>$id]);
        $msg = 'Utilisateur supprimé.';
    }
}

$users = $pdo->query('SELECT id,email,role,created_at FROM users ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Gérer les utilisateurs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <a href="<?= URL_ADMINISTRATEUR ?>" class="btn btn-secondary mb-3">← Retour au dashboard</a>
  <?php if ($msg): ?><div class="alert alert-info"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

  <div class="row">
    <div class="col-md-5">
      <div class="card mb-3">
        <div class="card-body">
          <h5>Ajouter un utilisateur</h5>
          <form method="post">
            <input type="hidden" name="action" value="add_user">
            <div class="mb-2">
              <label class="form-label">Email</label>
              <input name="email" type="email" class="form-control" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Mot de passe</label>
              <input name="password" type="password" class="form-control" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Rôle</label>
              <select name="role" class="form-select">
                <option value="user">user</option>
                <option value="slider">slider</option>
                <option value="admin">admin</option>
              </select>
            </div>
            <button class="btn btn-primary">Créer</button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-7">
      <div class="card">
        <div class="card-body">
          <h5>Utilisateurs</h5>
          <div class="table-responsive">
            <table class="table table-sm">
              <thead><tr><th>#</th><th>Email</th><th>Rôle</th><th>Créé</th><th></th></tr></thead>
              <tbody>
              <?php foreach ($users as $u): ?>
                <tr>
                  <td><?= $u['id'] ?></td>
                  <td><?= htmlspecialchars($u['email']) ?></td>
                  <td><?= htmlspecialchars($u['role']) ?></td>
                  <td><?= htmlspecialchars($u['created_at']) ?></td>
                  <td>
                    <?php if ($u['id'] != ($_SESSION['user_id'] ?? 0)): ?>
                      <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
                    <?php else: ?>
                      <span class="text-muted small">(vous)</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
