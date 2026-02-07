<?php
// Batch thumbnail generator for img/galerie
// Usage (CLI): php scripts/generate_thumbs.php
// Usage (HTTP): open /scripts/generate_thumbs.php in browser

set_time_limit(0);
$root = dirname(__DIR__);
$imgDir = $root . '/img/galerie/';
$thumbDir = $imgDir . 'thumbs/';
if (!is_dir($imgDir)){
    echo "img/galerie not found\n";
    exit(1);
}
if (!is_dir($thumbDir)) mkdir($thumbDir, 0755, true);

$allowed = ['jpg','jpeg','png','gif','webp'];
$files = array_values(array_filter(scandir($imgDir), function($f) use($imgDir, $thumbDir, $allowed){
    if (in_array($f, ['.','..','thumbs'])) return false;
    $p = $imgDir . $f;
    if (!is_file($p)) return false;
    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
    return in_array($ext, $allowed);
}));

$generated = 0; $skipped = 0; $errors = [];
foreach($files as $f){
    $src = $imgDir . $f;
    $dst = $thumbDir . $f;
    if (file_exists($dst)){
        $skipped++;
        continue;
    }
    // create thumb 420x240
    $w = 420; $h = 240;
    $info = getimagesize($src);
    if (!$info){ $errors[] = "$f: not an image"; continue; }
    $mime = $info['mime'];
    switch($mime){
        case 'image/jpeg': $srcImg = imagecreatefromjpeg($src); break;
        case 'image/png': $srcImg = imagecreatefrompng($src); break;
        case 'image/gif': $srcImg = imagecreatefromgif($src); break;
        case 'image/webp': $srcImg = function_exists('imagecreatefromwebp') ? imagecreatefromwebp($src) : null; break;
        default: $srcImg = null; break;
    }
    if (!$srcImg){ $errors[] = "$f: unsupported format"; continue; }
    $srcW = imagesx($srcImg); $srcH = imagesy($srcImg);
    $srcRatio = $srcW / $srcH; $dstRatio = $w / $h;
    if ($srcRatio > $dstRatio){
        $newH = $srcH; $newW = (int)($srcH * $dstRatio); $srcX = (int)(($srcW - $newW)/2); $srcY = 0;
    } else {
        $newW = $srcW; $newH = (int)($srcW / $dstRatio); $srcX = 0; $srcY = (int)(($srcH - $newH)/2);
    }
    $dstImg = imagecreatetruecolor($w, $h);
    imagefill($dstImg, 0, 0, imagecolorallocate($dstImg, 255,255,255));
    if (in_array($mime, ['image/png','image/gif'])){
        imagecolortransparent($dstImg, imagecolorallocatealpha($dstImg, 0,0,0,127));
        imagealphablending($dstImg, false); imagesavealpha($dstImg, true);
    }
    imagecopyresampled($dstImg, $srcImg, 0,0, $srcX, $srcY, $w, $h, $newW, $newH);
    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
    $ok = false;
    if ($ext === 'png') $ok = imagepng($dstImg, $dst, 8);
    elseif ($ext === 'webp' && function_exists('imagewebp')) $ok = imagewebp($dstImg, $dst, 80);
    else $ok = imagejpeg($dstImg, $dst, 85);
    imagedestroy($srcImg); imagedestroy($dstImg);
    if ($ok) $generated++; else $errors[] = "$f: failed to save";
}

$out = "Generated: $generated, Skipped: $skipped";
if ($errors) $out .= "\nErrors:\n" . implode("\n", $errors);

if (php_sapi_name() === 'cli') echo $out . "\n";
else echo "<pre>" . htmlspecialchars($out) . "</pre>";

?>
