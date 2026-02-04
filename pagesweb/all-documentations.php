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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    try {
        $id = (int)$_POST['doc_id'];
        $titreDoc = trim($_POST['titreDoc']);
        $auteur = trim($_POST['auteur']);
        $anneePub = $_POST['anneePub'] ?: null;
        $datePub = $_POST['datePub'];
        $pan = isset($_POST['pan']) ? (int)$_POST['pan'] : 0;

        $targetDir = __DIR__ . '/../img/documentations/';
        $imgFile = uploadFile('img', $targetDir, ['image/jpeg', 'image/png', 'image/webp', 'image/jpg']);
        $pdfFile = uploadFile('fichier_pdf', $targetDir, ['application/pdf']);

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
        $message = "<div class='alert alert-success text-center'>✅ Documentation modifiée avec succès.<br>🔄 Actualisation dans 3 secondes...</div>
                    <script>setTimeout(()=>location.href='" . URL_ALLDOCUMENTATIONS . "', 3000);</script>";
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger text-center'>❌ " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// 🔹 Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM documentations WHERE id=:id");
    $stmt->execute([':id' => $id]);
    $message = "<div class='alert alert-warning text-center'>🗑 Documentation supprimée avec succès.<br>🔄 Actualisation dans 3 secondes...</div>
                <script>setTimeout(()=>location.href='" . URL_ALLDOCUMENTATIONS . "', 3000);</script>";
}

// 🔹 Récupération des documentations
$stmt = $pdo->query("SELECT * FROM documentations ORDER BY datePub DESC");
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des documentations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold">📚 Admin – Documentation 1325</span>
    <div class="ms-auto">
        <a href="<?= URL_ADDSPACEADMIN; ?>" class="btn btn-outline-light me-2">MENU ADMIN</a>
        <a href="<?= URL_ADDDOCUMENTATIONS; ?>" class="btn btn-outline-light me-2">📋 Formulaire d'Ajout documentation</a>
        <a href="<?= URL_LOGOUT; ?>" class="btn btn-danger">Déconnexion</a>
    </div>
  </div>
</nav>

<div class="container py-5">
    <?= $message ?>

    <!-- 🔹 Formulaire de modification (caché par défaut) -->
    <div id="editForm" class="card shadow-sm border-0 mb-4 d-none">
        <div class="card-header bg-primary text-white fw-semibold">✏️ Modifier une documentation</div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="doc_id" id="edit_id">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Titre</label>
                        <input type="text" name="titreDoc" id="edit_titreDoc" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Auteur</label>
                        <input type="text" name="auteur" id="edit_auteur" class="form-control">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Année</label>
                        <input type="number" name="anneePub" id="edit_anneePub" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date</label>
                        <input type="date" name="datePub" id="edit_datePub" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">PAN</label>
                        <input type="number" name="pan" id="edit_pan" class="form-control">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Image (optionnelle)</label>
                        <input type="file" name="img" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fichier PDF (optionnel)</label>
                        <input type="file" name="fichier_pdf" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-success">💾 Enregistrer les modifications</button>
                <button type="button" class="btn btn-secondary" onclick="toggleEditForm(false)">Annuler</button>
            </form>
        </div>
    </div>

    <!-- 🔹 Tableau des documentations -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white fw-semibold">📋 Liste des documentations</div>
        <div class="card-body p-0">
            <?php if (empty($docs)): ?>
                <p class="text-center py-3 mb-0 text-muted">Aucune documentation disponible.</p>
            <?php else: ?>
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Année</th>
                            <th>Date</th>
                            <th>PAN</th>
                            <th>PDF</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($docs as $doc): ?>
                            <tr>
                                <td><?= htmlspecialchars($doc['id']) ?></td>
                                <td>
                                    <?php if ($doc['img']): ?>
                                        <img src="<?= BASE_URL . 'img/documentations/' . htmlspecialchars($doc['img']) ?>" style="width:60px; height:50px; object-fit:cover;">
                                    <?php else: ?>
                                        <span class="text-muted">–</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($doc['titreDoc']) ?></td>
                                <td><?= htmlspecialchars($doc['auteur'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($doc['anneePub'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($doc['datePub']) ?></td>
                                <td><?= htmlspecialchars($doc['pan']) ?></td>
                                <td>
                                    <?php if ($doc['fichier_pdf']): ?>
                                        <a href="<?= BASE_URL . 'img/documentations/' . htmlspecialchars($doc['fichier_pdf']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">Ouvrir</a>
                                    <?php else: ?>
                                        <span class="text-muted">–</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" 
                                        onclick='openEditForm(<?= json_encode($doc) ?>)'>Modifier</button>
                                    <button class="btn btn-sm btn-danger" 
                                        onclick="confirmDelete(<?= $doc['id'] ?>)">Supprimer</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// 🔹 Fonction pour confirmation de suppression
function confirmDelete(id) {
    Swal.fire({
        title: "Êtes-vous sûr ?",
        text: "⚠️ Cette action est irréversible !",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Oui, supprimer",
        cancelButtonText: "Non, annuler"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "?delete=" + id;
        }
    });
}

// 🔹 Gestion du formulaire de modification
function openEditForm(doc) {
    document.getElementById('editForm').classList.remove('d-none');
    document.getElementById('edit_id').value = doc.id;
    document.getElementById('edit_titreDoc').value = doc.titreDoc;
    document.getElementById('edit_auteur').value = doc.auteur ?? '';
    document.getElementById('edit_anneePub').value = doc.anneePub ?? '';
    document.getElementById('edit_datePub').value = doc.datePub;
    document.getElementById('edit_pan').value = doc.pan;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function toggleEditForm(show) {
    const form = document.getElementById('editForm');
    if (show) form.classList.remove('d-none');
    else form.classList.add('d-none');
}
</script>

</body>
</html>
