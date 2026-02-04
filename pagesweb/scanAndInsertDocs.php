<?php
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // Connexion à la base de données

// Dossier contenant les PDF
$directory = __DIR__ . '/../img/documentations/';

// Vérification de l’existence du dossier
if (!is_dir($directory)) {
    die("❌ Dossier non trouvé : $directory");
}

// Scanner le dossier
$files = scandir($directory);

// Initialiser le compteur
$inserted = 0;
$skipped = 0;

foreach ($files as $file) {
    // Ignorer les fichiers système
    if ($file === '.' || $file === '..') continue;

    // On ne prend que les fichiers PDF
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if ($extension !== 'pdf') {
        $skipped++;
        continue;
    }

    // Déterminer le titre à partir du nom du fichier
    // Exemple : "rapport_annuel_2025.pdf" => "Rapport Annuel 2025"
    $titreDoc = pathinfo($file, PATHINFO_FILENAME);
    $titreDoc = str_replace(['_', '-', '.'], ' ', $titreDoc);
    $titreDoc = ucwords(trim($titreDoc));

    // Données par défaut
    $img = 'default-doc.jpg'; // miniature par défaut
    $anneePub = date('Y');    // année actuelle
    $datePub = date('Y-m-d'); // date du jour
    $auteur = 'Inconnu';

    try {
        // Vérifier si le fichier est déjà dans la base
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM documentations WHERE fichier_pdf = ?");
        $checkStmt->execute([$file]);
        if ($checkStmt->fetchColumn() > 0) {
            $skipped++;
            continue;
        }

        // Insertion
        $stmt = $pdo->prepare("
            INSERT INTO documentations (img, fichier_pdf, titreDoc, anneePub, datePub, auteur)
            VALUES (:img, :fichier_pdf, :titreDoc, :anneePub, :datePub, :auteur)
        ");

        $stmt->execute([
            ':img' => $img,
            ':fichier_pdf' => $file,
            ':titreDoc' => $titreDoc,
            ':anneePub' => $anneePub,
            ':datePub' => $datePub,
            ':auteur' => $auteur
        ]);

        $inserted++;
    } catch (PDOException $e) {
        echo "⚠️ Erreur sur le fichier $file : " . $e->getMessage() . "<br>";
    }
}

// Résumé
echo "<h3>✅ Scan terminé !</h3>";
echo "<p>Fichiers insérés : <strong>$inserted</strong></p>";
echo "<p>Fichiers ignorés (non PDF ou déjà présents) : <strong>$skipped</strong></p>";
?>
