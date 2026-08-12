<?php

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect;
require_once __DIR__ . '/track_visitor.php';
require_once __DIR__ . '/csrf_helper.php';
require_once __DIR__ . '/actualites_helper.php';

ensure_actualites_schema($pdo);

$pageCss = CSS_DIR . 'actualites.css';
$SKIP_PAGE_TITLE = true;
$slug = trim((string)($_GET['slug'] ?? ''));
$legacyId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isDetail = $slug !== '' || $legacyId > 0;

function actualite_find_public(PDO $pdo, string $slug, int $legacyId): ?array
{
    if ($slug !== '') {
        $stmt = $pdo->prepare("SELECT * FROM actualites WHERE slug = :slug AND statut = 'publie' LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    if ($legacyId > 0) {
        $stmt = $pdo->prepare("SELECT * FROM actualites WHERE id = :id AND statut = 'publie' LIMIT 1");
        $stmt->execute([':id' => $legacyId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    return null;
}

function actualite_author(array $actualite): string
{
    return trim((string)($actualite['auteur'] ?? '')) ?: 'Secrétariat National 1325';
}

if ($isDetail) {
    $actu = actualite_find_public($pdo, $slug, $legacyId);

    if (!$actu) {
        http_response_code(404);
        require_once $headerPath;
        ?>
        <section class="actualites-empty">
            <div class="container">
                <div class="empty-state">
                    <span>Actualité introuvable</span>
                    <h1>Cette publication n'est pas disponible.</h1>
                    <p>Elle a peut-être été archivée, déplacée ou retirée de la publication.</p>
                    <a href="<?= URL_ACTUALITES ?>" class="news-button">Voir les actualités</a>
                </div>
            </div>
        </section>
        <?php
        require_once $footerPath;
        exit;
    }

    if ($legacyId > 0 && !empty($actu['slug'])) {
        header('Location: ' . actualite_url($actu), true, 301);
        exit;
    }

    $relatedStmt = $pdo->prepare("
        SELECT *
        FROM actualites
        WHERE statut = 'publie' AND id != :id
        ORDER BY a_la_une DESC, date_pub DESC, id DESC
        LIMIT 3
    ");
    $relatedStmt->execute([':id' => (int)$actu['id']]);
    $relatedNews = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

    $pageTitle = $actu['titre'] . ' - Actualités 1325';
    $pageDescription = actualite_summary($actu, 220);
    $pageUrl = actualite_url($actu);
    $pageImage = actualite_image_url($actu['imgMise'] ?? null);
    $pageImageAlt = $actu['titre'];
    $pageType = 'article';
    $pageAuthor = actualite_author($actu);
    if (!empty($actu['date_pub'])) {
        $pagePublishedTime = (new DateTime((string)$actu['date_pub']))->format(DateTime::ATOM);
    }
    require_once $headerPath;
    ?>

    <main class="actualites-page">
        <article class="article-detail">
            <header class="article-header">
                <div class="container">
                    <a href="<?= URL_ACTUALITES ?>" class="back-link">Actualités</a>
                    <div class="article-meta">
                        <span><?= actualite_escape($actu['categorie'] ?: 'Actualité') ?></span>
                        <span><?= actualite_escape(actualite_published_label($actu['date_pub'] ?? null)) ?></span>
                        <span><?= actualite_escape(actualite_author($actu)) ?></span>
                    </div>
                    <h1><?= actualite_escape($actu['titre']) ?></h1>
                    <?php $summary = actualite_summary($actu, 320); ?>
                    <?php if ($summary !== ''): ?>
                        <p class="article-standfirst"><?= actualite_escape($summary) ?></p>
                    <?php endif; ?>
                </div>
            </header>

            <div class="container article-shell">
                <figure class="article-cover">
                    <img src="<?= actualite_escape(actualite_image_url($actu['imgMise'] ?? null)) ?>" alt="<?= actualite_escape($actu['titre']) ?>">
                </figure>

                <div class="article-content">
                    <?= actualite_sanitize_html((string)($actu['contenu'] ?? '')) ?>
                </div>

                <?php if (!empty($relatedNews)): ?>
                    <section class="related-news">
                        <div class="section-heading">
                            <span>À lire aussi</span>
                            <h2>Autres actualités</h2>
                        </div>
                        <div class="news-grid compact-grid">
                            <?php foreach ($relatedNews as $item): ?>
                                <article class="news-card">
                                    <a class="news-card-image" href="<?= actualite_escape(actualite_url($item)) ?>">
                                        <img src="<?= actualite_escape(actualite_image_url($item['imgMise'] ?? null)) ?>" alt="<?= actualite_escape($item['titre']) ?>">
                                    </a>
                                    <div class="news-card-body">
                                        <div class="news-card-meta">
                                            <span><?= actualite_escape($item['categorie'] ?: 'Actualité') ?></span>
                                            <time><?= actualite_escape(actualite_published_label($item['date_pub'] ?? null)) ?></time>
                                        </div>
                                        <h3><a href="<?= actualite_escape(actualite_url($item)) ?>"><?= actualite_escape($item['titre']) ?></a></h3>
                                        <p><?= actualite_escape(actualite_summary($item, 135)) ?></p>
                                        <a class="read-link" href="<?= actualite_escape(actualite_url($item)) ?>">Lire la suite</a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            </div>
        </article>
    </main>

    <?php
    require_once $footerPath;
    exit;
}

$featuredStmt = $pdo->query("
    SELECT *
    FROM actualites
    WHERE statut = 'publie'
    ORDER BY a_la_une DESC, date_pub DESC, id DESC
    LIMIT 1
");
$featured = $featuredStmt->fetch(PDO::FETCH_ASSOC) ?: null;

$stmt = $pdo->query("
    SELECT *
    FROM actualites
    WHERE statut = 'publie'
    ORDER BY date_pub DESC, id DESC
");
$actualites = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Actualités 1325';
$pageDescription = "Articles, activités, plaidoyers et annonces autour de l'agenda Femmes, Paix et Sécurité en RDC.";
$pageUrl = URL_ACTUALITES;
if ($featured && !empty($featured['imgMise'])) {
    $pageImage = actualite_image_url($featured['imgMise']);
    $pageImageAlt = $featured['titre'] ?? $pageTitle;
}
require_once $headerPath;
?>

<main class="actualites-page">
    <section class="news-index-hero">
        <div class="container">
            <div class="index-heading">
                <span>Actualités</span>
                <h1>Les dernières publications du Secrétariat National 1325</h1>
                <p>Articles, activités, plaidoyers et annonces autour de l'agenda Femmes, Paix et Sécurité en RDC.</p>
            </div>
        </div>
    </section>

    <section class="news-index">
        <div class="container">
            <?php if (empty($actualites)): ?>
                <div class="empty-state">
                    <span>Actualités</span>
                    <h2>Aucune publication pour le moment.</h2>
                    <p>Les prochains articles publiés apparaîtront automatiquement ici.</p>
                </div>
            <?php else: ?>
                <?php if ($featured): ?>
                    <article class="featured-article">
                        <a class="featured-image" href="<?= actualite_escape(actualite_url($featured)) ?>">
                            <img src="<?= actualite_escape(actualite_image_url($featured['imgMise'] ?? null)) ?>" alt="<?= actualite_escape($featured['titre']) ?>">
                        </a>
                        <div class="featured-copy">
                            <div class="news-card-meta">
                                <span><?= actualite_escape($featured['categorie'] ?: 'Actualité') ?></span>
                                <time><?= actualite_escape(actualite_published_label($featured['date_pub'] ?? null)) ?></time>
                            </div>
                            <h2><a href="<?= actualite_escape(actualite_url($featured)) ?>"><?= actualite_escape($featured['titre']) ?></a></h2>
                            <p><?= actualite_escape(actualite_summary($featured, 260)) ?></p>
                            <a href="<?= actualite_escape(actualite_url($featured)) ?>" class="news-button">Lire l'article</a>
                        </div>
                    </article>
                <?php endif; ?>

                <div class="section-heading">
                    <span>Publications récentes</span>
                    <h2>Toutes les actualités</h2>
                </div>

                <div class="news-grid">
                    <?php foreach ($actualites as $item): ?>
                        <article class="news-card">
                            <a class="news-card-image" href="<?= actualite_escape(actualite_url($item)) ?>">
                                <img src="<?= actualite_escape(actualite_image_url($item['imgMise'] ?? null)) ?>" alt="<?= actualite_escape($item['titre']) ?>">
                            </a>
                            <div class="news-card-body">
                                <div class="news-card-meta">
                                    <span><?= actualite_escape($item['categorie'] ?: 'Actualité') ?></span>
                                    <time><?= actualite_escape(actualite_published_label($item['date_pub'] ?? null)) ?></time>
                                </div>
                                <h3><a href="<?= actualite_escape(actualite_url($item)) ?>"><?= actualite_escape($item['titre']) ?></a></h3>
                                <p><?= actualite_escape(actualite_summary($item, 165)) ?></p>
                                <a class="read-link" href="<?= actualite_escape(actualite_url($item)) ?>">Lire la suite</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once $footerPath; ?>
