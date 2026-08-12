<!-- Slider Area -->

<?php
try {
    require_once __DIR__ . '/../configUrl.php';
    require_once __DIR__ . '/../defConstLiens.php';
    require_once $dateDbConnect;
    require_once __DIR__ . '/actualites_helper.php';

    ensure_actualites_schema($pdo);

    $featuredStmt = $pdo->query("
        SELECT *
        FROM actualites
        WHERE statut = 'publie' AND a_la_une = 1
        ORDER BY date_pub DESC, id DESC
        LIMIT 2
    ");
    $featuredNews = $featuredStmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($featuredNews) < 2) {
        $excludeIds = array_map(static function ($row) {
            return (int)$row['id'];
        }, $featuredNews);
        $limit = 2 - count($featuredNews);
        $sql = "
            SELECT *
            FROM actualites
            WHERE statut = 'publie'
        ";

        if (!empty($excludeIds)) {
            $sql .= ' AND id NOT IN (' . implode(',', $excludeIds) . ')';
        }

        $sql .= " ORDER BY date_pub DESC, id DESC LIMIT " . (int)$limit;
        $featuredNews = array_merge($featuredNews, $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));
    }

    $stmt = $pdo->query('SELECT * FROM slides WHERE active = 1 ORDER BY `position` ASC');
    $manualSlides = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $featuredNews = [];
    $manualSlides = [];
}

if (!function_exists('slideImagePath')) {
    function slideImagePath(array $row, int $pos): string
    {
        if (!empty($row['image'])) {
            return IMG_DIR . 'slider/' . rawurlencode($row['image']);
        }

        $candidates = ["slider{$pos}.jpg", "slider{$pos}.png", "slider{$pos}.jpeg", "slider{$pos}.webp", "slider{$pos}.gif", "slider.jpg", "slider.png"];
        foreach ($candidates as $candidate) {
            if (file_exists(__DIR__ . '/../img/' . $candidate)) {
                return IMG_DIR . $candidate;
            }
        }

        return IMG_DIR . 'banner3.png';
    }
}
?>

<section class="slider">
    <div class="hero-slider">
        <?php if (!empty($featuredNews) || !empty($manualSlides)): ?>
            <?php foreach ($featuredNews as $news): $featuredImage = actualite_image_url($news['imgMise'] ?? null); ?>
                <div class="single-slider featured-news-slide">
                    <div class="slide-backdrop" style="background-image:url('<?= htmlspecialchars($featuredImage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>')" aria-hidden="true"></div>
                    <img class="slide-image" src="<?= htmlspecialchars($featuredImage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" alt="" aria-hidden="true" decoding="async">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-7">
                                <div class="text hero-slide-copy">
                                    <span class="hero-slide-eyebrow">Actualité à la une</span>
                                    <h1><?= htmlspecialchars($news['titre']) ?></h1>
                                    <div class="hero-slide-meta"><?= htmlspecialchars(actualite_published_label($news['date_pub'] ?? null)) ?></div>
                                    <p class="hero-slide-summary"><?= htmlspecialchars(actualite_summary($news, 170)) ?></p>
                                    <div class="button hero-slide-actions">
                                        <a href="<?= htmlspecialchars(actualite_url($news)) ?>" class="btn">Lire la suite</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php foreach ($manualSlides as $slide): $img = slideImagePath($slide, (int)($slide['position'] ?? 1)); ?>
                <div class="single-slider" style="background-image:url('<?= htmlspecialchars($img) ?>')">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-7">
                                <div class="text hero-slide-copy">
                                    <?php if (!empty($slide['title'])): ?>
                                        <span class="hero-slide-eyebrow">SN1325</span>
                                        <h1><?= htmlspecialchars($slide['title']) ?></h1>
                                    <?php endif; ?>
                                    <?php if (!empty($slide['subtitle'])): ?>
                                        <p class="hero-slide-summary"><?= nl2br(htmlspecialchars($slide['subtitle'])) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($slide['btn_text'])): ?>
                                        <div class="button hero-slide-actions">
                                            <a href="<?= htmlspecialchars($slide['btn_url'] ?: '#') ?>" class="btn"><?= htmlspecialchars($slide['btn_text']) ?></a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="single-slider" style="background-image:url('<?= IMG_DIR ?>femme.jpg')">
                <div class="container"><div class="row align-items-center"><div class="col-lg-7"><div class="text hero-slide-copy"><span class="hero-slide-eyebrow">SN1325</span><h1>La RDC, cas prioritaire pour l'application de la Résolution 1325</h1></div></div></div></div>
            </div>
            <div class="single-slider" style="background-image:url('<?= IMG_DIR ?>ministre.jpeg')">
                <div class="container"><div class="row align-items-center"><div class="col-lg-7"><div class="text hero-slide-copy"><span class="hero-slide-eyebrow">SN1325</span><h1>République Démocratique du Congo</h1><p class="hero-slide-summary">Participation effective des femmes à la prévention et à la résolution des conflits en RDC.</p><div class="button hero-slide-actions"><a href="https://genre.gouv.cd/" class="btn">MINGENRE</a></div></div></div></div></div>
            </div>
            <div class="single-slider" style="background-image:url('<?= IMG_DIR ?>banner3.png')">
                <div class="container"><div class="row align-items-center"><div class="col-lg-7"><div class="text hero-slide-copy"><span class="hero-slide-eyebrow">Résolution 1325</span><h1>Un cadre national pour l'agenda Femmes, Paix et Sécurité</h1><div class="button hero-slide-actions"><a href="mailto:lapiardidier561@gmail.com" target="_blank" class="btn primary">Contacter le Secrétariat</a></div></div></div></div></div>
            </div>
            <div class="single-slider" style="background-image:url('<?= IMG_DIR ?>PAN.png')">
                <div class="container"><div class="row align-items-center"><div class="col-lg-7"><div class="text hero-slide-copy"><span class="hero-slide-eyebrow">Plan d'Action National</span><h1>Vulgarisation du PAN 1325 de troisième génération</h1><div class="button hero-slide-actions"><a href="pagesweb/Plan d'Action National 3eme génération_125445.pdf" class="btn primary">Consulter</a></div></div></div></div></div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- End Slider Area -->
