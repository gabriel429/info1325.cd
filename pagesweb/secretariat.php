<?php
        require_once __DIR__ . '/../configUrl.php'; // __DIR__ = dossier racine
        require_once __DIR__ . '/../defConstLiens.php'; // __DIR__ = dossier racine

    // Page-specific CSS (chargé via headerPage.php)
        $SKIP_PAGE_TITLE = true; // this page renders its own hero
        $pageCss = CSS_DIR . 'secretariat.css';

        // Hero image basename (used to avoid duplicating it in the slider)
        $heroImgName = 'snational1325.png';
        $heroImg = IMG_DIR . $heroImgName;

    //require_once $dataDbConnect; 

?>

<!-- Composant header  page cn debut -->
        <?php require_once $headerPath;  ?>
<!-- Composant header page cn fin  -->

<!-- Hero CareMed pour le secrétariat -->
<section class="caremed-hero" style="background-image:url('<?= $heroImg ?>'); background-size:cover; background-position:center;">
    <div class="overlay"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-breadcrumb">Accueil / Secrétariat</div>
            <h1>Secrétariat National 1325</h1>
            <p class="lead">Coordination, actions et ressources pour la mise en œuvre de la Résolution 1325 en RDC.</p>
        </div>
    </div>
</section>
<!-- Start Portfolio Details Area -->
		<section class="pf-details section">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="inner-content">
                            <div class="image-slider">
                                <div class="pf-details-slider">
                                    <?php
                                    // Use helper to render slider and avoid duplicating the hero image
                                    require_once __DIR__ . '/slider_helper.php';
                                    // Prefer a page-specific image folder (img/secretariat/) if present
                                    $pageImgDirFs = __DIR__ . '/../img/secretariat/';
                                    if (is_dir($pageImgDirFs)){
                                        // pattern: common image extensions
                                        $heroFsPath = __DIR__ . '/../img/' . $heroImgName;
                                        render_image_slider_from_dir($pageImgDirFs, '/\.(jpe?g|png|gif)$/i', $heroImgName, IMG_DIR . 'secretariat/', $heroFsPath);
                                    } else {
                                        // fallback: explicit list
                                        $sliderImages = ['didier.jpg', 'snational1325.png', 'snational132503.png'];
                                        $imgFsDir = __DIR__ . '/../img/';
                                        render_image_slider($sliderImages, $heroImgName, IMG_DIR, $imgFsDir);
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="body-text">
                              
                                <section id="contexte" class="info-section">
                                    <div class="info-card">
                                        <h4 class="section-badge">SECRETARIAT National 1325</h4>
                                        <p>Le Secrétariat National de la Résolution 1325 en RDC est la structure chargée du suivi et de la gestion quotidienne de la mise en œuvre de la Résolution sur l’ensemble du pays.</p>
                                        <p>Il a été créé et mis en place par deux arrêtés ministériels du 04 août 2015 : l’un portant création, organisation et fonctionnement et l’autre portant nomination des membres du secrétariat national, installé officiellement le 08 septembre 2015.</p>
							
                                        <h5>De la composition</h5>
                                        <ul>
                                            <li>Il est composé de quatre Experts Nationaux permanents et de douze Experts Nationaux non permanents.</li>
                                        </ul>
                                        <p><strong>Les quatre Experts Nationaux permanents sont :</strong></p>
                                        <ul>
                                            <li><strong>Madame Annie KENDA</strong> — Directeur chef de service Juridique et Secrétaire Permanente du Conseil national de la Femme : <strong>Coordonnatrice Nationale du SN1325</strong></li>
                                            <li><strong>Madame Esther KAMUANYA</strong> — Directeur chef de service chargée des questions socioéconomiques : <strong>Chargée des Finances</strong></li>
                                            <li><strong>Monsieur Didier LAPIAR</strong> — Expert à la cellule d’étude et de planification au Ministère du Genre, de la Famille et de l’Enfant : <strong>Chargé de l’administration et des questions techniques</strong></li>
                                            <li><strong>Délégué du Cabinet de Madame la Ministre du Genre, Enfant et Famille</strong> — <strong>Chargé de la logistique</strong></li>
                                        </ul>
							
                                        <h5>Les douze Experts Nationaux non permanents sont :</h5>
                                        <ul>
                                            <li>Un(e) Expert(e) du Ministère de la Justice</li>
                                            <li>Un(e) Expert(e) du Ministère des Affaires Étrangères</li>
                                            <li>Un(e) Expert(e) du Ministère du Budget</li>
                                            <li>Un(e) Expert(e) du Ministère du Plan</li>
                                            <li>Un(e) Expert(e) du Ministère de la Défense et des Anciens Combattants</li>
                                            <li>Un(e) Expert(e) du Ministère de l’Intérieur et de la Sécurité</li>
                                            <li>Trois représentants(es) de la société civile (ex. CAFCO, CJR1325, WILF/RDC)</li>
                                            <li>Un(e) Expert(e) du secrétariat général du Ministère du Genre, de la Famille et de l’Enfant</li>
                                            <li>Un(e) Expert(e) de la Cellule d’Études et de Planification de la promotion de la Femme, de la Famille et de la protection de l’Enfant</li>
                                            <li>Un(e) Expert(e) du Cabinet du Ministre du Genre</li>
                                        </ul>
							
                                        <h5>Mission assignée au SN1325</h5>
                                        <ul>
                                            <li>Participer à l’ensemble des activités du programme, effectuer des missions de suivi et de supervision et produire des rapports périodiques sur l’état de mise en œuvre du plan d’action national auprès du comité de pilotage.</li>
                                            <li>Préparer les réunions du comité de pilotage et assurer son secrétariat ; créer et maintenir une base de données pour faciliter le travail du Secrétariat.</li>
                                            <li>Assurer une concertation permanente autour des questions d’inégalités de genre entre les différents acteurs impliqués.</li>
                                            <li>Initier des enquêtes périodiques sur la prise en compte du genre et la lutte contre les violences sexuelles, et publier des résultats.</li>
                                            <li>Créer et réviser annuellement les critères d’évaluation technique.</li>
                                            <li>Examiner les propositions initiales déposées et établir une liste de propositions ou projets à présenter au Comité de Pilotage.</li>
                                            <li>Apporter des contributions techniques et assister les bénéficiaires de subventions lors de la mise en œuvre, du suivi et du plaidoyer.</li>
                                            <li>Participer aux renforcements des capacités des bénéficiaires et d’autres ONG nationales sélectionnées.</li>
                                            <li>Toutes les propositions sélectionnées par le Secrétariat National seront présentées au Comité de Pilotage en collaboration avec ONU Femmes en tant qu’administrateur du Fonds pour décision finale et recommandations.</li>
                                        </ul>
                                        </div>
                                    </section>
                                <section id="institutions" class="info-section institutions">
                                    <h4 class="section-badge">Structure institutionnelle et partenaires</h4>
                                    <div class="institution-cards">
                                        <article class="inst-card" aria-labelledby="inst-acteurs">
                                            <h5 id="inst-acteurs">Acteurs clés</h5>
                                            <ul>
                                                <li>Ministère du Genre, Famille et Enfant — point focal</li>
                                                <li>Secrétariat National 1325 — coordination</li>
                                                <li>Points focaux provinciaux — déploiement territorial</li>
                                                <li>Société civile — mise en œuvre & monitoring</li>
                                            </ul>
                                        </article>

                                        <article class="inst-card" aria-labelledby="inst-partenaires">
                                            <h5 id="inst-partenaires">Partenaires techniques et financiers</h5>
                                            <ul>
                                                <li>ONU Femmes RDC — appui technique et financier</li>
                                                <li>MONUSCO — division Genre</li>
                                                <li>Ambassade de Norvège</li>
                                                <li>ONGs nationales et internationales</li>
                                            </ul>
                                        </article>
                                    </div>
                                </section>

                                <section id="resultats" class="info-section results">
                                    <h4 class="section-badge">Résultats et défis</h4>
                                    <div class="results-cards">
                                        <article class="result-card" aria-labelledby="res-succes">
                                            <h5 id="res-succes">Succès documentés</h5>
                                            <ul>
                                                <li>Augmentation du nombre de femmes dans les instances</li>
                                                <li>Renforcement des capacités des organisations féminines</li>
                                                <li>Prise en compte du genre dans la réforme sécuritaire</li>
                                                <li>Documentation systématique des violences basées sur le genre</li>
                                            </ul>
                                        </article>

                                        <article class="result-card" aria-labelledby="res-defis">
                                            <h5 id="res-defis">Défis persistants</h5>
                                            <ul>
                                                <li>Financement insuffisant des initiatives de genre</li>
                                                <li>Insécurité dans les zones de conflit</li>
                                                <li>Résistances à l'égalité de genre</li>
                                            </ul>
                                        </article>
                                    </div>
                                </section>

                                <div style="height:12px"></div>

                                <div class="share">
									<h4>Nous suivres</h4>
									<ul>
										<li><a href="https://web.facebook.com/sn1325/" target="_blank"><i class="fa fa-facebook-official" aria-hidden="true"></i></a></li>
										<li><a href="https://x.com/R1325RDC" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
										<li><a href="https://www.linkedin.com/company/R%C3%A9solution%201325%20RDC/" target="_blank"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- End Portfolio Details Area -->
		<!-- Composant footer  page cn debut -->
    <?php require_once $footerPath;  ?>
<!-- Composant footer page cn fin  -->