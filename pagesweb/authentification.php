<?php
// Start session and output buffering to avoid "headers already sent" issues
session_start();
ob_start();
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // provides $pdo
require_once __DIR__ . '/rate_limiter.php'; // Rate limiting protection

// Ensure users table exists and create a default admin if none exists
function ensureUsersTable(
    PDO $pdo
) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) NOT NULL DEFAULT 'admin',
        active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Add 'active' column if it doesn't exist (for existing tables)
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN active TINYINT(1) NOT NULL DEFAULT 1 AFTER role");
    } catch (PDOException $e) {
        // Column already exists, ignore
    }

    // If no users, create default admin
    $stmt = $pdo->query("SELECT COUNT(*) as c FROM users");
    $count = (int)$stmt->fetchColumn();
    if ($count === 0) {
        $defaultEmail = 'admin@sn1325.cd';
        $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $ins = $pdo->prepare('INSERT INTO users (email, password, role, active) VALUES (:e,:p,:r,:a)');
        $ins->execute([':e'=>$defaultEmail,':p'=>$defaultPassword,':r'=>'admin',':a'=>1]);
        // Note: first-time password is "admin123" — change it after first login.
    }
}

ensureUsersTable($pdo);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Get client IP for rate limiting
    $client_ip = get_client_ip();

    // Check rate limit (5 attempts per 15 minutes)
    $rate_check = rate_limit_check('login', $client_ip, 5, 900);

    if (!$rate_check['allowed']) {
        $time_remaining = format_time_remaining($rate_check['reset_time'] - time());
        $error = "Trop de tentatives de connexion échouées. Veuillez réessayer dans $time_remaining.";
    } elseif ($email === '' || $password === '') {
        $error = 'Veuillez renseigner tous les champs.';
        rate_limit_record('login', $client_ip);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email'=>$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
          // Check if account is active
          if (!($user['active'] ?? 1)) {
            $error = 'Votre compte a été désactivé. Veuillez contacter un administrateur.';
            rate_limit_record('login', $client_ip);
          } else {
            // Successful login - reset rate limit
            rate_limit_reset('login', $client_ip);

            // Set session variables
            session_regenerate_id(true);
            $_SESSION['user'] = $user['email'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['active'] = $user['active'];

            // Redirect admins directly to the admin dashboard
            if (isset($user['role']) && $user['role'] === 'admin') {
              header('Location: ' . URL_ADMINISTRATEUR);
            } else {
              header('Location: ' . URL_ADDSPACEADMIN);
            }
            exit;
          }
        } else {
            $error = 'Email ou mot de passe incorrect.';
            rate_limit_record('login', $client_ip);
        }
    }
}
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Connexion - SN1325</title>
    <link rel="stylesheet" href="<?= CSS_DIR ?>bootstrap.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            position: relative;
        }

        .animated-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(-45deg, #1e3a8a, #3b82f6, #8b5cf6, #ec4899);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .animated-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            animation: pulse 8s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 0.5;
            }
            50% {
                opacity: 1;
            }
        }

        .card {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border: none !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
        }

        .container {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body class="d-flex align-items-center" style="height:100vh;">
<div class="animated-background"></div>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h4 class="mb-3 text-center">Administration SN1325</h4>
          <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>
          <form method="post" novalidate>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input name="email" type="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Mot de passe</label>
              <input name="password" type="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100">Se connecter</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="<?= JS_DIR ?>bootstrap.min.js"></script>
</body>
</html>