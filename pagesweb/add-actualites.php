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
        $action = $_POST['action'] ?? 'create';

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

        if ($action === 'update') {
            // MODE MODIFICATION
            $actu_id = (int)($_POST['actu_id'] ?? 0);

            // Récupérer les images existantes
            $stmt = $pdo->prepare("SELECT imgMise, imgPub1, imgPub2 FROM actualites WHERE id = :id");
            $stmt->execute([':id' => $actu_id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            // Upload nouvelles images ou garder les anciennes
            $imgMise = uploadImage('imgMise', $targetDir) ?? $existing['imgMise'];
            $imgPub1 = uploadImage('imgPub1', $targetDir) ?? $existing['imgPub1'];
            $imgPub2 = uploadImage('imgPub2', $targetDir) ?? $existing['imgPub2'];

            // Requête de mise à jour
            $sql = "UPDATE actualites SET
                        titre = :titre,
                        auteur = :auteur,
                        date_pub = :date_pub,
                        commentaire = :commentaire,
                        nbrVues = :nbrVues,
                        imgMise = :imgMise,
                        imgPub1 = :imgPub1,
                        imgPub2 = :imgPub2,
                        messageFort = :messageFort,
                        paraph1 = :paraph1,
                        paraph2 = :paraph2,
                        paraph3 = :paraph3,
                        paraph4 = :paraph4,
                        paraph5 = :paraph5,
                        paraph6 = :paraph6,
                        paraph7 = :paraph7,
                        paraph8 = :paraph8,
                        paraph9 = :paraph9,
                        paraph10 = :paraph10
                    WHERE id = :id";

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
                ':paraph10' => $paragraphes['paraph10'],
                ':id' => $actu_id
            ]);

            if ($ok) {
                $message = "<div class='alert alert-success alert-dismissible fade show'><i class='bi bi-check-circle-fill me-2'></i><strong>Succès!</strong> L'actualité a été modifiée avec succès.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
            } else {
                $message = "<div class='alert alert-danger alert-dismissible fade show'><i class='bi bi-x-circle-fill me-2'></i><strong>Erreur!</strong> Une erreur est survenue lors de la modification.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
            }

        } else {
            // MODE CRÉATION
            $imgMise = uploadImage('imgMise', $targetDir);
            $imgPub1 = uploadImage('imgPub1', $targetDir);
            $imgPub2 = uploadImage('imgPub2', $targetDir);

            // 🔹 Préparation de la requête d'insertion
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


            if ($ok) {
                $message = "<div class='alert alert-success alert-dismissible fade show'><i class='bi bi-check-circle-fill me-2'></i><strong>Succès!</strong> L'actualité a été ajoutée avec succès.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
            } else {
                $message = "<div class='alert alert-danger alert-dismissible fade show'><i class='bi bi-x-circle-fill me-2'></i><strong>Erreur!</strong> Une erreur est survenue lors de l'ajout.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
            }
        }

    } catch (Exception $e) {
        $message = "<div class='alert alert-danger alert-dismissible fade show'><i class='bi bi-exclamation-triangle-fill me-2'></i><strong>Erreur:</strong> " . htmlspecialchars($e->getMessage()) . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une actualité</title>
    <link rel="stylesheet" href="<?= CSS_DIR ?>bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .preview-container {
            position: relative;
            margin-top: 10px;
        }
        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .remove-preview {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .remove-preview:hover {
            background: #dc3545;
        }
        .char-counter {
            font-size: 0.85rem;
            color: #6c757d;
            float: right;
        }
        .char-counter.warning {
            color: #fd7e14;
            font-weight: bold;
        }
        .char-counter.danger {
            color: #dc3545;
            font-weight: bold;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .form-label .required {
            color: #dc3545;
        }
        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .collapse-toggle {
            cursor: pointer;
            transition: all 0.3s;
        }
        .collapse-toggle:hover {
            background-color: #f8f9fa;
        }
        .paragraph-section {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: #f8f9fa;
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold">📰 Admin – Gestion des Actualités</span>
    <div class="ms-auto">
        <a href="<?= URL_ADDSPACEADMIN; ?>" class="btn btn-outline-light me-2">MENU ADMIN</a>
        <a href="<?= URL_LOGOUT; ?>" class="btn btn-danger">Déconnexion</a>
    </div>
  </div>
</nav>

<div class="container py-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Ajouter une nouvelle actualité</h4>
        </div>
        <div class="card-body p-4">
            <?= $message ?>
            <form method="POST" enctype="multipart/form-data" id="newsForm">

                <!-- Section 1: Informations de base -->
                <div class="section-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informations de base</h5>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="bi bi-card-heading"></i> Titre <span class="required">*</span>
                        </label>
                        <input type="text"
                               name="titre"
                               id="titre"
                               class="form-control"
                               required
                               maxlength="200"
                               placeholder="Titre accrocheur de l'actualité">
                        <span class="char-counter" id="titre-counter">0/200</span>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">
                            <i class="bi bi-person"></i> Auteur
                        </label>
                        <input type="text"
                               name="auteur"
                               class="form-control"
                               value="<?= htmlspecialchars($_SESSION['user'] ?? 'Admin') ?>"
                               placeholder="Nom de l'auteur">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">
                            <i class="bi bi-calendar-event"></i> Date de publication <span class="required">*</span>
                        </label>
                        <input type="date"
                               name="date_pub"
                               class="form-control"
                               value="<?= date('Y-m-d') ?>"
                               required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-exclamation-diamond"></i> Message fort / Citation
                    </label>
                    <textarea name="messageFort"
                              id="messageFort"
                              class="form-control"
                              rows="2"
                              maxlength="300"
                              placeholder="Une phrase clé ou citation qui résume l'actualité"></textarea>
                    <span class="char-counter" id="messageFort-counter">0/300</span>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-chat-left-text"></i> Résumé / Introduction
                    </label>
                    <textarea name="commentaire"
                              id="commentaire"
                              class="form-control"
                              rows="4"
                              maxlength="500"
                              placeholder="Description courte qui apparaîtra dans les prévisualisations"></textarea>
                    <span class="char-counter" id="commentaire-counter">0/500</span>
                </div>

                <!-- Section 2: Images -->
                <div class="section-header mt-4">
                    <h5 class="mb-0"><i class="bi bi-images"></i> Images</h5>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <i class="bi bi-image"></i> Image principale
                        </label>
                        <input type="file"
                               name="imgMise"
                               id="imgMise"
                               class="form-control"
                               accept="image/*"
                               onchange="previewImage(this, 'preview-imgMise')">
                        <small class="text-muted">Taille recommandée: 1200x800px</small>
                        <div id="preview-imgMise" class="preview-container"></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <i class="bi bi-image"></i> Image secondaire 1
                        </label>
                        <input type="file"
                               name="imgPub1"
                               id="imgPub1"
                               class="form-control"
                               accept="image/*"
                               onchange="previewImage(this, 'preview-imgPub1')">
                        <small class="text-muted">Optionnelle</small>
                        <div id="preview-imgPub1" class="preview-container"></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <i class="bi bi-image"></i> Image secondaire 2
                        </label>
                        <input type="file"
                               name="imgPub2"
                               id="imgPub2"
                               class="form-control"
                               accept="image/*"
                               onchange="previewImage(this, 'preview-imgPub2')">
                        <small class="text-muted">Optionnelle</small>
                        <div id="preview-imgPub2" class="preview-container"></div>
                    </div>
                </div>

                <!-- Section 3: Contenu détaillé -->
                <div class="section-header mt-4">
                    <h5 class="mb-0">
                        <i class="bi bi-file-text"></i> Contenu détaillé de l'article
                    </h5>
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-lightbulb"></i> <strong>Astuce:</strong> Remplissez uniquement les paragraphes nécessaires. Vous pouvez en laisser vides.
                </div>

                <div class="accordion" id="paragraphsAccordion">
                    <?php for ($i = 1; $i <= 10; $i++):
                        $isFirst = $i === 1;
                    ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button <?= $isFirst ? '' : 'collapsed' ?>"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse<?= $i ?>">
                                    <i class="bi bi-paragraph me-2"></i> Paragraphe <?= $i ?>
                                </button>
                            </h2>
                            <div id="collapse<?= $i ?>"
                                 class="accordion-collapse collapse <?= $isFirst ? 'show' : '' ?>"
                                 data-bs-parent="#paragraphsAccordion">
                                <div class="accordion-body">
                                    <textarea name="paraph<?= $i ?>"
                                              id="paraph<?= $i ?>"
                                              class="form-control"
                                              rows="4"
                                              maxlength="2000"
                                              placeholder="Contenu du paragraphe <?= $i ?>..."></textarea>
                                    <span class="char-counter" id="paraph<?= $i ?>-counter">0/2000</span>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

                <!-- Section 4: Options avancées -->
                <div class="section-header mt-4">
                    <h5 class="mb-0"><i class="bi bi-sliders"></i> Options avancées</h5>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="bi bi-eye"></i> Nombre de vues initial
                        </label>
                        <input type="number"
                               name="nbrVues"
                               class="form-control"
                               min="0"
                               value="0"
                               placeholder="0">
                        <small class="text-muted">Laissez à 0 pour un nouveau compteur</small>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <hr class="mt-4 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <button type="reset" class="btn btn-outline-secondary" onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser le formulaire ?')">
                        <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-check-circle"></i> Publier l'actualité
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 📋 Liste des actualités existantes -->
    <div class="card shadow-lg mt-5 p-4 border-0">
        <h4 class="mb-4 text-success fw-semibold">📋 Liste des actualités publiées</h4>

        <?php
        try {
            // Récupérer toutes les actualités avec tri par date de création décroissante
            $stmt = $pdo->query("SELECT * FROM actualites ORDER BY date_creation DESC");
            $actualites = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($actualites) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 5%">ID</th>
                                <th style="width: 10%">Image</th>
                                <th style="width: 30%">Titre</th>
                                <th style="width: 15%">Auteur</th>
                                <th style="width: 12%">Date pub.</th>
                                <th style="width: 8%">Vues</th>
                                <th style="width: 12%">Créé le</th>
                                <th style="width: 8%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($actualites as $actu): ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars($actu['id']) ?></strong></td>
                                    <td>
                                        <?php if (!empty($actu['imgMise']) && file_exists(__DIR__ . '/../img/actualites/' . $actu['imgMise'])): ?>
                                            <img src="<?= URL_IMG_ACTU . htmlspecialchars($actu['imgMise']) ?>"
                                                 alt="Image"
                                                 class="img-thumbnail"
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
                                                 style="width: 60px; height: 60px; font-size: 24px;">
                                                📰
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($actu['titre']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($actu['auteur'] ?? 'N/A') ?></td>
                                    <td><?= date('d/m/Y', strtotime($actu['date_pub'])) ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            👁️ <?= number_format($actu['nbrVues']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($actu['date_creation'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?= URL_ACTUALITES ?>?id=<?= $actu['id'] ?>"
                                               class="btn btn-outline-primary"
                                               title="Voir l'actualité"
                                               target="_blank">
                                                👁️
                                            </a>
                                            <button class="btn btn-outline-warning editBtn"
                                                    title="Modifier"
                                                    data-actu='<?= htmlspecialchars(json_encode($actu), ENT_QUOTES, 'UTF-8') ?>'>
                                                ✏️
                                            </button>
                                            <a href="<?= URL_ADMINISTRATEUR ?>?delete=<?= $actu['id'] ?>"
                                               class="btn btn-outline-danger"
                                               title="Supprimer"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?')">
                                                🗑️
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong><?= count($actualites) ?></strong> actualité(s) publiée(s) au total.
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    Aucune actualité publiée pour le moment.
                </div>
            <?php endif;

        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erreur lors du chargement des actualités : " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        ?>
    </div>
</div>

<!-- Modal de modification -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editModalLabel">
                    <i class="bi bi-pencil-square"></i> Modifier l'actualité
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data" id="editForm">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="actu_id" id="edit_id">

                    <!-- Informations de base -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-card-heading"></i> Titre <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="titre" id="edit_titre" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                <i class="bi bi-person"></i> Auteur
                            </label>
                            <input type="text" name="auteur" id="edit_auteur" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                <i class="bi bi-calendar-event"></i> Date de publication <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date_pub" id="edit_date_pub" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-exclamation-diamond"></i> Message fort / Citation
                        </label>
                        <textarea name="messageFort" id="edit_messageFort" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-chat-left-text"></i> Résumé / Introduction
                        </label>
                        <textarea name="commentaire" id="edit_commentaire" class="form-control" rows="3"></textarea>
                    </div>

                    <!-- Images -->
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Laissez vide pour garder les images actuelles
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="bi bi-image"></i> Image principale
                            </label>
                            <input type="file" name="imgMise" class="form-control" accept="image/*">
                            <small class="text-muted" id="edit_img_current"></small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="bi bi-image"></i> Image secondaire 1
                            </label>
                            <input type="file" name="imgPub1" class="form-control" accept="image/*">
                            <small class="text-muted" id="edit_img1_current"></small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="bi bi-image"></i> Image secondaire 2
                            </label>
                            <input type="file" name="imgPub2" class="form-control" accept="image/*">
                            <small class="text-muted" id="edit_img2_current"></small>
                        </div>
                    </div>

                    <!-- Paragraphes -->
                    <div class="accordion" id="editParagraphsAccordion">
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#editCollapse<?= $i ?>">
                                        <i class="bi bi-paragraph me-2"></i> Paragraphe <?= $i ?>
                                    </button>
                                </h2>
                                <div id="editCollapse<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#editParagraphsAccordion">
                                    <div class="accordion-body">
                                        <textarea name="paraph<?= $i ?>" id="edit_paraph<?= $i ?>" class="form-control" rows="4"></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <!-- Options avancées -->
                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-eye"></i> Nombre de vues
                            </label>
                            <input type="number" name="nbrVues" id="edit_nbrVues" class="form-control" min="0">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scripts personnalisés -->
<script>
// Prévisualisation des images
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            const container = document.createElement('div');
            container.className = 'preview-container';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'preview-image';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-preview';
            removeBtn.innerHTML = '&times;';
            removeBtn.onclick = function() {
                preview.innerHTML = '';
                input.value = '';
            };

            container.appendChild(img);
            container.appendChild(removeBtn);
            preview.appendChild(container);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

// Compteurs de caractères
function updateCharCounter(textareaId, counterId, maxLength) {
    const textarea = document.getElementById(textareaId);
    const counter = document.getElementById(counterId);

    if (!textarea || !counter) return;

    textarea.addEventListener('input', function() {
        const currentLength = this.value.length;
        counter.textContent = currentLength + '/' + maxLength;

        // Changer la couleur selon le nombre de caractères
        counter.classList.remove('warning', 'danger');
        if (currentLength > maxLength * 0.9) {
            counter.classList.add('danger');
        } else if (currentLength > maxLength * 0.75) {
            counter.classList.add('warning');
        }
    });
}

// Initialiser les compteurs
document.addEventListener('DOMContentLoaded', function() {
    // Compteurs pour les champs principaux
    updateCharCounter('titre', 'titre-counter', 200);
    updateCharCounter('messageFort', 'messageFort-counter', 300);
    updateCharCounter('commentaire', 'commentaire-counter', 500);

    // Compteurs pour les paragraphes
    for (let i = 1; i <= 10; i++) {
        updateCharCounter('paraph' + i, 'paraph' + i + '-counter', 2000);
    }

    // Validation du formulaire avant soumission
    const form = document.getElementById('newsForm');
    form.addEventListener('submit', function(e) {
        const titre = document.getElementById('titre').value.trim();

        if (titre.length < 10) {
            e.preventDefault();
            alert('Le titre doit contenir au moins 10 caractères.');
            document.getElementById('titre').focus();
            return false;
        }

        // Afficher un indicateur de chargement
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Publication en cours...';
    });

    // Auto-scroll vers le message après soumission
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0) {
        alerts[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});

// Sauvegarde automatique dans localStorage (brouillon)
let autoSaveTimer;
function autoSave() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(function() {
        const formData = {
            titre: document.getElementById('titre').value,
            auteur: document.querySelector('input[name="auteur"]').value,
            messageFort: document.getElementById('messageFort').value,
            commentaire: document.getElementById('commentaire').value,
            timestamp: new Date().toISOString()
        };

        // Sauvegarder les paragraphes remplis
        for (let i = 1; i <= 10; i++) {
            const paraph = document.getElementById('paraph' + i);
            if (paraph && paraph.value.trim()) {
                formData['paraph' + i] = paraph.value;
            }
        }

        localStorage.setItem('newsFormDraft', JSON.stringify(formData));
        console.log('Brouillon sauvegardé automatiquement');
    }, 3000); // Sauvegarder 3 secondes après la dernière modification
}

// Restaurer le brouillon si disponible
document.addEventListener('DOMContentLoaded', function() {
    const draft = localStorage.getItem('newsFormDraft');
    if (draft) {
        const confirm = window.confirm('Un brouillon non publié a été trouvé. Voulez-vous le restaurer ?');
        if (confirm) {
            const formData = JSON.parse(draft);
            document.getElementById('titre').value = formData.titre || '';
            document.querySelector('input[name="auteur"]').value = formData.auteur || '';
            document.getElementById('messageFort').value = formData.messageFort || '';
            document.getElementById('commentaire').value = formData.commentaire || '';

            // Restaurer les paragraphes
            for (let i = 1; i <= 10; i++) {
                if (formData['paraph' + i]) {
                    document.getElementById('paraph' + i).value = formData['paraph' + i];
                }
            }

            // Mettre à jour tous les compteurs
            document.querySelectorAll('textarea, input[type="text"]').forEach(el => {
                if (el.id) {
                    const event = new Event('input', { bubbles: true });
                    el.dispatchEvent(event);
                }
            });
        } else {
            localStorage.removeItem('newsFormDraft');
        }
    }

    // Activer la sauvegarde automatique
    const fields = ['titre', 'auteur', 'messageFort', 'commentaire'];
    fields.forEach(fieldId => {
        const el = document.getElementById(fieldId) || document.querySelector('input[name="' + fieldId + '"]');
        if (el) {
            el.addEventListener('input', autoSave);
        }
    });

    for (let i = 1; i <= 10; i++) {
        const paraph = document.getElementById('paraph' + i);
        if (paraph) {
            paraph.addEventListener('input', autoSave);
        }
    }
});

// Supprimer le brouillon après publication réussie
<?php if (!empty($message) && strpos($message, 'succès') !== false): ?>
    localStorage.removeItem('newsFormDraft');
    console.log('Brouillon supprimé après publication');
<?php endif; ?>

// Gestion du bouton d'édition
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.editBtn');

    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const actu = JSON.parse(this.getAttribute('data-actu'));

            // Remplir les champs du modal
            document.getElementById('edit_id').value = actu.id;
            document.getElementById('edit_titre').value = actu.titre || '';
            document.getElementById('edit_auteur').value = actu.auteur || '';
            document.getElementById('edit_date_pub').value = actu.date_pub || '';
            document.getElementById('edit_messageFort').value = actu.messageFort || '';
            document.getElementById('edit_commentaire').value = actu.commentaire || '';
            document.getElementById('edit_nbrVues').value = actu.nbrVues || 0;

            // Afficher les images actuelles
            document.getElementById('edit_img_current').textContent = actu.imgMise ? 'Actuelle: ' + actu.imgMise : 'Aucune';
            document.getElementById('edit_img1_current').textContent = actu.imgPub1 ? 'Actuelle: ' + actu.imgPub1 : 'Aucune';
            document.getElementById('edit_img2_current').textContent = actu.imgPub2 ? 'Actuelle: ' + actu.imgPub2 : 'Aucune';

            // Remplir les paragraphes
            for (let i = 1; i <= 10; i++) {
                const paraphField = document.getElementById('edit_paraph' + i);
                if (paraphField) {
                    paraphField.value = actu['paraph' + i] || '';
                }
            }

            // Ouvrir le modal
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        });
    });

    // Indicateur de chargement pour le formulaire d'édition
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', function() {
            const submitBtn = editForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement...';
        });
    }
});
</script>

</body>
</html>
