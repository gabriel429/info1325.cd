<?php
session_start();

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect;
require_once __DIR__ . '/csrf_helper.php';
require_once __DIR__ . '/upload_helper.php';
require_once __DIR__ . '/actualites_helper.php';

if (!isset($_SESSION['user'])) {
    header('Location:' . URL_AUTHENTIFICATION);
    exit;
}

ensure_actualites_schema($pdo);

function news_editor_status_label(string $status): string
{
    return [
        'brouillon' => 'Brouillon',
        'publie' => 'Publié',
        'archive' => 'Archivé',
    ][$status] ?? 'Brouillon';
}

function news_editor_status_badge(string $status): string
{
    return [
        'brouillon' => 'bg-secondary',
        'publie' => 'bg-success',
        'archive' => 'bg-dark',
    ][$status] ?? 'bg-secondary';
}

function news_editor_upload_cover(string $current = ''): string
{
    $uploaded = uploadFile('imgMise', __DIR__ . '/../img/actualites/', [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ]);

    return $uploaded ?: $current;
}

function news_editor_plain_excerpt(string $html, string $summary): array
{
    $plain = trim(html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8'));
    $fallback = $summary !== '' ? $summary : $plain;
    $paragraphs = array_fill(1, 10, '');
    $paragraphs[1] = mb_substr($fallback, 0, 1800);

    return $paragraphs;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    try {
        $articleId = (int)($_POST['actu_id'] ?? 0);
        $title = trim((string)($_POST['titre'] ?? ''));
        $category = trim((string)($_POST['categorie'] ?? 'Actualité'));
        $summary = trim((string)($_POST['resume'] ?? ''));
        $rawContent = (string)($_POST['contenu'] ?? '');
        $content = actualite_sanitize_html($rawContent);
        $author = trim((string)($_POST['auteur'] ?? ''));
        $datePub = trim((string)($_POST['date_pub'] ?? '')) ?: date('Y-m-d');
        $status = (string)($_POST['statut'] ?? 'brouillon');
        $featured = isset($_POST['a_la_une']) ? 1 : 0;
        $requestedSlug = trim((string)($_POST['slug'] ?? ''));

        if ($title === '') {
            throw new Exception('Le titre de l\'actualité est obligatoire.');
        }

        if (!in_array($status, ['brouillon', 'publie', 'archive'], true)) {
            $status = 'brouillon';
        }

        if ($category === '') {
            $category = 'Actualité';
        }

        if ($author === '') {
            $author = (string)($_SESSION['user'] ?? 'Secrétariat National 1325');
        }

        if ($content === '' && $summary !== '') {
            $content = '<p>' . htmlspecialchars($summary, ENT_QUOTES, 'UTF-8') . '</p>';
        }

        $slug = actualite_unique_slug($pdo, $title, $articleId > 0 ? $articleId : null, $requestedSlug ?: null);
        $paragraphs = news_editor_plain_excerpt($content, $summary);

        if ($articleId > 0) {
            $stmt = $pdo->prepare('SELECT imgMise, imgPub1, imgPub2, nbrVues, messageFort FROM actualites WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $articleId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$existing) {
                throw new Exception('Actualité introuvable.');
            }

            $imgMise = news_editor_upload_cover((string)($existing['imgMise'] ?? ''));

            $sql = "UPDATE actualites SET
                    titre = :titre,
                    slug = :slug,
                    categorie = :categorie,
                    auteur = :auteur,
                    date_pub = :date_pub,
                    commentaire = :commentaire,
                    resume = :resume,
                    imgMise = :imgMise,
                    imgPub1 = :imgPub1,
                    imgPub2 = :imgPub2,
                    messageFort = :messageFort,
                    contenu = :contenu,
                    statut = :statut,
                    a_la_une = :a_la_une,
                    paraph1 = :paraph1,
                    paraph2 = :paraph2,
                    paraph3 = :paraph3,
                    paraph4 = :paraph4,
                    paraph5 = :paraph5,
                    paraph6 = :paraph6,
                    paraph7 = :paraph7,
                    paraph8 = :paraph8,
                    paraph9 = :paraph9,
                    paraph10 = :paraph10,
                    updated_at = NOW()
                WHERE id = :id";

            $params = [
                ':titre' => $title,
                ':slug' => $slug,
                ':categorie' => $category,
                ':auteur' => $author,
                ':date_pub' => $datePub,
                ':commentaire' => $summary,
                ':resume' => $summary,
                ':imgMise' => $imgMise,
                ':imgPub1' => $existing['imgPub1'] ?? null,
                ':imgPub2' => $existing['imgPub2'] ?? null,
                ':messageFort' => $existing['messageFort'] ?? '',
                ':contenu' => $content,
                ':statut' => $status,
                ':a_la_une' => $featured,
                ':id' => $articleId,
            ];

            for ($i = 1; $i <= 10; $i++) {
                $params[':paraph' . $i] = $paragraphs[$i];
            }

            $pdo->prepare($sql)->execute($params);
            $_SESSION['flash_news'] = 'Actualité mise à jour.';
            header('Location: ' . URL_ADDACTUALITES . '?edit=' . $articleId);
            exit;
        }

        $imgMise = news_editor_upload_cover('');
        $sql = "INSERT INTO actualites (
                titre, slug, categorie, auteur, date_pub, commentaire, resume, nbrVues,
                imgMise, imgPub1, imgPub2, messageFort, contenu, statut, a_la_une,
                paraph1, paraph2, paraph3, paraph4, paraph5, paraph6, paraph7, paraph8, paraph9, paraph10,
                date_creation, updated_at
            ) VALUES (
                :titre, :slug, :categorie, :auteur, :date_pub, :commentaire, :resume, 0,
                :imgMise, NULL, NULL, '', :contenu, :statut, :a_la_une,
                :paraph1, :paraph2, :paraph3, :paraph4, :paraph5, :paraph6, :paraph7, :paraph8, :paraph9, :paraph10,
                NOW(), NOW()
            )";

        $params = [
            ':titre' => $title,
            ':slug' => $slug,
            ':categorie' => $category,
            ':auteur' => $author,
            ':date_pub' => $datePub,
            ':commentaire' => $summary,
            ':resume' => $summary,
            ':imgMise' => $imgMise,
            ':contenu' => $content,
            ':statut' => $status,
            ':a_la_une' => $featured,
        ];

        for ($i = 1; $i <= 10; $i++) {
            $params[':paraph' . $i] = $paragraphs[$i];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $newId = (int)$pdo->lastInsertId();

        $_SESSION['flash_news'] = 'Actualité créée.';
        header('Location: ' . URL_ADDACTUALITES . '?edit=' . $newId);
        exit;
    } catch (Exception $e) {
        $_SESSION['flash_news_error'] = $e->getMessage();
        $redirect = URL_ADDACTUALITES;
        if (!empty($articleId)) {
            $redirect .= '?edit=' . (int)$articleId;
        }
        header('Location: ' . $redirect);
        exit;
    }
}

$message = '';
if (!empty($_SESSION['flash_news'])) {
    $message = '<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill me-2"></i>' . htmlspecialchars($_SESSION['flash_news']) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['flash_news']);
}
if (!empty($_SESSION['flash_news_error'])) {
    $message = '<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle-fill me-2"></i>' . htmlspecialchars($_SESSION['flash_news_error']) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['flash_news_error']);
}

