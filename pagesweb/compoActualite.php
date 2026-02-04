
<?php
    require_once __DIR__ . '/../configUrl.php'; // __DIR__ = dossier racine
    require_once __DIR__ . '/../defConstLiens.php'; // __DIR__ = dossier racine

  // Requête : récupérer les 3 dernières actualités
    $sql = "SELECT 
				id,
				titre,
				auteur,
				date_pub,
				commentaire,
				nbrVues,
				imgMise,
				imgPub1,
				imgPub2,
				messageFort,
				paraph1,
				paraph2,
				paraph3,
				paraph4,
				paraph5,
				paraph6,
				paraph7,
				paraph8,
				paraph9,
				paraph10,
				date_creation
            FROM actualites 
            ORDER BY id DESC 
            LIMIT 3";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $actualites = $stmt->fetchAll();

?>
<!-- Start Blog Area -->
		<section class="blog section" id="blog">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="section-title">
							<h2>Actualités 1325</h2>
						</div>
					</div>
				</div>
				<div class="row">
					<?php foreach ($actualites as $actualite): ?>
					<div class="col-lg-4 col-md-6 col-12">
						<!-- Single Blog -->
						<div class="single-news">
							<div class="news-head">
								<img src="<?php echo IMG_DIR . 'actualites/' . htmlspecialchars($actualite['imgMise']); ?>" alt="#">
							</div>
							<div class="news-body">
								<div class="news-content">
									<div class="date">12/01/2025</div>
									<h2><a href="<?= URL_ACTUALITES ?>?id=<?= urlencode($actualite['id']) ?>"><?= htmlspecialchars($actualite['titre']) ?></a></h2>

									<p class="text"><?= htmlspecialchars(trim($actualite['paraph1'])) ?></p>
								</div>
							</div>
						</div>
					</div>
					<!-- End Single Blog -->
					<?php endforeach; ?>	
					
				</div>
			</div>
		</section>
		<!-- End Blog Area -->