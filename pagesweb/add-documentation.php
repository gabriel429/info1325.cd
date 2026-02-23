<?php
session_start();

// 🔹 Inclusion des fichiers nécessaires
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // contient $pdo
require_once __DIR__ . '/csrf_helper.php';
require_once __DIR__ . '/upload_helper.php';

// 🔒 Protection d'accès
if (!isset($_SESSION['user'])) {
    header('Location:' . URL_AUTHENTIFICATION);
    exit;
}

$message = "";

// 🔹 Ajout ou mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    try {
        $action = $_POST['action'] ?? 'create';
        $titreDoc = trim($_POST['titreDoc']);
        $auteur = trim($_POST['auteur']);
        $anneePub = $_POST['anneePub'] ?: null;
        $datePub = $_POST['datePub'];
        $pan = isset($_POST['pan']) ? (int)$_POST['pan'] : 0;

        $targetDir = __DIR__ . '/../img/documentations/';

        if ($action === 'update') {
            // MODE MODIFICATION
            $id = (int)$_POST['doc_id'];

            // Récupérer les fichiers existants
            $stmt = $pdo->prepare("SELECT img, fichier_pdf FROM documentations WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            // Upload nouveaux fichiers ou garder les anciens
            $imgFile = uploadFile('img', $targetDir, ['image/jpeg', 'image/png', 'image/webp', 'image/jpg']) ?? $existing['img'];
            $pdfFile = uploadFile('fichier_pdf', $targetDir, ['application/pdf']) ?? $existing['fichier_pdf'];

            $stmt = $pdo->prepare("UPDATE documentations
                    SET titreDoc=:titreDoc, auteur=:auteur, anneePub=:anneePub,
                        datePub=:datePub, pan=:pan, img=:img, fichier_pdf=:fichier_pdf
                    WHERE id=:id");

            $stmt->execute([
                ':titreDoc' => $titreDoc,
                ':auteur' => $auteur,
                ':anneePub' => $anneePub,
                ':datePub' => $datePub,
                ':pan' => $pan,
                ':img' => $imgFile,
                ':fichier_pdf' => $pdfFile,
                ':id' => $id
            ]);

            $message = "<div class='alert alert-success alert-dismissible fade show'><i class='bi bi-check-circle-fill me-2'></i><strong>Succès!</strong> La documentation a été modifiée avec succès.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } else {
            // MODE CRÉATION
            $imgFile = uploadFile('img', $targetDir, ['image/jpeg', 'image/png', 'image/webp', 'image/jpg']);
            $pdfFile = uploadFile('fichier_pdf', $targetDir, ['application/pdf']);

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

            $message = "<div class='alert alert-success alert-dismissible fade show'><i class='bi bi-check-circle-fill me-2'></i><strong>Succès!</strong> La documentation a été ajoutée avec succès.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger alert-dismissible fade show'><i class='bi bi-exclamation-triangle-fill me-2'></i><strong>Erreur:</strong> " . htmlspecialchars($e->getMessage()) . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

// 🔹 Suppression
if (isset($_GET['delete'])) {
    try {
        $id = (int)$_GET['delete'];
        $stmt = $pdo->prepare("DELETE FROM documentations WHERE id=:id");
        $stmt->execute([':id' => $id]);
        $message = "<div class='alert alert-warning alert-dismissible fade show'><i class='bi bi-trash-fill me-2'></i>Documentation supprimée avec succès.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger alert-dismissible fade show'><i class='bi bi-exclamation-triangle-fill me-2'></i>Erreur lors de la suppression.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}
<?php
$pageTitle  = 'Gestion des documentations';
$breadcrumb = [['label' => 'Documentation']];
$activePage = 'documentation';
require_once __DIR__ . '/admin_layout_top.php';
?>

<style>
.preview-container { position: relative; margin-top: 10px; }
.preview-image { max-width: 100%; max-height: 200px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,.1); }
.remove-preview { position: absolute; top: 5px; right: 5px; background: rgba(220,53,69,.9); color: #fff; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; }
.remove-preview:hover { background: #dc3545; }
.file-info { font-size: .85rem; color: #6c757d; margin-top: 5px; }
.doc-card { transition: transform .2s; }
.doc-card:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,.1) !important; }
</style>

<div class="page-header">
    <div>
        <h1><i class="bi bi-file-earmark-text me-2" style="color:var(--accent)"></i>Gestion des documentations</h1>
        <p>Ajouter, modifier et supprimer les documents</p>
    </div>
</div>

<div class="admin-card mb-4">
    <div class="card-header">
        <i class="bi bi-file-earmark-plus me-2" style="color:var(--accent)"></i>Ajouter une documentation
    </div>
    <div class="card-body p-4">
            <?= $message ?>
            <form method="POST" enctype="multipart/form-data" id="docForm">
                <?= csrf_field() ?>

                <!-- Section 1: Informations de base -->
                <div class="section-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informations de base</h5>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="bi bi-file-text"></i> Titre de la documentation <span class="required">*</span>
                        </label>
                        <input type="text"
                               name="titreDoc"
                               id="titreDoc"
                               class="form-control"
                               required
                               maxlength="200"
                               placeholder="Ex: Plan d'Action National 2023-2025">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">
                            <i class="bi bi-person"></i> Auteur
                        </label>
                        <input type="text"
                               name="auteur"
                               class="form-control"
                               value="<?= htmlspecialchars($_SESSION['user'] ?? '') ?>"
                               placeholder="Nom de l'auteur">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">
                            <i class="bi bi-calendar3"></i> Année de publication
                        </label>
                        <input type="number"
                               name="anneePub"
                               class="form-control"
                               min="1900"
                               max="2099"
                               value="<?= date('Y') ?>"
                               placeholder="<?= date('Y') ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <i class="bi bi-calendar-event"></i> Date de publication <span class="required">*</span>
                        </label>
                        <input type="date"
                               name="datePub"
                               class="form-control"
                               value="<?= date('Y-m-d') ?>"
                               required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <i class="bi bi-flag"></i> Type PAN
                        </label>
                        <select name="pan" class="form-select">
                            <option value="0">Non-PAN</option>
                            <option value="1">PAN (Plan d'Action National)</option>
                        </select>
                    </div>
                </div>

                <!-- Section 2: Fichiers -->
                <div class="section-header mt-4">
                    <h5 class="mb-0"><i class="bi bi-folder2-open"></i> Fichiers</h5>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="bi bi-image"></i> Image de couverture
                        </label>
                        <input type="file"
                               name="img"
                               id="img"
                               class="form-control"
                               accept="image/jpeg,image/png,image/jpg,image/webp"
                               onchange="previewImage(this, 'preview-img')">
                        <div class="file-info">Formats acceptés: JPG, PNG, WEBP (Max: 5 MB)</div>
                        <div id="preview-img" class="preview-container"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="bi bi-file-earmark-pdf"></i> Fichier PDF
                        </label>
                        <input type="file"
                               name="fichier_pdf"
                               id="fichier_pdf"
                               class="form-control"
                               accept="application/pdf"
                               onchange="previewPDF(this)">
                        <div class="file-info">Format accepté: PDF (Max: 20 MB)</div>
                        <div id="pdf-info" class="alert alert-info mt-2 d-none">
                            <i class="bi bi-file-pdf"></i> <span id="pdf-name"></span>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <hr class="mt-4 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <button type="reset" class="btn btn-outline-secondary" onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser le formulaire ?')">
                        <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        <i class="bi bi-check-circle"></i> Enregistrer la documentation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 📋 Liste des documentations existantes -->
    <div class="card shadow-lg mt-5 border-0">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="bi bi-files"></i> Documentations publiées</h4>
        </div>
        <div class="card-body p-4">
            <?php
            try {
                // Récupérer toutes les documentations
                $stmt = $pdo->query("SELECT * FROM documentations ORDER BY datePub DESC");
                $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($docs) > 0): ?>
                    <div class="row">
                        <?php foreach ($docs as $doc): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card doc-card shadow-sm h-100">
                                    <?php if (!empty($doc['img'])): ?>
                                        <img src="<?= IMG_DIR ?>documentations/<?= htmlspecialchars($doc['img']) ?>"
                                             class="card-img-top"
                                             style="height: 200px; object-fit: cover;"
                                             alt="<?= htmlspecialchars($doc['titreDoc']) ?>">
                                    <?php else: ?>
                                        <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center"
                                             style="height: 200px; background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%);">
                                            <i class="bi bi-file-earmark-text text-white" style="font-size: 80px;"></i>
                                        </div>
                                    <?php endif; ?>

                                    <div class="card-body">
                                        <?php if ($doc['pan'] == 1): ?>
                                            <span class="badge bg-warning text-dark mb-2">
                                                <i class="bi bi-star-fill"></i> PAN
                                            </span>
                                        <?php endif; ?>

                                        <h5 class="card-title">
                                            <?= htmlspecialchars($doc['titreDoc']) ?>
                                        </h5>

                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-person"></i> <?= htmlspecialchars($doc['auteur'] ?? 'N/A') ?><br>
                                                <i class="bi bi-calendar"></i> <?= date('d/m/Y', strtotime($doc['datePub'])) ?>
                                                <?php if ($doc['anneePub']): ?>
                                                    (<?= htmlspecialchars($doc['anneePub']) ?>)
                                                <?php endif; ?>
                                            </small>
                                        </p>
                                    </div>

                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100" role="group">
                                            <?php if (!empty($doc['fichier_pdf'])): ?>
                                                <a href="<?= IMG_DIR ?>documentations/<?= htmlspecialchars($doc['fichier_pdf']) ?>"
                                                   class="btn btn-sm btn-outline-primary"
                                                   target="_blank"
                                                   title="Télécharger PDF">
                                                    <i class="bi bi-file-pdf"></i> PDF
                                                </a>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-outline-warning editDocBtn"
                                                    title="Modifier"
                                                    data-doc='<?= htmlspecialchars(json_encode($doc), ENT_QUOTES, 'UTF-8') ?>'>
                                                <i class="bi bi-pencil"></i> Modifier
                                            </button>
                                            <a href="?delete=<?= $doc['id'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               title="Supprimer"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette documentation ?')">
                                                <i class="bi bi-trash"></i> Supprimer
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="alert alert-info mt-4">
                        <i class="bi bi-info-circle"></i>
                        <strong><?= count($docs) ?></strong> documentation(s) disponible(s).
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        Aucune documentation disponible pour le moment.
                    </div>
                <?php endif;

            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>Erreur lors du chargement des documentations : " . htmlspecialchars($e->getMessage()) . "</div>";
            }
            ?>
        </div>
</div>

<!-- Modal de modification -->
<div class="modal fade" id="editDocModal" tabindex="-1" aria-labelledby="editDocModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editDocModalLabel">
                    <i class="bi bi-pencil-square"></i> Modifier la documentation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data" id="editDocForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="doc_id" id="edit_doc_id">

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                <i class="bi bi-file-text"></i> Titre <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="titreDoc" id="edit_titreDoc" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-person"></i> Auteur
                            </label>
                            <input type="text" name="auteur" id="edit_auteur" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-calendar3"></i> Année
                            </label>
                            <input type="number" name="anneePub" id="edit_anneePub" class="form-control" min="1900" max="2099">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-calendar-event"></i> Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="datePub" id="edit_datePub" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-flag"></i> Type
                            </label>
                            <select name="pan" id="edit_pan" class="form-select">
                                <option value="0">Non-PAN</option>
                                <option value="1">PAN</option>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Laissez vide pour garder les fichiers actuels
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-image"></i> Nouvelle image
                            </label>
                            <input type="file" name="img" class="form-control" accept="image/*">
                            <small class="text-muted" id="edit_img_current"></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-file-pdf"></i> Nouveau PDF
                            </label>
                            <input type="file" name="fichier_pdf" class="form-control" accept="application/pdf">
                            <small class="text-muted" id="edit_pdf_current"></small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Prévisualisation image
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

// Prévisualisation PDF
function previewPDF(input) {
    const pdfInfo = document.getElementById('pdf-info');
    const pdfName = document.getElementById('pdf-name');

    if (input.files && input.files[0]) {
        pdfName.textContent = input.files[0].name;
        pdfInfo.classList.remove('d-none');
    } else {
        pdfInfo.classList.add('d-none');
    }
}

// Gestion modification
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.editDocBtn');

    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const doc = JSON.parse(this.getAttribute('data-doc'));

            document.getElementById('edit_doc_id').value = doc.id;
            document.getElementById('edit_titreDoc').value = doc.titreDoc || '';
            document.getElementById('edit_auteur').value = doc.auteur || '';
            document.getElementById('edit_anneePub').value = doc.anneePub || '';
            document.getElementById('edit_datePub').value = doc.datePub || '';
            document.getElementById('edit_pan').value = doc.pan || 0;

            document.getElementById('edit_img_current').textContent = doc.img ? 'Actuelle: ' + doc.img : 'Aucune';
            document.getElementById('edit_pdf_current').textContent = doc.fichier_pdf ? 'Actuel: ' + doc.fichier_pdf : 'Aucun';

            const editModal = new bootstrap.Modal(document.getElementById('editDocModal'));
            editModal.show();
        });
    });

    // Indicateur de chargement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement...';
            }
        });
    });

    // Auto-scroll vers message
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0) {
        alerts[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});
</script>

<?php require_once __DIR__ . '/admin_layout_bottom.php'; ?>
