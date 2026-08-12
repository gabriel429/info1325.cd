<?php
/**
 * Helpers for the news publishing module.
 */

function ensure_actualites_schema(PDO $pdo): void
{
    $columns = [
        'slug' => "ALTER TABLE actualites ADD COLUMN slug VARCHAR(255) DEFAULT NULL AFTER titre",
        'categorie' => "ALTER TABLE actualites ADD COLUMN categorie VARCHAR(120) DEFAULT 'Actualite' AFTER auteur",
        'resume' => "ALTER TABLE actualites ADD COLUMN resume TEXT NULL AFTER commentaire",
        'contenu' => "ALTER TABLE actualites ADD COLUMN contenu MEDIUMTEXT NULL AFTER messageFort",
        'statut' => "ALTER TABLE actualites ADD COLUMN statut VARCHAR(20) NOT NULL DEFAULT 'publie' AFTER contenu",
        'a_la_une' => "ALTER TABLE actualites ADD COLUMN a_la_une TINYINT(1) NOT NULL DEFAULT 0 AFTER statut",
        'updated_at' => "ALTER TABLE actualites ADD COLUMN updated_at DATETIME DEFAULT NULL AFTER date_creation",
    ];

    foreach ($columns as $column => $sql) {
        try {
            $check = $pdo->query("SHOW COLUMNS FROM actualites LIKE " . $pdo->quote($column));
            if ($check && $check->rowCount() === 0) {
                $pdo->exec($sql);
            }
        } catch (PDOException $e) {
            error_log('actualites schema column error (' . $column . '): ' . $e->getMessage());
        }
    }

    try {
        $checkIndex = $pdo->query("SHOW INDEX FROM actualites WHERE Key_name = 'idx_actualites_slug'");
        if ($checkIndex && $checkIndex->rowCount() === 0) {
            $pdo->exec('CREATE INDEX idx_actualites_slug ON actualites (slug)');
        }
    } catch (PDOException $e) {
        error_log('actualites schema index error: ' . $e->getMessage());
    }

    actualites_backfill_content_and_slugs($pdo);
}

function actualites_backfill_content_and_slugs(PDO $pdo): void
{
    try {
        $rows = $pdo->query('SELECT id, titre, slug, contenu, commentaire, paraph1, paraph2, paraph3, paraph4, paraph5, paraph6, paraph7, paraph8, paraph9, paraph10 FROM actualites ORDER BY id ASC')
            ->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('actualites backfill read error: ' . $e->getMessage());
        return;
    }

    foreach ($rows as $row) {
        $updates = [];
        $params = [':id' => (int)$row['id']];

        if (trim((string)($row['slug'] ?? '')) === '') {
            $updates[] = 'slug = :slug';
            $params[':slug'] = actualite_unique_slug($pdo, (string)($row['titre'] ?? 'actualite'), (int)$row['id']);
        }

        if (trim((string)($row['contenu'] ?? '')) === '') {
            $paragraphs = [];
            for ($i = 1; $i <= 10; $i++) {
                $value = trim((string)($row['paraph' . $i] ?? ''));
                if ($value !== '') {
                    $paragraphs[] = '<p>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</p>';
                }
            }

            if (empty($paragraphs) && trim((string)($row['commentaire'] ?? '')) !== '') {
                $paragraphs[] = '<p>' . htmlspecialchars((string)$row['commentaire'], ENT_QUOTES, 'UTF-8') . '</p>';
            }

            if (!empty($paragraphs)) {
                $updates[] = 'contenu = :contenu';
                $params[':contenu'] = implode("\n", $paragraphs);
            }
        }

        if (!empty($updates)) {
            try {
                $stmt = $pdo->prepare('UPDATE actualites SET ' . implode(', ', $updates) . ' WHERE id = :id');
                $stmt->execute($params);
            } catch (PDOException $e) {
                error_log('actualites backfill update error: ' . $e->getMessage());
            }
        }
    }
}

function actualite_slugify(string $title): string
{
    $title = trim($title);
    if ($title === '') {
        return 'actualite';
    }

    $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title);
    if ($converted !== false) {
        $title = $converted;
    }

    $title = strtolower($title);
    $title = preg_replace('/[^a-z0-9]+/', '-', $title);
    $title = trim((string)$title, '-');
    $title = preg_replace('/-+/', '-', $title);

    return $title !== '' ? $title : 'actualite';
}

function actualite_starts_with(string $value, string $prefix): bool
{
    return $prefix === '' || strpos($value, $prefix) === 0;
}

function actualite_unique_slug(PDO $pdo, string $title, ?int $currentId = null, ?string $requestedSlug = null): string
{
    $base = actualite_slugify($requestedSlug ?: $title);
    $slug = $base;
    $suffix = 2;

    while (true) {
        $sql = 'SELECT id FROM actualites WHERE slug = :slug';
        $params = [':slug' => $slug];
        if ($currentId !== null && $currentId > 0) {
            $sql .= ' AND id != :id';
            $params[':id'] = $currentId;
        }
        $sql .= ' LIMIT 1';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        if (!$stmt->fetch()) {
            return $slug;
        }

        $slug = $base . '-' . $suffix;
        $suffix++;
    }
}

function actualite_url(array $actualite): string
{
    $slug = trim((string)($actualite['slug'] ?? ''));
    if ($slug === '') {
        $slug = actualite_slugify((string)($actualite['titre'] ?? 'actualite'));
    }

    return BASE_URL . 'actualites/' . rawurlencode($slug);
}

