<?php
/**
 * Utilitaire d'upload de fichiers sécurisé.
 * Utilise finfo pour valider le type MIME réel (magic bytes).
 */

/**
 * Upload un fichier validé vers le dossier cible.
 *
 * @param string $fileKey       Clé dans $_FILES
 * @param string $targetDir     Chemin absolu du dossier de destination
 * @param array  $allowedMimes  Types MIME autorisés (ex: ['image/jpeg', 'application/pdf'])
 * @return string|null          Nom du fichier enregistré, ou null si aucun fichier fourni
 * @throws Exception            En cas d'erreur d'upload ou de type non autorisé
 */
function uploadFile(string $fileKey, string $targetDir, array $allowedMimes): ?string {
    if (!isset($_FILES[$fileKey]) || empty($_FILES[$fileKey]['name'])) {
        return null;
    }

    $file = $_FILES[$fileKey];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $messages = [
            UPLOAD_ERR_INI_SIZE   => "Fichier trop grand (limite serveur).",
            UPLOAD_ERR_FORM_SIZE  => "Fichier trop grand (limite formulaire).",
            UPLOAD_ERR_PARTIAL    => "Téléchargement partiel.",
            UPLOAD_ERR_NO_FILE    => "Aucun fichier fourni.",
            UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant.",
            UPLOAD_ERR_CANT_WRITE => "Erreur d'écriture disque.",
            UPLOAD_ERR_EXTENSION  => "Upload bloqué par une extension PHP.",
        ];
        throw new Exception($messages[$file['error']] ?? "Erreur upload inconnue ({$file['error']}).");
    }

    // Validation du type MIME réel via magic bytes (finfo)
    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $realMime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($realMime, $allowedMimes, true)) {
        throw new Exception("Format de fichier non autorisé pour '$fileKey' (détecté: $realMime).");
    }

    // Nom de fichier sécurisé
    $fileName   = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($file['name']));
    $targetFile = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $fileName;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
        throw new Exception("Impossible de déplacer le fichier '$fileKey'.");
    }

    return $fileName;
}
