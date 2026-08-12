<?php

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect;
require_once __DIR__ . '/track_visitor.php';
require_once __DIR__ . '/csrf_helper.php';

$pageCss = CSS_DIR . 'documentation.css';

$heroFsPath = __DIR__ . '/../img/documentations/hero-docs.jpg';
$hero = file_exists($heroFsPath)
    ? BASE_URL . 'img/documentations/hero-docs.jpg'
    : BASE_URL . 'img/bread-bg21.jpg';

$resolveDocImage = static function (?string $imgName): string {
    $imgName = trim((string) $imgName);

    if ($imgName !== '') {
        $imgFsPath = __DIR__ . '/../img/documentations/' . $imgName;
        if (file_exists($imgFsPath)) {
            return BASE_URL . 'img/documentations/' . rawurlencode($imgName);
        }
    }

    return BASE_URL . 'img/section-img.png';
};

try {
    $stmt = $pdo->query("SELECT * FROM documentations ORDER BY datePub DESC");
    $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de récupération : " . $e->getMessage());
}

$featuredDocs = array_slice($docs, 0, 3);
$libraryDocs = array_slice($docs, 3);
$totalDocs = count($docs);
$latestDocYear = !empty($docs[0]['anneePub']) ? $docs[0]['anneePub'] : date('Y');

$SKIP_PAGE_TITLE = true;
require_once $headerPath;
?>

<section class="caremed-hero documentation-hero" style="background-image: url('<?= $hero ?>');">
    <div class="overlay"></div>
    <div class="container">
        <div class="hero-content">
            <h1>Documentation 1325</h1>
            <p class="lead">Publications, rapports, plans d’action et ressources de référence mobilisés par le Secrétariat National 1325.</p>
        </div>
    </div>
</section>

<section class="documentation-intro section">
    <div class="container">
        <div class="section-heading library-heading">
            <span class="section-kicker">Publications récentes</span>
            <h3>Documents de référence</h3>
            <p><?= $totalDocs ?> ressources disponibles · Dernière mise à jour <?= htmlspecialchars((string) $latestDocYear) ?></p>
        </div>

        <?php if (!empty($featuredDocs)): ?>
            <div class="featured-docs">
                <?php foreach ($featuredDocs as $doc):
                    $docId = (int) ($doc['id'] ?? 0);
                    $imgName = $doc['img'] ?? '';
                    $imgPath = $resolveDocImage($imgName);
                    $pdfFileName = basename($doc['fichier_pdf'] ?? '');
                    $viewUrl = ($docId > 0 && $pdfFileName !== '')
                        ? BASE_URL . 'pagesweb/documentation_event.php?doc_id=' . $docId . '&action=view&file=' . rawurlencode($pdfFileName)
                        : '#';
                    $downloadUrl = ($docId > 0 && $pdfFileName !== '')
                        ? BASE_URL . 'pagesweb/documentation_event.php?doc_id=' . $docId . '&action=download&file=' . rawurlencode($pdfFileName)
                        : '#';
                ?>
                    <article class="featured-doc-card">
                        <div class="featured-thumb" style="background-image:url('<?= htmlspecialchars($imgPath) ?>')"></div>
                        <div class="featured-body">
                            <span class="feature-meta"><?= htmlspecialchars(date('d M Y', strtotime($doc['datePub'] ?? 'now'))) ?></span>
                            <h3><a href="<?= htmlspecialchars($viewUrl) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($doc['titreDoc'] ?? 'Document') ?></a></h3>
                            <p><?= htmlspecialchars($doc['auteur'] ?? 'Publication SN1325') ?><?php if (!empty($doc['anneePub'])): ?> · <?= htmlspecialchars($doc['anneePub']) ?><?php endif; ?></p>
                            <div class="featured-actions">
                                <a class="btn btn-outline-primary" href="<?= htmlspecialchars($viewUrl) ?>" target="_blank" rel="noopener">Voir</a>
                                <a class="btn btn-primary" href="<?= htmlspecialchars($downloadUrl) ?>" rel="noopener">Télécharger</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section documentation-grid">
    <div class="container">
        <div class="section-heading">
            <span class="section-kicker">Bibliothèque complète</span>
            <h3>Toutes les publications disponibles</h3>
            <p>Rapports, plans d’action, textes de référence et publications institutionnelles.</p>
        </div>

        <div class="doc-library-grid">
            <?php foreach ($docs as $doc):
                $imgName = $doc['img'] ?? '';
                $docId = (int) ($doc['id'] ?? 0);
                $imgPath = $resolveDocImage($imgName);
                $pdfFileName = basename($doc['fichier_pdf'] ?? '');
                $viewUrl = ($docId > 0 && $pdfFileName !== '')
                    ? BASE_URL . 'pagesweb/documentation_event.php?doc_id=' . $docId . '&action=view&file=' . rawurlencode($pdfFileName)
                    : '#';
                $downloadUrl = ($docId > 0 && $pdfFileName !== '')
                    ? BASE_URL . 'pagesweb/documentation_event.php?doc_id=' . $docId . '&action=download&file=' . rawurlencode($pdfFileName)
                    : '#';
            ?>
                <article class="doc-card">
                    <a class="doc-thumb-link" href="<?= htmlspecialchars($imgPath) ?>" title="<?= htmlspecialchars($doc['titreDoc'] ?? '') ?>">
                        <div class="doc-thumb" style="background-image:url('<?= htmlspecialchars($imgPath) ?>')"></div>
                    </a>
                    <div class="doc-body">
                        <div class="doc-meta"><?= htmlspecialchars(date('d M, Y', strtotime($doc['datePub'] ?? 'now'))) ?></div>
                        <h4 class="doc-title"><a href="<?= htmlspecialchars($viewUrl) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($doc['titreDoc'] ?? 'Document') ?></a></h4>
                        <div class="doc-excerpt">Auteur: <?= htmlspecialchars($doc['auteur'] ?? 'Inconnu') ?><?php if (!empty($doc['anneePub'])): ?> · Année: <?= htmlspecialchars($doc['anneePub']) ?><?php endif; ?></div>
                        <div class="doc-actions">
                            <a class="btn btn-outline-primary" href="<?= htmlspecialchars($viewUrl) ?>" target="_blank" rel="noopener">Voir</a>
                            <a class="btn btn-primary" href="<?= htmlspecialchars($downloadUrl) ?>" rel="noopener">Télécharger</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once $footerPath; ?>
