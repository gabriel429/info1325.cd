<!-- Slider Area -->

<?php
// Render slider dynamically from DB slides table. If table missing or empty, fall back to existing static slides.
try {
	require_once __DIR__ . '/../configUrl.php';
	require_once __DIR__ . '/../defConstLiens.php';
	require_once $dateDbConnect; // $pdo
	$stmt = $pdo->query('SELECT * FROM slides WHERE active = 1 ORDER BY `position` ASC');
	$slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
	$slides = [];
}

// debug: output number of slides found (view page source to see)
echo "<!-- SLIDES_FOUND:" . count($slides) . " -->\n";

function slideImagePath($row, $pos){
	if (!empty($row['image'])) return IMG_DIR . 'slider/' . $row['image'];
	// try legacy files in /img root named slider.jpg, slider2.jpg, slider3.jpg or slider.png
	$candidates = ["slider{$pos}.jpg","slider{$pos}.png","slider{$pos}.jpeg","slider{$pos}.webp","slider{$pos}.gif","slider.jpg","slider.png"];
	foreach ($candidates as $c) {
		if (file_exists(__DIR__ . '/../img/' . $c)) return IMG_DIR . $c;
	}
	return IMG_DIR . 'banner3.png';
}

?>

		<section class="slider">

			<div class="hero-slider">

				<?php if (!empty($slides)): ?>
					<?php foreach ($slides as $s): $img = slideImagePath($s, (int)$s['position']); ?>
						<div class="single-slider" style="background-image:url('<?= $img ?>')">
							<div class="container">
								<div class="row">
									<div class="col-lg-7">
										<div class="text">
											<?php if (!empty($s['title'])): ?>
												<div style="background: rgba(128,128,128,0.6); display: inline-block; padding: 18px 24px; border-radius: 6px;">
													<h1 style="color:#FFF; margin:0;"><?= htmlspecialchars($s['title']) ?></h1>
												</div>
											<?php endif; ?>
											<?php if (!empty($s['subtitle'])): ?>
												<p><?= nl2br(htmlspecialchars($s['subtitle'])) ?></p>
											<?php endif; ?>
											<?php if (!empty($s['btn_text'])): ?>
												<div class="button">
													<a href="<?= htmlspecialchars($s['btn_url'] ?: '#') ?>" class="btn"><?= htmlspecialchars($s['btn_text']) ?></a>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<!-- fallback: keep original static slides -->
					<div class="single-slider" style="background-image:url('<?= IMG_DIR ?>femme.jpg')">
						<div class="container"><div class="row"><div class="col-lg-7"><div class="text"><div style="background: rgba(128,128,128,0.6); display: inline-block; padding: 18px 24px; border-radius: 6px;"><h1 style="color:#FFF; margin:0;">La RDC, marquée par des décennies de conflits armés dans l'Est du pays, est un cas prioritaire pour l'application de la Résolution 1325.<span></span></h1></div><p> </p><div class="button"><a href="#" class=""></a></div></div></div></div></div>
					<div class="single-slider" style="background-image:url('<?= IMG_DIR ?>ministre.jpeg')">
						<div class="container"><div class="row"><div class="col-lg-7"><div class="text"><h1>République Democratique du Congo<span> SN1325 </span> Madame la Ministre Micheline OMBAE</h1><p>Objectif global :"Assurer la participation effective des femmes à la prévention et résolution des conflits en RDC"</p><div class="button"><a href="https://genre.gouv.cd/" class="btn">MINGENRE</a></div></div></div></div></div>
					<div class="single-slider" style="background-image:url('<?= IMG_DIR ?>banner3.png')">
						<div class="container"><div class="row"><div class="col-lg-7"><div class="text"><div style="background: rgba(128,128,128,0.6); display: inline-block; padding: 18px 24px; border-radius: 6px;"><h1 style="color:#000000; margin:0;">La RDC est considérée comme un cas emblématique pour l'application de la Résolution 1325.<span></span></h1></div><p> </p><div class="button"><a href="mailto:lapiardidier561@gmail.com" target="_blank" class="btn primary">Contacter le Secretariat National</a></div></div></div></div></div>
					<div class="single-slider" style="background-image:url('<?= IMG_DIR ?>PAN.png')">
						<div class="container"><div class="row"><div class="col-lg-7"><div class="text"><div style="background: rgba(128,128,128,0.6); display: inline-block; padding: 18px 24px; border-radius: 6px;"><h1 style="color:#fff; margin:0;">Vulgarisation du Plan d'Action National — 3ème génération</h1></div><p> </p><div class="button"><a href="pagesweb/Plan d'Action National 3eme génération_125445.pdf" class="btn primary">consulter</a></div></div></div></div></div>
				<?php endif; ?>

			</div>

		</section>

		<!--/ End Slider Area -->

		<!-- Start Schedule Area -->

		<!--/End Start schedule Area -->