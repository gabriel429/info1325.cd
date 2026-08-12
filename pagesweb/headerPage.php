<?php
ini_set('default_charset', 'UTF-8');
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}

if (!function_exists('meta_escape')) {
	function meta_escape($value): string
	{
		return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	}
}

if (!function_exists('meta_plain_text')) {
	function meta_plain_text($value, int $length = 220): string
	{
		$text = trim(preg_replace('/\s+/', ' ', strip_tags((string)$value)) ?? '');
		if ($text === '') {
			return '';
		}

		return function_exists('mb_strimwidth')
			? mb_strimwidth($text, 0, $length, '...')
			: substr($text, 0, $length);
	}
}

if (!function_exists('meta_origin')) {
	function meta_origin(): string
	{
		$host = $_SERVER['HTTP_HOST'] ?? 'info1325.cd';
		$forwardedProto = trim(explode(',', (string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))[0]);
		$isLocal = preg_match('/(^localhost$|^127\.0\.0\.1|\.local$|\.test$)/i', $host);
		$scheme = $forwardedProto !== ''
			? $forwardedProto
			: ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || !$isLocal ? 'https' : 'http');

		return $scheme . '://' . $host;
	}
}

if (!function_exists('meta_absolute_url')) {
	function meta_absolute_url($url): string
	{
		$url = trim((string)$url);
		if ($url === '') {
			return '';
		}
		if (preg_match('#^https?://#i', $url)) {
			return $url;
		}
		if (strpos($url, '//') === 0) {
			return parse_url(meta_origin(), PHP_URL_SCHEME) . ':' . $url;
		}

		$origin = meta_origin();
		$host = (string)(parse_url($origin, PHP_URL_HOST) ?? '');
		$isLocalHost = preg_match('/(^localhost$|^127\.0\.0\.1$|^\[?::1\]?$)/i', $host);
		if (!$isLocalHost && defined('PROJECT_ROOT_URL')) {
			$projectPrefix = '/' . trim(PROJECT_ROOT_URL, '/') . '/';
			if ($projectPrefix !== '//' && strpos($url, $projectPrefix) === 0) {
				$url = '/' . substr($url, strlen($projectPrefix));
			}
		}

		return rtrim($origin, '/') . '/' . ltrim($url, '/');
	}
}

$metaTitle = $pageTitle ?? $PAGE_TITLE ?? 'SN1325 - Secrétariat National de la Résolution 1325 en RDC';
$metaDescription = meta_plain_text($pageDescription ?? 'Plateforme officielle du Secrétariat National Permanent 1325 en République Démocratique du Congo.');
$metaUrl = meta_absolute_url($pageUrl ?? ($_SERVER['REQUEST_URI'] ?? BASE_URL));
$metaImage = meta_absolute_url($pageImage ?? (IMG_DIR . 'logo.png'));
$metaImageAlt = meta_plain_text($pageImageAlt ?? $metaTitle, 120);
$metaType = $pageType ?? 'website';
$googleTagId = '';
try {
	$settingsHelperPath = __DIR__ . '/settings_helper.php';
	if (file_exists($settingsHelperPath)) {
		require_once $settingsHelperPath;
		if (function_exists('get_setting')) {
			$googleTagRaw = trim((string)get_setting('seo_google_analytics', ''));
			if (preg_match('/\b(G-[A-Z0-9-]+|AW-\d+|GT-[A-Z0-9-]+)\b/i', $googleTagRaw, $googleTagMatch)) {
				$googleTagId = strtoupper($googleTagMatch[1]);
			}
		}
	}
} catch (Throwable $e) {
	error_log('Google tag settings error: ' . $e->getMessage());
}

if (!function_exists('header_social_url')) {
	function header_social_url(string $settingKey, string $fallback): string
	{
		if (function_exists('get_setting')) {
			$value = trim((string)get_setting($settingKey, ''));
			if ($value !== '') {
				return $value;
			}
		}

		return $fallback;
	}
}

$headerSocialLinks = [
	[
		'label' => 'Facebook',
		'url' => header_social_url('social_facebook', 'https://web.facebook.com/sn1325/'),
		'icon' => '<i class="fa fa-facebook" aria-hidden="true"></i>',
	],
	[
		'label' => 'X',
		'url' => header_social_url('social_twitter', 'https://x.com/R1325RDC'),
		'icon' => '<span class="social-x" aria-hidden="true">X</span>',
	],
	[
		'label' => 'WhatsApp',
		'url' => header_social_url('social_whatsapp', 'https://whatsapp.com/channel/0029VbBYE3UJENxszyUe2e3F'),
		'icon' => '<i class="fa fa-whatsapp" aria-hidden="true"></i>',
	],
	[
		'label' => 'LinkedIn',
		'url' => header_social_url('social_linkedin', 'https://www.linkedin.com/company/r1325rdc/'),
		'icon' => '<i class="fa fa-linkedin" aria-hidden="true"></i>',
	],
];
?>
<!doctype html>

