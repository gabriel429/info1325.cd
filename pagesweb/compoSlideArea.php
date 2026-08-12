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
            <?php foreach ($featuredNews as $news): ?>
                <div class="single-slider" style="background-image:url('<?= htmlspecialchars(actualite_image_url($news['imgMise'] ?? null)) ?>')">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-7">
                                <div class="text">
                                    <div style="background: rgba(16,35,63,0.78); display: inline-block; padding: 18px 24px; border-radius: 6px;">
                                        <h1 style="color:#fff; margin:0;"><?= htmlspecialchars($news['titre']) ?></h1>
                                    </div>
                                    <p style="margin-top:14px;font-weight:700;"><?= htmlspecialchars(actualite_published_label($news['date_pub'] ?? null)) ?></p>
                                    <p><?= htmlspecialchars(actualite_summary($news, 170)) ?></p>
                                    <div class="button">
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
                        <div class="row">
                            <div class="col-lg-7">
                                <div class="text">
                                    <?php if (!empty($slide['title'])): ?>
                                        <div style="background: rgba(16,35,63,0.72); display: inline-block; padding: 18px 24px; border-radius: 6px;">
                                            <h1 style="color:#fff; margin:0;"><?= htmlspecialchars($slide['title']) ?></h1>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($slide['subtitle'])): ?>
                                        <p><?= nl2br(htmlspecialchars($slide['subtitle'])) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($slide['btn_text'])): ?>
                                        <div class="button">
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
                <div class="container"><div class="row"><div class="col-lg-7"><div class="text"><div style="background: rgba(16,35,63,0.72); display: inline-block; padding: 18px 24px; border-radius: 6px;"><h1 style="color:#FFF; margin:0;">La RDC, marquée par des décennies de conflits armés dans l'Est du pays, est un cas prioritaire pour l'application de la Résolution 1325.</h1></div><p></p></div></div></div></div>
            </div>
            <div class="single-slider" style="background-image:url('<?= IMG_DIR ?>ministre.jpeg')">
                <div class="container"><div class="row"><div class="col-lg-7"><div class="text"><h1>République Démocratique du Congo <span>SN1325</span></h1><p>Assurer la participation effective des femmes à la prévention et résolution des conflits en RDC.</p><div class="button"><a href="https://genre.gouv.cd/" class="btn">MINGENRE</a></div></div></div></div></div>
            </div>
            <div class="single-slider" style="background-image:url('<?= IMG_DIR ?>banner3.png')">
                <div class="container"><div class="row"><div class="col-lg-7"><div class="text"><div style="background: rgba(16,35,63,0.72); display: inline-block; padding: 18px 24px; border-radius: 6px;"><h1 style="color:#fff; margin:0;">La RDC est considérée comme un cas emblématique pour l'application de la Résolution 1325.</h1></div><p></p><div class="button"><a href="mailto:lapiardidier561@gmail.com" target="_blank" class="btn primary">Contacter le Secrétariat National</a></div></div></div></div></div>
            </div>
            <div class="single-slider" style="background-image:url('<?= IMG_DIR ?>PAN.png')">
                <div class="container"><div class="row"><div class="col-lg-7"><div class="text"><div style="background: rgba(16,35,63,0.72); display: inline-block; padding: 18px 24px; border-radius: 6px;"><h1 style="color:#fff; margin:0;">Vulgarisation du Plan d'Action National - 3ème génération</h1></div><p></p><div class="button"><a href="pagesweb/Plan d'Action National 3eme génération_125445.pdf" class="btn primary">Consulter</a></div></div></div></div></div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- End Slider Area -->
