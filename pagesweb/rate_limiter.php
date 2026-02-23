<?php
/**
 * Rate Limiting System — persistant en base de données
 * Protège contre les attaques brute-force.
 */

/**
 * Crée la table de rate limiting si elle n'existe pas.
 */
function ensure_rate_limit_table(PDO $pdo): void {
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS rate_limits (
            id            INT AUTO_INCREMENT PRIMARY KEY,
            action        VARCHAR(64)  NOT NULL,
            identifier    VARCHAR(64)  NOT NULL,
            attempts      INT          NOT NULL DEFAULT 0,
            blocked_until DATETIME     DEFAULT NULL,
            last_attempt  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_action_id (action, identifier),
            INDEX idx_blocked_until (blocked_until)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } catch (PDOException $e) {
        error_log('rate_limiter: cannot create table: ' . $e->getMessage());
    }
}

/**
 * Retourne le PDO depuis le scope global (injecté par connectDb.php).
 */
function _rl_get_pdo(): ?PDO {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        return null;
    }
    ensure_rate_limit_table($pdo);
    return $pdo;
}

/**
 * Vérifie si l'action est autorisée pour cet identifiant.
 *
 * @return array ['allowed' => bool, 'remaining' => int, 'reset_time' => int, 'blocked' => bool]
 */
function rate_limit_check(string $action, string $identifier, int $max_attempts = 5, int $window_seconds = 900): array {
    $db  = _rl_get_pdo();
    $now = time();

    if ($db === null) {
        return _rl_session_check($action, $identifier, $max_attempts, $window_seconds);
    }

    try {
        $stmt = $db->prepare('SELECT attempts, blocked_until, last_attempt FROM rate_limits WHERE action = :a AND identifier = :i LIMIT 1');
        $stmt->execute([':a' => $action, ':i' => md5($identifier)]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Vérifier blocage actif
            if ($row['blocked_until'] !== null && strtotime($row['blocked_until']) > $now) {
                return ['allowed' => false, 'remaining' => 0, 'reset_time' => strtotime($row['blocked_until']), 'blocked' => true];
            }
            // Réinitialiser si la fenêtre de temps est dépassée
            if (($now - strtotime($row['last_attempt'])) >= $window_seconds) {
                $db->prepare('DELETE FROM rate_limits WHERE action = :a AND identifier = :i')
                   ->execute([':a' => $action, ':i' => md5($identifier)]);
                $row = null;
            }
        }

        $attempts  = $row ? (int)$row['attempts'] : 0;
        $remaining = max(0, $max_attempts - $attempts);

        return ['allowed' => $attempts < $max_attempts, 'remaining' => $remaining, 'reset_time' => $now + $window_seconds, 'blocked' => false];
    } catch (PDOException $e) {
        error_log('rate_limit_check error: ' . $e->getMessage());
        return ['allowed' => true, 'remaining' => $max_attempts, 'reset_time' => $now + $window_seconds, 'blocked' => false];
    }
}

/**
 * Enregistre une tentative échouée.
 */
function rate_limit_record(string $action, string $identifier, int $max_attempts = 5, int $window_seconds = 900): void {
    $db  = _rl_get_pdo();
    $now = time();

    if ($db === null) {
        _rl_session_record($action, $identifier);
        return;
    }

    try {
        $stmt = $db->prepare('SELECT id, attempts FROM rate_limits WHERE action = :a AND identifier = :i LIMIT 1');
        $stmt->execute([':a' => $action, ':i' => md5($identifier)]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $newAttempts  = (int)$row['attempts'] + 1;
            $blockedUntil = $newAttempts >= $max_attempts ? date('Y-m-d H:i:s', $now + $window_seconds) : null;
            $db->prepare('UPDATE rate_limits SET attempts = :att, blocked_until = :bu WHERE id = :id')
               ->execute([':att' => $newAttempts, ':bu' => $blockedUntil, ':id' => $row['id']]);
        } else {
            $db->prepare('INSERT INTO rate_limits (action, identifier, attempts) VALUES (:a, :i, 1)')
               ->execute([':a' => $action, ':i' => md5($identifier)]);
        }
    } catch (PDOException $e) {
        error_log('rate_limit_record error: ' . $e->getMessage());
    }
}

/**
 * Réinitialise le compteur après une action réussie.
 */
function rate_limit_reset(string $action, string $identifier): void {
    $db = _rl_get_pdo();

    if ($db === null) {
        _rl_session_reset($action, $identifier);
        return;
    }

    try {
        $db->prepare('DELETE FROM rate_limits WHERE action = :a AND identifier = :i')
           ->execute([':a' => $action, ':i' => md5($identifier)]);
    } catch (PDOException $e) {
        error_log('rate_limit_reset error: ' . $e->getMessage());
    }
}

// ---- Fallback session (si BDD non disponible) ----

function _rl_session_check(string $action, string $identifier, int $max, int $window): array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $key  = 'rate_limit_' . $action . '_' . md5($identifier);
    $now  = time();
    $data = $_SESSION[$key] ?? ['attempts' => [], 'blocked_until' => 0];

    if ($data['blocked_until'] > $now) {
        return ['allowed' => false, 'remaining' => 0, 'reset_time' => $data['blocked_until'], 'blocked' => true];
    }
    $data['attempts'] = array_filter($data['attempts'], fn($t) => ($now - $t) < $window);
    $count = count($data['attempts']);
    $_SESSION[$key] = $data;
    return ['allowed' => $count < $max, 'remaining' => max(0, $max - $count), 'reset_time' => $now + $window, 'blocked' => false];
}

function _rl_session_record(string $action, string $identifier): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $key = 'rate_limit_' . $action . '_' . md5($identifier);
    $data = $_SESSION[$key] ?? ['attempts' => [], 'blocked_until' => 0];
    $data['attempts'][] = time();
    $_SESSION[$key] = $data;
}

function _rl_session_reset(string $action, string $identifier): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    unset($_SESSION['rate_limit_' . $action . '_' . md5($identifier)]);
}

/**
 * Retourne l'adresse IP du client.
 */
function get_client_ip(): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    return $ip;
}

/**
 * Formate le temps restant pour affichage utilisateur.
 */
function format_time_remaining(int $seconds): string {
    if ($seconds < 60) {
        return $seconds . ' seconde' . ($seconds > 1 ? 's' : '');
    } elseif ($seconds < 3600) {
        $minutes = (int)ceil($seconds / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    }
    $hours = (int)ceil($seconds / 3600);
    return $hours . ' heure' . ($hours > 1 ? 's' : '');
}
