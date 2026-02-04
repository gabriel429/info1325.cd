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
						<?php
							// Always render background image: uploaded image or default file or SVG placeholder
							if (!empty($a['image'])) {
								$imgSrc = IMG_DIR . 'axes/' . $a['image'];
							} else {
								$found = null;
								$basePath = __DIR__ . '/../img/axes/';
								$nameBase = 'axis_' . $i;
								$exts = ['jpg','jpeg','png','webp','gif'];
								foreach ($exts as $e) {
									if (file_exists($basePath . $nameBase . '.' . $e)) { $found = $nameBase . '.' . $e; break; }
								}
								if ($found) {
									$imgSrc = IMG_DIR . 'axes/' . $found;
								} else {
									$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400">'
										. '<rect fill="#e9ecef" width="100%" height="100%"/>'
										. '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#6c757d" font-size="20">Image manquante</text>'
										. '</svg>';
									$imgSrc = 'data:image/svg+xml;utf8,' . rawurlencode($svg);
								}
							}
						?>
						<div class="single-service" style="background-image:url('<?= $imgSrc ?>');background-size:cover;background-position:center;position:relative;min-height:220px;border-radius:6px;overflow:hidden;">
							<div class="ss-overlay" style="position:absolute;inset:0;background:linear-gradient(rgba(0,0,0,0.45),rgba(0,0,0,0.2));z-index:1;"></div>
							<div class="ss-content" style="position:relative;z-index:2;padding:20px;color:#fff;">
								<h4 style="margin-top:0;margin-bottom:8px;"><a href="service-details.html" style="color:#fff;text-decoration:none;text-shadow:0 2px 6px rgba(0,0,0,0.6);"><?= htmlspecialchars($a['title']) ?></a></h4>
								<?php if (!empty($a['description'])): ?>
								<p style="color:rgba(255,255,255,0.95);text-shadow:0 1px 3px rgba(0,0,0,0.6);margin-bottom:0;"><?= htmlspecialchars($a['description']) ?></p>
								<?php endif; ?>
							</div>
						</div>
						<!-- End Single Axe -->
					</div>
					<?php } ?>
				</div>
			</div>
		</section>
		<!--/ End axes -->