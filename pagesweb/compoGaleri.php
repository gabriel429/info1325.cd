<!-- Start portfolio -->
<?php
$galleryDataFile = __DIR__ . '/../data/galerie.json';
$galleryFsDir = __DIR__ . '/../img/galerie/';
$galleryItems = [];

if (file_exists($galleryDataFile)) {
    $decodedGalleryItems = json_decode(file_get_contents($galleryDataFile), true);
    $galleryItems = is_array($decodedGalleryItems) ? $decodedGalleryItems : [];
}

if (!function_exists('home_gallery_h')) {
    function home_gallery_h($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('home_gallery_image_url')) {
    function home_gallery_image_url(string $file): string
    {
        return IMG_DIR . 'galerie/' . rawurlencode($file);
    }
}

if (!function_exists('home_gallery_item_id')) {
    function home_gallery_item_id(string $file): string
    {
        return 'gallery-image-' . preg_replace('/[^A-Za-z0-9_-]+/', '-', $file);
    }
}

if (!function_exists('home_gallery_page_url')) {
    function home_gallery_page_url(string $activity, string $file): string
    {
        $activity = trim($activity) !== '' ? trim($activity) : 'Sans catégorie';
        $query = http_build_query([
            'activity' => $activity,
            'image' => $file,
        ]);

        return URL_GALERIE . '?' . $query . '#' . home_gallery_item_id($file);
    }
}

$galleryItems = array_values(array_filter($galleryItems, static function ($item) use ($galleryFsDir): bool {
    $file = trim((string) ($item['file'] ?? ''));
    return $file !== '' && is_file($galleryFsDir . basename($file));
}));

usort($galleryItems, static function ($left, $right): int {
    $rightUploaded = (int) ($right['uploaded'] ?? 0);
    $leftUploaded = (int) ($left['uploaded'] ?? 0);

    if ($rightUploaded === $leftUploaded) {
        return strcmp((string) ($right['file'] ?? ''), (string) ($left['file'] ?? ''));
    }

    return $rightUploaded <=> $leftUploaded;
});
?>

<section class="portfolio section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2>GALERIE PHOTOS</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-12">
                <div class="owl-carousel portfolio-slider">
                    <?php if (empty($galleryItems)): ?>
                        <?php
                        $staticGalleryImages = [
                            ['file' => 'didier.jpg', 'label' => 'SN1325'],
                            ['file' => 'snational132513.png', 'label' => 'SN1325'],
                            ['file' => 'snational1325.png', 'label' => 'SN1325'],
                            ['file' => 'snational132505.png', 'label' => 'SN1325'],
                            ['file' => 'snational132506.png', 'label' => 'SN1325'],
                            ['file' => 'snational132507.png', 'label' => 'SN1325'],
                            ['file' => 'snational132508.png', 'label' => 'SN1325'],
                        ];
                        ?>
                        <?php foreach ($staticGalleryImages as $staticImage): ?>
                            <div class="single-pf">
                                <a class="portfolio-gallery-link" href="<?= home_gallery_h(URL_GALERIE) ?>">
                                    <img class="img-galery" src="<?= home_gallery_h(IMG_DIR . $staticImage['file']) ?>" alt="<?= home_gallery_h($staticImage['label']) ?>">
                                    <span class="btn">Voir la galerie</span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php foreach ($galleryItems as $item): ?>
                            <?php
                            $file = basename(trim((string) ($item['file'] ?? '')));
                            $activity = trim((string) ($item['activity'] ?? 'Sans catégorie'));
                            $activity = $activity !== '' ? $activity : 'Sans catégorie';
                            $imageUrl = home_gallery_image_url($file);
                            $galleryUrl = home_gallery_page_url($activity, $file);
                            ?>
                            <div class="single-pf">
                                <a class="portfolio-gallery-link" href="<?= home_gallery_h($galleryUrl) ?>" title="<?= home_gallery_h($activity) ?>">
                                    <img class="img-galery" src="<?= home_gallery_h($imageUrl) ?>" alt="<?= home_gallery_h($activity) ?>" loading="lazy" decoding="async">
                                    <span class="btn">Voir la galerie</span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!--/ End portfolio -->
