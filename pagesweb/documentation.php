<?php

// ===============================

// documentation.php

// Composant Documentation SN1325

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // Connexion PDO

// Page-specific CSS
$pageCss = CSS_DIR . 'documentation.css';

// Récupération des documentations depuis la base
try {
    $stmt = $pdo->query("SELECT * FROM documentations ORDER BY datePub DESC");
    $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de récupération : " . $e->getMessage());
}

// Composants header/footer
$SKIP_PAGE_TITLE = true; // hide the header-rendered h2 for this page
require_once $headerPath;

?>

<!-- Hero -->
<?php $hero = BASE_URL . 'img/documentations/hero-docs.jpg'; ?>
<section class="caremed-hero" style="background-image: url('<?= $hero ?>');">
    <div class="overlay"></div>
    <div class="container">
        <div class="hero-content">
            <h1>DOCUMENTATION 1325</h1>
            <p class="lead">Retrouvez nos publications, rapports et ressources téléchargeables.</p>
        </div>
    </div>
</section>

<!-- Grid -->
<section class="section documentation-grid">
    <div class="container">
        <div class="row">
            <?php foreach ($docs as $doc):
                    $imgName = $doc['img'] ?? '';
                    $encodedImg = rawurlencode($imgName);
                    $imgPath = BASE_URL . 'img/documentations/' . $encodedImg;
                    $pdfFile = ROOT_DIR . 'img/documentations/' . ($doc['fichier_pdf'] ?? '');
                    $pdfPath = file_exists($pdfFile) ? BASE_URL . 'img/documentations/' . $doc['fichier_pdf'] : '#';
            ?>
            <div class="col-lg-4 col-md-6 col-12 mb-4">
                <div class="doc-card">
                    <a class="doc-thumb-link" href="<?= htmlspecialchars($imgPath) ?>" title="<?= htmlspecialchars($doc['titreDoc'] ?? '') ?>">
                        <div class="doc-thumb" style="background-image:url('<?= htmlspecialchars($imgPath) ?>')"></div>
                    </a>
                    <div class="doc-body">
                        <div class="doc-meta"><?= htmlspecialchars(date('d M, Y', strtotime($doc['datePub']))) ?></div>
                        <div class="doc-title"><a href="<?= htmlspecialchars($pdfPath) ?>" target="_blank"><?= htmlspecialchars($doc['titreDoc']) ?></a></div>
                        <div class="doc-excerpt">Auteur: <?= htmlspecialchars($doc['auteur'] ?? 'Inconnu') ?> — Année: <?= htmlspecialchars($doc['anneePub'] ?? 'N/A') ?></div>
                        <div class="doc-actions mt-2">
                              <a class="btn btn-outline-primary btn-sm me-2" href="<?= htmlspecialchars($pdfPath) ?>" target="_blank" rel="noopener">Voir</a>
                            <a class="btn btn-primary btn-sm" href="<?= htmlspecialchars($pdfPath) ?>" target="_blank" rel="noopener" download>Télécharger</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<?php require_once $footerPath; ?>