$editingId = (int)($_GET['edit'] ?? 0);
$editing = null;
if ($editingId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM actualites WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $editingId]);
    $editing = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

$articles = $pdo->query("
    SELECT *
    FROM actualites
    ORDER BY
        CASE statut WHEN 'publie' THEN 1 WHEN 'brouillon' THEN 2 ELSE 3 END,
        date_pub DESC,
        id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$stats = [
    'total' => count($articles),
    'publie' => 0,
    'brouillon' => 0,
    'archive' => 0,
    'featured' => 0,
];

foreach ($articles as $article) {
    $statusKey = (string)($article['statut'] ?? 'publie');
    if (isset($stats[$statusKey])) {
        $stats[$statusKey]++;
    }
    if (!empty($article['a_la_une'])) {
        $stats['featured']++;
    }
}

$formAction = $editing ? 'Modifier l\'actualité' : 'Créer une actualité';
$editorContent = actualite_sanitize_html((string)($editing['contenu'] ?? ''));
$currentCoverUrl = $editing && !empty($editing['imgMise']) ? actualite_image_url($editing['imgMise']) : '';

$pageTitle = 'Rédaction des actualités';
$breadcrumb = [['label' => 'Actualités']];
$activePage = 'actualites';
require_once __DIR__ . '/admin_layout_top.php';
?>

<style>
.editor-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 320px;
    gap: 20px;
    align-items: start;
}
.editor-panel {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    box-shadow: var(--card-shadow);
}
.editor-panel-header {
    padding: 16px 18px;
    border-bottom: 1px solid #eef1f4;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.editor-panel-header h2 {
    margin: 0;
    color: #1e2a3a;
    font-size: 16px;
    font-weight: 700;
}
.editor-panel-body {
    padding: 18px;
}
.status-cards {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}
.status-tile {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 16px;
    box-shadow: var(--card-shadow);
}
.status-tile span {
    color: #6c757d;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}
.status-tile strong {
    display: block;
    margin-top: 6px;
    color: #1e2a3a;
    font-size: 26px;
    line-height: 1;
}
.toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 10px;
    border: 1px solid #dee2e6;
    border-bottom: 0;
    border-radius: 8px 8px 0 0;
    background: #f8fafc;
}
.toolbar-group {
    display: inline-flex;
    gap: 4px;
    padding-right: 6px;
    margin-right: 2px;
    border-right: 1px solid #dee2e6;
}
.toolbar-group:last-child {
    border-right: 0;
}
.toolbar button {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #d9e0e8;
    background: #fff;
    color: #2f3b4a;
    border-radius: 6px;
    font-size: 16px;
}
.toolbar button:hover,
.toolbar button:focus {
    border-color: var(--accent);
    color: var(--accent);
}
.wysiwyg-editor {
    min-height: 460px;
    padding: 22px;
    border: 1px solid #dee2e6;
    border-radius: 0 0 8px 8px;
    background: #fff;
    color: #1f2937;
    font-size: 16px;
    line-height: 1.8;
    outline: none;
}
.wysiwyg-editor:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(77, 168, 218, .15);
}
.wysiwyg-editor h2,
.wysiwyg-editor h3 {
    color: #1e2a3a;
    line-height: 1.3;
}
.wysiwyg-editor figure {
    margin: 18px 0;
}
.wysiwyg-editor img,
.preview-article img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}
.wysiwyg-editor figcaption,
.preview-article figcaption {
    margin-top: 6px;
    color: #6c757d;
    font-size: 13px;
}
.wysiwyg-editor .article-embed,
.preview-article .article-embed {
    aspect-ratio: 16 / 9;
    width: 100%;
    background: #101827;
    border-radius: 8px;
    overflow: hidden;
}
.wysiwyg-editor iframe,
.preview-article iframe {
    width: 100%;
    aspect-ratio: 16 / 9;
    height: auto;
    border: 0;
}
.cover-preview {
    width: 100%;
    aspect-ratio: 16 / 9;
    border-radius: 8px;
    background: #eef1f4;
    object-fit: cover;
    display: block;
}
.article-title-cell {
    min-width: 260px;
}
.article-title-cell a {
    color: #1e2a3a;
    font-weight: 700;
    text-decoration: none;
}
.article-title-cell small {
    display: block;
    margin-top: 4px;
    color: #6c757d;
}
.preview-article {
    color: #263342;
    line-height: 1.8;
}
.preview-cover {
    width: 100%;
    max-height: 360px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 20px;
}
@media (max-width: 1199px) {
    .editor-grid,
    .status-cards {
        grid-template-columns: 1fr 1fr;
    }
}
@media (max-width: 767px) {
    .editor-grid,
    .status-cards {
        grid-template-columns: 1fr;
    }
    .toolbar {
        gap: 5px;
    }
    .toolbar-group {
        border-right: 0;
        padding-right: 0;
    }
}
</style>

