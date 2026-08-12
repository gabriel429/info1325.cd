<?php
        require_once __DIR__ . '/../configUrl.php'; // __DIR__ = dossier racine
        require_once __DIR__ . '/../defConstLiens.php'; // __DIR__ = dossier racine

    // Page-specific CSS (chargé via headerPage.php)
        $SKIP_PAGE_TITLE = true; // this page renders its own hero
        $pageCss = CSS_DIR . 'secretariat.css';

        // Hero image basename (used to avoid duplicating it in the slider)
        $heroImgName = 'snational1325.png';
        $heroImg = IMG_DIR . $heroImgName;
        require_once __DIR__ . '/track_visitor.php';
        require_once __DIR__ . '/csrf_helper.php';

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
            <p class="lead">Instance nationale de coordination, de suivi et d’appui technique pour la mise en œuvre de l’agenda Femmes, Paix et Sécurité en République Démocratique du Congo.</p>
        </div>
    </div>
</section>
<!-- Start Portfolio Details Area -->
		<section class="pf-details section">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="inner-content">
                                <div class="secretariat-overview">
                                    <article class="overview-card">
                                        <span class="overview-label">Création officielle</span>
                                        <strong>2015</strong>
                                        <p>Création et installation officielles par arrêtés ministériels du Ministère du Genre.</p>
                                    </article>
                                    <article class="overview-card">
                                        <span class="overview-label">Équipe nationale</span>
                                        <strong>16 experts</strong>
                                        <p>4 experts permanents et 12 experts non permanents mobilisés autour du plan d’action national.</p>
                                    </article>
                                    <article class="overview-card">
                                        <span class="overview-label">Portée</span>
                                        <strong>Nationale</strong>
                                        <p>Coordination, suivi, supervision et capitalisation des actions sur l’ensemble du territoire.</p>
                                    </article>
                                    <article class="overview-card">
                                        <span class="overview-label">Pilotage</span>
                                        <strong>Multi-acteurs</strong>
                                        <p>Dispositif articulé entre institutions publiques, société civile et partenaires techniques et financiers.</p>
                                    </article>
                                </div>

                                <div class="secretariat-layout">
                                    <div class="secretariat-main">
                                        <section class="story-panel">
                                            <div class="story-media">
                                                <div class="pf-details-slider story-slider">
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
                                                        $sliderImages = ['didier.jpg', 'snational132503.png', 'snational132504.png', 'snational132505.png', 'snational132506.png', 'snational132507.png', 'snational132508.png', 'snational132510.png', 'snational132511.png', 'snational132513.png'];
                                                        $imgFsDir = __DIR__ . '/../img/';
                                                        render_image_slider($sliderImages, $heroImgName, IMG_DIR, $imgFsDir);
                                                    }
                                                    ?>
                                                </div>
                                                <p class="story-caption">Galerie institutionnelle du Secrétariat National 1325, illustrant la coordination, les activités de suivi et les cadres de concertation mis en œuvre autour de la Résolution 1325.</p>
                                            </div>
                                            <div class="story-copy">
                                                <span class="eyebrow">Structure de coordination</span>
                                                <h2>Une instance opérationnelle au service de la Résolution 1325</h2>
                                                <p>Le Secrétariat National de la Résolution 1325 en RDC constitue le dispositif technique chargé d’assurer le suivi opérationnel, la coordination quotidienne et l’animation institutionnelle de la mise en œuvre de la Résolution sur l’ensemble du territoire national.</p>
                                                <p>Mis en place à la suite des arrêtés ministériels du 04 août 2015 portant création, organisation, fonctionnement et nomination de ses membres, il a été officiellement installé le 08 septembre 2015 pour soutenir l’ancrage national de l’agenda Femmes, Paix et Sécurité.</p>
                                                <div class="story-highlights">
                                                    <span>Suivi national</span>
                                                    <span>Coordination interministérielle</span>
                                                    <span>Appui aux bénéficiaires</span>
                                                    <span>Gestion des données</span>
                                                </div>
                                            </div>
                                        </section>

                                        <div class="body-text">
                                            <section id="contexte" class="section-block">
                                                <div class="section-heading">
                                                    <span class="section-kicker">Organisation</span>
                                                    <h3>Composition du Secrétariat</h3>
                                                    <p>Une architecture mixte qui associe un noyau opérationnel permanent à une contribution institutionnelle élargie, afin d’assurer expertise, représentation et relais sectoriels.</p>
                                                </div>
                                                <div class="composition-grid">
                                                    <article class="content-card accent-card">
                                                        <h4>Experts nationaux permanents</h4>
                                                        <p class="card-intro">Le noyau permanent garantit la continuité administrative, financière, technique et logistique du Secrétariat.</p>
                                                        <ul class="feature-list strong-list">
                                                            <li><strong>Madame Annie KENDA</strong> — Directeur chef de service Juridique et Secrétaire Permanente du Conseil national de la Femme : <strong>Coordonnatrice Nationale du SN1325</strong></li>
                                                            <li><strong>Madame Esther KAMUANYA</strong> — Directeur chef de service chargée des questions socioéconomiques : <strong>Chargée des Finances</strong></li>
                                                            <li><strong>Monsieur Didier LAPIARD</strong> — Expert à la cellule d’étude et de planification au Ministère du Genre, de la Famille et de l’Enfant : <strong>Chargé de l’administration et des questions techniques</strong></li>
                                                            <li><strong>Délégué du Cabinet de Madame la Ministre du Genre, Enfant et Famille</strong> — <strong>Chargé de la logistique</strong></li>
                                                        </ul>
                                                    </article>
                                                    <article class="content-card soft-card">
                                                        <h4>Experts nationaux non permanents</h4>
                                                        <p class="card-intro">Douze expertises complémentaires issues des institutions publiques et de la société civile appuient le pilotage stratégique, le plaidoyer et l’ancrage intersectoriel.</p>
                                                        <ul class="feature-list compact-list">
                                                            <li>Un(e) Expert(e) du Ministère de la Justice</li>
                                                            <li>Un(e) Expert(e) du Ministère des Affaires Étrangères</li>
                                                            <li>Un(e) Expert(e) du Ministère du Budget</li>
                                                            <li>Un(e) Expert(e) du Ministère du Plan</li>
                                                            <li>Un(e) Expert(e) du Ministère de la Défense et des Anciens Combattants</li>
                                                            <li>Un(e) Expert(e) du Ministère de l’Intérieur et de la Sécurité</li>
                                                            <li>Trois représentants(es) de la société civile, notamment CAFCO, CJR1325 et WILF/RDC</li>
                                                            <li>Un(e) Expert(e) du secrétariat général du Ministère du Genre, de la Famille et de l’Enfant</li>
                                                            <li>Un(e) Expert(e) de la Cellule d’Études et de Planification de la promotion de la Femme, de la Famille et de la protection de l’Enfant</li>
                                                            <li>Un(e) Expert(e) du Cabinet du Ministre du Genre</li>
                                                        </ul>
                                                    </article>
                                                </div>
                                            </section>

                                            <section id="missions" class="section-block">
                                                <div class="section-heading">
                                                    <span class="section-kicker">Mandat opérationnel</span>
                                                    <h3>Missions assignées au SN1325</h3>
                                                    <p>Le Secrétariat agit comme centre de coordination, de suivi, d’accompagnement technique, de concertation et de capitalisation des actions engagées.</p>
                                                </div>
                                                <div class="mission-grid">
                                                    <article class="mission-card"><span>01</span><p>Participer aux activités du programme, conduire des missions de suivi et produire des rapports périodiques sur l’état de mise en œuvre du plan d’action national.</p></article>
                                                    <article class="mission-card"><span>02</span><p>Préparer les réunions du comité de pilotage, assurer son secrétariat et maintenir une base de données utile au suivi des actions.</p></article>
                                                    <article class="mission-card"><span>03</span><p>Assurer une concertation permanente autour des inégalités de genre entre les différents acteurs impliqués.</p></article>
                                                    <article class="mission-card"><span>04</span><p>Initier des enquêtes périodiques sur la prise en compte du genre et la lutte contre les violences sexuelles, puis publier les résultats.</p></article>
                                                    <article class="mission-card"><span>05</span><p>Créer et réviser annuellement les critères d’évaluation technique des propositions reçues.</p></article>
                                                    <article class="mission-card"><span>06</span><p>Examiner les propositions initiales, établir une liste de projets à soumettre au Comité de Pilotage et formuler des recommandations.</p></article>
                                                    <article class="mission-card"><span>07</span><p>Apporter des contributions techniques, assister les bénéficiaires de subventions dans la mise en œuvre, le suivi et le plaidoyer.</p></article>
                                                    <article class="mission-card"><span>08</span><p>Participer au renforcement des capacités des bénéficiaires et d’autres ONG nationales sélectionnées.</p></article>
                                                    <article class="mission-card"><span>09</span><p>Présenter, avec ONU Femmes en tant qu’administrateur du Fonds, les propositions retenues au Comité de Pilotage pour décision finale.</p></article>
                                                </div>
                                            </section>

                                            <section id="institutions" class="section-block institutions">
                                                <div class="section-heading">
                                                    <span class="section-kicker">Écosystème</span>
                                                    <h3>Structure institutionnelle et partenaires</h3>
                                                    <p>La mise en œuvre de la Résolution 1325 repose sur une coordination institutionnelle forte, relayée par des partenaires techniques et financiers engagés dans l’agenda genre, paix et sécurité.</p>
                                                </div>
                                                <div class="institution-cards">
                                                    <article class="inst-card" aria-labelledby="inst-acteurs">
                                                        <h5 id="inst-acteurs">Acteurs clés</h5>
                                                        <ul>
                                                            <li>Ministère du Genre, Famille et Enfant — point focal institutionnel</li>
                                                            <li>Secrétariat National 1325 — coordination stratégique et opérationnelle</li>
                                                            <li>Points focaux provinciaux — déploiement territorial</li>
                                                            <li>Société civile — mise en œuvre, veille et monitoring</li>
                                                        </ul>
                                                    </article>

                                                    <article class="inst-card" aria-labelledby="inst-partenaires">
                                                        <h5 id="inst-partenaires">Partenaires techniques et financiers</h5>
                                                        <ul>
                                                            <li>ONU Femmes RDC — appui technique et financier</li>
                                                            <li>MONUSCO — division Genre</li>
                                                            <li>Ambassade de Norvège</li>
                                                            <li>ONG nationales et internationales</li>
                                                        </ul>
                                                    </article>
                                                </div>
                                            </section>

                                            <section id="resultats" class="section-block results">
                                                <div class="section-heading">
                                                    <span class="section-kicker">Impact</span>
                                                    <h3>Résultats et défis</h3>
                                                    <p>Des avancées réelles sont observées dans la prise en compte du genre, mais elles demeurent fragiles face aux contraintes institutionnelles, sécuritaires et financières.</p>
                                                </div>
                                                <div class="results-cards">
                                                    <article class="result-card success-card" aria-labelledby="res-succes">
                                                        <h5 id="res-succes">Succès documentés</h5>
                                                        <ul>
                                                            <li>Augmentation du nombre de femmes dans les instances</li>
                                                            <li>Renforcement des capacités des organisations féminines</li>
                                                            <li>Prise en compte du genre dans la réforme sécuritaire</li>
                                                            <li>Documentation systématique des violences basées sur le genre</li>
                                                        </ul>
                                                    </article>

                                                    <article class="result-card challenge-card" aria-labelledby="res-defis">
                                                        <h5 id="res-defis">Défis persistants</h5>
                                                        <ul>
                                                            <li>Financement insuffisant des initiatives de genre</li>
                                                            <li>Insécurité dans les zones de conflit</li>
                                                            <li>Résistances à l’égalité de genre</li>
                                                        </ul>
                                                    </article>
                                                </div>
                                            </section>

                                            <section class="engagement-band">
                                                <div>
                                                    <span class="section-kicker">Rester connecté</span>
                                                    <h3>Suivre les actions du Secrétariat</h3>
                                                    <p>Retrouvez les publications, interventions publiques, informations institutionnelles et actualités liées à la mise en œuvre de la Résolution 1325 en RDC.</p>
                                                </div>
                                                <div class="share">
                                    <h4>Nous suivre</h4>
                                    <ul>
                                        <li><a href="https://web.facebook.com/sn1325/" target="_blank" rel="noopener noreferrer"><i class="fa fa-facebook-official" aria-hidden="true"></i></a></li>
                                        <li><a href="https://x.com/R1325RDC" target="_blank" rel="noopener noreferrer"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                        <li><a href="https://www.linkedin.com/company/R%C3%A9solution%201325%20RDC/" target="_blank" rel="noopener noreferrer"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
                                    </ul>
                                </div>
                                            </section>
                                        </div>
                                    </div>

                                    <aside class="secretariat-sidebar">
                                        <div class="sidebar-stack">
                                            <article class="sidebar-card quick-nav-card">
                                                <span class="sidebar-label">Navigation rapide</span>
                                                <ul>
                                                    <li><a href="#contexte">Composition</a></li>
                                                    <li><a href="#missions">Missions</a></li>
                                                    <li><a href="#institutions">Institutions et partenaires</a></li>
                                                    <li><a href="#resultats">Résultats et défis</a></li>
                                                </ul>
                                            </article>
                                            <article class="sidebar-card emphasis-card">
                                                <span class="sidebar-label">Repères clés</span>
                                                <ul class="fact-list">
                                                    <li><strong>4</strong><span>experts permanents</span></li>
                                                    <li><strong>12</strong><span>experts non permanents</span></li>
                                                    <li><strong>2015</strong><span>année de création</span></li>
                                                    <li><strong>1</strong><span>base de coordination nationale</span></li>
                                                </ul>
                                            </article>
                                            <article class="sidebar-card contact-card">
                                                <span class="sidebar-label">Coordination</span>
                                                <h4>Secrétariat National 1325</h4>
                                                <p>Le Secrétariat reçoit au siège de coordination à Kinshasa-Gombe, dans la concession du Secrétariat au Développement Rural, en diagonale du Premier Shopping Mall.</p>
                                                <ul class="contact-points">
                                                    <li><i class="fa fa-envelope" aria-hidden="true"></i><span>contact@sn1325.cd</span></li>
                                                    <li><i class="fa fa-calendar" aria-hidden="true"></i><span>Lundi à vendredi, 8h00 à 16h00</span></li>
                                                </ul>
                                                <a class="sidebar-cta" href="<?= URL_CONTACT ?>">Contacter le secrétariat</a>
                                            </article>
                                        </div>
                                    </aside>
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
