<!-- Start portfolio -->
<!-- Start portfolio -->


<style>

	.single-pf{

		height: 180px !important;

	}

	.img-galery {

		width: 100%;

		object-fit: cover !important;

	}

</style>

		<section class="portfolio section" >

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

	<?php
	$dataFile = __DIR__ . '/../data/galerie.json';
	$items = [];
	if (file_exists($dataFile)){
		$items = json_decode(file_get_contents($dataFile), true) ?: [];
	}
	function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
	if (empty($items)){
		// fallback to legacy static images
		$static = ['didier.jpg','snational132513.png','snational1325.png','snational132505.png','snational132506.png','snational132507.png','snational132508.png'];
		foreach($static as $s){
			echo "\t\t\t\t\t<div class=\"single-pf\">\n\t\t\t\t\t\t<img class=\"img-galery\" src=\"" . IMG_DIR . h($s) . "\" alt=\">\n\t\t\t\t\t</div>\n";
		}
	} else {
		foreach($items as $it){
			$file = $it['file'] ?? '';
			$activity = $it['activity'] ?? '';
			if (!$file) continue;
			$url = BASE_URL . 'pagesweb/gallery.php?activity=' . rawurlencode($activity ?: 'all');
			echo "\t\t\t\t\t\t<div class=\"single-pf\">\n\t\t\t\t\t\t\t<a href=\"$url\"><img class=\"img-galery\" src=\"" . IMG_DIR . "galerie/" . h($file) . "\" alt=\"" . h($activity) . "\"></a>\n\t\t\t\t\t\t</div>\n";
		}
	}
	?>

							</div>

						</div>

					</div>

				</div>

		</section>

		<!--/ End portfolio -->











