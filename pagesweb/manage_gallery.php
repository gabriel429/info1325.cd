<?php
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
session_start();
// Simple protection: require logged user for admin gallery management
if (!isset($_SESSION['user'])){
    header('Location:' . URL_AUTHENTIFICATION);
    exit;
}
// Simple admin page to upload/delete gallery images. Integrate into your admin dashboard as needed.
$dataDir = __DIR__ . '/../data';
$dataFile = $dataDir . '/galerie.json';
$imgDir = __DIR__ . '/../img/galerie/';
if (!is_dir($dataDir)) mkdir($dataDir, 0755, true);
if (!is_dir($imgDir)) mkdir($imgDir, 0755, true);
$entries = [];
if (file_exists($dataFile)){
    $entries = json_decode(file_get_contents($dataFile), true) ?? [];
}
$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (!empty($_POST['action']) && $_POST['action'] === 'delete' && !empty($_POST['file'])){
        $file = basename($_POST['file']);
        // remove from disk and from json
        $path = $imgDir . $file;
        if (file_exists($path)) unlink($path);
        // remove entry
        $entries = array_values(array_filter($entries, function($e) use($file){ return ($e['file'] ?? '') !== $file; }));
        file_put_contents($dataFile, json_encode($entries, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        $success = 'Image supprimée.';
    }
    if (!empty($_POST['action']) && $_POST['action'] === 'upload'){
        $act = trim($_POST['activity'] ?? '');
        // Support up to 5 files via input name="images[]"
        if (!empty($_FILES['images'])){
            $fArr = $_FILES['images'];
            $count = is_array($fArr['name']) ? count($fArr['name']) : 0;
            $count = min($count, 5);
            $uploaded = [];
            for ($i=0;$i<$count;$i++){
                if ($fArr['error'][$i] !== UPLOAD_ERR_OK){
                    $errors[] = 'Erreur upload pour ' . ($fArr['name'][$i] ?? 'fichier') . '.';
                    continue;
                }
                $origName = $fArr['name'][$i] ?? 'file';
                $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])){
                    $errors[] = 'Type non autorisé: ' . h($origName);
                    continue;
                }
                $name = time() . '_' . $i . '_' . preg_replace('/[^a-z0-9\-_.]/i','',basename($origName));
                $target = $imgDir . $name;
                if (move_uploaded_file($fArr['tmp_name'][$i], $target)){
                    $entries[] = ['file'=>$name,'activity'=>$act,'uploaded'=>time()];
                    $uploaded[] = $name;
                } else {
                    $errors[] = 'Impossible de déplacer ' . h($origName);
                }
            }
            if ($uploaded){
                file_put_contents($dataFile, json_encode($entries, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
                $success = count($uploaded) . ' image(s) ajoutée(s).';
            }
        } elseif (!empty($_FILES['image'])){
            // fallback single file input compatibility
            $f = $_FILES['image'];
            if ($f['error'] !== UPLOAD_ERR_OK) $errors[] = 'Erreur upload.';
            else {
                $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) $errors[] = 'Type d\'image non autorisé.';
                else {
                    $name = time() . '_' . preg_replace('/[^a-z0-9\-_.]/i','',basename($f['name']));
                    $target = $imgDir . $name;
                    if (move_uploaded_file($f['tmp_name'], $target)){
                        $entries[] = ['file'=>$name,'activity'=>$act,'uploaded'=>time()];
                        file_put_contents($dataFile, json_encode($entries, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
                        $success = 'Image ajoutée.';
                    } else $errors[] = 'Impossible de déplacer le fichier.';
                }
            }
        }
    }
}
// sanitize for output
function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?><!doctype html>
<html><head><meta charset="utf-8"><title>Manage Galerie</title>
<link rel="stylesheet" href="<?= CSS_DIR ?>bootstrap.min.css">
<style>.thumb{height:100px;object-fit:cover}</style>
</head><body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>🖼️ Gestion Galerie</h2>
        <a href="<?= URL_ADMINISTRATEUR ?>" class="btn btn-secondary">← Retour au dashboard</a>
    </div>
    <?php if($success): ?><div class="alert alert-success"><?php echo h($success);?></div><?php endif; ?>
    <?php if($errors): foreach($errors as $e): ?><div class="alert alert-danger"><?php echo h($e);?></div><?php endforeach; endif; ?>

    <h4>Ajouter des images (max 5)</h4>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="upload">
        <div class="mb-2">
            <label>Images (sélection multiple)
                <input type="file" id="images" name="images[]" multiple accept="image/*">
            </label>
            <small class="form-text text-muted">Sélectionnez jusqu'à 5 images.</small>
            <div id="preview" class="mt-2 d-flex flex-wrap" aria-live="polite"></div>
        </div>
        <div class="mb-2"><label>Activité (catégorie)<br><input class="form-control" name="activity" placeholder="ex: conférence, atelier"></label></div>
        <button class="btn btn-primary">Uploader</button>
    </form>

    <hr>
    <h4>Images existantes</h4>
    <table class="table table-sm">
        <thead><tr><th>Preview</th><th>Fichier</th><th>Activité</th><th>Uploaded</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach($entries as $e): $file = $e['file'] ?? ''; $act = $e['activity'] ?? ''; $up = $e['uploaded'] ?? 0; ?>
            <tr>
                <td><img src="<?= IMG_DIR ?>galerie/<?= h($file) ?>" class="thumb" alt=""></td>
                <td><?= h($file) ?></td>
                <td><?= h($act) ?></td>
                <td><?= $up?date('Y-m-d H:i',$up):'' ?></td>
                <td>
                    <form method="post" style="display:inline" onsubmit="return confirm('Supprimer ?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="file" value="<?= h($file) ?>">
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
    <script>
document.addEventListener('DOMContentLoaded', function(){
    var input = document.getElementById('images');
    var preview = document.getElementById('preview');
    var maxFiles = 5;
    if (!input) return;
    function clearPreview(){
        if (preview) preview.innerHTML = '';
    }
    input.addEventListener('change', function(){
        clearPreview();
        if (this.files.length > maxFiles){
            alert('Sélectionnez au maximum ' + maxFiles + ' fichiers.');
            this.value = '';
            return;
        }
        Array.from(this.files).slice(0, maxFiles).forEach(function(f){
            if (!f.type.startsWith('image/')) return;
            var reader = new FileReader();
            reader.onload = function(e){
                var img = document.createElement('img');
                img.src = e.target.result;
                img.alt = f.name;
                img.className = 'thumb m-1';
                img.style.width = '100px';
                img.style.height = '70px';
                img.style.objectFit = 'cover';
                if (preview) preview.appendChild(img);
            };
            reader.readAsDataURL(f);
        });
    });
    var form = input.closest('form');
    if (form){
        form.addEventListener('submit', function(e){
            if (!input.files || input.files.length === 0){
                e.preventDefault();
                alert('Veuillez sélectionner au moins un fichier.');
            } else if (input.files.length > maxFiles){
                e.preventDefault();
                alert('Maximum ' + maxFiles + ' fichiers autorisés.');
            }
        });
    }
});
</script>
</body></html>