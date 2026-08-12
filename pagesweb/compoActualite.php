<?php

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect;
require_once __DIR__ . '/actualites_helper.php';

ensure_actualites_schema($pdo);

$stmt = $pdo->query("
    SELECT *
    FROM actualites
    WHERE statut = 'publie'
    ORDER BY date_pub DESC, id DESC
    LIMIT 3
");
$actualites = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!-- Start Blog Area -->

<style>
.single-news .news-head img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    display: block;
}
.single-news .news-body {
    min-height: 220px;
}
.single-news .news-content .text {
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-align: left;
}
.single-news .news-content .date {
    font-size: .9rem;
    color: #777;
    margin-bottom: 8px;
}
.single-news .news-content h2 a {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    text-align: left;
}
</style>

<section class="blog section" id="blog">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2>Actualités 1325</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <?php foreach ($actualites as $actualite): ?>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="single-news">
                        <div class="news-head">
                            <a href="<?= htmlspecialchars(actualite_url($actualite)) ?>">
                                <img src="<?= htmlspecialchars(actualite_image_url($actualite['imgMise'] ?? null)) ?>" alt="<?= htmlspecialchars($actualite['titre']) ?>">
                            </a>
                        </div>
                        <div class="news-body">
                            <div class="news-content">
                                <div class="date"><?= htmlspecialchars(actualite_published_label($actualite['date_pub'] ?? null)) ?></div>
                                <h2><a href="<?= htmlspecialchars(actualite_url($actualite)) ?>"><?= htmlspecialchars($actualite['titre']) ?></a></h2>
                                <p class="text"><?= htmlspecialchars(actualite_summary($actualite, 190)) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<!-- End Blog Area -->
