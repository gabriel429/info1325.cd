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

// create slides table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `position` INT NOT NULL UNIQUE,
    title VARCHAR(255) DEFAULT NULL,
    subtitle TEXT DEFAULT NULL,
    btn_text VARCHAR(255) DEFAULT NULL,
    btn_url VARCHAR(255) DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $maxSlides = 6;
    $targetDir = __DIR__ . '/../img/slider/';
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    try {
        $pdo->beginTransaction();
        for ($i=1;$i<=$maxSlides;$i++) {
            $title = trim($_POST['title'][$i] ?? '');
            $subtitle = trim($_POST['subtitle'][$i] ?? '');
            $btn_text = trim($_POST['btn_text'][$i] ?? '');
            $btn_url = trim($_POST['btn_url'][$i] ?? '');
            $active = isset($_POST['active'][$i]) ? 1 : 0;

            // handle image upload
            $uploadedImage = null;
            if (isset($_FILES['image']) && isset($_FILES['image']['name'][$i]) && $_FILES['image']['error'][$i] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['image']['tmp_name'][$i];
                $orig = basename($_FILES['image']['name'][$i]);
                $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
                    $fileName = time() . '_' . $i . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $orig);
                    $dest = $targetDir . $fileName;
                    if (move_uploaded_file($tmp, $dest)) $uploadedImage = $fileName;
                }
            }

            // if fields empty and no uploaded image -> delete slide
            if ($title === '' && $subtitle === '' && !$uploadedImage) {
                $stmt = $pdo->prepare('DELETE FROM slides WHERE `position` = :pos');
                $stmt->execute([':pos'=>$i]);
                continue;
            }

            $stmt = $pdo->prepare('SELECT id FROM slides WHERE `position` = :pos');
            $stmt->execute([':pos'=>$i]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                if ($uploadedImage) {
                    $stmt = $pdo->prepare('UPDATE slides SET title=:t, subtitle=:s, btn_text=:b, btn_url=:u, image=:img, active=:a WHERE `position`=:pos');
                    $stmt->execute([':t'=>$title,':s'=>$subtitle,':b'=>$btn_text,':u'=>$btn_url,':img'=>$uploadedImage,':a'=>$active,':pos'=>$i]);
                } else {
                    $stmt = $pdo->prepare('UPDATE slides SET title=:t, subtitle=:s, btn_text=:b, btn_url=:u, active=:a WHERE `position`=:pos');
                    $stmt->execute([':t'=>$title,':s'=>$subtitle,':b'=>$btn_text,':u'=>$btn_url,':a'=>$active,':pos'=>$i]);
                }
            } else {
                $stmt = $pdo->prepare('INSERT INTO slides (`position`, title, subtitle, btn_text, btn_url, image, active) VALUES (:pos,:t,:s,:b,:u,:img,:a)');
                $stmt->execute([':pos'=>$i,':t'=>$title,':s'=>$subtitle,':b'=>$btn_text,':u'=>$btn_url,':img'=>$uploadedImage,':a'=>$active]);
            }
        }
        $pdo->commit();
        $message = "<div class='alert alert-success'>Slider mis à jour.</div>";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "<div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// load existing slides
$stmt = $pdo->query('SELECT * FROM slides ORDER BY `position` ASC');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$slides = [];
foreach ($rows as $r) $slides[(int)$r['position']] = $r;

// defaults if empty
$defaults = [];
for ($i=1;$i<=6;$i++) {
    if (!isset($slides[$i])) $slides[$i] = ['title'=>'','subtitle'=>'','btn_text'=>'','btn_url'=>'','image'=>null,'active'=>0];
}

?><!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Gérer le slider</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark px-3">
  <span class="navbar-brand">Admin – Slider</span>
  <div class="ms-auto">
    <a href="<?= URL_ADMINISTRATEUR ?>" class="btn btn-outline-light">Retour</a>
  </div>