<html class="no-js" lang="zxx">

    <head>

        <!-- Meta Tags -->

		<meta charset="utf-8">

		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<meta name="keywords" content="Site keywords here">

		<meta name="description" content="<?= meta_escape($metaDescription) ?>">

		<meta name='copyright' content=''>

		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<link rel="canonical" href="<?= meta_escape($metaUrl) ?>">
		<meta property="og:locale" content="fr_FR">
		<meta property="og:site_name" content="SN1325">
		<meta property="og:type" content="<?= meta_escape($metaType) ?>">
		<meta property="og:title" content="<?= meta_escape($metaTitle) ?>">
		<meta property="og:description" content="<?= meta_escape($metaDescription) ?>">
		<meta property="og:url" content="<?= meta_escape($metaUrl) ?>">
		<meta property="og:image" content="<?= meta_escape($metaImage) ?>">
		<meta property="og:image:secure_url" content="<?= meta_escape($metaImage) ?>">
		<meta property="og:image:alt" content="<?= meta_escape($metaImageAlt) ?>">
		<meta name="twitter:card" content="summary_large_image">
		<meta name="twitter:title" content="<?= meta_escape($metaTitle) ?>">
		<meta name="twitter:description" content="<?= meta_escape($metaDescription) ?>">
		<meta name="twitter:image" content="<?= meta_escape($metaImage) ?>">
		<?php if (!empty($pagePublishedTime)): ?>
			<meta property="article:published_time" content="<?= meta_escape($pagePublishedTime) ?>">
		<?php endif; ?>
		<?php if (!empty($pageAuthor)): ?>
			<meta property="article:author" content="<?= meta_escape($pageAuthor) ?>">
		<?php endif; ?>
		<?php if ($googleTagId !== ''): ?>
			<script async src="https://www.googletagmanager.com/gtag/js?id=<?= meta_escape($googleTagId) ?>"></script>
			<script>
				window.dataLayer = window.dataLayer || [];
				function gtag(){dataLayer.push(arguments);}
				gtag('js', new Date());
				gtag('config', '<?= meta_escape($googleTagId) ?>');
			</script>
		<?php endif; ?>

		

		<!-- Title -->

        <title><?= meta_escape($metaTitle) ?></title>

		

		<!-- Favicon -->

		<link rel="icon" type="image/png" href="<?= asset_url(IMG_DIR . 'favicon.png') ?>">

		

		<!-- Google Fonts -->

		<link href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">

