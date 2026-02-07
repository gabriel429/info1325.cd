<?php
    require_once __DIR__ . '/../configUrl.php'; // __DIR__ = dossier racine
    require_once __DIR__ . '/../defConstLiens.php'; // __DIR__ = dossier racine

	// Page-specific CSS
	$SKIP_PAGE_TITLE = true; // we'll render a hero on this page
	$pageCss = CSS_DIR . 'contact.css';
	// keep compatibility with older inline handler checks
	$simple_mail_sent = null;
	//require_once $dataDbConnect; 

// Use central mail handler at ../mail/mail.php which prefers PHPMailer/SMTP when available
// The form below posts to ../mail/mail.php
 
?>

<!-- Composant header  page cn debut -->
    <?php require_once $headerPath;  ?>
<!-- Composant header page cn fin  -->		
		
		<!-- Hero -->
		<section class="caremed-hero" style="background:linear-gradient(90deg, rgba(17,82,147,0.9), rgba(0,123,193,0.9));">
			<div class="container">
				<div class="hero-content">
					<div class="hero-breadcrumb">Accueil / Contact</div>
					<h1>Contactez le Secrétariat</h1>
					<p class="lead">Pour questions, collaborations ou informations, écrivez-nous.</p>
				</div>
			</div>
		</section>

		<!-- Contact area -->
		<section class="contact-area section">
			<div class="container">
				<div class="row align-items-stretch">
					<div class="col-lg-6">
						<div class="contact-form card">
							<h3>Écrivez-nous</h3>
							<?php if ($simple_mail_sent === true): ?>
								<div class="alert alert-success">Merci — votre message a été envoyé.</div>
							<?php elseif ($simple_mail_sent === false): ?>
								<div class="alert alert-danger">Erreur lors de l'envoi du message. Le message a été enregistré pour diagnostic.</div>
							<?php endif; ?>

							<form method="post" action="/info1325.cd/mail/mail.php" novalidate>
								<div class="row">
									<div class="col-md-6"><input type="text" name="name" placeholder="Nom" required></div>
									<div class="col-md-6"><input type="email" name="email" placeholder="Email" required></div>
									<div class="col-md-6"><input type="text" name="phone" placeholder="Téléphone"></div>
									<div class="col-md-6"><input type="text" name="subject" placeholder="Objet"></div>
									<div class="col-12"><textarea name="message" placeholder="Votre message" required></textarea></div>
									<div class="col-12 form-actions"><button class="btn-primary" type="submit">Envoyer</button></div>
								</div>
							</form>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="contact-map card">
							<div class="map-embed" style="height:320px; border-radius:8px; overflow:hidden">
								<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d3978.5619295358747!2d15.297094775712122!3d-4.304897446378!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sfr!2scd!4v1770377691242!5m2!1sfr!2scd" width="100%" height="320" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
							</div>
							<div class="contact-cards">
								<div class="info-card">
									<i class="icofont icofont-ui-call"></i>
									<div>
										<h5>+(243) *** *** ***</h5>
										<p>secretariat@info1325.cd</p>
									</div>
								</div>
								<div class="info-card">
									<i class="icofont-google-map"></i>
									<div>
										<h5>Kinshasa, Gombe</h5>
										<p>En diagonale de PREMIERMALL</p>
									</div>
								</div>
								<div class="info-card">
									<i class="icofont icofont-wall-clock"></i>
									<div>
										<h5>Heures</h5>
										<p>Lundi-Samedi: 8h-17h</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	<!-- Composant footer  page cn debut -->
    <?php require_once $footerPath;  ?>
<!-- Composant footer page cn fin  -->			