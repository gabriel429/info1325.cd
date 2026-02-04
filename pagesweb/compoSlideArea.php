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

		<section class="schedule">

			<div class="container">

				<div class="schedule-inner">

					<div class="row">

						<div class="col-lg-4 col-md-6 col-12 ">

							<!-- single-schedule -->

							<div class="single-schedule first">

								<div class="inner">

									<div class="icon">

										<i class="fa fa-ambulance"></i>

									</div>

									<div class="single-content">

										<h4>BASE DE DONNEES 1325</h4>

										<ul class="time-sidual">

											<li class="day">Le système de suivi des résolutions sur les femmes, la paix et la sécurité de l'ONU</li>

											<li class="day">Une base de données nationale en RDC sur les plans d'action 1325, des observatoires qui recensent les progrès et les violations</li>

										</ul>

										<a href="https://sn1325.org/" target="_blank">Découvrer la base de données 1325<i class="fa fa-long-arrow-right"></i></a>

									</div>

								</div>

							</div>

						</div>

						<div class="col-lg-4 col-md-6 col-12">

							<!-- single-schedule -->

							<div class="single-schedule middle">

								<div class="inner">

									<div class="icon">

										<i class="icofont-prescription"></i>

									</div>

									<div class="single-content">

										<h4>DOCUMENTATION 1325</h4>

										<p>Dans le contexte 1325-RDC, documenter = prouver + apprendre + partager + perpétuer.C'est le socle qui donne de la légitimité et de l'impact aux actions entreprises pour les femmes, la paix et la sécurité.</p>

										<a href="<?= URL_DOCUMENTATION ?>">Consulter notre documentation<i class="fa fa-long-arrow-right"></i></a>

									</div>

								</div>

							</div>

						</div>

						<div class="col-lg-4 col-md-12 col-12">

							<!-- single-schedule -->

							<div class="single-schedule last">

								<div class="inner">

									<div class="icon">

										<i class="icofont-ui-clock"></i>

									</div>

									<div class="single-content">

										<h4>PLANS D'ACTION NATIONAL</h4>

										<p>Feuille de route officielle pour mettre en œuvre la Résolution 1325 de manière structurée et mesurable, document stratégique et opérationnel qui traduit en mesures concrètes les principes "Femmes, Paix et Sécurité"</p>

										<a href="pagesweb/Plan d'Action National 3eme génération_125445.pdf">Découvrer les plans d'action national<i class="fa fa-long-arrow-right"></i></a>

									</div>

								</div>

							</div>

						</div>

					</div>

				</div>

			</div>

		</section>

		<!--/End Start schedule Area -->