</nav>
<div class="container py-4">
    <?= $message ?>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="save" value="1">
        <div class="row">
            <?php for ($i=1;$i<=6;$i++):
                $s = $slides[$i];
            ?>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">Slide #<?= $i ?></div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label">Titre (h1)</label>
                            <input type="text" name="title[<?= $i ?>]" class="form-control" value="<?= htmlspecialchars($s['title']) ?>" maxlength="200">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Sous-titre / paragraphe</label>
                            <textarea name="subtitle[<?= $i ?>]" class="form-control" rows="3" maxlength="800"><?= htmlspecialchars($s['subtitle']) ?></textarea>
                        </div>
                        <div class="mb-2 row">
                            <div class="col">
                                <label class="form-label">Texte bouton</label>
                                <input type="text" name="btn_text[<?= $i ?>]" class="form-control" value="<?= htmlspecialchars($s['btn_text']) ?>" maxlength="80">
                            </div>
                            <div class="col">
                                <label class="form-label">URL bouton</label>
                                <input type="text" name="btn_url[<?= $i ?>]" class="form-control" value="<?= htmlspecialchars($s['btn_url']) ?>" maxlength="255">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Image (optionnelle)</label>
                            <input type="file" name="image[<?= $i ?>]" accept="image/*" class="form-control">
                            <?php if (!empty($s['image'])): ?>
                                <div class="mt-2"><img src="<?= IMG_DIR ?>slider/<?= htmlspecialchars($s['image']) ?>" alt="slide-<?= $i ?>" style="max-width:160px;max-height:100px;object-fit:cover;border:1px solid #ddd;padding:2px;"></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="active[<?= $i ?>]" id="active<?= $i ?>" <?= (!empty($s['active']) ? 'checked' : '')?>>
                            <label class="form-check-label" for="active<?= $i ?>">Activer cette slide</label>
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

    <hr>
    <h5>Aperçu du slider</h5>
    <div class="hero-slider" id="sliderPreview">
        <?php for ($i=1;$i<=6;$i++): $s=$slides[$i];
            $img = !empty($s['image']) ? IMG_DIR . 'slider/' . $s['image'] : IMG_DIR . 'slider' . $i . '.jpg';
        ?>
        <div class="single-slider" style="background-image:url('<?= $img ?>');padding:40px;color:#fff;margin-bottom:12px;border-radius:6px;">
            <div class="container"><div class="row"><div class="col-lg-7"><h1 class="preview-title"><?= htmlspecialchars($s['title']) ?></h1><p class="preview-subtitle"><?= htmlspecialchars($s['subtitle']) ?></p><?php if(!empty($s['btn_text'])): ?><a class="btn btn-light preview-btn" href="<?= htmlspecialchars($s['btn_url']) ?>"><?= htmlspecialchars($s['btn_text']) ?></a><?php endif; ?></div></div></div>
        </div>
        <?php endfor; ?>
    </div>
</div>
</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', function(){
    // preview bindings
    const max = 6;
    for(let i=1;i<=max;i++){
        const t = document.querySelector('input[name="title['+i+']"]');
        const s = document.querySelector('textarea[name="subtitle['+i+']"]');
        const b = document.querySelector('input[name="btn_text['+i+']"]');
        const u = document.querySelector('input[name="btn_url['+i+']"]');
        const pTitle = document.querySelectorAll('.preview-title')[i-1];
        const pSub = document.querySelectorAll('.preview-subtitle')[i-1];
        const pBtn = document.querySelectorAll('.preview-btn')[i-1];
        if(t) t.addEventListener('input', e=>{ if(pTitle) pTitle.textContent = e.target.value; });
        if(s) s.addEventListener('input', e=>{ if(pSub) pSub.textContent = e.target.value; });
        if(b) b.addEventListener('input', e=>{ if(pBtn) pBtn.textContent = e.target.value; });
        if(u) u.addEventListener('input', e=>{ if(pBtn) pBtn.href = e.target.value; });
    }
});
</script>
