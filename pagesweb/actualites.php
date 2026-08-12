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

function actualite_share_absolute_url(string $url): string
{
    $url = trim($url);
    if ($url === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $url)) {
        return $url;
    }

    $host = $_SERVER['HTTP_HOST'] ?? 'info1325.cd';
    $forwardedProto = trim(explode(',', (string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))[0]);
    $isLocal = preg_match('/(^localhost$|^127\.0\.0\.1|\.local$|\.test$)/i', $host);
    $scheme = $forwardedProto !== ''
        ? $forwardedProto
        : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || !$isLocal ? 'https' : 'http');

    return rtrim($scheme . '://' . $host, '/') . '/' . ltrim($url, '/');
}

function actualite_share_links(array $actualite): array
{
    $url = actualite_share_absolute_url(actualite_url($actualite));
    $title = actualite_normalize_utf8((string)($actualite['titre'] ?? 'Actualité SN1325'));

    return [
        'url' => $url,
        'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($url),
        'x' => 'https://twitter.com/intent/tweet?text=' . rawurlencode($title) . '&url=' . rawurlencode($url),
        'whatsapp' => 'https://api.whatsapp.com/send?text=' . rawurlencode($title . ' ' . $url),
        'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($url),
    ];
}

function actualite_share_bar(array $actualite): void
{
    $links = actualite_share_links($actualite);
    ?>
    <div class="news-share" aria-label="Partager cette actualité">
        <a class="share-button" href="<?= actualite_escape($links['facebook']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Partager sur Facebook" title="Partager sur Facebook">
            <i class="fa fa-facebook" aria-hidden="true"></i>
        </a>
        <a class="share-button share-x" href="<?= actualite_escape($links['x']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Partager sur X" title="Partager sur X">
            <span aria-hidden="true">X</span>
        </a>
        <a class="share-button" href="<?= actualite_escape($links['whatsapp']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Partager sur WhatsApp" title="Partager sur WhatsApp">
            <i class="fa fa-whatsapp" aria-hidden="true"></i>
        </a>
        <a class="share-button" href="<?= actualite_escape($links['linkedin']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Partager sur LinkedIn" title="Partager sur LinkedIn">
            <i class="fa fa-linkedin" aria-hidden="true"></i>
        </a>
        <button class="share-button share-copy" type="button" data-copy-link="<?= actualite_escape($links['url']) ?>" aria-label="Copier le lien" title="Copier le lien">
            <i class="fa fa-link" aria-hidden="true"></i>
            <span class="share-copy-label">Lien copié</span>
        </button>
    </div>
    <?php
}

function actualite_share_script(): void
{
    static $rendered = false;
    if ($rendered) {
        return;
    }
    $rendered = true;
    ?>
    <script>
    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-copy-link]');
        if (!button) {
            return;
        }

        var link = button.getAttribute('data-copy-link') || '';
        var markCopied = function () {
            button.classList.add('copied');
            window.setTimeout(function () {
                button.classList.remove('copied');
            }, 1800);
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(link).then(markCopied).catch(function () {
                fallbackCopy(link, markCopied);
            });
            return;
        }

        fallbackCopy(link, markCopied);
    });

    function fallbackCopy(link, callback) {
        var input = document.createElement('textarea');
        input.value = link;
        input.setAttribute('readonly', 'readonly');
        input.style.position = 'fixed';
        input.style.top = '-999px';
        document.body.appendChild(input);
        input.select();

        try {
            document.execCommand('copy');
            callback();
        } catch (error) {
            window.prompt('Copiez ce lien', link);
        }

        document.body.removeChild(input);
    }
    </script>
    <?php
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
                                        <?php actualite_share_bar($item); ?>
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
    actualite_share_script();
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
                            <?php actualite_share_bar($featured); ?>
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
                                <?php actualite_share_bar($item); ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
actualite_share_script();
require_once $footerPath;
?>
