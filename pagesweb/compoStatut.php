<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="<?= CSS_DIR ?>bootstrap.min.css">
		<!-- Nice Select CSS -->
		<link rel="stylesheet" href="<?= CSS_DIR ?>nice-select.css">
		<!-- Font Awesome CSS -->
        <link rel="stylesheet" href="<?= CSS_DIR ?>font-awesome.min.css">
		<!-- icofont CSS -->
        <link rel="stylesheet" href="<?= CSS_DIR ?>icofont.css">
		<!-- Slicknav -->
		<link rel="stylesheet" href="<?= CSS_DIR ?>slicknav.min.css">
		<!-- Owl Carousel CSS -->
        <link rel="stylesheet" href="<?= CSS_DIR ?>owl-carousel.css">
		<!-- Datepicker CSS -->
		<link rel="stylesheet" href="<?= CSS_DIR ?>datepicker.css">
		<!-- Animate CSS -->
        <link rel="stylesheet" href="<?= CSS_DIR ?>animate.min.css">
		<!-- Magnific Popup CSS -->
        <link rel="stylesheet" href="<?= CSS_DIR ?>magnific-popup.css">
		
		<!-- Medipro CSS -->
        <link rel="stylesheet" href="<?= CSS_DIR ?>normalize.css">
        <link rel="stylesheet" href="<?= CSS_DIR ?>style.css">
        <link rel="stylesheet" href="<?= CSS_DIR ?>responsive.css">
<!-- Start Fun-facts -->
<?php
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // provides $pdo

$rows = [];
// Try to load 4 fun facts from DB, fallback to defaults if table missing or error
try {
    $stmt = $pdo->query('SELECT * FROM fun_facts ORDER BY `position` ASC LIMIT 4');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Table may not exist on this environment yet; we'll use defaults below
    $rows = [];
}
$defaults = [
    1 => ['value'=>'34','text'=>"% des femmes font parties du Gouvernement National"],
    2 => ['value'=>'8.5','text'=>"% des femmes occupent des postes de responsabilité au sein de la police"],
    3 => ['value'=>'13.6','text'=>"% des sieges sont occupés par des femmes au parlement(Assemblée Nationale)"],
    4 => ['value'=>'3','text'=>"% des Plans d'Action Provinciaux ont été élaborés et adoptés"],
];
$facts = [];
foreach ($rows as $r) {
    $facts[(int)$r['position']] = $r;
}
for ($i=1;$i<=4;$i++) {
    if (!isset($facts[$i])) $facts[$i] = ['value'=>$defaults[$i]['value'],'text'=>$defaults[$i]['text']];
}
?>
 <h2> Selon le rapport pays SN1325 2025</h2>
        <div id="fun-facts" class="fun-facts section overlay">
            <div class="container">
                <div class="row">
                    <?php for ($i=1;$i<=4;$i++): ?>
                    <div class="col-lg-3 col-md-6 col-12">
                        <!-- Start Single Fun -->
                        <div class="single-fun">
                                <div class="content">
                                <span class="counter"><?= htmlspecialchars($facts[$i]['value']) ?></span>
                                <p><?= htmlspecialchars($facts[$i]['text']) ?></p>
                            </div>
                        </div>
                        <!-- End Single Fun -->
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        <!--/ End Fun-facts -->