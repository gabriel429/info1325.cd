<!-- Start axes -->
 		<section class="axes section">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="section-title">
							<h2>Les Axes du Plan d'Action National de la R1325 Sur les femmes,Paix et Sécurité</h2>
						</div>
					</div>
				</div>
				<div class="row">
					<?php
					// load up to 6 axes from DB; fallback to static content if missing
					require_once $dateDbConnect; // ensure $pdo is available
					$rows = [];
					try {
						$stmt = $pdo->query('SELECT * FROM axes ORDER BY `position` ASC LIMIT 6');
						$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
					} catch (PDOException $e) {
						// table may not exist yet on this environment; fallback to defaults below
						$rows = [];
					}
					$defaults = [
						1 => ['title'=>'PREVENTION','description'=>'Intégrer la perspective de genre dans la prévention des conflits et éliminer les violences basées sur le genre'],
						2 => ['title'=>'PARTICIPATION','description'=>'Garantir la participation pleine et effective des femmes à tous les niveaux de prise de décision'],
						3 => ['title'=>'PROTECTION','description'=>'Protéger les femmes et les filles contre toutes les formes de violences dans les conflits'],
						4 => ['title'=>'SECOURS ET RELEVEMENT','description'=>'Assurer l\'accès aux services de base et soutenir le relèvement économique des femmes affectées par les conflits'],
						5 => ['title'=>'GESTION DES CONFLITS','description'=>'Gestion des conflits émergents et aide humanitaire'],
						6 => ['title'=>'CADRE STRATEGIQUE','description'=>'Cadre stratégique pour traduire les engagements internationaux en actions concrètes']
					];
					$axes = [];
					foreach ($rows as $r) $axes[(int)$r['position']] = $r;
					for ($i=1;$i<=6;$i++) {
						$a = $axes[$i] ?? $defaults[$i];
					?>
					<div class="col-lg-4 col-md-6 col-12">
						<!-- Start Single Axe -->
						<div class="single-service">
							<?php
								// Always render an image: uploaded image or SVG placeholder
								if (!empty($a['image'])) {
									$imgSrc = IMG_DIR . 'axes/' . $a['image'];
								} else {
									$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400">'<
										.'<rect fill="#e9ecef" width="100%" height="100%"/>'
										.'<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#6c757d" font-size="20">Image manquante</text>'
										.'</svg>';
									$imgSrc = 'data:image/svg+xml;utf8,' . rawurlencode($svg);
								}
							?>
							<div class="service-img" style="margin-bottom:12px;"><img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($a['title']) ?>" style="width:100%;height:160px;object-fit:cover;border-radius:4px;"></div>
							<h4><a href="service-details.html"><?= htmlspecialchars($a['title']) ?></a></h4>
							<?php if (!empty($a['description'])): ?>
							<p><?= htmlspecialchars($a['description']) ?></p>
							<?php endif; ?>
						</div>
						<!-- End Single Axe -->
					</div>
					<?php } ?>
				</div>
			</div>
		</section>
		<!--/ End axes -->