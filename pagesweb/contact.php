<?php
    require_once __DIR__ . '/../configUrl.php'; // __DIR__ = dossier racine
    require_once __DIR__ . '/../defConstLiens.php'; // __DIR__ = dossier racine

	// Page-specific CSS
	$SKIP_PAGE_TITLE = true; // we'll render a hero on this page
	$pageCss = CSS_DIR . 'contact.css';
	// keep compatibility with older inline handler checks
	$simple_mail_sent = null;
	require_once __DIR__ . '/track_visitor.php';
	require_once __DIR__ . '/csrf_helper.php';
	//require_once $dataDbConnect; 

// Use central mail handler at ../mail/mail.php which prefers PHPMailer/SMTP when available
// The form below posts to ../mail/mail.php
 
?>

<!-- Composant header  page cn debut -->
    <?php require_once $headerPath;  ?>
<!-- Composant header page cn fin  -->		
		
		<!-- Hero -->
		<section class="site-page-hero">
			<div class="container">
					<div class="site-page-heading">
						<span>Contact</span>
						<h1>Contactez le Secrétariat</h1>
						<p class="lead">Point de contact institutionnel pour les demandes d’information, les partenariats et les collaborations techniques.</p>
					</div>
			</div>
		</section>

		<!-- Contact area -->
		<section class="contact-area section">
			<div class="container">
				<div class="row align-items-stretch">
					<div class="col-lg-6">
						<div class="contact-form card">
							<div class="section-intro">
								<span class="section-kicker">Correspondance officielle</span>
								<h3>Adressez votre demande au Secrétariat</h3>
							</div>
							<?php if ($simple_mail_sent === true): ?>
								<div class="alert alert-success">Merci — votre message a été envoyé.</div>
							<?php elseif ($simple_mail_sent === false): ?>
								<div class="alert alert-danger">Erreur lors de l'envoi du message. Le message a été enregistré pour diagnostic.</div>
							<?php endif; ?>

							<form id="contactForm" method="post" action="/mail/mail.php" novalidate>
								<?= csrf_field() ?>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<input type="text" name="name" id="name" placeholder="Nom *" required>
											<small class="text-danger" id="nameError"></small>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<input type="email" name="email" id="email" placeholder="Email *" required>
											<small class="text-danger" id="emailError"></small>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<input type="text" name="phone" id="phone" placeholder="Téléphone / WhatsApp *" required>
											<small class="text-danger" id="phoneError"></small>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<input type="text" name="subject" id="subject" placeholder="Objet *" required>
											<small class="text-danger" id="subjectError"></small>
										</div>
									</div>
									<div class="col-12">
										<div class="form-group">
											<textarea name="message" id="message" placeholder="Votre message *" required></textarea>
											<small class="text-danger" id="messageError"></small>
										</div>
									</div>
									<div class="col-12 form-actions">
										<button class="btn-primary" type="submit" id="submitBtn">Envoyer</button>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="contact-map card">
							<div class="section-intro map-intro">
								<span class="section-kicker">Coordonnées</span>
								<h3>Secrétariat National 1325</h3>
							</div>
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
										<p>Concession du Secrétariat au Développement Rural</p>
									</div>
								</div>
								<div class="info-card">
									<i class="icofont icofont-wall-clock"></i>
									<div>
										<h5>Heures</h5>
										<p>Lundi-Vendredi: 8h00-16h00</p>
									</div>
								</div>
							</div>
							<div class="contact-note">
								<p>Kinshasa-Gombe, concession du Secrétariat au Développement Rural, en diagonale du Premier Shopping Mall.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	<!-- Composant footer  page cn debut -->
    <?php require_once $footerPath;  ?>
<!-- Composant footer page cn fin  -->

