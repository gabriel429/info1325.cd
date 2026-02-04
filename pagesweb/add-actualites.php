<?php
session_start();

// 🔹 Inclusion des fichiers de configuration
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // Fichier qui contient $pdo (connexion PDO)

// 🔒 Vérification de la session (protection)
if (!isset($_SESSION['user'])) {
    header('Location:' . URL_AUTHENTIFICATION);
    exit;
}

$message = "";

// 🔹 Fonction d’upload améliorée
function uploadImage($fileKey, $targetDir)
{
    if (!isset($_FILES[$fileKey]) || empty($_FILES[$fileKey]['name'])) {
        return null;
    }

    $error = $_FILES[$fileKey]['error'];
    if ($error !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => "Le fichier $fileKey dépasse la taille autorisée par le serveur.",
            UPLOAD_ERR_FORM_SIZE => "Le fichier $fileKey dépasse la taille maximale du formulaire.",
            UPLOAD_ERR_PARTIAL => "Le fichier $fileKey n’a été que partiellement téléchargé.",
            UPLOAD_ERR_NO_FILE => "Aucun fichier fourni pour $fileKey.",
            UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant sur le serveur.",
            UPLOAD_ERR_CANT_WRITE => "Erreur d’écriture sur le disque pour $fileKey.",
            UPLOAD_ERR_EXTENSION => "Une extension PHP a bloqué l’upload de $fileKey."
        ];
        echo "<div class='alert alert-warning'>" . ($errorMessages[$error] ?? "Erreur inconnue : $error") . "</div>";
        return null;
    }

    // Nettoyage du nom du fichier
    $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '_', basename($_FILES[$fileKey]['name']));
    $targetFile = $targetDir . $fileName;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetFile)) {
        return $fileName;
    } else {
        echo "<div class='alert alert-danger'>Impossible de déplacer le fichier $fileKey.</div>";
        return null;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 🔹 Récupération des champs
        $titre = htmlspecialchars(trim($_POST['titre'] ?? ''));
        $auteur = htmlspecialchars(trim($_POST['auteur'] ?? ''));
        $date_pub = $_POST['date_pub'] ?? date('Y-m-d');
        $commentaire = htmlspecialchars(trim($_POST['commentaire'] ?? ''));
        $nbrVues = (int)($_POST['nbrVues'] ?? 0);
        $messageFort = htmlspecialchars(trim($_POST['messageFort'] ?? ''));

        // 🔹 Paragraphes
        $paragraphes = [];
        for ($i = 1; $i <= 10; $i++) {
            $paragraphes["paraph$i"] = htmlspecialchars(trim($_POST["paraph$i"] ?? ''));
        }

        // 🔹 Upload des images
        $targetDir = __DIR__ . '/../img/actualites/';
        $imgMise = uploadImage('imgMise', $targetDir);
        $imgPub1 = uploadImage('imgPub1', $targetDir);
        $imgPub2 = uploadImage('imgPub2', $targetDir);

        // 🔹 Préparation de la requête d’insertion
        $sql = "INSERT INTO actualites (
                    titre, auteur, date_pub, commentaire, nbrVues,
                    imgMise, imgPub1, imgPub2, messageFort,
                    paraph1, paraph2, paraph3, paraph4, paraph5,
                    paraph6, paraph7, paraph8, paraph9, paraph10,
                    date_creation
                ) VALUES (
                    :titre, :auteur, :date_pub, :commentaire, :nbrVues,
                    :imgMise, :imgPub1, :imgPub2, :messageFort,
                    :paraph1, :paraph2, :paraph3, :paraph4, :paraph5,
                    :paraph6, :paraph7, :paraph8, :paraph9, :paraph10,
                    NOW()
                )";

        $stmt = $pdo->prepare($sql);

        $ok = $stmt->execute([
            ':titre' => $titre,
            ':auteur' => $auteur,
            ':date_pub' => $date_pub,
            ':commentaire' => $commentaire,
            ':nbrVues' => $nbrVues,
            ':imgMise' => $imgMise,
            ':imgPub1' => $imgPub1,
            ':imgPub2' => $imgPub2,
            ':messageFort' => $messageFort,
            ':paraph1' => $paragraphes['paraph1'],
            ':paraph2' => $paragraphes['paraph2'],
            ':paraph3' => $paragraphes['paraph3'],
            ':paraph4' => $paragraphes['paraph4'],
            ':paraph5' => $paragraphes['paraph5'],
            ':paraph6' => $paragraphes['paraph6'],
            ':paraph7' => $paragraphes['paraph7'],
            ':paraph8' => $paragraphes['paraph8'],
            ':paraph9' => $paragraphes['paraph9'],
            ':paraph10' => $paragraphes['paraph10']
        ]);


        if ($message = $ok) {
            "<div class='alert alert-success'>✅ Documentation ajoutée avec succès.</div>";
            
        } else {
             "<div class='alert alert-danger'>❌ Une erreur est survenue lors de l’ajout.</div>";
        }
        

    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une actualité</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold">📰 Admin – Actualités 1325</span>
    <div class="ms-auto">
        <a href="<?= URL_ADDSPACEADMIN; ?>" class="btn btn-outline-light me-2">MENU ADMIN</a>
        <a href="<?= URL_ADMINISTRATEUR; ?>" class="btn btn-outline-light me-2">📋 Voir toutes les actualités</a>
        <a href="<?= URL_LOGOUT; ?>" class="btn btn-danger">Déconnexion</a>
    </div>
  </div>
</nav>

<div class="container py-5">
    <div class="card shadow-lg p-4 border-0">
        <h4 class="mb-4 text-primary fw-semibold">Ajouter une nouvelle actualité</h4>
        <?= $message ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Titre</label>
                    <input type="text" name="titre" class="form-control" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Auteur</label>
                    <input type="text" name="auteur" class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Date de publication</label>
                    <input type="date" name="date_pub" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Message fort</label>
                <textarea name="messageFort" class="form-control" rows="2"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Commentaire général</label>
                <textarea name="commentaire" class="form-control" rows="3"></textarea>
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
                <input type="number" name="nbrVues" class="form-control" min="0" placeholder="Ex: 0">
            </div>

            <hr>
            <h5 class="text-primary mt-4 mb-3">🧩 Paragraphes du contenu</h5>
            <div class="row">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Paragraphe <?= $i ?></label>
                        <textarea name="paraph<?= $i ?>" class="form-control" rows="2"></textarea>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary px-4">💾 Enregistrer</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
