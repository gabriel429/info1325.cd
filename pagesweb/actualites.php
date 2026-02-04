<?php
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // Connexion PDO : $pdo

// ===============================
// Vérification de l'ID passé en URL
// ===============================
// Si aucun ID n’est passé, on récupère le plus récent
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
} else {
    $stmt = $pdo->query("SELECT id FROM actualites ORDER BY id DESC LIMIT 1");
    $last = $stmt->fetch(PDO::FETCH_ASSOC);
    $id = $last ? $last['id'] : 0;
}

// Si toujours pas d’ID (aucune actualité en base)
if (!$id) {
    echo "<div class='alert alert-warning text-center py-5'>Aucune actualité disponible pour le moment.</div>";
    exit;
}


// ===============================
// Récupération de l'actualité
// ===============================
$stmt = $pdo->prepare("SELECT * FROM actualites WHERE id = :id");
$stmt->execute([':id' => $id]);
$actu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$actu) {
    echo "<div class='alert alert-danger text-center py-5'>Actualité introuvable !</div>";
    exit;
}

// ===============================
// Récupération des 3 dernières actualités
// ===============================
$stmt = $pdo->query("
    SELECT id, titre, auteur, date_pub, imgMise
    FROM actualites
    ORDER BY id DESC
    LIMIT 3
");
$actualites = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ===============================
// Récupération des 6 dernières documentations
// ===============================
$stmt = $pdo->query("SELECT * FROM documentations ORDER BY datePub DESC LIMIT 6");
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- Composant Header -->
<?php require_once $headerPath; ?>

<!-- Breadcrumbs -->
<div class="breadcrumbs overlay">
    <div class="container">
        <div class="bread-inner">
            <div class="row">
                <div class="col-12">
                    <h2>Actualités 1325</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Single News -->
<section class="news-single section">
    <div class="container">
        <div class="row">
            <!-- Actualité principale -->
            <div class="col-lg-8 col-12">
                <div class="single-main">
                    <div class="news-head">
                        <img src="<?= IMG_DIR . 'actualites/' . htmlspecialchars($actu['imgMise']) ?>" alt="<?= htmlspecialchars($actu['titre']) ?>">
                    </div>
                    <h1 class="news-title"><?= htmlspecialchars($actu['titre']) ?></h1>
                    <div class="meta">
                        <div class="meta-left">
                            <span class="author"><?= htmlspecialchars($actu['auteur']) ?></span>
                            <span class="date"><i class="fa fa-clock-o"></i><?= htmlspecialchars($actu['date_pub']) ?></span>
                        </div>
                    </div>
                    <div class="news-text">
                        <?php for ($i = 1; $i <= 10; $i++): 
                            $para = $actu['paraph'.$i] ?? '';
                            if ($para): ?>
                                <p><?= htmlspecialchars($para) ?></p>
                        <?php endif; endfor; ?>

                        <?php if (!empty($actu['imgPub1']) && !empty($actu['imgPub2'])): ?>
                        <div class="image-gallery row">
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="single-image">
                                    <img src="<?= IMG_DIR . 'actualites/' . htmlspecialchars($actu['imgPub1']) ?>" alt="#">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="single-image">
                                    <img src="<?= IMG_DIR . 'actualites/' . htmlspecialchars($actu['imgPub2']) ?>" alt="#">
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($actu['messageFort'])): ?>
                        <blockquote class="overlay">
                            <p><?= htmlspecialchars($actu['messageFort']) ?></p>
                        </blockquote>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 col-12">
                <div class="main-sidebar">

                    <!-- Recherche -->
                    <div class="single-widget search">
                        <div class="form">
                            <input type="text" placeholder="Rechercher ici...">
                            <a class="button" href="#"><i class="fa fa-search"></i></a>
                        </div>
                    </div>

                    <!-- Documentations -->
                    <div class="single-widget category">
                        <h3 class="title">Notre Documentation</h3>
                        <ul class="categor-list">
                            <?php foreach ($docs as $doc): ?>
                                <li>
                                    <a href="<?= BASE_URL . 'img/documentations/' . htmlspecialchars($doc['fichier_pdf']) ?>" target="_blank">
                                        <?= htmlspecialchars($doc['titreDoc']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Recentes actualités -->
                    <div class="single-widget recent-post">
                        <h3 class="title">Récentes actualités</h3>
                        <?php foreach ($actualites as $recent): ?>
                            <div class="single-post">
                                <div class="image">
                                    <img src="<?= IMG_DIR . 'actualites/' . htmlspecialchars($recent['imgMise']) ?>" alt="#">
                                </div>
                                <div class="content">
                                    <h5>
                                        <a href="<?= URL_ACTUALITES ?>?id=<?= urlencode($recent['id']) ?>">
                                            <?= htmlspecialchars($recent['titre']) ?>
                                        </a>
                                    </h5>
                                    <ul class="comment">
                                        <li><i class="fa fa-calendar"></i> <?= htmlspecialchars($recent['date_pub']) ?></li>
                                    </ul>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Composant Footer -->
<?php require_once $footerPath; ?>
