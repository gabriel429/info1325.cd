<?php
session_start();

// 🔹 Inclusion des fichiers nécessaires
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // contient $pdo

// 🔒 Protection d'accès
if (!isset($_SESSION['user'])) {
    header('Location:' . URL_AUTHENTIFICATION);
    exit;
}

$message = "";

// 🔹 Fonction d'upload
function uploadFile($fileKey, $targetDir, $allowedTypes)
{
    if (!isset($_FILES[$fileKey]) || empty($_FILES[$fileKey]['name'])) {
        return null;
    }

    $file = $_FILES[$fileKey];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception("Format de fichier non autorisé pour $fileKey !");
    }

    $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($file['name']));
    $targetFile = $targetDir . $fileName;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return $fileName;
    } else {
        throw new Exception("Erreur lors du téléchargement du fichier $fileKey.");
    }
}

// 🔹 Ajout ou mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $titreDoc = trim($_POST['titreDoc']);
        $auteur = trim($_POST['auteur']);
        $anneePub = $_POST['anneePub'] ?: null;
        $datePub = $_POST['datePub'];
        $pan = isset($_POST['pan']) ? (int)$_POST['pan'] : 0;

        $targetDir = __DIR__ . '/../img/documentations/';

        // Uploads
        $imgFile = uploadFile('img', $targetDir, ['image/jpeg', 'image/png', 'image/webp', 'image/jpg']);
        $pdfFile = uploadFile('fichier_pdf', $targetDir, ['application/pdf']);

        // 🔹 Vérifie si c’est une mise à jour
        if (!empty($_POST['doc_id'])) {
            $id = (int)$_POST['doc_id'];
            $sql = "UPDATE documentations 
                    SET titreDoc=:titreDoc, auteur=:auteur, anneePub=:anneePub, datePub=:datePub, pan=:pan";
            if ($imgFile) $sql .= ", img=:img";
            if ($pdfFile) $sql .= ", fichier_pdf=:fichier_pdf";
            $sql .= " WHERE id=:id";

            $stmt = $pdo->prepare($sql);
            $params = [
                ':titreDoc' => $titreDoc,
                ':auteur' => $auteur,
                ':anneePub' => $anneePub,
                ':datePub' => $datePub,
                ':pan' => $pan,
                ':id' => $id
            ];
            if ($imgFile) $params[':img'] = $imgFile;
            if ($pdfFile) $params[':fichier_pdf'] = $pdfFile;

            $stmt->execute($params);
            $message = "<div class='alert alert-success'>✅ Documentation mise à jour avec succès.</div>";
        } else {
            // 🔹 Insertion
            $stmt = $pdo->prepare("
                INSERT INTO documentations (img, fichier_pdf, titreDoc, anneePub, datePub, auteur, pan, date_creation)
                VALUES (:img, :fichier_pdf, :titreDoc, :anneePub, :datePub, :auteur, :pan, NOW())
            ");

            $stmt->execute([
                ':img' => $imgFile,
                ':fichier_pdf' => $pdfFile,
                ':titreDoc' => $titreDoc,
                ':anneePub' => $anneePub,
                ':datePub' => $datePub,
                ':auteur' => $auteur,
                ':pan' => $pan
            ]);
            
            $message = "<div class='alert alert-success'>✅ Documentation ajoutée avec succès.</div>";
            header('Location:' . URL_SUCCESSADDDOCUMENTATION);
            exit;


            $message = "<div class='alert alert-success'>✅ Documentation ajoutée avec succès.</div>";
        }
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>❌ " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// 🔹 Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM documentations WHERE id=:id");
    $stmt->execute([':id' => $id]);
    $message = "<div class='alert alert-warning'>🗑 Documentation supprimée.</div>";
}

// 🔹 Récupération des documentations
$stmt = $pdo->query("SELECT * FROM documentations ORDER BY datePub DESC");
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une documentation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold">📚 Admin – Documentation 1325</span>
    <div class="ms-auto">
        <a href="<?= URL_ADDSPACEADMIN; ?>" class="btn btn-outline-light me-2">MENU ADMIN</a>
        <a href="<?= URL_ALLDOCUMENTATIONS; ?>" class="btn btn-outline-light me-2">📋 Voir toutes la documentation</a>
        <a href="<?= URL_LOGOUT; ?>" class="btn btn-danger">Déconnexion</a>
    </div>
  </div>
</nav>

<div class="container py-5">
    <div class="card shadow-lg border-0 p-4 mb-5">
        <h4 class="mb-4 text-primary fw-semibold">Formulaire d'ajout d'une documentation</h4>
        <?= $message ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="doc_id" value="<?= htmlspecialchars($_GET['edit'] ?? '') ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Titre de la documentation</label>
                    <input type="text" name="titreDoc" class="form-control" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Auteur</label>
                    <input type="text" name="auteur" class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Année de publication</label>
                    <input type="number" name="anneePub" min="1900" max="2099" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Date de publication</label>
                    <input type="date" name="datePub" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Image (jpg, png, webp)</label>
                    <input type="file" name="img" class="form-control" accept="image/*">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Fichier PDF</label>
                    <input type="file" name="fichier_pdf" class="form-control" accept="application/pdf">
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold">PAN</label>
                <select name="pan" class="form-select">
                    <option value="0" selected>0</option>
                    <option value="1">1</option>
                </select>
            </div>

            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary px-4">💾 Enregistrer</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
