<?php
// Script d'aide pour créer dossiers et copier images manquantes localement
// Usage (navigateur): http://localhost/info1325.cd/pagesweb/fix_assets.php

header('Content-Type: text/plain; charset=utf-8');

$root = realpath(__DIR__ . '/../');
$imgDir = $root . DIRECTORY_SEPARATOR . 'img';

function ensureDir($d) {
    if (!is_dir($d)) {
        if (!mkdir($d, 0777, true)) {
            echo "Échec création dossier: $d\n";
            return false;
        }
        echo "Créé: $d\n";
    } else {
        echo "Existe: $d\n";
    }
    return true;
}

function safeCopy($src, $dst) {
    if (!file_exists($src)) {
        echo "Source manquante: $src -> $dst\n";
        return false;
    }
    if (file_exists($dst)) {
        echo "Cible existe déjà: $dst\n";
        return true;
    }
    if (copy($src, $dst)) {
        echo "Copié: " . basename($src) . " -> " . basename($dst) . "\n";
        return true;
    }
    echo "Échec copie: $src -> $dst\n";
    return false;
}

// Crée dossiers attendus
ensureDir($imgDir . DIRECTORY_SEPARATOR . 'slider');
ensureDir($imgDir . DIRECTORY_SEPARATOR . 'axes');

// Cartographie source -> cible pour slider
$map = [
    $imgDir . DIRECTORY_SEPARATOR . 'femme.jpg' => $imgDir . DIRECTORY_SEPARATOR . 'slider' . DIRECTORY_SEPARATOR . '1770250041_1_femme.jpg',
    $imgDir . DIRECTORY_SEPARATOR . 'slider.png' => $imgDir . DIRECTORY_SEPARATOR . 'slider' . DIRECTORY_SEPARATOR . '1770250041_2_slider.png',
    $imgDir . DIRECTORY_SEPARATOR . 'ministre.jpeg' => $imgDir . DIRECTORY_SEPARATOR . 'slider' . DIRECTORY_SEPARATOR . '1770250041_3_ministre.jpeg',
    $imgDir . DIRECTORY_SEPARATOR . 'slider2.jpg' => $imgDir . DIRECTORY_SEPARATOR . 'slider' . DIRECTORY_SEPARATOR . '1770250041_4_1767878512_WhatsApp_Image_2025-12-24_at_22.23.51.jpeg',
    $imgDir . DIRECTORY_SEPARATOR . 'PAN.png' => $imgDir . DIRECTORY_SEPARATOR . 'slider' . DIRECTORY_SEPARATOR . '1770250041_5_1761779164_02PAN.jpeg',
    $imgDir . DIRECTORY_SEPARATOR . 'slider3.jpg' => $imgDir . DIRECTORY_SEPARATOR . 'slider' . DIRECTORY_SEPARATOR . '1770250041_6_vs1.jpg',
];

// Cartographie pour axes
$map += [
    $imgDir . DIRECTORY_SEPARATOR . 'pf1.jpg' => $imgDir . DIRECTORY_SEPARATOR . 'axes' . DIRECTORY_SEPARATOR . '1770249605_1_participation.jpg',
    $imgDir . DIRECTORY_SEPARATOR . 'pf2.jpg' => $imgDir . DIRECTORY_SEPARATOR . 'axes' . DIRECTORY_SEPARATOR . '1770249605_2_prevention.jpg',
    $imgDir . DIRECTORY_SEPARATOR . 'pf3.jpg' => $imgDir . DIRECTORY_SEPARATOR . 'axes' . DIRECTORY_SEPARATOR . '1770249605_3_protect.jpg',
    $imgDir . DIRECTORY_SEPARATOR . 'pf4.jpg' => $imgDir . DIRECTORY_SEPARATOR . 'axes' . DIRECTORY_SEPARATOR . '1770249605_4_relevement.jpg',
    $imgDir . DIRECTORY_SEPARATOR . 'pp1.jpg' => $imgDir . DIRECTORY_SEPARATOR . 'axes' . DIRECTORY_SEPARATOR . '1770249605_5_conflit.jpg',
    $imgDir . DIRECTORY_SEPARATOR . 'Logo-Min-genre-rdc-Png.jpg' => $imgDir . DIRECTORY_SEPARATOR . 'axes' . DIRECTORY_SEPARATOR . '1770249605_6_Logo-Min-genre-rdc-Png.jpg',
];

echo "== Fix assets : copie des fichiers disponibles vers noms attendus ==\n";
foreach ($map as $src => $dst) {
    if (!safeCopy($src, $dst)) {
        // Si la source est manquante, on écrit un placeholder PNG encodé en base64
        $placeholder = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMB/6W1Z1sAAAAASUVORK5CYII='; // 1x1 PNG
        $data = base64_decode($placeholder);
        if (@file_put_contents($dst, $data) !== false) {
            echo "Placeholder créé: " . basename($dst) . "\n";
        } else {
            echo "Impossible de créer placeholder pour: " . basename($dst) . "\n";
        }
    }
}

echo "\nVérifiez maintenant les URLs problématiques dans le navigateur et videz le cache si nécessaire.\n";

?>
