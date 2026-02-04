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
$pdo->exec("CREATE TABLE IF NOT EXISTS axes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `position` INT NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    // Validation serveur : limites de longueur
    $titleMax = 80;
    $descMax = 300;
    $bad = false;
    for ($i = 1; $i <= 6; $i++) {
        $ti = $_POST['title'][$i] ?? '';
        $di = $_POST['description'][$i] ?? '';
        if (mb_strlen($ti) > $titleMax) { $message = "<div class='alert alert-danger'>Le titre #$i dépasse $titleMax caractères.</div>"; $bad = true; break; }
        if (mb_strlen($di) > $descMax) { $message = "<div class='alert alert-danger'>La description #$i dépasse $descMax caractères.</div>"; $bad = true; break; }
    }
    if (!$bad) {
    try {
        $pdo->beginTransaction();
        // allow up to 6 axes
        for ($i = 1; $i <= 6; $i++) {
            $title = trim($_POST['title'][$i] ?? '');
            $desc = trim($_POST['description'][$i] ?? '');
            if ($title === '' && $desc === '') {
                $stmt = $pdo->prepare('DELETE FROM axes WHERE `position` = :pos');
                $stmt->execute([':pos'=>$i]);
                continue;
            }
            $stmt = $pdo->prepare('SELECT id FROM axes WHERE `position` = :pos');
            $stmt->execute([':pos'=>$i]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $stmt = $pdo->prepare('UPDATE axes SET title = :title, description = :desc WHERE `position` = :pos');
                $stmt->execute([':title'=>$title, ':desc'=>$desc, ':pos'=>$i]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO axes (`position`, title, description) VALUES (:pos, :title, :desc)');
                $stmt->execute([':pos'=>$i, ':title'=>$title, ':desc'=>$desc]);
            }
        }
        $pdo->commit();
        $message = "<div class='alert alert-success'>Axes sauvegardés.</div>";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "<div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    } // end !bad
}

// Load existing
$stmt = $pdo->query('SELECT * FROM axes ORDER BY `position` ASC');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$axes = [];
foreach ($rows as $r) $axes[(int)$r['position']] = $r;

// Defaults
$defaults = [
    1 => ['title'=>'PREVENTION','description'=>'Intégrer la perspective de genre dans la prévention des conflits et éliminer les violences basées sur le genre'],
    2 => ['title'=>'PARTICIPATION','description'=>'Garantir la participation pleine et effective des femmes à tous les niveaux de prise de décision'],
    3 => ['title'=>'PROTECTION','description'=>'Protéger les femmes et les filles contre toutes les formes de violences dans les conflits'],
    4 => ['title'=>'SECOURS ET RELEVEMENT','description'=>'Assurer l\'accès aux services de base et soutenir le relèvement économique des femmes affectées par les conflits'],
    5 => ['title'=>'GESTION DES CONFLITS','description'=>'Gestion des conflits émergents et aide humanitaire'],
    6 => ['title'=>'CADRE STRATÉGIQUE','description'=>'Cadre stratégique pour traduire les engagements internationaux en actions concrètes']
];
for ($i=1;$i<=6;$i++) {
    if (!isset($axes[$i])) $axes[$i] = $defaults[$i];
}

?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Gérer les axes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark px-3">
  <span class="navbar-brand">Admin – Axes</span>
  <div class="ms-auto">
    <a href="<?= URL_ADMINISTRATEUR ?>" class="btn btn-outline-light">Retour</a>
  </div>
</nav>
<div class="container py-4">
    <?= $message ?>
    <form method="post">
        <input type="hidden" name="save" value="1">
        <div class="row">
<?php for ($i=1;$i<=6;$i++):
    $t = htmlspecialchars($axes[$i]['title'] ?? '');
    $d = htmlspecialchars($axes[$i]['description'] ?? '');
?>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">Axe #<?= $i ?></div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label">Titre (h4)</label>
                            <input type="text" name="title[<?= $i ?>]" class="form-control axis-title" value="<?= $t ?>" required maxlength="80" data-index="<?= $i ?>">
                        </div>
                        <div>
                            <label class="form-label">Description (p)</label>
                            <textarea name="description[<?= $i ?>]" class="form-control axis-desc" rows="3" maxlength="300" data-index="<?= $i ?>"><?= $d ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
<?php endfor; ?>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" id="saveBtn">Enregistrer</button>
            <button type="button" class="btn btn-outline-secondary" id="resetPreview">Réinitialiser aperçu</button>
        </div>
    </form>

    <!-- Aperçu en direct -->
    <hr>
    <h5>Aperçu des axes</h5>
    <div class="row" id="axesPreview">
        <?php for ($i=1;$i<=6;$i++):
            $a = $axes[$i];
        ?>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="preview-title" data-index="<?= $i ?>"><?= htmlspecialchars($a['title']) ?></h4>
                    <p class="preview-desc" data-index="<?= $i ?>"><?= htmlspecialchars($a['description']) ?></p>
                </div>
            </div>
        </div>
        <?php endfor; ?>
    </div>
</div>
</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const titleInputs = document.querySelectorAll('.axis-title');
    const descInputs = document.querySelectorAll('.axis-desc');

    function updatePreviewFor(index, title, desc){
        const t = document.querySelector('.preview-title[data-index="'+index+'"]');
        const d = document.querySelector('.preview-desc[data-index="'+index+'"]');
        if(t) t.textContent = title || '';
        if(d) d.textContent = desc || '';
    }

    titleInputs.forEach(i=>{
        i.addEventListener('input', e=>{
            const idx = e.target.dataset.index;
            updatePreviewFor(idx, e.target.value, document.querySelector('.axis-desc[data-index="'+idx+'"]')?.value || '');
        });
    });
    descInputs.forEach(i=>{
        i.addEventListener('input', e=>{
            const idx = e.target.dataset.index;
            updatePreviewFor(idx, document.querySelector('.axis-title[data-index="'+idx+'"]')?.value || '', e.target.value);
        });
    });

    document.getElementById('resetPreview').addEventListener('click', ()=>{
        titleInputs.forEach(i=>i.value='');
        descInputs.forEach(i=>i.value='');
        document.querySelectorAll('.preview-title').forEach(t=>t.textContent='');
        document.querySelectorAll('.preview-desc').forEach(d=>d.textContent='');
    });

    // client-side simple validation before submit
    document.getElementById('saveBtn').addEventListener('click', function(e){
        const tMax = 80, dMax = 300;
        for(let i=1;i<=6;i++){
            const ti = document.querySelector('.axis-title[data-index="'+i+'"]').value || '';
            const di = document.querySelector('.axis-desc[data-index="'+i+'"]').value || '';
            if(ti.length > tMax){ alert('Le titre #'+i+' dépasse '+tMax+' caractères'); e.preventDefault(); return; }
            if(di.length > dMax){ alert('La description #'+i+' dépasse '+dMax+' caractères'); e.preventDefault(); return; }
        }
    });
});
</script>