<script>
document.addEventListener('DOMContentLoaded', function() {
	const form = document.getElementById('contactForm');
	const nameInput = document.getElementById('name');
	const emailInput = document.getElementById('email');
	const phoneInput = document.getElementById('phone');
	const subjectInput = document.getElementById('subject');
	const messageInput = document.getElementById('message');
	
	const nameError = document.getElementById('nameError');
	const emailError = document.getElementById('emailError');
	const phoneError = document.getElementById('phoneError');
	const subjectError = document.getElementById('subjectError');
	const messageError = document.getElementById('messageError');

	// Validation en temps réel
	nameInput.addEventListener('blur', validateName);
	emailInput.addEventListener('blur', validateEmail);
	phoneInput.addEventListener('blur', validatePhone);
	subjectInput.addEventListener('blur', validateSubject);
	messageInput.addEventListener('blur', validateMessage);

	function validateName() {
		nameError.textContent = '';
		if (nameInput.value.trim() === '') {
			nameError.textContent = 'Le nom est obligatoire';
			nameInput.classList.add('is-invalid');
			return false;
		}
		if (nameInput.value.trim().length < 2) {
			nameError.textContent = 'Le nom doit contenir au moins 2 caractères';
			nameInput.classList.add('is-invalid');
			return false;
		}
		nameInput.classList.remove('is-invalid');
		nameInput.classList.add('is-valid');
		return true;
	}

	function validateEmail() {
		emailError.textContent = '';
		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		if (emailInput.value.trim() === '') {
			emailError.textContent = 'L\'email est obligatoire';
			emailInput.classList.add('is-invalid');
			return false;
		}
		if (!emailRegex.test(emailInput.value)) {
			emailError.textContent = 'Veuillez entrer un email valide';
			emailInput.classList.add('is-invalid');
			return false;
		}
		emailInput.classList.remove('is-invalid');
		emailInput.classList.add('is-valid');
		return true;
	}

	function validatePhone() {
		phoneError.textContent = '';
		if (phoneInput.value.trim() === '') {
			phoneError.textContent = 'Le téléphone est obligatoire';
			phoneInput.classList.add('is-invalid');
			return false;
		}
		if (phoneInput.value.trim().length < 7) {
			phoneError.textContent = 'Le téléphone doit contenir au moins 7 chiffres';
			phoneInput.classList.add('is-invalid');
			return false;
		}
		phoneInput.classList.remove('is-invalid');
		phoneInput.classList.add('is-valid');
		return true;
	}

	function validateSubject() {
		subjectError.textContent = '';
		if (subjectInput.value.trim() === '') {
			subjectError.textContent = 'L\'objet est obligatoire';
			subjectInput.classList.add('is-invalid');
			return false;
		}
		if (subjectInput.value.trim().length < 3) {
			subjectError.textContent = 'L\'objet doit contenir au moins 3 caractères';
			subjectInput.classList.add('is-invalid');
			return false;
		}
		subjectInput.classList.remove('is-invalid');
		subjectInput.classList.add('is-valid');
		return true;
	}

	function validateMessage() {
		messageError.textContent = '';
		if (messageInput.value.trim() === '') {
			messageError.textContent = 'Le message est obligatoire';
			messageInput.classList.add('is-invalid');
			return false;
		}
		if (messageInput.value.trim().length < 10) {
			messageError.textContent = 'Le message doit contenir au moins 10 caractères';
			messageInput.classList.add('is-invalid');
			return false;
		}
		messageInput.classList.remove('is-invalid');
		messageInput.classList.add('is-valid');
		return true;
	}

	// Validation à la soumission
	form.addEventListener('submit', function(e) {
		const isNameValid = validateName();
		const isEmailValid = validateEmail();
		const isPhoneValid = validatePhone();
		const isSubjectValid = validateSubject();
		const isMessageValid = validateMessage();

		if (!isNameValid || !isEmailValid || !isPhoneValid || !isSubjectValid || !isMessageValid) {
			e.preventDefault();
			alert('Veuillez remplir correctement tous les champs obligatoires');
		}
	});
});
</script>