<div class="page-header">
    <div>
        <h1><i class="bi bi-newspaper me-2" style="color:var(--accent)"></i>Rédaction des actualités</h1>
        <p>Créer, enrichir et publier les articles du site.</p>
    </div>
    <a href="<?= URL_ADDACTUALITES ?>" class="btn btn-admin-primary"><i class="bi bi-plus-circle me-1"></i>Nouvel article</a>
</div>

<?= $message ?>

<div class="status-cards">
    <div class="status-tile"><span>Total</span><strong><?= (int)$stats['total'] ?></strong></div>
    <div class="status-tile"><span>Publiés</span><strong><?= (int)$stats['publie'] ?></strong></div>
    <div class="status-tile"><span>Brouillons</span><strong><?= (int)$stats['brouillon'] ?></strong></div>
    <div class="status-tile"><span>À la une</span><strong><?= (int)$stats['featured'] ?></strong></div>
</div>

<form method="POST" enctype="multipart/form-data" id="articleForm">
    <?= csrf_field() ?>
    <input type="hidden" name="actu_id" value="<?= (int)($editing['id'] ?? 0) ?>">
    <input type="hidden" name="contenu" id="contenuInput">

    <div class="editor-grid">
        <div class="editor-panel">
            <div class="editor-panel-header">
                <h2><?= htmlspecialchars($formAction) ?></h2>
                <button type="button" class="btn btn-outline-primary btn-sm" id="previewBtn"><i class="bi bi-eye me-1"></i>Aperçu</button>
            </div>
            <div class="editor-panel-body">
                <div class="mb-3">
                    <label class="form-label" for="titreInput">Titre de l'actualité</label>
                    <input type="text" name="titre" id="titreInput" class="form-control form-control-lg" required value="<?= htmlspecialchars($editing['titre'] ?? '') ?>">
                </div>

                <div class="row">
                    <div class="col-lg-7 mb-3">
                        <label class="form-label" for="slugInput">Slug</label>
                        <input type="text" name="slug" id="slugInput" class="form-control" value="<?= htmlspecialchars($editing['slug'] ?? '') ?>">
                    </div>
                    <div class="col-lg-5 mb-3">
                        <label class="form-label" for="categorieInput">Catégorie</label>
                        <input type="text" name="categorie" id="categorieInput" class="form-control" list="categoriesList" value="<?= htmlspecialchars($editing['categorie'] ?? 'Actualité') ?>">
                        <datalist id="categoriesList">
                            <option value="Actualité">
                            <option value="Atelier">
                            <option value="Plaidoyer">
                            <option value="Communiqué">
                            <option value="Formation">
                        </datalist>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="resumeInput">Résumé / chapeau de l'article</label>
                    <textarea name="resume" id="resumeInput" class="form-control" rows="4"><?= htmlspecialchars($editing['resume'] ?? $editing['commentaire'] ?? '') ?></textarea>
                </div>

                <label class="form-label">Contenu complet</label>
                <div class="toolbar" role="toolbar" aria-label="Outils de mise en forme">
                    <div class="toolbar-group">
                        <button type="button" title="Gras" data-command="bold"><i class="bi bi-type-bold"></i></button>
                        <button type="button" title="Italique" data-command="italic"><i class="bi bi-type-italic"></i></button>
                        <button type="button" title="Souligné" data-command="underline"><i class="bi bi-type-underline"></i></button>
                    </div>
                    <div class="toolbar-group">
                        <button type="button" title="Paragraphe" data-block="P"><i class="bi bi-text-paragraph"></i></button>
                        <button type="button" title="Titre H2" data-block="H2"><strong>H2</strong></button>
                        <button type="button" title="Titre H3" data-block="H3"><strong>H3</strong></button>
                    </div>
                    <div class="toolbar-group">
                        <button type="button" title="Aligner à gauche" data-command="justifyLeft"><i class="bi bi-text-left"></i></button>
                        <button type="button" title="Centrer" data-command="justifyCenter"><i class="bi bi-text-center"></i></button>
                        <button type="button" title="Aligner à droite" data-command="justifyRight"><i class="bi bi-text-right"></i></button>
                        <button type="button" title="Justifier" data-command="justifyFull"><i class="bi bi-justify"></i></button>
                    </div>
                    <div class="toolbar-group">
                        <button type="button" title="Liste à puces" data-command="insertUnorderedList"><i class="bi bi-list-ul"></i></button>
                        <button type="button" title="Liste numérotée" data-command="insertOrderedList"><i class="bi bi-list-ol"></i></button>
                        <button type="button" title="Citation" data-block="BLOCKQUOTE"><i class="bi bi-quote"></i></button>
                    </div>
                    <div class="toolbar-group">
                        <button type="button" title="Lien" id="linkBtn"><i class="bi bi-link-45deg"></i></button>
                        <button type="button" title="Image" id="imageBtn"><i class="bi bi-image"></i></button>
                        <button type="button" title="Légende" id="captionBtn"><i class="bi bi-card-text"></i></button>
                        <button type="button" title="YouTube" id="youtubeBtn"><i class="bi bi-youtube"></i></button>
                        <button type="button" title="Vidéo" id="videoBtn"><i class="bi bi-camera-video"></i></button>
                    </div>
                    <div class="toolbar-group">
                        <button type="button" title="Séparateur" data-command="insertHorizontalRule"><i class="bi bi-hr"></i></button>
                        <button type="button" title="Annuler" data-command="undo"><i class="bi bi-arrow-counterclockwise"></i></button>
                        <button type="button" title="Rétablir" data-command="redo"><i class="bi bi-arrow-clockwise"></i></button>
                    </div>
                </div>
                <div id="editor" class="wysiwyg-editor" contenteditable="true"><?= $editorContent ?></div>
                <input type="file" id="bodyImageInput" class="d-none" accept="image/*">
            </div>
        </div>

        <aside class="editor-panel">
            <div class="editor-panel-header">
                <h2>Publication</h2>
            </div>
            <div class="editor-panel-body">
                <div class="mb-3">
                    <label class="form-label" for="auteurInput">Auteur / journaliste</label>
                    <input type="text" name="auteur" id="auteurInput" class="form-control" value="<?= htmlspecialchars($editing['auteur'] ?? $_SESSION['user'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="dateInput">Date de publication</label>
                    <input type="date" name="date_pub" id="dateInput" class="form-control" required value="<?= htmlspecialchars($editing['date_pub'] ?? date('Y-m-d')) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="statutInput">Statut</label>
                    <?php $currentStatus = (string)($editing['statut'] ?? 'brouillon'); ?>
                    <select name="statut" id="statutInput" class="form-select">
                        <option value="brouillon" <?= $currentStatus === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                        <option value="publie" <?= $currentStatus === 'publie' ? 'selected' : '' ?>>Publié</option>
                        <option value="archive" <?= $currentStatus === 'archive' ? 'selected' : '' ?>>Archivé</option>
                    </select>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch" name="a_la_une" id="featuredInput" value="1" <?= !empty($editing['a_la_une']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="featuredInput">Mettre cette actualité à la une</label>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="coverInput">Image principale / couverture</label>
                    <input type="file" name="imgMise" id="coverInput" class="form-control" accept="image/*">
                </div>

                <img id="coverPreview" class="cover-preview" src="<?= htmlspecialchars($currentCoverUrl ?: IMG_DIR . 'bread-bg21.jpg') ?>" alt="">

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-admin-primary btn-lg"><i class="bi bi-save me-1"></i>Enregistrer</button>
                    <?php if ($editing && ($editing['statut'] ?? '') === 'publie'): ?>
                        <a class="btn btn-outline-secondary" href="<?= htmlspecialchars(actualite_url($editing)) ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right me-1"></i>Voir en ligne</a>
                    <?php endif; ?>
                </div>
            </div>
        </aside>
    </div>
