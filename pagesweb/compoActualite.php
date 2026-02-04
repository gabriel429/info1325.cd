
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

<style>
/* Uniformiser la taille des images et limiter le texte à 4 lignes */
.single-news .news-head img{
	width:100%;
	height:220px;
	object-fit:cover;
	display:block;
}
.single-news .news-body{
	min-height:200px;
}
.single-news .news-content .text{
	display:-webkit-box;
	-webkit-line-clamp:4;
	-webkit-box-orient:vertical;
	overflow:hidden;
	text-align:justify;
}
.single-news .news-content .date{
	font-size:0.9rem;
	color:#777;
	margin-bottom:8px;
}
</style>
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
									<div class="date"><?php
										// afficher la date de publication si disponible, sinon date de création
										$dtStr = $actualite['date_pub'] ?: $actualite['date_creation'];
										try {
											$dt = new DateTime($dtStr);
											echo $dt->format('d/m/Y');
										} catch (Exception $e) {
											echo htmlspecialchars($dtStr);
										}
									?></div>
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