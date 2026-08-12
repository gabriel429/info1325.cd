<?php
session_start();
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // provides $pdo
require_once __DIR__ . '/csrf_helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . URL_AUTHENTIFICATION);
    exit;
}

$msg = '';
$msgType = 'info';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $current = $_POST['current'] ?? '';
    $new = $_POST['new'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($new === '' || $confirm === '' || $current === '') {
        $msg = "Veuillez remplir tous les champs.";
        $msgType = 'warning';
    } elseif ($new !== $confirm) {
        $msg = "Le nouveau mot de passe et la confirmation ne correspondent pas.";
        $msgType = 'danger';
    } else {
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || !password_verify($current, $row['password'])) {
            $msg = "Mot de passe actuel incorrect.";
            $msgType = 'danger';
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $upd = $pdo->prepare('UPDATE users SET password = :p WHERE id = :id');
            $upd->execute([':p' => $hash, ':id' => $_SESSION['user_id']]);
            $msg = "Mot de passe mis à jour avec succès.";
            $msgType = 'success';
        }
    }
}

$pageTitle  = 'Changer le mot de passe';
$breadcrumb = [['label' => 'Mot de passe']];
$activePage = 'password';

require_once __DIR__ . '/admin_layout_top.php';
?>

<div class="page-header">
    <div>
        <h1><i class="bi bi-key me-2" style="color:var(--accent)"></i>Changer le mot de passe</h1>
        <p>Modifiez votre mot de passe de connexion</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="admin-card">
            <div class="card-header">
                <i class="bi bi-shield-lock me-2" style="color:var(--accent)"></i>Nouveau mot de passe
            </div>
            <div class="card-body">
                <?php if ($msg): ?>
                    <div class="alert alert-<?= $msgType ?> alert-dismissible fade show">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <form method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Mot de passe actuel</label>
                        <input type="password" name="current" class="form-control" required
                               placeholder="Saisir votre mot de passe actuel">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nouveau mot de passe</label>
                        <input type="password" name="new" class="form-control" required
                               placeholder="Saisir le nouveau mot de passe">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Confirmer le nouveau mot de passe</label>
                        <input type="password" name="confirm" class="form-control" required
                               placeholder="Confirmer le nouveau mot de passe">
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-admin-primary">
                            <i class="bi bi-floppy me-1"></i>Mettre à jour le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/admin_layout_bottom.php'; ?>
