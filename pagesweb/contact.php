<?php
    require_once __DIR__ . '/../configUrl.php'; // __DIR__ = dossier racine
    require_once __DIR__ . '/../defConstLiens.php'; // __DIR__ = dossier racine

  //require_once $dataDbConnect; 
 
?>

<!-- Composant header  page cn debut -->
    <?php require_once $headerPath;  ?>
<!-- Composant header page cn fin  -->		
		
		<!-- Breadcrumbs -->
		<div class="breadcrumbs overlay">
			<div class="container">
				<div class="bread-inner">
					<div class="row">
						<div class="col-12">
							<h2>Contactez-nous</h2>
							
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Breadcrumbs -->
		 <!-- Start Contact Us -->
		<section class="contact-us section">
			<div class="container">
				<div class="inner">
					<div class="row"> 
						<div class="col-lg-6">
							<div class="contact-us-left">
								<!--Start Google-map -->
								<div id="myMap"></div>
								<!--/End Google-map -->
							</div>
						</div>
						<div class="col-lg-6">
							<div class="contact-us-form">
								<h2>Contactez-nous</h2>
								<p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
								<!-- Form -->
								<form class="form" method="post" action="mail/mail.php">
									<div class="row">
										<div class="col-lg-6">
											<div class="form-group">
												<input type="text" name="name" placeholder="Nom" required="">
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<input type="email" name="email" placeholder="Email" required="">
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<input type="text" name="phone" placeholder="Téléphone" required="">
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<input type="text" name="subject" placeholder="Objet" required="">
											</div>
										</div>
										<div class="col-lg-12">
											<div class="form-group">
												<textarea name="message" placeholder="Votre message" required=""></textarea>
											</div>
										</div>
										<div class="col-12">
											<div class="form-group login-btn">
												<button class="btn" type="submit">Envoyer</button>
											</div>
											<div class="checkbox">
												<label class="checkbox-inline" for="2"><input name="news" id="2" type="checkbox">Souhaitez-vous vous abonner à notre newsletter ?</label>
											</div>
										</div>
									</div>
								</form>
								<!--/ End Form -->
							</div>
						</div>
					</div>
				</div>
				<div class="contact-info">
					<div class="row">
						<!-- single-info -->
						<div class="col-lg-4 col-12 ">
							<div class="single-info">
								<i class="icofont icofont-ui-call"></i>
								<div class="content">
									<h3>+(243) *** *** ***</h3>
									<p>secretariat@info1325.cd</p>
								</div>
							</div>
						</div>
						<!--/End single-info -->
						<!-- single-info -->
						<div class="col-lg-4 col-12 ">
							<div class="single-info">
								<i class="icofont-google-map"></i>
								<div class="content">
									<h3>En diagonale de PREMIERMALL</h3>
									<p>Kinshasa, Gombe</p>
								</div>
							</div>
						</div>
						<!--/End single-info -->
						<!-- single-info -->
						<div class="col-lg-4 col-12 ">
							<div class="single-info">
								<i class="icofont icofont-wall-clock"></i>
								<div class="content">
									<h3>Lundi- Samedi: 8h - 17h</h3>
									<p>Dimanche Fermé</p>
								</div>
							</div>
						</div>
						<!--/End single-info -->
					</div>
				</div>
			</div>
		</section>
		<!--/ End Contact Us -->
	<!-- Composant footer  page cn debut -->
    <?php require_once $footerPath;  ?>
<!-- Composant footer page cn fin  -->			