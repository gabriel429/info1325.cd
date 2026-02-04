<?php
// ===============================
// documentation.php
// Composant Documentation SN1325
// ===============================

require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // Connexion PDO

// Récupération des documentations depuis la base
try {
    $stmt = $pdo->query("SELECT * FROM documentations ORDER BY datePub DESC");
    $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de récupération : " . $e->getMessage());
}

// Composants header/footer
require_once $headerPath;
?>

<section class="blog section" id="blog">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-title">
                    <h2>DOCUMENTATION 1325</h2>
                </div>
            </div>
        </div>

        <div class="row">
            <?php foreach ($docs as $doc): ?>
                <?php
                    // Construction des chemins absolus
               
                    $pdfFile = ROOT_DIR . 'img/documentations/' . ($doc['fichier_pdf'] ?? '');

                    //$imgPath = file_exists($imgFile) ? BASE_URL . 'img/documentations/' . $doc['img'] : 'Aucune image trouvé pour l\'affichage !';
                    $pdfPath = file_exists($pdfFile) ? BASE_URL . 'img/documentations/' . $doc['fichier_pdf'] : '#';
                ?>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="single-news">
                        <?php
                            $imgName = $doc['img'] ?? '';
                            $encodedImg = rawurlencode($imgName); // encode seulement le nom du fichier
                            $imgPath = BASE_URL . 'img/documentations/' . $encodedImg;
                        ?>
                        <div class="news-head">
                            <img src="<?= htmlspecialchars($imgPath) ?>" 
                             alt="<?= htmlspecialchars($doc['titreDoc'] ?? '') ?>" 
                             style="width:100%; height:auto;">
                        </div>
                        <div class="news-body">
                            <div class="news-content">
                                <div class="date"><?= htmlspecialchars(date('d M, Y', strtotime($doc['datePub']))) ?></div>
                                <h2>
                                    <a href="<?= htmlspecialchars($pdfPath) ?>" target="_blank">
                                        <?= htmlspecialchars($doc['titreDoc']) ?>
                                    </a>
                                </h2>
                                <p class="text">
                                    Auteur : <?= htmlspecialchars($doc['auteur'] ?? 'Inconnu') ?><br>
                                    Année : <?= htmlspecialchars($doc['anneePub'] ?? 'N/A') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once $footerPath; ?>
