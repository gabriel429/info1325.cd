<?php

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once __DIR__ . '/track_visitor.php';
require_once __DIR__ . '/csrf_helper.php';

$pageCss = CSS_DIR . 'gallery.css';

$dataFile = __DIR__ . '/../data/galerie.json';
$items = [];

if (file_exists($dataFile)) {
    $items = json_decode(file_get_contents($dataFile), true) ?: [];
}

function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$activity = isset($_GET['activity']) ? trim((string) $_GET['activity']) : 'all';
$activity = $activity === '' ? 'all' : $activity;

$activityCounts = [];
$latestTimestamp = 0;

foreach ($items as $item) {
    $label = trim((string) ($item['activity'] ?? 'Sans catégorie'));
    $label = $label !== '' ? $label : 'Sans catégorie';
    $activityCounts[$label] = ($activityCounts[$label] ?? 0) + 1;

    $uploaded = isset($item['uploaded']) ? (int) $item['uploaded'] : 0;
    if ($uploaded > $latestTimestamp) {
        $latestTimestamp = $uploaded;
    }
}

ksort($activityCounts, SORT_NATURAL | SORT_FLAG_CASE);

$filtered = [];
foreach ($items as $item) {
    $label = trim((string) ($item['activity'] ?? 'Sans catégorie'));
    $label = $label !== '' ? $label : 'Sans catégorie';

    if ($activity === 'all' || strcasecmp($label, $activity) === 0) {
        $filtered[] = $item;
    }
}

$currentActivityLabel = $activity === 'all' ? 'Toutes les activités' : $activity;
$latestPublication = $latestTimestamp > 0 ? date('d/m/Y', $latestTimestamp) : 'Mise à jour continue';

$SKIP_PAGE_TITLE = true;
require_once $headerPath;
?>

<section class="site-page-hero">
    <div class="container">
        <div class="site-page-heading">
            <span>Galerie</span>
            <h1>Galerie photo du SN1325</h1>
            <p class="lead">Temps forts, ateliers, activités médiatiques et moments institutionnels documentés par le Secrétariat National 1325.</p>
        </div>
    </div>
</section>

<section class="gallery-shell gallery-intro section">
    <div class="container">
        <div class="gallery-filters-panel">
            <div class="gallery-filters-header">
                <div>
                    <span class="section-kicker">Photothèque</span>
                    <h2><?= h($currentActivityLabel) ?></h2>
                </div>
                <p><?= count($filtered) ?> image<?= count($filtered) > 1 ? 's' : '' ?> publiée<?= count($filtered) > 1 ? 's' : '' ?> · <?= count($activityCounts) ?> rubrique<?= count($activityCounts) > 1 ? 's' : '' ?></p>
            </div>

            <div class="gallery-filters">
                <a href="?activity=all" class="filter-btn <?= $activity === 'all' ? 'active' : '' ?>">
                    <span>Toutes les activites</span>
                    <span class="count"><?= count($items) ?></span>
                </a>
                <?php foreach ($activityCounts as $label => $count): ?>
                    <a href="?activity=<?= rawurlencode($label) ?>" class="filter-btn <?= strcasecmp($label, $activity) === 0 ? 'active' : '' ?>">
                        <span><?= h($label) ?></span>
                        <span class="count"><?= $count ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (empty($filtered)): ?>
            <div class="gallery-empty">
                <span class="section-kicker">Aucune image</span>
                <h3>Aucun visuel publié pour cette rubrique</h3>
            </div>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($filtered as $item):
                    $file = trim((string) ($item['file'] ?? ''));
                    if ($file === '') {
                        continue;
                    }

                    $label = trim((string) ($item['activity'] ?? 'Sans categorie'));
                    $label = $label !== '' ? $label : 'Sans categorie';
                    $uploaded = isset($item['uploaded']) ? (int) $item['uploaded'] : 0;
                    $dateLabel = $uploaded > 0 ? date('d/m/Y', $uploaded) : 'Date non renseignee';
                    $imageUrl = BASE_URL . 'img/galerie/' . rawurlencode($file);
                ?>
                    <article class="gallery-card">
                        <a class="gallery-link image-link" href="<?= h($imageUrl) ?>" title="<?= h($label) ?>">
                            <div class="gallery-media">
                                <img src="<?= h($imageUrl) ?>" alt="<?= h($label) ?>">
                            </div>
                            <div class="gallery-body">
                                <h3><?= h($label) ?></h3>
                                <div class="gallery-meta">
                                    <span><?= h($dateLabel) ?></span>
                                    <span>Voir en grand</span>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once $footerPath; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && typeof window.jQuery.fn.magnificPopup === 'function') {
        window.jQuery('.image-link').magnificPopup({
            type: 'image',
            gallery: {
                enabled: true
            }
        });
    }
});
</script>
