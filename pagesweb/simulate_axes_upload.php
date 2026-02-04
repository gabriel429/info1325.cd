<?php
// Script pour simuler des uploads d'images pour les axes
// Supporte l'exécution CLI et HTTP (navigateur)
require_once __DIR__ . '/connectDb.php'; // fournit $pdo

$srcDir = __DIR__ . '/../img/';
$targetDir = __DIR__ . '/../img/axes/';
if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

$candidates = ['pf1.jpg','pf2.jpg','pf3.jpg','pf4.jpg','slider.jpg','slider2.jpg','slider3.jpg'];

function run_simulation($pdo, $srcDir, $targetDir, $candidates) {
    $out = [];
    try {
        $pdo->beginTransaction();
        for ($i=1;$i<=6;$i++) {
            $src = $srcDir . ($candidates[($i-1) % count($candidates)]);
            if (!file_exists($src)) {
                $imgName = null;
                $out[] = "Source manquante pour l'axe #$i : $src";
            } else {
                $orig = basename($src);
                $imgName = time() . '_sim_' . $i . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $orig);
                $dest = $targetDir . $imgName;
                if (!copy($src, $dest)) {
                    $out[] = "Échec copie pour l'axe #$i ($src -> $dest)";
                    $imgName = null;
                } else {
                    $out[] = "Image copiée pour axe #$i -> img/axes/$imgName";
                }
            }

            $title = "SIMULATED AXE #$i";
            $desc = "Description simulée pour l'axe $i";

            $stmt = $pdo->prepare('SELECT id FROM axes WHERE `position` = :pos');
            $stmt->execute([':pos'=>$i]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                if ($imgName !== null) {
                    $u = $pdo->prepare('UPDATE axes SET title=:t, description=:d, image=:img WHERE `position`=:pos');
                    $u->execute([':t'=>$title,':d'=>$desc,':img'=>$imgName,':pos'=>$i]);
                } else {
                    $u = $pdo->prepare('UPDATE axes SET title=:t, description=:d WHERE `position`=:pos');
                    $u->execute([':t'=>$title,':d'=>$desc,':pos'=>$i]);
                }
                $out[] = "Axe #$i mis à jour en base.";
            } else {
                $ins = $pdo->prepare('INSERT INTO axes (`position`, title, description, image) VALUES (:pos,:t,:d,:img)');
                $ins->execute([':pos'=>$i,':t'=>$title,':d'=>$desc,':img'=>$imgName]);
                $out[] = "Axe #$i inséré en base.";
            }
        }
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $out[] = "Erreur pendant la simulation : " . $e->getMessage();
        return $out;
    }

    $out[] = "\nContenu actuel de la table axes :";
    $rows = $pdo->query('SELECT `position`, title, image FROM axes ORDER BY `position` ASC')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        $out[] = sprintf("pos=%d | title=%s | image=%s", $r['position'], $r['title'], $r['image'] ?? 'NULL');
    }
    $out[] = "\nSimulation terminée. Vérifiez le dossier img/axes/ et la table axes.";
    return $out;
}

if (PHP_SAPI === 'cli') {
    $lines = run_simulation($pdo, $srcDir, $targetDir, $candidates);
    foreach ($lines as $l) echo $l . PHP_EOL;
    exit(0);
}

// HTTP mode (navigateur)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run'])) {
    $lines = run_simulation($pdo, $srcDir, $targetDir, $candidates);
    $outputHtml = '<pre>' . htmlspecialchars(implode("\n", $lines)) . '</pre>';
} else {
    $outputHtml = '';
}
?><!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Simulateur d'upload - Axes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="container">
    <h3>Simulateur d'upload des images pour les axes</h3>
    <p>Cette page copie des images existantes depuis <code>img/</code> vers <code>img/axes/</code> et met à jour la table <code>axes</code>.</p>
    <form method="post">
      <button class="btn btn-primary" name="run" value="1">Lancer la simulation</button>
    </form>
    <hr>
    <?= $outputHtml ?>
  </div>
</body>
</html>
