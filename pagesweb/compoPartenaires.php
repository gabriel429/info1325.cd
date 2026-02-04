<?php
// Partners band: renders partner logos from DB (fallback to static) in an owl-carousel
if (!isset($pdo)) require_once __DIR__ . '/connectDb.php';
?>
<section class="partners-band section">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<?php
				$partners = [];
				try {
						$stmt = $pdo->query("SELECT id, name, url, image FROM partenaires WHERE active=1 ORDER BY IFNULL(position, id) ASC");
						$partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch (Exception $e) {
						try {
								$stmt = $pdo->query("SELECT id, name, url, image FROM partners WHERE active=1 ORDER BY IFNULL(position, id) ASC");
								$partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
						} catch (Exception $e2) {
								$partners = [];
						}
				}

				if (empty($partners)) {
						$partners = [
								['name'=>'Gouvernement','url'=>'https://www.primature.gouv.cd/membres-du-gouvernement-suminwa-2/','image'=>'gouvernement.jpg'],
								['name'=>'Min-Genre','url'=>'https://genre.gouv.cd/','image'=>'logoMingenre.png'],
								['name'=>'UN Women','url'=>'https://africa.unwomen.org/fr','image'=>'client2.jpg'],
								['name'=>'Norvege','url'=>'#','image'=>'norvege.png'],
								['name'=>'Partenaire A','url'=>'#','image'=>'partenaire1325.png'],
								['name'=>'Partenaire B','url'=>'#','image'=>'partenaire13252.png'],
						];
				}
				?>
				<div class="owl-carousel partners-slider">
					<?php foreach ($partners as $p):
							$img = htmlspecialchars($p['image'] ?? '');
							$url = htmlspecialchars($p['url'] ?? '#');
							$alt = htmlspecialchars($p['name'] ?? 'partner');
					?>
						<div class="single-partner"><a href="<?= $url ?>" target="_blank" rel="noopener noreferrer"><img src="<?= IMG_DIR ?><?= $img ?>" alt="<?= $alt ?>"></a></div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
		<!--/Ens clients -->