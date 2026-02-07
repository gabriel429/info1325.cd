<?php

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // Connexion PDO : $pdo

// Page-specific CSS
$pageCss = CSS_DIR . 'actualites.css';

// Header
require_once $headerPath;

// Récupérer toutes les actualités
$stmt = $pdo->query("SELECT * FROM actualites ORDER BY id DESC");
$actualites = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- CareMed-style Hero -->
<section class="caremed-hero" style="background-color:var(--primary)">
    <div class="overlay"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-breadcrumb">Accueil / Actualités</div>
            <h1>Actualités 1325</h1>
        </div>
    </div>
</section>

<!-- Listing: grille d'actualités -->
<section class="news-list section">
    <div class="container">
        <div class="row news-grid">
            <?php if (empty($actualites)): ?>
                <div class="col-12"><div class="alert alert-warning text-center">Aucune actualité pour le moment.</div></div>
            <?php else: ?>
                <?php foreach ($actualites as $item): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="news-card-mini card">
                            <div class="card-image">
                                <?php if (!empty($item['imgMise'])): ?>
                                    <a href="<?= URL_ACTUALITES ?>?id=<?= urlencode($item['id']) ?>">
                                        <img src="<?= IMG_DIR . 'actualites/' . htmlspecialchars($item['imgMise']) ?>" alt="<?= htmlspecialchars($item['titre']) ?>">
                                    </a>
                                <?php else: ?>
                                    <a href="<?= URL_ACTUALITES ?>?id=<?= urlencode($item['id']) ?>">
                                        <img src="<?= IMG_DIR . 'no-image.png' ?>" alt="<?= htmlspecialchars($item['titre']) ?>">
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
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once $footerPath; ?>
