<?php

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect;

$SKIP_PAGE_TITLE = true;
$pageCss = CSS_DIR . 'actualites.css';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
} else {
    $stmt = $pdo->query("SELECT id FROM actualites ORDER BY id DESC LIMIT 1");
    $last = $stmt->fetch(PDO::FETCH_ASSOC);
    $id = $last ? (int) $last['id'] : 0;
}

if (!$id) {
    require_once $headerPath;
    echo "<section class='section'><div class='container'><div class='alert alert-warning text-center py-5'>Aucune actualité disponible pour le moment.</div></div></section>";
    require_once $footerPath;
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM actualites WHERE id = :id");
$stmt->execute([':id' => $id]);
$actu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$actu) {
    require_once $headerPath;
    echo "<section class='section'><div class='container'><div class='alert alert-warning text-center py-5'>Cette actualité est introuvable.</div></div></section>";
    require_once $footerPath;
    exit;
}

$actualitesStmt = $pdo->query("SELECT * FROM actualites ORDER BY id DESC LIMIT 12");
$actualites = $actualitesStmt->fetchAll(PDO::FETCH_ASSOC);

$docsStmt = $pdo->query("SELECT * FROM documentations ORDER BY id DESC LIMIT 6");
$docs = $docsStmt->fetchAll(PDO::FETCH_ASSOC);

$sidebarItems = [];
foreach ($actualites as $row) {
    if ((int) $row['id'] === (int) $actu['id']) {
        continue;
    }
    $sidebarItems[] = $row;
}

$relatedNews = array_slice($sidebarItems, 0, 6);
$sidebarDocs = array_slice($docs, 0, 3);
$heroImg = !empty($actu['imgMise']) ? IMG_DIR . 'actualites/' . rawurlencode($actu['imgMise']) : '';
$publishedAt = !empty($actu['date_pub']) ? date('d M Y', strtotime($actu['date_pub'])) : 'Date non renseignée';
$author = !empty($actu['auteur']) ? $actu['auteur'] : 'Secrétariat 1325';
$leadParagraph = '';

for ($i = 1; $i <= 10; $i++) {
    if (!empty($actu['paraph' . $i])) {
        $leadParagraph = $actu['paraph' . $i];
        break;
    }
}

$galleryImgs = array_filter([
    $actu['imgPub1'] ?? '',
    $actu['imgPub2'] ?? ''
]);
$galleryImgs = array_values(array_unique($galleryImgs));

if (!empty($actu['imgMise'])) {
    $galleryImgs = array_values(array_filter($galleryImgs, function ($value) use ($actu) {
        return $value !== $actu['imgMise'];
    }));
}

require_once $headerPath;
?>

<section class="caremed-hero actualites-hero" style="background-image: url('<?= htmlspecialchars($heroImg) ?>'); background-color: <?= $heroImg ? 'transparent' : 'var(--primary)' ?>;">
    <div class="overlay"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-breadcrumb">Accueil / Actualités</div>
            <h1>Actualités 1325</h1>
            <p class="lead">Suivez les temps forts, initiatives, ateliers et prises de parole qui structurent la mise en œuvre de l’agenda Femmes, Paix et Sécurité en RDC.</p>
        </div>
    </div>
</section>

