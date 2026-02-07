<?php

require_once __DIR__ . '/../configUrl.php';

require_once __DIR__ . '/../defConstLiens.php';

require_once $dateDbConnect; // Connexion PDO : $pdo

// Page-specific CSS (chargé via headerPage.php)
$SKIP_PAGE_TITLE = true; // hide the h2 page title for this page
$pageCss = CSS_DIR . 'actualites.css';

// Header
require_once $headerPath;



// ===============================

// Vérification de l'ID passé en URL

// ===============================

// Si aucun ID n’est passé, on récupère le plus récent

if (isset($_GET['id'])) {

    $id = (int) $_GET['id'];

} else {

    $stmt = $pdo->query("SELECT id FROM actualites ORDER BY id DESC LIMIT 1");

    $last = $stmt->fetch(PDO::FETCH_ASSOC);

    $id = $last ? $last['id'] : 0;

}



// Si toujours pas d’ID (aucune actualité en base)

if (!$id) {

    echo "<div class='alert alert-warning text-center py-5'>Aucune actualité disponible pour le moment.</div>";

    exit;

}





// ===============================

// Récupération de l'actualité

// ===============================

$stmt = $pdo->prepare("SELECT * FROM actualites WHERE id = :id");
$stmt->execute([':id' => $id]);
$actu = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les actualités récentes
$actualitesStmt = $pdo->query("SELECT * FROM actualites ORDER BY id DESC LIMIT 10");
$actualites = $actualitesStmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les documents
$docsStmt = $pdo->query("SELECT * FROM documentations ORDER BY id DESC LIMIT 10");
$docs = $docsStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- CareMed-style Hero -->
<?php $heroImg = !empty($actu['imgMise']) ? IMG_DIR . 'actualites/' . htmlspecialchars($actu['imgMise']) : ''; ?>
<section class="caremed-hero" style="background-image: url('<?= $heroImg ?>'); background-color:<?= $heroImg ? 'transparent' : 'var(--primary)' ?>;">
    <div class="overlay"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-breadcrumb">Accueil / Actualités</div>
            <h1>Actualités 1325</h1>
        </div>
    </div>
</section>

