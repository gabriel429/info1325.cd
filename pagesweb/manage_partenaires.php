<?php
session_start();
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/connectDb.php';

if (!isset($_SESSION['user'])) {
    header('Location:' . URL_AUTHENTIFICATION);
    exit;
}

$message = '';
$targetDir = __DIR__ . '/../img/partenaires/';

// Création du dossier si nécessaire
if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

// Ajout d'un partenaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    try {
        if (!isset($_FILES['logo']) || empty($_FILES['logo']['name'])) throw new Exception('Aucun fichier fourni');
        $file = $_FILES['logo'];
        if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Erreur upload');
        $mime = mime_content_type($file['tmp_name']);
        $allowed = ['image/png','image/jpeg','image/webp'];
        if (!in_array($mime, $allowed)) throw new Exception('Format non autorisé');
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/','_', basename($file['name']));
        $dest = $targetDir . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) throw new Exception('Impossible de déplacer le fichier');

        $name = trim($_POST['name'] ?? '');
        $url = trim($_POST['url'] ?? '#');
        $stmt = $pdo->prepare("INSERT INTO partenaires (name, url, image, active) VALUES (:name,:url,:image,1)");
        $stmt->execute([':name'=>$name, ':url'=>$url, ':image'=>$filename]);
        $message = "<div class='alert alert-success'>Partenaire ajouté.</div>";
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>Erreur: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Import CSV
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'import') {
    try {
        if (!isset($_FILES['csvfile']) || $_FILES['csvfile']['error'] !== UPLOAD_ERR_OK) throw new Exception('Aucun fichier CSV fourni');
        $tmp = $_FILES['csvfile']['tmp_name'];
        $fh = fopen($tmp, 'r');
        if (!$fh) throw new Exception('Impossible d' . "'ouvrir le fichier CSV");

        // Attendu: header ou colonnes: name,url,image,active,position (image optionnelle)
        $rowCount = 0;
        $insert = $pdo->prepare("INSERT INTO partenaires (name, url, image, active, position) VALUES (:name,:url,:image,:active,:position)");
        // Skip header if present
        $first = fgetcsv($fh);
        $hasHeader = false;
        if ($first !== false) {
            $lower = array_map('strtolower', $first);
            if (in_array('name', $lower) && in_array('image', $lower)) {
                $hasHeader = true;
            } else {
                // not a header, process first row
                $data = $first;
                $name = trim($data[0] ?? '');
                $url = trim($data[1] ?? '#');
                $image = trim($data[2] ?? null);
                $active = isset($data[3]) ? (int)$data[3] : 1;
                $position = isset($data[4]) && is_numeric($data[4]) ? (int)$data[4] : null;
                if ($name !== '') { $insert->execute([':name'=>$name,':url'=>$url,':image'=>$image,':active'=>$active,':position'=>$position]); $rowCount++; }
            }
        }

        while (($data = fgetcsv($fh)) !== false) {
            $name = trim($data[0] ?? '');
            if ($name === '') continue;
            $url = trim($data[1] ?? '#');
            $image = trim($data[2] ?? null);
            $active = isset($data[3]) ? (int)$data[3] : 1;
            $position = isset($data[4]) && is_numeric($data[4]) ? (int)$data[4] : null;
            $insert->execute([':name'=>$name,':url'=>$url,':image'=>$image,':active'=>$active,':position'=>$position]);
            $rowCount++;
        }
        fclose($fh);
        $message = "<div class='alert alert-success'>Import terminé — $rowCount ligne(s) insérée(s).</div>";
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>Erreur import CSV: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Import ZIP d'images (logos)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'import_zip') {
    try {
        if (!isset($_FILES['zipfile']) || $_FILES['zipfile']['error'] !== UPLOAD_ERR_OK) throw new Exception('Aucun fichier ZIP fourni');
        $tmp = $_FILES['zipfile']['tmp_name'];
        $zip = new ZipArchive();
        if ($zip->open($tmp) !== true) throw new Exception('Impossible d\'ouvrir l\'archive ZIP');

        $allowedExt = ['png','jpg','jpeg','webp','gif'];
        $extracted = 0;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            $basename = basename($entry);
            if ($basename === '') continue;
            $ext = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExt)) continue;

            $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $basename);
            $stream = $zip->getStream($entry);
            if (!$stream) continue;
            $outPath = $targetDir . $safeName;
            $out = fopen($outPath, 'wb');
            if (!$out) { fclose($stream); continue; }
            while (!feof($stream)) {
                fwrite($out, fread($stream, 1024));
            }
            fclose($out);
            fclose($stream);
            $extracted++;
            // Insert into DB if not exists (avoid duplicates by image name)
            try {
                $check = $pdo->prepare("SELECT COUNT(*) FROM partenaires WHERE image = :image");
                $check->execute([':image' => $safeName]);
                $exists = (int)$check->fetchColumn();
                if ($exists === 0) {
                    // Derive a readable name from file name (strip timestamp and extension)
                    $readable = preg_replace('/^[0-9]+_/', '', $safeName);
                    $readable = pathinfo($readable, PATHINFO_FILENAME);
                    $readable = str_replace(['_','-'], ' ', $readable);
                    $readable = ucwords($readable);
                    $ins = $pdo->prepare("INSERT INTO partenaires (name, url, image, active) VALUES (:name, '#', :image, 1)");
                    $ins->execute([':name' => $readable, ':image' => $safeName]);
                }
            } catch (Exception $e) {
                // ignore DB insert errors for single files, but continue
            }
        }
        $zip->close();
        $message = "<div class='alert alert-success'>ZIP traité — $extracted image(s) extraites dans img/partenaires/.</div>";
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>Erreur import ZIP: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM partenaires WHERE id=:id");
    $stmt->execute([':id'=>$id]);
    $row = $stmt->fetch();
    if ($row) {
        if ($row['image'] && file_exists($targetDir . $row['image'])) unlink($targetDir . $row['image']);
        $del = $pdo->prepare("DELETE FROM partenaires WHERE id=:id");
        $del->execute([':id'=>$id]);
        $message = "<div class='alert alert-warning'>Partenaire supprimé.</div>";
    }
}

