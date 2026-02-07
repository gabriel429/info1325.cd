<?php
require_once __DIR__ . '/../configUrl.php';
$dataFile = __DIR__ . '/../data/galerie.json';
$items = [];
if (file_exists($dataFile)) $items = json_decode(file_get_contents($dataFile), true) ?: [];
$activity = isset($_GET['activity']) ? trim($_GET['activity']) : 'all';
function h($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
$filtered = [];
if ($activity === '' || $activity === 'all') $filtered = $items;
else {
        foreach($items as $it) if (isset($it['activity']) && strcasecmp($it['activity'],$activity)===0) $filtered[] = $it;
}

$activities = [];
foreach($items as $it) if (!empty($it['activity'])) $activities[$it['activity']] = true;
?>

<!doctype html>
<html><head><meta charset="utf-8"><title>Galerie - <?= h($activity) ?></title>
<link rel="stylesheet" href="<?= CSS_DIR ?>bootstrap.min.css">
<link rel="stylesheet" href="<?= CSS_DIR ?>magnific-popup.css">
<link rel="stylesheet" href="<?= CSS_DIR ?>style.css">
<style>
/* CareMed-inspired hero + cards */
.page-hero{background:linear-gradient(90deg,rgba(8,50,97,0.9),rgba(3,169,244,0.85));color:#fff;padding:60px 0;margin-bottom:20px}
.page-hero h1{font-weight:700}
.service-filter .btn{margin:4px}
.caremed-card{background:#fff;border-radius:8px;overflow:hidden;transition:transform .18s,box-shadow .18s}
.caremed-card:hover{transform:translateY(-6px);box-shadow:0 10px 30px rgba(0,0,0,0.12)}
.card-bg{height:200px;background-size:cover;background-position:center}
.card-body p{margin:0}
.card-overlay{position:absolute;left:0;right:0;bottom:0;padding:12px;background:linear-gradient(180deg,transparent,rgba(0,0,0,0.45));color:#fff}
.mfp-img{max-width:100%}
@media(min-width:992px){.card-bg{height:180px}}
</style>
</head><body>
<header class="page-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1>Galerie</h1>
                <p class="lead">Découvrez les dernières photos par activité. Cliquez pour agrandir.</p>
            </div>
            <div class="col-md-4 text-md-right">
                <p class="mb-0"><a href="<?= BASE_URL ?>" style="color:rgba(255,255,255,0.9)">Accueil</a> &rsaquo; <strong><?= ($activity=='all'?'Toutes activités':h($activity)) ?></strong></p>
            </div>
        </div>
    </div>
</header>

<main class="container">
    <div class="row mb-3">
        <div class="col-12">
            <div class="service-filter">
                <a href="?activity=all" class="btn btn-outline-primary <?= ($activity==='all'?'active':'') ?>">Toutes activités</a>
                <?php foreach(array_keys($activities) as $act): ?>
                    <a href="?activity=<?= urlencode($act) ?>" class="btn btn-outline-secondary <?= (strcasecmp($act,$activity)===0?'active':'') ?>"><?= h($act) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php if(empty($filtered)): ?><div class="alert alert-info">Aucune image trouvée pour cette activité.</div><?php endif; ?>

    <div class="row">
        <?php foreach($filtered as $it): 
                $file = $it['file'] ?? ''; if(!$file) continue; 
                $act = $it['activity'] ?? '';
                $url = IMG_DIR . 'galerie/' . $file;
                $ts = isset($it['uploaded']) ? (int)$it['uploaded'] : 0;
                $date = $ts?date('d/m/Y', $ts):'';
        ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="caremed-card position-relative">
                    <a href="<?= $url ?>" class="image-link d-block" title="<?= h($act) ?>">
                        <div class="card-bg" style="background-image:url('<?= $url ?>')"></div>
                        <div class="card-overlay">
                            <h5 class="mb-1" style="color:#fff"><?= h($act) ?></h5>
                            <small><?= h($date) ?></small>
                        </div>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<script src="<?= JS_DIR ?>jquery.min.js"></script>
<script src="<?= JS_DIR ?>jquery.magnific-popup.min.js"></script>
<script>
$(function(){
    $('.image-link').magnificPopup({type:'image',gallery:{enabled:true}});
});
</script>
</body>

</html>
