<?php
/**
 * Render an image slider from a list of image basenames.
 *
 * @param array $images Array of image file names (basename.png/jpg)
 * @param string|null $excludeBasename Basename to exclude (e.g. hero image)
 * @param string $imgDir URL prefix for images (defaults to IMG_DIR)
 */
function render_image_slider(array $images, $excludeBasename = null, $imgDir = IMG_DIR, $imgFsDir = null){
    $images = array_values(array_unique($images));
    // determine filesystem directory for hash comparisons
    if ($imgFsDir === null) $imgFsDir = __DIR__ . '/../img/';
    // prepare exclude hash if possible
    $excludeHash = null;
    if ($excludeBasename){
        $exFs = rtrim($imgFsDir, '/\\') . DIRECTORY_SEPARATOR . $excludeBasename;
        if (file_exists($exFs)) $excludeHash = @md5_file($exFs);
    }
    foreach ($images as $img){
        if (!$img) continue;
        // skip exact basename match
        if ($excludeBasename && $img === $excludeBasename) continue;
        // skip by content hash when available
        $candidateFs = rtrim($imgFsDir, '/\\') . DIRECTORY_SEPARATOR . $img;
        if ($excludeHash && file_exists($candidateFs) && @md5_file($candidateFs) === $excludeHash) continue;
        $src = $imgDir . $img;
        echo "<img src=\"" . htmlspecialchars($src) . "\" alt=\"\">\n";
    }
}

// Helper: render images from a directory matching a pattern
function render_image_slider_from_dir($dirFsPath, $pattern = '/.*/', $excludeBasename = null, $imgUrlPrefix = IMG_DIR, $excludeFsPath = null){
    if (!is_dir($dirFsPath)) return;
    $files = scandir($dirFsPath);
    $imgs = [];
    foreach ($files as $f){
        if (in_array($f, ['.','..'])) continue;
        if (!preg_match($pattern, $f)) continue;
        $imgs[] = $f;
    }
    sort($imgs);
    // if an explicit exclude filesystem path is provided, compute its hash
    $excludeHash = null;
    if ($excludeFsPath && file_exists($excludeFsPath)) $excludeHash = @md5_file($excludeFsPath);
    // build URL prefix ensuring trailing slash
    $urlPrefix = rtrim($imgUrlPrefix, '/\\') . '/';
    // render images, skipping by basename and by content-hash if provided
    foreach ($imgs as $img){
        if ($excludeBasename && $img === $excludeBasename) continue;
        $candidateFs = rtrim($dirFsPath, '/\\') . DIRECTORY_SEPARATOR . $img;
        if ($excludeHash && file_exists($candidateFs) && @md5_file($candidateFs) === $excludeHash) continue;
        echo "<img src=\"" . htmlspecialchars($urlPrefix . $img) . "\" alt=\"\">\n";
    }
}

?>
