<?php

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';

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
$heroImage = BASE_URL . 'img/bread-bg21.jpg';

$SKIP_PAGE_TITLE = true;
require_once $headerPath;
?>

<section class="gallery-hero" style="background-image: url('<?= h($heroImage) ?>');">
    <div class="container">
        <div class="hero-content">
            <div class="hero-breadcrumb">Accueil / Galerie</div>
            <h1>Galerie photo du SN1325</h1>
            <p class="lead">Retrouvez les temps forts, ateliers, activites mediatiques et moments institutionnels documentes par le Secretariat National 1325.</p>
        </div>
    </div>
</section>

<section class="gallery-shell gallery-intro section">
    <div class="container">
        <div class="gallery-overview">
            <article class="overview-card">
                <span class="overview-label">Collection active</span>
                <strong><?= count($items) ?> visuels</strong>
                <p>Une archive photographique organisee par activite pour documenter les actions menees autour de la Resolution 1325.</p>
            </article>
            <article class="overview-card">
                <span class="overview-label">Categories</span>
                <strong><?= count($activityCounts) ?> rubriques</strong>
                <p>Les albums sont filtrables par evenement, emission, atelier ou sequence institutionnelle.</p>
            </article>
            <article class="overview-card">
                <span class="overview-label">Derniere mise a jour</span>
                <strong><?= h($latestPublication) ?></strong>
                <p>Les nouvelles images sont integrees au fil des activites relayees par la plateforme.</p>
            </article>
        </div>

        <div class="gallery-lead-panel">
            <div>
                <span class="section-kicker">Parcours visuel</span>
                <h2>Une galerie alignee sur l'identite editoriale du site</h2>
                <p>Cette page reprend le meme header, le meme footer et la meme logique visuelle que les autres espaces editoriaux du site, tout en conservant un acces rapide aux images et aux filtres par activite.</p>
            </div>
            <div>
                <span class="section-kicker">Navigation</span>
                <ul class="gallery-points">
                    <li>Filtrez les images par activite ou affichez l'ensemble de la collection.</li>
                    <li>Cliquez sur une vignette pour ouvrir l'image en grand format.</li>
                    <li>Conservez un parcours coherent avec le header et le footer partages du site.</li>
                </ul>
            </div>
        </div>

        <div class="gallery-filters-panel">
            <div class="gallery-filters-header">
                <div>
                    <span class="section-kicker">Filtrer la galerie</span>
                    <h2><?= h($currentActivityLabel) ?></h2>
                </div>
                <p><?= count($filtered) ?> image<?= count($filtered) > 1 ? 's' : '' ?> affichee<?= count($filtered) > 1 ? 's' : '' ?></p>
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
                <h3>Cette activite ne contient pas encore de visuels publies</h3>
                <p>Choisissez une autre rubrique ou revenez a l'ensemble de la galerie pour continuer la navigation.</p>
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