// Récupération
try {
    $stmt = $pdo->query("SELECT * FROM partenaires ORDER BY IFNULL(position,id) ASC");
    $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $partners = [];
    $message = "<div class='alert alert-danger'>Erreur BDD: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Gestion Partenaires</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <span class="navbar-brand">🤝 Gestion des Partenaires</span>
    <div class="d-flex">
        <a href="<?= URL_ADMINISTRATEUR; ?>" class="btn btn-outline-light">Retour</a>
    </div>
  </div>
</nav>

<div class="container py-4">
    <?= $message ?>

    <div class="card mb-4">
        <div class="card-header">Ajouter un partenaire</div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="mb-3">
                    <label class="form-label">Nom</label>
                    <input type="text" name="name" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">URL (optionnel)</label>
                    <input type="text" name="url" class="form-control" placeholder="https://...">
                </div>
                <div class="mb-3">
                    <label class="form-label">Logo (PNG/JPG/WEBP)</label>
                    <input type="file" name="logo" accept="image/*" class="form-control" required>
                </div>
                <button class="btn btn-primary">Ajouter</button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Import CSV (name,url,image,active,position)</div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="import">
                <div class="mb-3">
                    <label class="form-label">Fichier CSV</label>
                    <input type="file" name="csvfile" accept=".csv" class="form-control" required>
                </div>
                <div class="mb-3 text-muted small">Colonnes attendues : <code>name,url,image,active,position</code>. L'image est le nom de fichier déjà présent dans <code>img/partenaires/</code> si vous souhaitez l'afficher.</div>
                <button class="btn btn-secondary">Importer</button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Importer logos (ZIP)</div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="import_zip">
                <div class="mb-3">
                    <label class="form-label">Archive ZIP</label>
                    <input type="file" name="zipfile" accept=".zip" class="form-control" required>
                </div>
                <div class="mb-3 text-muted small">Le ZIP sera extrait dans <code>img/partenaires/</code>. Seuls les fichiers PNG/JPG/WEBP/GIF sont autorisés.</div>
                <button class="btn btn-secondary">Téléverser et extraire</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Liste des partenaires</div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr><th>ID</th><th>Nom</th><th>Logo</th><th>URL</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach($partners as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['id']) ?></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?php if ($p['image'] && file_exists($targetDir . $p['image'])): ?><img src="<?= IMG_DIR . 'partenaires/' . rawurlencode($p['image']) ?>" style="height:40px"><?php else: ?>—<?php endif; ?></td>
                        <td><?= htmlspecialchars($p['url']) ?></td>
                        <td>
                            <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>