<!-- Bootstrap CSS -->

		<link rel="stylesheet" href="<?= asset_url(CSS_DIR . 'bootstrap.min.css') ?>">

		<!-- Nice Select CSS -->

		<link rel="stylesheet" href="<?= asset_url(CSS_DIR . 'nice-select.css') ?>">

		<!-- Font Awesome CSS -->

		<link rel="stylesheet" href="<?= asset_url(CSS_DIR . 'font-awesome.min.css') ?>">

		<!-- icofont CSS -->

		<link rel="stylesheet" href="<?= asset_url(CSS_DIR . 'icofont.css') ?>">

        
			<?php
			// Debug helper: show resolved CSS paths and whether files exist on server
			// Only output debug comments when DEBUG_ASSETS is enabled.
			$s1 = rtrim(ROOT_DIR, '/') . '/css/style.css';
			$s2 = rtrim(ROOT_DIR, '/') . '/css/responsive.css';
			if (defined('DEBUG_ASSETS') && DEBUG_ASSETS) {
			    echo "<!-- ASSET_DEBUG: style=" . htmlspecialchars(CSS_DIR . 'style.css', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . " exists=" . (file_exists($s1) ? '1' : '0') . " -->\n";
			    echo "<!-- ASSET_DEBUG: responsive=" . htmlspecialchars(CSS_DIR . 'responsive.css', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . " exists=" . (file_exists($s2) ? '1' : '0') . " -->\n";
			}
			?>
		<!-- Slicknav -->

		<link rel="stylesheet" href="<?= asset_url(CSS_DIR . 'slicknav.min.css') ?>">

		<!-- Owl Carousel CSS -->

		<link rel="stylesheet" href="<?= asset_url(CSS_DIR . 'owl-carousel.css') ?>">

		<!-- Datepicker CSS -->

		<link rel="stylesheet" href="<?= asset_url(CSS_DIR . 'datepicker.css') ?>">

		<!-- Animate CSS -->

		<link rel="stylesheet" href="<?= asset_url(CSS_DIR . 'animate.min.css') ?>">

		<!-- Magnific Popup CSS -->

		<link rel="stylesheet" href="<?= asset_url(CSS_DIR . 'magnific-popup.css') ?>">

		

		<!-- Medipro CSS -->

		<link rel="stylesheet" href="<?= asset_url(CSS_DIR . 'normalize.css') ?>">

		<link rel="stylesheet" href="<?= asset_url(CSS_DIR . 'style.css') ?>">

		<link rel="stylesheet" href="<?= asset_url(CSS_DIR . 'responsive.css') ?>">
		<!-- Theme overrides: CareMed-inspired variables (applies site-wide) -->
		<link rel="stylesheet" href="<?= asset_url(CSS_DIR . 'caremed-variables.css') ?>">
		<!-- Material Design 3 Global Styles -->
		<link rel="stylesheet" href="<?= asset_url(CSS_DIR . 'material-design-global.css') ?>">
		<?php if (!empty($pageCss)) : ?>
			<link rel="stylesheet" href="<?= asset_url($pageCss) ?>">
		<?php endif; ?>
		<!-- Override global pour tous les boutons - charge en dernier -->
		<link rel="stylesheet" href="<?= asset_url(CSS_DIR . 'buttons-override.css') ?>">

		<style>
			/* Réduction des grands espaces entre sections pour la page d'accueil */
			.breadcrumbs-ac {
				background-image: url('../img/actualites/1762163084_rt.png');
				background-size: cover;
				background-position: center;
				background-repeat: no-repeat;
				position: relative;
				padding: 40px 0px; /* réduit de 120px à 40px */
			}
			/* règle globale plus serrée pour les sections */
			section, .section, .section-padding {
				padding-top: 30px !important;
				padding-bottom: 30px !important;
				margin-top: 0 !important;
				margin-bottom: 0 !important;
			}
		</style>

		

    </head>

    <body>

	

		<!-- Preloader -->

       <!--<div class="preloader">

            <div class="loader">

                <div class="loader-outter"></div>

                <div class="loader-inner"></div>



                <div class="indicator"> 

                    <svg width="16px" height="12px">

                        <polyline id="back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline>

                        <polyline id="front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline>

                    </svg>

                </div>

            </div>

        </div>-->

        <!-- End Preloader -->

		

		<!-- Header Area -->
		<?php
		$currentScript = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
		$requestPath = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
		$isHomePage = $currentScript === '' || $currentScript === 'index.php' || $requestPath === '/' || preg_match('#/info1325\.cd/?$#', $requestPath);
		$isActualitesPage = in_array($currentScript, ['actualites.php', 'actualites-list.php'], true)
			|| preg_match('#/actualites(?:/|$)#', $requestPath);
		$isDocumentationPage = in_array($currentScript, ['documentation.php', 'all-documentations.php', 'documentation_event.php'], true);
		$isResolutionPage = $currentScript === 'resolution.php';
		$isSecretariatGroup = in_array($currentScript, ['secretariat.php', 'contact.php', 'gallery.php'], true);
		?>

		<header class="header" >

			<!-- Topbar -->

			<div class="topbar">

				<div class="container">

					<div class="topbar-grid">

						<nav class="topbar-links" aria-label="Liens rapides">
							<ul class="top-link">
								<li><a href="<?= URL_SECRETAIRIATNATIONAL ?>">A propos</a></li>
								<li><a href="<?= URL_CONTACT ?>">Contact</a></li>
							</ul>
						</nav>

						<ul class="top-social" aria-label="Réseaux sociaux">
							<?php foreach ($headerSocialLinks as $socialLink): ?>
								<li>
									<a href="<?= meta_escape($socialLink['url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= meta_escape($socialLink['label']) ?>">
										<?= $socialLink['icon'] ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>

						<ul class="top-contact">

							<li><i class="fa fa-phone"></i>+243 *** *** ***</li>

							<li><i class="fa fa-envelope"></i><a href="mailto:lapiardidier561@gmail.com">Secretariat1325</a></li>

						</ul>

					</div>

				</div>

			</div>

			<!-- End Topbar -->

			<!-- Header Inner -->

			<div class="header-inner">

				<div class="container">

					<div class="inner">

						<div class="row">

							<div class="col-lg-3 col-md-3 col-12">

								<!-- Start Logo -->

								<div class="logo">

									<a href="https://info1325.cd/">
										<img src="<?= IMG_DIR ?>logoMingenre02.png" alt="logo" class="logo-img">
									</a>

								</div>

								<!-- End Logo --> 

								<!-- Mobile Nav -->

								<div class="mobile-nav"></div>

								<!-- End Mobile Nav -->

							</div>

							<div class="col-lg-6 col-md-9 col-12">

								<!-- Main Menu -->

								<div class="main-menu">

									<nav class="navigation">

										<ul class="nav menu">

												<li class="<?= $isHomePage ? 'active' : '' ?>"><a href="https://info1325.cd/">Accueil</a>

											</li>

												<li class="<?= $isActualitesPage ? 'active' : '' ?>"><a href="<?= URL_ACTUALITES ?>">ACTUALITÉS</a></li>

												<li class="<?= $isDocumentationPage ? 'active' : '' ?>"><a href="<?= URL_DOCUMENTATION ?>">DOCUMENTATION</a></li>

												<li class="<?= $isResolutionPage ? 'active' : '' ?>"><a href="<?= URL_RESOLUTION1325 ?>">RÉSOLUTION<i class="icofont-rounded-down"></i></a></li>

												<li class="<?= $isSecretariatGroup ? 'active' : '' ?>"><a href="<?= URL_SECRETAIRIATNATIONAL ?>">SECRÉTARIAT<i class="icofont-rounded-down"></i></a>

												<ul class="dropdown">

														<li class="<?= $currentScript === 'contact.php' ? 'active' : '' ?>"><a href="<?= URL_CONTACT ?>">Contact</a></li>
														<li class="<?= $currentScript === 'gallery.php' ? 'active' : '' ?>"><a href="<?= URL_GALERIE ?>">Galerie</a></li>

												</ul>

											</li>

										</ul>

									</nav>

								</div>

								<!--/ End Main Menu -->

							</div>

							<div class="col-lg-3 col-12">

								<div class="get-quote">

									<a href="https://sn1325.org/" target="_blank" rel="noopener noreferrer" class="btn">Base de données SN1325</a>

								</div>

							</div>

						</div>

					</div>

				</div>

			</div>

			<!--/ End Header Inner -->

		</header>

		<!-- End Header Area -->

<?php
// Affiche une zone "breadcrumbs overlay" par défaut pour toutes les pages
// - Utilise $PAGE_TITLE si défini par la page
// - Ne l'affiche pas pour certaines pages qui gèrent déjà leur propre breadcrumbs
$skipBreadcrumbFor = [
	'/pagesweb/actualites.php',
	'/pagesweb/axes.php',
	'/pagesweb/contact.php',
	'/pagesweb/resolution.php',
	'/index.php',
	'/' // root URL (Accueil)
];
$script = $_SERVER['SCRIPT_NAME'] ?? '';
// default: show breadcrumb unless a skip match is found
$showBreadcrumb = true;
foreach ($skipBreadcrumbFor as $skip) {
	if (stripos($script, $skip) !== false) {
		// page gère déjà son breadcrumb
		$showBreadcrumb = false;
		break;
	}
}

// Decide and render either an overlay breadcrumb or a simple centered page title
// titre priorité: $PAGE_TITLE sinon heuristique depuis l'URL
if (!isset($PAGE_TITLE) || trim($PAGE_TITLE) === '') {
	$path = $_SERVER['REQUEST_URI'] ?? $_SERVER['SCRIPT_NAME'];
	$name = basename(parse_url($path, PHP_URL_PATH));
	$name = preg_replace('/\.(php|html)$/i', '', $name);
	$name = str_replace(['index','pagesweb',''], '', $name);
	if ($name === '' || $name === '/') $name = 'Accueil';
	// pretty name
	$PAGE_TITLE = ucwords(str_replace(['-','_'], ' ', $name));
}

if (!empty($showBreadcrumb)) {
	// existing overlay breadcrumb
	?>
	<div class="breadcrumbs overlay">
		<div class="container">
			<div class="bread-inner">
				<div class="row">
					<div class="col-12">
						<h2><?= htmlspecialchars($PAGE_TITLE, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h2>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
} else {
	// render a simple, centered page title for inner pages (excluding index)
	$script = $_SERVER['SCRIPT_NAME'] ?? '';
	if (!preg_match('#(?:/index\.php$|/\s*$)#i', $script)) {
		// Allow pages to suppress the page header by setting $SKIP_PAGE_TITLE = true
		if (empty($SKIP_PAGE_TITLE)) {
			?>
			<div class="container page-header">
				<h2><?= htmlspecialchars($PAGE_TITLE, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h2>
			</div>
			<?php
		}
	}
}
?>
