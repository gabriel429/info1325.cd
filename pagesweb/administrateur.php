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
        $id = (int)$_POST['actu_id'];
        $titre = trim($_POST['titre']);
        $auteur = trim($_POST['auteur']);
        $date_pub = $_POST['date_pub'];
        $messageFort = $_POST['messageFort'] ?? null;
        $commentaire = $_POST['commentaire'] ?? null;
        $nbrVues = $_POST['nbrVues'] ?? 0;

        // Paragraphes
        $paragraphs = [];
        for ($i = 1; $i <= 10; $i++) {
            $paragraphs['paraph'.$i] = $_POST['paraph'.$i] ?? null;
        }

        $targetDir = __DIR__ . '/../img/actualites/';
        $imgMise = uploadFile('imgMise', $targetDir, ['image/jpeg','image/png','image/webp','image/jpg']);
        $imgPub1 = uploadFile('imgPub1', $targetDir, ['image/jpeg','image/png','image/webp','image/jpg']);
        $imgPub2 = uploadFile('imgPub2', $targetDir, ['image/jpeg','image/png','image/webp','image/jpg']);

        $sql = "UPDATE actualites SET titre=:titre, auteur=:auteur, date_pub=:date_pub, 
                messageFort=:messageFort, commentaire=:commentaire, nbrVues=:nbrVues";

        foreach ($paragraphs as $key => $value) {
            $sql .= ", $key=:$key";
        }

        if ($imgMise) $sql .= ", imgMise=:imgMise";
        if ($imgPub1) $sql .= ", imgPub1=:imgPub1";
        if ($imgPub2) $sql .= ", imgPub2=:imgPub2";

        $sql .= " WHERE id=:id";

        $stmt = $pdo->prepare($sql);
        $params = [
            ':titre' => $titre,
            ':auteur' => $auteur,
            ':date_pub' => $date_pub,
            ':messageFort' => $messageFort,
            ':commentaire' => $commentaire,
            ':nbrVues' => $nbrVues,
            ':id' => $id
        ];

        foreach ($paragraphs as $key => $value) {
            $params[":$key"] = $value;
        }

        if ($imgMise) $params[':imgMise'] = $imgMise;
        if ($imgPub1) $params[':imgPub1'] = $imgPub1;
        if ($imgPub2) $params[':imgPub2'] = $imgPub2;

        $stmt->execute($params);

        $message = "<div class='alert alert-success text-center'>✅ Actualité modifiée avec succès.<br>🔄 Actualisation dans 3 secondes...</div>
                    <script>setTimeout(()=>location.href='" . URL_ADMINISTRATEUR . "', 3000);</script>";

    } catch (Exception $e) {
        $message = "<div class='alert alert-danger text-center'>❌ " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// 🔹 Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT imgMise,imgPub1,imgPub2 FROM actualites WHERE id=:id");
    $stmt->execute([':id'=>$id]);
    $imgs = $stmt->fetch(PDO::FETCH_ASSOC);

    foreach ($imgs as $img) {
        if ($img && file_exists(__DIR__ . '/../img/actualites/' . $img)) {
            unlink(__DIR__ . '/../img/actualites/' . $img);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM actualites WHERE id=:id");
    $stmt->execute([':id'=>$id]);
    $message = "<div class='alert alert-warning text-center'>🗑 Actualité supprimée avec succès.<br>🔄 Actualisation dans 3 secondes...</div>
                <script>setTimeout(()=>location.href='" . URL_ADMINISTRATEUR . "', 3000);</script>";
}

// 🔹 Récupération des actualités
$stmt = $pdo->query("SELECT * FROM actualites ORDER BY date_pub DESC");
$actualites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des actualités</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold">📰 Admin – Actualités 1325</span>
    <div class="ms-auto">
        <a href="<?= URL_ADDSPACEADMIN; ?>" class="btn btn-outline-light me-2">MENU ADMIN</a>
        <a href="<?= URL_ADDACTUALITES; ?>" class="btn btn-outline-light me-2">📋 Formulaire d'Ajout actualité</a>
        <a href="<?= URL_LOGOUT; ?>" class="btn btn-danger">Déconnexion</a>
    </div>
  </div>
</nav>

<div class="container py-5">
    <?= $message ?>

    <!-- 🔹 Formulaire de modification caché -->
    <div id="editForm" class="card shadow-sm border-0 mb-4 d-none p-4">
        <div class="card-header bg-primary text-white fw-semibold">✏️ Modifier une actualité</div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="actu_id" id="edit_id">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Titre</label>
                        <input type="text" name="titre" id="edit_titre" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Auteur</label>
                        <input type="text" name="auteur" id="edit_auteur" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Date de publication</label>
                        <input type="date" name="date_pub" id="edit_date_pub" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Message fort</label>
                    <textarea name="messageFort" id="edit_messageFort" class="form-control" rows="2"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Commentaire général</label>
                    <textarea name="commentaire" id="edit_commentaire" class="form-control" rows="3"></textarea>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Image principale</label>
                        <input type="file" name="imgMise" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Image secondaire 1</label>
                        <input type="file" name="imgPub1" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Image secondaire 2</label>
                        <input type="file" name="imgPub2" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre de vues</label>
                    <input type="number" name="nbrVues" id="edit_nbrVues" class="form-control" min="0">
                </div>

                <hr>
                <h5 class="text-primary mt-4 mb-3">🧩 Paragraphes du contenu</h5>
                <div class="row">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Paragraphe <?= $i ?></label>
                            <textarea name="paraph<?= $i ?>" id="edit_paraph<?= $i ?>" class="form-control" rows="2"></textarea>
                        </div>
                    <?php endfor; ?>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">💾 Enregistrer les modifications</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleEditForm(false)">Annuler</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 🔹 Tableau des actualités -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white fw-semibold">📋 Liste des actualités</div>
        <div class="card-body p-0">
            <?php if (empty($actualites)): ?>
                <p class="text-center py-3 mb-0 text-muted">Aucune actualité disponible.</p>
            <?php else: ?>
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Date</th>
                            <th>Vues</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($actualites as $a): ?>
                            <tr>
                                <td><?= $a['id'] ?></td>
                                <td><?= htmlspecialchars($a['titre']) ?></td>
                                <td><?= htmlspecialchars($a['auteur'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($a['date_pub']) ?></td>
                                <td><?= htmlspecialchars($a['nbrVues']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary editBtn" 
                                        data-actu='<?= htmlspecialchars(json_encode($a), ENT_QUOTES, 'UTF-8') ?>'>
                                        Modifier
                                    </button>
                                    <button class="btn btn-sm btn-danger" 
                                        onclick="confirmDelete(<?= $a['id'] ?>)">Supprimer</button>
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

function openEditForm(a) {
    document.getElementById('editForm').classList.remove('d-none');
    document.getElementById('edit_id').value = a.id;
    document.getElementById('edit_titre').value = a.titre;
    document.getElementById('edit_auteur').value = a.auteur ?? '';
    document.getElementById('edit_date_pub').value = a.date_pub;
    document.getElementById('edit_messageFort').value = a.messageFort ?? '';
    document.getElementById('edit_commentaire').value = a.commentaire ?? '';
    document.getElementById('edit_nbrVues').value = a.nbrVues ?? 0;

    for (let i=1; i<=10; i++) {
        document.getElementById('edit_paraph'+i).value = a['paraph'+i] ?? '';
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function toggleEditForm(show) {
    const form = document.getElementById('editForm');
    if (show) form.classList.remove('d-none');
    else form.classList.add('d-none');
}

// 🔹 Assign event listener aux boutons Modifier
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', function() {
        const a = JSON.parse(this.getAttribute('data-actu'));
        openEditForm(a);
    });
});
</script>

</body>
</html>