<!-- Main News Area -->
<section class="news-single section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12">
                <article class="news-card">
                    <?php if (!empty($actu['imgMise'])): ?>
                        <div class="news-image">
                            <img src="<?= IMG_DIR . 'actualites/' . htmlspecialchars($actu['imgMise']) ?>" alt="<?= htmlspecialchars($actu['titre']) ?>">
                        </div>
                    <?php endif; ?>
                    <div class="news-body">
                        <div class="news-meta"><?= htmlspecialchars($actu['date_pub']) ?> &nbsp; | &nbsp; <?= htmlspecialchars($actu['auteur']) ?></div>
                        <h2 class="news-title"><?= htmlspecialchars($actu['titre']) ?></h2>
                        <div class="news-excerpt">
                            <?php for ($i = 1; $i <= 10; $i++):
                                $para = $actu['paraph'.$i] ?? '';
                                if ($para): ?>
                                    <p><?= htmlspecialchars($para) ?></p>
                            <?php endif; endfor; ?>
                        </div>

                        <?php
                        // Build unique gallery list and avoid duplicates (including main image)
                        $galleryImgs = array_filter([
                            $actu['imgPub1'] ?? '',
                            $actu['imgPub2'] ?? ''
                        ]);
                        // remove empty and duplicate values
                        $galleryImgs = array_values(array_unique($galleryImgs));
                        // also exclude the main featured image if present
                        if (!empty($actu['imgMise'])) {
                            $galleryImgs = array_filter($galleryImgs, function($v) use ($actu) { return $v !== $actu['imgMise']; });
                            $galleryImgs = array_values($galleryImgs);
                        }
                        if (!empty($galleryImgs)): ?>
                        <div class="image-gallery mt-3">
                            <div class="image-gallery-carousel owl-carousel">
                                <?php foreach ($galleryImgs as $gimg): ?>
                                    <div class="item single-image">
                                        <a href="<?= IMG_DIR . 'actualites/' . htmlspecialchars($gimg) ?>" class="gallery-link" title="<?= htmlspecialchars($actu['titre']) ?>">
                                            <img src="<?= IMG_DIR . 'actualites/' . htmlspecialchars($gimg) ?>" alt="">
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($actu['messageFort'])): ?>
                            <blockquote class="overlay"><p><?= htmlspecialchars($actu['messageFort']) ?></p></blockquote>
                        <?php endif; ?>
                    </div>
                </article>

                <!-- Autres actualités (inspiré CareMed) - grille sous l'article -->
                <?php if (!empty($sidebarItems)): ?>
                <section class="related-news mt-5">
                    <h3 class="section-title">Autres actualités</h3>
                    <div class="news-grid row">
                        <?php foreach ($sidebarItems as $item): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="news-card-mini card">
                                    <div class="card-image">
                                        <?php if (!empty($item['imgMise'])): ?>
                                            <a href="<?= URL_ACTUALITES ?>?id=<?= urlencode($item['id']) ?>">
                                                <img src="<?= IMG_DIR . 'actualites/' . htmlspecialchars($item['imgMise']) ?>" alt="<?= htmlspecialchars($item['titre']) ?>">
                                            </a>
                                        <?php endif; ?>
                                        <span class="date-badge"><?= htmlspecialchars($item['date_pub']) ?></span>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><a href="<?= URL_ACTUALITES ?>?id=<?= urlencode($item['id']) ?>"><?= htmlspecialchars($item['titre']) ?></a></h5>
                                        <p class="card-text"><?= mb_strimwidth(strip_tags($item['paraph1'] ?? ''), 0, 120, '...') ?></p>
                                        <a href="<?= URL_ACTUALITES ?>?id=<?= urlencode($item['id']) ?>" class="btn btn-sm btn-primary">Lire la suite</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

            </div>

            <!-- Sidebar: afficher toutes les actualités (hors article courant) -->
            <div class="col-lg-4 col-12">
                <div class="main-sidebar">
                    <!-- Recherche -->
                    <div class="single-widget search">
                        <div class="form">
                            <input type="text" placeholder="Rechercher ici...">
                            <a class="button" href="#"><i class="fa fa-search"></i></a>
                        </div>
                    </div>

                    <!-- (Documentation block removed as requested) -->

                    <!-- Toutes les actualités (sidebar) with 'Charger plus' -->
                    <div class="single-widget recent-post">
                        <h3 class="title">Autres actualités</h3>
                        <?php
                        // Build filtered list (exclude current)
                        $sidebarItems = [];
                        foreach ($actualites as $r) {
                            if (!empty($actu['id']) && $r['id'] == $actu['id']) continue;
                            $sidebarItems[] = $r;
                        }
                        $initialVisible = 6;
                        $batch = 5;
                        $idx = 0;
                        foreach ($sidebarItems as $recent):
                            $hidden = ($idx >= $initialVisible) ? 'd-none extra-news' : '';
                        ?>
                            <div class="single-post <?= $hidden ?>" data-news-index="<?= $idx ?>">
                                <div class="image">
                                    <img src="<?= IMG_DIR . 'actualites/' . htmlspecialchars($recent['imgMise']) ?>" alt="#">
                                </div>
                                <div class="content">
                                    <h5>
                                        <a href="<?= URL_ACTUALITES ?>?id=<?= urlencode($recent['id']) ?>">
                                            <?= htmlspecialchars($recent['titre']) ?>
                                        </a>
                                    </h5>
                                    <ul class="comment">
                                        <li><i class="fa fa-calendar"></i> <?= htmlspecialchars($recent['date_pub']) ?></li>
                                    </ul>
                                </div>
                            </div>
                        <?php $idx++; endforeach; ?>

                        <?php if (count($sidebarItems) > $initialVisible): ?>
                            <div class="text-center mt-3">
                                <button id="loadMoreNews" class="btn btn-outline-primary" data-batch="<?= $batch ?>" data-initial="<?= $initialVisible ?>">Charger plus</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

    </div>

</section>


<!-- Composant Footer -->

<?php require_once $footerPath; ?>

