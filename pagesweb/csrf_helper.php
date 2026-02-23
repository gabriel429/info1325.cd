<?php
/**
 * Protection CSRF — génération et validation de tokens
 */

/**
 * Génère (ou retourne) le token CSRF de la session courante.
 */
function csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Retourne le champ HTML caché à insérer dans chaque formulaire POST.
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
}

/**
 * Valide le token CSRF soumis avec le formulaire.
 * En cas d'échec, arrête l'exécution avec une erreur 403.
 */
function csrf_verify(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $submitted = $_POST['csrf_token'] ?? '';
    $expected  = $_SESSION['csrf_token'] ?? '';

    if ($submitted === '' || $expected === '' || !hash_equals($expected, $submitted)) {
        http_response_code(403);
        exit('Requête invalide (token CSRF incorrect). Veuillez recharger la page et réessayer.');
    }
}
