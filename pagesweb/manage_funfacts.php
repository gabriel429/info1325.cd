<?php
session_start();
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // provides $pdo

if (!isset($_SESSION['user'])) {
    header('Location:' . URL_AUTHENTIFICATION);
    exit;
}

$message = '';

// Create table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS fun_facts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `position` INT NOT NULL UNIQUE,
    `value` VARCHAR(255) NOT NULL,
    `text` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    try {
        $pdo->beginTransaction();
        // For positions 1..4
        for ($i = 1; $i <= 4; $i++) {
            $val = trim($_POST['value'][$i] ?? '');
            $txt = trim($_POST['text'][$i] ?? '');
            if ($val === '' && $txt === '') {
                // delete if exists
                $stmt = $pdo->prepare('DELETE FROM fun_facts WHERE `position` = :pos');
                $stmt->execute([':pos'=>$i]);
                continue;
            }
            // upsert
            $stmt = $pdo->prepare('SELECT id FROM fun_facts WHERE `position` = :pos');
            $stmt->execute([':pos'=>$i]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $stmt = $pdo->prepare('UPDATE fun_facts SET `value` = :val, `text` = :txt WHERE `position` = :pos');
                $stmt->execute([':val'=>$val,':txt'=>$txt,':pos'=>$i]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO fun_facts (`position`,`value`,`text`) VALUES (:pos,:val,:txt)');
                $stmt->execute([':pos'=>$i,':val'=>$val,':txt'=>$txt]);
            }
        }
        $pdo->commit();
        $message = "<div class='alert alert-success'>Modifications enregistrées.</div>";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "<div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Load existing
$stmt = $pdo->query('SELECT * FROM fun_facts ORDER BY `position` ASC');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$facts = [];
foreach ($rows as $r) $facts[(int)$r['position']] = $r;

?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Gérer Fun Facts</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark px-3">
  <span class="navbar-brand">Admin – Fun Facts</span>
  <div class="ms-auto">
    <a href="<?= URL_ADMINISTRATEUR ?>" class="btn btn-outline-light">Retour</a>
  </div>
</nav>
<div class="container py-4">
    <?= $message ?>
    <form method="post">
        <input type="hidden" name="save" value="1">
        <div class="row">
<?php for ($i=1;$i<=4;$i++):
    $v = htmlspecialchars($facts[$i]['value'] ?? '');
    $t = htmlspecialchars($facts[$i]['text'] ?? '');
?>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">Bloc #<?= $i ?></div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label">Valeur (counter)</label>
                            <input type="text" name="value[<?= $i ?>]" class="form-control" value="<?= $v ?>">
                        </div>
                        <div>
                            <label class="form-label">Texte (paragraphe)</label>
                            <input type="text" name="text[<?= $i ?>]" class="form-control" value="<?= $t ?>">
                        </div>
                    </div>
                </div>
            </div>
<?php endfor; ?>
        </div>
        <button class="btn btn-primary">Enregistrer</button>
    </form>
</div>
</body>
</html>
