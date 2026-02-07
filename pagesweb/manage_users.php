<?php
session_start();
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // provides $pdo
require_once __DIR__ . '/csrf.php'; // CSRF protection

// Only admins may manage users
if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ' . URL_AUTHENTIFICATION);
    exit;
}

// Validate CSRF token for all POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate(true);
}

// Add 'active' column if it doesn't exist
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN active TINYINT(1) NOT NULL DEFAULT 1 AFTER role");
} catch (PDOException $e) {
    // Column already exists, ignore
}

$msg = '';
$msgType = 'info';

// Add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_user') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $active = isset($_POST['active']) ? 1 : 0;

    if ($email === '' || $password === '') {
        $msg = 'Email et mot de passe requis.';
        $msgType = 'danger';
    } else {
        // Check unique
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :e LIMIT 1');
        $stmt->execute([':e'=>$email]);
        if ($stmt->fetch()) {
            $msg = 'Un utilisateur avec cet email existe déjà.';
            $msgType = 'warning';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO users (email,password,role,active) VALUES (:e,:p,:r,:a)');
            $ins->execute([':e'=>$email,':p'=>$hash,':r'=>$role,':a'=>$active]);
            $msg = 'Utilisateur créé avec succès.';
            $msgType = 'success';
        }
    }
}

// Edit user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit_user') {
    $id = (int)($_POST['user_id'] ?? 0);
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $active = isset($_POST['active']) ? 1 : 0;

    if ($email === '') {
        $msg = 'Email requis.';
        $msgType = 'danger';
    } else {
        // Check if email exists for another user
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :e AND id != :id LIMIT 1');
        $stmt->execute([':e'=>$email, ':id'=>$id]);
        if ($stmt->fetch()) {
            $msg = 'Un autre utilisateur avec cet email existe déjà.';
            $msgType = 'warning';
        } else {
            $upd = $pdo->prepare('UPDATE users SET email = :e, role = :r, active = :a WHERE id = :id');
            $upd->execute([':e'=>$email, ':r'=>$role, ':a'=>$active, ':id'=>$id]);
            $msg = 'Utilisateur mis à jour avec succès.';
            $msgType = 'success';
        }
    }
}

// Reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reset_password') {
    $id = (int)($_POST['user_id'] ?? 0);
    $newPassword = $_POST['new_password'] ?? '';

    if ($id === 0 || $newPassword === '') {
        $msg = 'ID utilisateur et nouveau mot de passe requis.';
        $msgType = 'danger';
    } else {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $upd = $pdo->prepare('UPDATE users SET password = :p WHERE id = :id');
        $upd->execute([':p'=>$hash, ':id'=>$id]);
        $msg = 'Mot de passe réinitialisé avec succès.';
        $msgType = 'success';
    }
}

// Toggle active status
if (isset($_GET['toggle_active'])) {
    $id = (int)$_GET['toggle_active'];
    if ($id === (int)($_SESSION['user_id'] ?? 0)) {
        $msg = 'Vous ne pouvez pas désactiver votre propre compte.';
        $msgType = 'warning';
    } else {
        $stmt = $pdo->prepare('SELECT active FROM users WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $newStatus = $user['active'] ? 0 : 1;
            $upd = $pdo->prepare('UPDATE users SET active = :a WHERE id = :id');
            $upd->execute([':a'=>$newStatus, ':id'=>$id]);
            $msg = $newStatus ? 'Utilisateur activé.' : 'Utilisateur désactivé.';
            $msgType = 'success';
        }
    }
}

// Delete user
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // prevent deleting yourself
    if ($id === (int)($_SESSION['user_id'] ?? 0)) {
        $msg = 'Vous ne pouvez pas supprimer votre propre compte.';
        $msgType = 'warning';
    } else {
        $del = $pdo->prepare('DELETE FROM users WHERE id = :id');
        $del->execute([':id'=>$id]);
        $msg = 'Utilisateur supprimé.';
        $msgType = 'success';
    }
}

// Get user for editing if requested
$editUser = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id'=>$editId]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
}