<section class="actualites-intro section">
    <div class="container">
        <div class="actualites-overview">
            <article class="overview-card">
                <span class="overview-label">Publication</span>
                <strong><?= htmlspecialchars($publishedAt) ?></strong>
                <p>Date de mise en ligne de l’actualité actuellement mise en avant.</p>
            </article>
            <article class="overview-card">
                <span class="overview-label">Auteur</span>
                <strong><?= htmlspecialchars($author) ?></strong>
                <p>Source éditoriale ou institutionnelle associée à cette publication.</p>
            </article>
            <article class="overview-card">
                <span class="overview-label">Veille active</span>
                <strong><?= count($sidebarItems) + 1 ?> contenus</strong>
                <p>Actualités récentes et archives mobilisées pour documenter les actions du Secrétariat.</p>
            </article>
        </div>

        <div class="news-lead-panel">
            <div class="lead-copy">
                <span class="section-kicker">À la une</span>
                <h2><?= htmlspecialchars($actu['titre']) ?></h2>
                <p><?= htmlspecialchars(mb_strimwidth(trim(strip_tags($leadParagraph)), 0, 260, '...')) ?></p>
            </div>
            <div class="lead-aside">
                <span class="aside-label">Repères rapides</span>
                <ul class="lead-points">
                    <li>Article principal enrichi avec visuels et citation forte</li>
                    <li>Accès direct aux autres publications récentes</li>
                    <li>Passerelle vers les documents de référence associés</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="news-single section">
    <div class="container">
        <div class="news-layout">
            <div class="news-main-column">
                <article class="news-card featured-news-card">
                    <?php if (!empty($actu['imgMise'])): ?>
                        <div class="news-image">
                            <img src="<?= IMG_DIR . 'actualites/' . htmlspecialchars($actu['imgMise']) ?>" alt="<?= htmlspecialchars($actu['titre']) ?>">
                        </div>
                    <?php endif; ?>

                    <div class="news-body">
                        <div class="news-meta">
                            <span><?= htmlspecialchars($publishedAt) ?></span>
                            <span><?= htmlspecialchars($author) ?></span>
                        </div>
                        <h2 class="news-title"><?= htmlspecialchars($actu['titre']) ?></h2>

                        <div class="news-excerpt">
                            <?php for ($i = 1; $i <= 10; $i++):
                                $para = $actu['paraph' . $i] ?? '';
                                if ($para): ?>
                                    <p><?= htmlspecialchars($para) ?></p>
                            <?php endif; endfor; ?>
                        </div>

                        <?php if (!empty($galleryImgs)): ?>
                            <div class="image-gallery">
                                <div class="gallery-head">
                                    <span class="section-kicker">Galerie</span>
                                    <h3>Visuels complémentaires</h3>
                                </div>
                                <div class="gallery-grid">
                                    <?php foreach ($galleryImgs as $gimg): ?>
                                        <a href="<?= IMG_DIR . 'actualites/' . htmlspecialchars($gimg) ?>" class="gallery-link" title="<?= htmlspecialchars($actu['titre']) ?>">
                                            <img src="<?= IMG_DIR . 'actualites/' . htmlspecialchars($gimg) ?>" alt="">
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($actu['messageFort'])): ?>
                            <blockquote class="news-quote">
                                <p><?= htmlspecialchars($actu['messageFort']) ?></p>
                            </blockquote>
                        <?php endif; ?>
                    </div>
                </article>

                <?php if (!empty($relatedNews)): ?>
                    <section class="related-news">
                        <div class="section-heading">
                            <span class="section-kicker">Continuer la lecture</span>
                            <h3>Autres actualités</h3>
                            <p>Une sélection complémentaire pour suivre les initiatives, plaidoyers et activités portés autour de la Résolution 1325.</p>
                        </div>

                        <div class="related-grid">
                            <?php foreach ($relatedNews as $item): ?>
                                <article class="news-card-mini">
                                    <div class="card-image">
                                        <?php if (!empty($item['imgMise'])): ?>
                                            <a href="<?= URL_ACTUALITES ?>?id=<?= urlencode($item['id']) ?>">
                                                <img src="<?= IMG_DIR . 'actualites/' . htmlspecialchars($item['imgMise']) ?>" alt="<?= htmlspecialchars($item['titre']) ?>">
                                            </a>
                                        <?php endif; ?>
                                        <span class="date-badge"><?= htmlspecialchars($item['date_pub']) ?></span>
                                    </div>
                                    <div class="card-body">
                                        <h4 class="card-title"><a href="<?= URL_ACTUALITES ?>?id=<?= urlencode($item['id']) ?>"><?= htmlspecialchars($item['titre']) ?></a></h4>
                                        <p class="card-text"><?= htmlspecialchars(mb_strimwidth(strip_tags($item['paraph1'] ?? ''), 0, 140, '...')) ?></p>
                                        <a href="<?= URL_ACTUALITES ?>?id=<?= urlencode($item['id']) ?>" class="mini-link">Lire l’article</a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            </div>

            <aside class="news-sidebar">
                <div class="sidebar-card emphasis-card">
                    <span class="aside-label">En bref</span>
                    <h3><?= htmlspecialchars(mb_strimwidth($actu['titre'], 0, 95, '...')) ?></h3>
                    <p><?= htmlspecialchars(mb_strimwidth(trim(strip_tags($leadParagraph)), 0, 180, '...')) ?></p>
                </div>

                <?php if (!empty($sidebarDocs)): ?>
                    <div class="sidebar-card doc-sidebar-card">
                        <span class="aside-label">Ressources</span>
                        <h3>Documents utiles</h3>
                        <div class="doc-sidebar-list">
                            <?php foreach ($sidebarDocs as $doc):
                                $docId = (int) ($doc['id'] ?? 0);
                                $pdfFileName = basename($doc['fichier_pdf'] ?? '');
                                $viewUrl = ($docId > 0 && $pdfFileName !== '')
                                    ? BASE_URL . 'pagesweb/documentation_event.php?doc_id=' . $docId . '&action=view&file=' . rawurlencode($pdfFileName)
                                    : '#';
                            ?>
                                <a class="doc-sidebar-item" href="<?= htmlspecialchars($viewUrl) ?>" target="_blank" rel="noopener">
                                    <strong><?= htmlspecialchars($doc['titreDoc'] ?? 'Document') ?></strong>
                                    <span><?= htmlspecialchars($doc['auteur'] ?? 'Publication SN1325') ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="sidebar-card recent-news-card">
                    <span class="aside-label">Archives récentes</span>
                    <h3>Autres actualités</h3>

                    <?php
                    $initialVisible = 6;
                    $batch = 4;
                    $idx = 0;
                    foreach ($sidebarItems as $recent):
                        $hidden = ($idx >= $initialVisible) ? 'd-none extra-news' : '';
                    ?>
                        <article class="single-post <?= $hidden ?>" data-news-index="<?= $idx ?>">
                            <div class="image">
                                <?php if (!empty($recent['imgMise'])): ?>
                                    <img src="<?= IMG_DIR . 'actualites/' . htmlspecialchars($recent['imgMise']) ?>" alt="<?= htmlspecialchars($recent['titre']) ?>">
                                <?php endif; ?>
                            </div>
                            <div class="content">
                                <h5><a href="<?= URL_ACTUALITES ?>?id=<?= urlencode($recent['id']) ?>"><?= htmlspecialchars($recent['titre']) ?></a></h5>
                                <ul class="comment">
                                    <li><i class="fa fa-calendar"></i> <?= htmlspecialchars($recent['date_pub']) ?></li>
                                </ul>
                            </div>
                        </article>
                    <?php $idx++; endforeach; ?>

                    <?php if (count($sidebarItems) > $initialVisible): ?>
                        <div class="sidebar-actions">
                            <button id="loadMoreNews" class="btn sidebar-btn" data-batch="<?= $batch ?>">Charger plus</button>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php require_once $footerPath; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('loadMoreNews');
    if (!button) {
        return;
    }

    button.addEventListener('click', function () {
        const hiddenItems = Array.from(document.querySelectorAll('.extra-news.d-none'));
        const batch = parseInt(button.dataset.batch || '4', 10);

        hiddenItems.slice(0, batch).forEach(function (item) {
            item.classList.remove('d-none');
        });

        if (document.querySelectorAll('.extra-news.d-none').length === 0) {
            button.style.display = 'none';
        }
    });
});
</script>