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

        // After successful upload, try to generate an optimized image and a thumbnail
        try {
            // Resize main image to max 1600x520 (no upscaling)
            resize_image_gd($targetFile, $targetFile, 1600, 520);
            // Ensure thumbs dir exists
            $thumbDir = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR;
            if (!is_dir($thumbDir)) mkdir($thumbDir, 0777, true);
            create_thumbnail_center($targetFile, $thumbDir . $fileName, 80);
        } catch (Exception $e) {
            // ignore processing errors
        }
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



?>



<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>Dashboard Administrateur – SN1325</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            margin: 0;
            padding: 0;
            position: relative;
            min-height: 100vh;
        }

        .animated-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(-45deg, #1e3a8a, #3b82f6, #8b5cf6, #ec4899);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .animated-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            animation: pulse 8s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 0.5;
            }
            50% {
                opacity: 1;
            }
        }

        .container-fluid, .card {
            position: relative;
            z-index: 1;
        }
    </style>

</head>

<body>

<div class="animated-background"></div>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">

  <div class="container-fluid">

    <span class="navbar-brand fw-bold">📊 Dashboard Administrateur – SN1325</span>

    <div class="ms-auto">

        <a href="<?= URL_ADDSPACEADMIN; ?>" class="btn btn-outline-light me-2">MENU ADMIN</a>

        <a href="<?= URL_ADDACTUALITES; ?>" class="btn btn-outline-light me-2">📋 Gérer les Actualités</a>
        <a href="<?= URL_MANAGE_FUNFACTS; ?>" class="btn btn-outline-light me-2">⚙️ Gérer Fun Facts</a>
        <a href="<?= URL_MANAGE_AXES; ?>" class="btn btn-outline-light me-2">🧭 Gérer Axes</a>
        <?php if (in_array($_SESSION['role'] ?? '', ['admin','slider'])): ?>
            <a href="<?= URL_MANAGE_SLIDER; ?>" class="btn btn-outline-light me-2">🎞️ Gérer Slider</a>
        <?php endif; ?>
        <a href="<?= URL_MANAGE_PARTENAIRES; ?>" class="btn btn-outline-light me-2">🤝 Gérer Partenaires</a>
        <a href="<?= URL_MANAGE_GALERIE; ?>" class="btn btn-outline-light me-2">🖼️ Gérer Galerie</a>
        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <a href="<?= URL_MANAGE_USERS; ?>" class="btn btn-outline-warning me-2">👥 Gérer Utilisateurs</a>
            <a href="<?= URL_MANAGE_SETTINGS; ?>" class="btn btn-outline-info me-2">⚙️ Paramètres</a>
        <?php endif; ?>

        <a href="<?= URL_LOGOUT; ?>" class="btn btn-danger">Déconnexion</a>

    </div>

  </div>

</nav>

<!-- Visitor Statistics Widget -->
<div class="container-fluid py-4 bg-light">
    <?php include __DIR__ . '/visitor_stats_widget.php'; ?>
</div>

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

</div>



<script>


                                    // Resize image using GD while preserving aspect ratio (no upscaling)
                                    function resize_image_gd($srcPath, $dstPath, $maxW, $maxH)
                                    {
                                        $info = @getimagesize($srcPath);
                                        if (!$info) return false;
                                        list($w, $h, $type) = $info;
                                        $ratio = min($maxW / $w, $maxH / $h, 1);
                                        $nw = (int) max(1, floor($w * $ratio));
                                        $nh = (int) max(1, floor($h * $ratio));

                                        switch ($type) {
                                            case IMAGETYPE_JPEG: $img = imagecreatefromjpeg($srcPath); break;
                                            case IMAGETYPE_PNG:  $img = imagecreatefrompng($srcPath); break;
                                            case IMAGETYPE_WEBP: $img = imagecreatefromwebp($srcPath); break;
                                            default: return false;
                                        }

                                        $dst = imagecreatetruecolor($nw, $nh);
                                        if ($type === IMAGETYPE_PNG) {
                                            imagealphablending($dst, false);
                                            imagesavealpha($dst, true);
                                        }
                                        imagecopyresampled($dst, $img, 0,0,0,0, $nw,$nh, $w,$h);

                                        $ok = false;
                                        if ($type === IMAGETYPE_JPEG) $ok = imagejpeg($dst, $dstPath, 85);
                                        elseif ($type === IMAGETYPE_PNG) $ok = imagepng($dst, $dstPath, 6);
                                        elseif ($type === IMAGETYPE_WEBP) $ok = imagewebp($dst, $dstPath, 85);

                                        imagedestroy($img);
                                        imagedestroy($dst);
                                        return $ok;
                                    }

                                    // Create square thumbnail by center-cropping after scaling to cover
                                    function create_thumbnail_center($srcPath, $dstPath, $size)
                                    {
                                        $info = @getimagesize($srcPath);
                                        if (!$info) return false;
                                        list($w, $h, $type) = $info;

                                        switch ($type) {
                                            case IMAGETYPE_JPEG: $img = imagecreatefromjpeg($srcPath); break;
                                            case IMAGETYPE_PNG:  $img = imagecreatefrompng($srcPath); break;
                                            case IMAGETYPE_WEBP: $img = imagecreatefromwebp($srcPath); break;
                                            default: return false;
                                        }

                                        // scale to cover
                                        $scale = max($size / $w, $size / $h);
                                        $sw = (int) ceil($w * $scale);
                                        $sh = (int) ceil($h * $scale);

                                        $tmp = imagecreatetruecolor($sw, $sh);
                                        if ($type === IMAGETYPE_PNG) {
                                            imagealphablending($tmp, false);
                                            imagesavealpha($tmp, true);
                                        }
                                        imagecopyresampled($tmp, $img, 0,0,0,0, $sw,$sh, $w,$h);

                                        // crop center
                                        $cx = (int) floor(($sw - $size) / 2);
                                        $cy = (int) floor(($sh - $size) / 2);
                                        $thumb = imagecreatetruecolor($size, $size);
                                        if ($type === IMAGETYPE_PNG) {
                                            imagealphablending($thumb, false);
                                            imagesavealpha($thumb, true);
                                        }
                                        imagecopy($thumb, $tmp, 0,0, $cx,$cy, $size,$size);

                                        $ok = false;
                                        if ($type === IMAGETYPE_JPEG) $ok = imagejpeg($thumb, $dstPath, 85);
                                        elseif ($type === IMAGETYPE_PNG) $ok = imagepng($thumb, $dstPath, 6);
                                        elseif ($type === IMAGETYPE_WEBP) $ok = imagewebp($thumb, $dstPath, 85);

                                        imagedestroy($img);
                                        imagedestroy($tmp);
                                        imagedestroy($thumb);
                                        return $ok;
                                    }
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