</form>

<div class="editor-panel mt-4">
    <div class="editor-panel-header">
        <h2>Actualités enregistrées</h2>
    </div>
    <div class="editor-panel-body">
        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Catégorie</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Une</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                        <tr>
                            <td class="article-title-cell">
                                <a href="<?= URL_ADDACTUALITES ?>?edit=<?= (int)$article['id'] ?>"><?= htmlspecialchars($article['titre']) ?></a>
                                <small><?= htmlspecialchars('/actualites/' . ($article['slug'] ?: actualite_slugify($article['titre'] ?? 'actualite'))) ?></small>
                            </td>
                            <td><?= htmlspecialchars($article['categorie'] ?: 'Actualité') ?></td>
                            <td><?= htmlspecialchars(actualite_published_label($article['date_pub'] ?? null)) ?></td>
                            <td><span class="badge <?= news_editor_status_badge((string)($article['statut'] ?? 'brouillon')) ?>"><?= news_editor_status_label((string)($article['statut'] ?? 'brouillon')) ?></span></td>
                            <td><?= !empty($article['a_la_une']) ? '<span class="badge bg-warning text-dark">Oui</span>' : '<span class="text-muted">Non</span>' ?></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <?php if (($article['statut'] ?? '') === 'publie'): ?>
                                        <a class="btn btn-outline-primary" href="<?= htmlspecialchars(actualite_url($article)) ?>" target="_blank" rel="noopener" title="Voir"><i class="bi bi-eye"></i></a>
                                    <?php endif; ?>
                                    <a class="btn btn-outline-secondary" href="<?= URL_ADDACTUALITES ?>?edit=<?= (int)$article['id'] ?>" title="Modifier"><i class="bi bi-pencil"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($articles)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Aucune actualité enregistrée.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aperçu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <article class="preview-article">
                    <img id="previewCover" class="preview-cover d-none" src="" alt="">
                    <div class="news-card-meta">
                        <span id="previewCategory"></span>
                        <time id="previewDate"></time>
                    </div>
                    <h1 id="previewTitle"></h1>
                    <p id="previewSummary" class="lead"></p>
                    <div id="previewContent"></div>
                </article>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const form = document.getElementById('articleForm');
    const editor = document.getElementById('editor');
    const contentInput = document.getElementById('contenuInput');
    const titleInput = document.getElementById('titreInput');
    const slugInput = document.getElementById('slugInput');
    const coverInput = document.getElementById('coverInput');
    const coverPreview = document.getElementById('coverPreview');
    const bodyImageInput = document.getElementById('bodyImageInput');
    const csrfToken = form.querySelector('input[name="csrf_token"]').value;
    const uploadUrl = '<?= BASE_URL ?>pagesweb/upload_actualite_media.php';
    let slugTouched = slugInput.value.trim() !== '';
    let savedRange = null;

    function slugify(value) {
        return value.normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .replace(/-+/g, '-');
    }

    function syncContent() {
        contentInput.value = editor.innerHTML.trim();
    }

    function saveSelection() {
        const selection = window.getSelection();
        if (selection && selection.rangeCount > 0 && editor.contains(selection.anchorNode)) {
            savedRange = selection.getRangeAt(0);
        }
    }

    function restoreSelection() {
        editor.focus();
        const selection = window.getSelection();
        if (selection && savedRange) {
            selection.removeAllRanges();
            selection.addRange(savedRange);
        }
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value;
        return div.innerHTML;
    }

    function escapeAttr(value) {
        return String(value).replace(/"/g, '&quot;');
    }

    function insertHtml(html) {
        restoreSelection();
        document.execCommand('insertHTML', false, html);
        syncContent();
    }

    function normalizeUrl(value) {
        const url = value.trim();
        if (!url) {
            return '';
        }
        return /^https?:\/\//i.test(url) ? url : 'https://' + url;
    }

    function youtubeEmbed(url) {
        const value = url.trim();
        const match = value.match(/(?:youtu\.be\/|youtube\.com\/watch\?.*v=|youtube\.com\/embed\/|youtube\.com\/shorts\/)([A-Za-z0-9_-]{6,})/);
        return match ? 'https://www.youtube.com/embed/' + match[1] : '';
    }

    editor.addEventListener('keyup', saveSelection);
    editor.addEventListener('mouseup', saveSelection);
    editor.addEventListener('input', syncContent);

    document.querySelectorAll('[data-command]').forEach(function (button) {
        button.addEventListener('click', function () {
            restoreSelection();
            document.execCommand(button.dataset.command, false, null);
            syncContent();
        });
    });

    document.querySelectorAll('[data-block]').forEach(function (button) {
        button.addEventListener('click', function () {
            restoreSelection();
            document.execCommand('formatBlock', false, button.dataset.block);
            syncContent();
        });
    });

    titleInput.addEventListener('input', function () {
        if (!slugTouched) {
            slugInput.value = slugify(titleInput.value);
        }
    });

    slugInput.addEventListener('input', function () {
        slugTouched = true;
        slugInput.value = slugify(slugInput.value);
    });

    coverInput.addEventListener('change', function () {
        const file = coverInput.files && coverInput.files[0];
        if (file) {
            coverPreview.src = URL.createObjectURL(file);
        }
    });

    document.getElementById('linkBtn').addEventListener('click', function () {
        const url = normalizeUrl(window.prompt('URL du lien') || '');
        if (!url) {
            return;
        }
        restoreSelection();
        document.execCommand('createLink', false, url);
        syncContent();
    });

    document.getElementById('imageBtn').addEventListener('click', function () {
        saveSelection();
        bodyImageInput.click();
    });

    bodyImageInput.addEventListener('change', async function () {
        const file = bodyImageInput.files && bodyImageInput.files[0];
        if (!file) {
            return;
        }

        const data = new FormData();
        data.append('csrf_token', csrfToken);
        data.append('media', file);

        try {
            const response = await fetch(uploadUrl, {
                method: 'POST',
                body: data,
                credentials: 'same-origin'
            });
            const payload = await response.json();
            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Upload impossible.');
            }
            const caption = window.prompt('Légende de l’image') || '';
            const captionHtml = caption.trim() ? '<figcaption>' + escapeHtml(caption.trim()) + '</figcaption>' : '';
            insertHtml('<figure><img src="' + escapeAttr(payload.url) + '" alt="">' + captionHtml + '</figure><p><br></p>');
        } catch (error) {
            alert(error.message);
        } finally {
            bodyImageInput.value = '';
        }
    });

    document.getElementById('captionBtn').addEventListener('click', function () {
        const selection = window.getSelection();
        const node = selection && selection.anchorNode ? (selection.anchorNode.nodeType === 1 ? selection.anchorNode : selection.anchorNode.parentElement) : null;
        const figure = node ? node.closest('figure') : null;
        if (!figure) {
            return;
        }
        const text = window.prompt('Légende') || '';
        if (!text.trim()) {
            return;
        }
        let caption = figure.querySelector('figcaption');
        if (!caption) {
            caption = document.createElement('figcaption');
            figure.appendChild(caption);
        }
        caption.textContent = text.trim();
        syncContent();
    });

    document.getElementById('youtubeBtn').addEventListener('click', function () {
        const embed = youtubeEmbed(window.prompt('Lien YouTube') || '');
        if (!embed) {
            return;
        }
        insertHtml('<figure class="article-embed"><iframe src="' + escapeAttr(embed) + '" allowfullscreen loading="lazy"></iframe></figure><p><br></p>');
    });

    document.getElementById('videoBtn').addEventListener('click', function () {
        const url = normalizeUrl(window.prompt('URL de la vidéo') || '');
        if (!url) {
            return;
        }
        insertHtml('<figure><video src="' + escapeAttr(url) + '" controls></video></figure><p><br></p>');
    });

    document.getElementById('previewBtn').addEventListener('click', function () {
        syncContent();
        document.getElementById('previewTitle').textContent = titleInput.value || 'Sans titre';
        document.getElementById('previewCategory').textContent = document.getElementById('categorieInput').value || 'Actualité';
        document.getElementById('previewDate').textContent = document.getElementById('dateInput').value || '';
        document.getElementById('previewSummary').textContent = document.getElementById('resumeInput').value || '';
        document.getElementById('previewContent').innerHTML = contentInput.value;

        const previewCover = document.getElementById('previewCover');
        const file = coverInput.files && coverInput.files[0];
        const currentCover = coverPreview.getAttribute('src');
        if (file) {
            previewCover.src = URL.createObjectURL(file);
            previewCover.classList.remove('d-none');
        } else if (currentCover) {
            previewCover.src = currentCover;
            previewCover.classList.remove('d-none');
        } else {
            previewCover.classList.add('d-none');
        }

        new bootstrap.Modal(document.getElementById('previewModal')).show();
    });

    form.addEventListener('submit', function () {
        syncContent();
    });

    syncContent();
})();
</script>

<?php require_once __DIR__ . '/admin_layout_bottom.php'; ?>