$users = $pdo->query('SELECT id,email,role,active,created_at FROM users ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Gérer les utilisateurs - SN1325</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people-fill"></i> Gestion des utilisateurs</h2>
    <a href="<?= URL_ADMINISTRATEUR ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour au dashboard</a>
  </div>

  <?php if ($msg): ?>
    <div class="alert alert-<?= $msgType ?> alert-dismissible fade show">
      <?= htmlspecialchars($msg) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="row">
    <!-- Add/Edit User Form -->
    <div class="col-md-4">
      <div class="card mb-3 shadow-sm">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">
            <i class="bi bi-<?= $editUser ? 'pencil-square' : 'person-plus' ?>"></i>
            <?= $editUser ? 'Modifier l\'utilisateur' : 'Ajouter un utilisateur' ?>
          </h5>
        </div>
        <div class="card-body">
          <form method="post">
            <?php csrf_field(); ?>
            <input type="hidden" name="action" value="<?= $editUser ? 'edit_user' : 'add_user' ?>">
            <?php if ($editUser): ?>
              <input type="hidden" name="user_id" value="<?= $editUser['id'] ?>">
            <?php endif; ?>

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input name="email" type="email" class="form-control"
                     value="<?= $editUser ? htmlspecialchars($editUser['email']) : '' ?>" required>
            </div>

            <?php if (!$editUser): ?>
              <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input name="password" type="password" class="form-control" required minlength="6">
                <small class="text-muted">Minimum 6 caractères</small>
              </div>
            <?php endif; ?>

            <div class="mb-3">
              <label class="form-label">Rôle</label>
              <select name="role" class="form-select" required>
                <option value="user" <?= $editUser && $editUser['role'] === 'user' ? 'selected' : '' ?>>Utilisateur</option>
                <option value="slider" <?= $editUser && $editUser['role'] === 'slider' ? 'selected' : '' ?>>Slider</option>
                <option value="admin" <?= $editUser && $editUser['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
              </select>
            </div>

            <div class="mb-3 form-check">
              <input type="checkbox" name="active" class="form-check-input" id="activeCheck"
                     <?= !$editUser || ($editUser && $editUser['active']) ? 'checked' : '' ?>>
              <label class="form-check-label" for="activeCheck">
                Compte actif
              </label>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-<?= $editUser ? 'check-lg' : 'plus-lg' ?>"></i>
                <?= $editUser ? 'Mettre à jour' : 'Créer' ?>
              </button>
              <?php if ($editUser): ?>
                <a href="manage_users.php" class="btn btn-secondary">Annuler</a>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Users List -->
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="bi bi-list-ul"></i> Liste des utilisateurs (<?= count($users) ?>)</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Email</th>
                  <th>Rôle</th>
                  <th>Statut</th>
                  <th>Créé le</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($users as $u): ?>
                <tr class="<?= !$u['active'] ? 'table-secondary' : '' ?>">
                  <td class="align-middle"><?= $u['id'] ?></td>
                  <td class="align-middle">
                    <i class="bi bi-envelope-fill text-muted"></i>
                    <?= htmlspecialchars($u['email']) ?>
                    <?php if ($u['id'] == ($_SESSION['user_id'] ?? 0)): ?>
                      <span class="badge bg-info ms-1">Vous</span>
                    <?php endif; ?>
                  </td>
                  <td class="align-middle">
                    <?php
                    $roleColors = ['admin' => 'danger', 'slider' => 'warning', 'user' => 'secondary'];
                    $roleLabels = ['admin' => 'Admin', 'slider' => 'Slider', 'user' => 'User'];
                    $color = $roleColors[$u['role']] ?? 'secondary';
                    $label = $roleLabels[$u['role']] ?? $u['role'];
                    ?>
                    <span class="badge bg-<?= $color ?>"><?= $label ?></span>
                  </td>
                  <td class="align-middle">
                    <?php if ($u['active']): ?>
                      <span class="badge bg-success"><i class="bi bi-check-circle"></i> Actif</span>
                    <?php else: ?>
                      <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Inactif</span>
                    <?php endif; ?>
                  </td>
                  <td class="align-middle">
                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></small>
                  </td>
                  <td class="align-middle text-end">
                    <div class="btn-group btn-group-sm">
                      <?php if ($u['id'] != ($_SESSION['user_id'] ?? 0)): ?>
                        <!-- Edit Button -->
                        <a href="?edit=<?= $u['id'] ?>" class="btn btn-outline-primary" title="Modifier">
                          <i class="bi bi-pencil"></i>
                        </a>

                        <!-- Reset Password Button -->
                        <button type="button" class="btn btn-outline-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#resetPasswordModal<?= $u['id'] ?>"
                                title="Réinitialiser mot de passe">
                          <i class="bi bi-key"></i>
                        </button>

                        <!-- Toggle Active Button -->
                        <a href="?toggle_active=<?= $u['id'] ?>"
                           class="btn btn-outline-<?= $u['active'] ? 'secondary' : 'success' ?>"
                           title="<?= $u['active'] ? 'Désactiver' : 'Activer' ?>"
                           onclick="return confirm('<?= $u['active'] ? 'Désactiver' : 'Activer' ?> cet utilisateur ?');">
                          <i class="bi bi-<?= $u['active'] ? 'toggle-on' : 'toggle-off' ?>"></i>
                        </a>

                        <!-- Delete Button -->
                        <a href="?delete=<?= $u['id'] ?>"
                           class="btn btn-outline-danger"
                           title="Supprimer"
                           onclick="return confirm('⚠️ Supprimer définitivement cet utilisateur ?\nCette action est irréversible.');">
                          <i class="bi bi-trash"></i>
                        </a>
                      <?php else: ?>
                        <span class="badge bg-light text-dark">Votre compte</span>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>

                <!-- Reset Password Modal -->
                <div class="modal fade" id="resetPasswordModal<?= $u['id'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">
                          <i class="bi bi-key-fill"></i> Réinitialiser le mot de passe
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <form method="post">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="action" value="reset_password">
                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                        <div class="modal-body">
                          <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Vous allez réinitialiser le mot de passe de <strong><?= htmlspecialchars($u['email']) ?></strong>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Nouveau mot de passe</label>
                            <input type="password" name="new_password" class="form-control"
                                   required minlength="6" autocomplete="new-password">
                            <small class="text-muted">Minimum 6 caractères</small>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                          <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg"></i> Réinitialiser
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
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
