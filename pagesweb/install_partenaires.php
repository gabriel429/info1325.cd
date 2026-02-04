<?php
// Script d'installation : crée la table `partenaires` et insère des exemples.
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/connectDb.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS partenaires (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        url VARCHAR(255) DEFAULT '#',
        image VARCHAR(255) DEFAULT NULL,
        active TINYINT(1) DEFAULT 1,
        position INT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql);

    // Insérer exemples si table vide
    $stmt = $pdo->query("SELECT COUNT(*) AS c FROM partenaires");
    $count = (int)($stmt->fetch()['c'] ?? 0);

    if ($count === 0) {
        $samples = [
            ['name' => 'Partenaire A', 'url' => '#', 'image' => 'partenaire1325.png'],
            ['name' => 'Partenaire B', 'url' => '#', 'image' => 'partenaire13252.png']
        ];

        $insert = $pdo->prepare("INSERT INTO partenaires (name, url, image, active) VALUES (:name, :url, :image, 1)");
        $copied = 0;
        foreach ($samples as $s) {
            $insert->execute([':name' => $s['name'], ':url' => $s['url'], ':image' => $s['image']]);

            // Copier le fichier s'il existe dans img/ vers img/partenaires/
            $src = __DIR__ . '/../img/' . $s['image'];
            $dstDir = __DIR__ . '/../img/partenaires/';
            $dst = $dstDir . $s['image'];
            if (file_exists($src) && (!file_exists($dst))) {
                if (!is_dir($dstDir)) mkdir($dstDir, 0777, true);
                if (copy($src, $dst)) $copied++;
            }
        }

        echo "OK — table 'partenaires' créée et exemples insérés. ($copied logos copiés si trouvés)";
    } else {
        echo "La table 'partenaires' existe déjà et contient $count enregistrement(s). Aucune insertion automatique effectuée.";
    }

    echo "\n\nAccéder au gestionnaire: " . BASE_URL . 'pagesweb/manage_partenaires.php';
} catch (Exception $e) {
    http_response_code(500);
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