function actualite_image_url(?string $image): string
{
    $image = trim((string)$image);
    if ($image !== '') {
        return IMG_DIR . 'actualites/' . rawurlencode($image);
    }

    return IMG_DIR . 'bread-bg21.jpg';
}

function actualite_summary(array $actualite, int $length = 170): string
{
    $summary = trim((string)($actualite['resume'] ?? ''));
    if ($summary === '') {
        $summary = trim((string)($actualite['commentaire'] ?? ''));
    }
    if ($summary === '') {
        $summary = strip_tags((string)($actualite['contenu'] ?? ''));
    }
    if ($summary === '') {
        $summary = trim((string)($actualite['paraph1'] ?? ''));
    }

    return mb_strimwidth(preg_replace('/\s+/', ' ', $summary), 0, $length, '...');
}

function actualite_published_label(?string $date): string
{
    if (!$date) {
        return 'Date non renseignee';
    }

    try {
        return (new DateTime($date))->format('d/m/Y');
    } catch (Exception $e) {
        return htmlspecialchars($date, ENT_QUOTES, 'UTF-8');
    }
}

function actualite_youtube_embed_url(string $url): ?string
{
    $url = trim($url);
    if ($url === '') {
        return null;
    }

    $patterns = [
        '#youtu\.be/([A-Za-z0-9_-]{6,})#',
        '#youtube\.com/watch\?[^"\']*v=([A-Za-z0-9_-]{6,})#',
        '#youtube\.com/embed/([A-Za-z0-9_-]{6,})#',
        '#youtube\.com/shorts/([A-Za-z0-9_-]{6,})#',
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }
    }

    return null;
}

function actualite_convert_youtube_links(string $html): string
{
    return preg_replace_callback(
        '#<p>\s*(https?://(?:www\.)?(?:youtube\.com/watch\?[^<\s]+|youtu\.be/[^<\s]+|youtube\.com/shorts/[^<\s]+))\s*</p>#i',
        static function ($matches) {
            $embed = actualite_youtube_embed_url($matches[1]);
            if (!$embed) {
                return $matches[0];
            }
            return '<figure class="article-embed"><iframe src="' . htmlspecialchars($embed, ENT_QUOTES, 'UTF-8') . '" allowfullscreen loading="lazy"></iframe></figure>';
        },
        $html
    ) ?? $html;
}

function actualite_sanitize_html(string $html): string
{
    $html = actualite_convert_youtube_links($html);
    $allowedTags = '<p><br><strong><b><em><i><u><h2><h3><h4><ul><ol><li><blockquote><a><img><figure><figcaption><iframe><video><source><div><span><hr>';
    $html = strip_tags($html, $allowedTags);

    if (trim($html) === '') {
        return '';
    }

    if (!class_exists('DOMDocument')) {
        return $html;
    }

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8"><div id="root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $allowedAttrs = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title'],
        'iframe' => ['src', 'allowfullscreen', 'loading'],
        'video' => ['src', 'controls', 'poster'],
        'source' => ['src', 'type'],
        '*' => ['class', 'style'],
    ];

    $walker = static function (DOMNode $node) use (&$walker, $allowedAttrs): void {
        if ($node instanceof DOMElement) {
            $tag = strtolower($node->tagName);
            $attrs = [];
            foreach ($node->attributes as $attr) {
                $attrs[] = $attr->name;
            }

            foreach ($attrs as $attrName) {
                $value = (string)$node->getAttribute($attrName);
                $allowed = in_array($attrName, $allowedAttrs[$tag] ?? [], true) || in_array($attrName, $allowedAttrs['*'], true);

                if (!$allowed) {
                    $node->removeAttribute($attrName);
                    continue;
                }

                if (in_array($attrName, ['href', 'src'], true)) {
                    $isSafe = preg_match('#^(https?:)?//#i', $value)
                        || actualite_starts_with($value, '/')
                        || actualite_starts_with($value, '../img/')
                        || actualite_starts_with($value, 'img/')
                        || actualite_starts_with($value, 'data:image/');
                    if (!$isSafe || preg_match('/javascript:/i', $value)) {
                        $node->removeAttribute($attrName);
                    }
                }

                if ($tag === 'iframe' && $attrName === 'src' && !preg_match('#^https://www\.youtube(?:-nocookie)?\.com/embed/[A-Za-z0-9_-]+#', $value)) {
                    $node->removeAttribute($attrName);
                }

                if ($attrName === 'style') {
                    if (preg_match('/text-align\s*:\s*(left|right|center|justify)/i', $value, $matches)) {
                        $node->setAttribute('style', 'text-align: ' . strtolower($matches[1]));
                    } else {
                        $node->removeAttribute('style');
                    }
                }
            }

            if ($tag === 'a') {
                $node->setAttribute('rel', 'noopener noreferrer');
                if (!$node->hasAttribute('target')) {
                    $node->setAttribute('target', '_blank');
                }
            }

            if ($tag === 'iframe') {
                $node->setAttribute('loading', 'lazy');
                $node->setAttribute('allowfullscreen', 'allowfullscreen');
            }
        }

        foreach (iterator_to_array($node->childNodes) as $child) {
            $walker($child);
        }
    };

    $root = $dom->getElementById('root');
    if ($root) {
        $walker($root);
        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $dom->saveHTML($child);
        }
        return $output;
    }

    return $html;
}
