# Système de Sécurité - SN1325

Ce document explique les protections de sécurité mises en place pour protéger votre application.

## 🔒 Protections Implémentées

### 1. Protection contre les Injections SQL
✅ **STATUS: DÉJÀ EN PLACE**
- Toutes les requêtes SQL utilisent des **prepared statements PDO**
- Aucune injection SQL possible

### 2. Protection d'Authentification (auth_check.php)
✅ **STATUS: IMPLÉMENTÉ**
- Vérifie que l'utilisateur est connecté
- Vérifie que le compte est actif
- Gestion des rôles (admin, user, slider)

**Comment utiliser:**
```php
<?php
// Au début de chaque page admin
require_once __DIR__ . '/auth_check.php';

// Pour restreindre à un rôle spécifique:
require_role('admin'); // Seulement les admins

// Ou plusieurs rôles:
require_role(['admin', 'slider']); // Admins ou sliders

// Vérifier le rôle dans le code:
if (is_admin()) {
    // Action réservée aux admins
}
```

### 3. Protection CSRF (csrf.php)
✅ **STATUS: IMPLÉMENTÉ**
- Tokens CSRF pour tous les formulaires
- Protection contre les attaques Cross-Site Request Forgery

**Comment utiliser:**
```php
<?php
// Au début du fichier
require_once __DIR__ . '/csrf.php';

// Pour les formulaires POST, valider le token:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate(true); // Arrête le script si invalide
}
?>

<!-- Dans vos formulaires HTML -->
<form method="post">
    <?php csrf_field(); ?> <!-- Ajoute le champ hidden avec le token -->
    <!-- Vos champs ... -->
    <button type="submit">Envoyer</button>
</form>
```

**Pour AJAX:**
```html
<head>
    <?php csrf_meta_tag(); ?>
</head>

<script>
// Récupérer le token pour les requêtes AJAX
const token = document.querySelector('meta[name="csrf-token"]').content;

fetch('/api/endpoint', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': token
    },
    body: JSON.stringify(data)
});
</script>
```

### 4. Rate Limiting (rate_limiter.php)
✅ **STATUS: IMPLÉMENTÉ**
- Limite le nombre de tentatives de connexion
- Protection contre les attaques brute-force
- Blocage temporaire après 5 échecs (15 minutes)

**Comment utiliser:**
```php
<?php
require_once __DIR__ . '/rate_limiter.php';

// Vérifier la limite avant d'autoriser une action
$client_ip = get_client_ip();
$rate_check = rate_limit_check('login', $client_ip, 5, 900);

if (!$rate_check['allowed']) {
    $time_remaining = format_time_remaining($rate_check['reset_time'] - time());
    die("Trop de tentatives. Réessayez dans $time_remaining.");
}

// Enregistrer une tentative (réussie ou échouée)
rate_limit_record('login', $client_ip);

// Réinitialiser après succès
rate_limit_reset('login', $client_ip);
```

**Paramètres personnalisables:**
```php
// Syntaxe: rate_limit_check($action, $identifier, $max_attempts, $window_seconds)
rate_limit_check('password_reset', $email, 3, 3600); // 3 tentatives par heure
rate_limit_check('contact_form', $ip, 10, 600); // 10 soumissions par 10 min
```

## 📋 Pages Protégées

### Pages avec authentification:
- ✅ `administrateur.php` - Dashboard admin
- ✅ `manage_users.php` - Gestion utilisateurs (admin seulement)
- ✅ `manage_partenaires.php` - Gestion partenaires
- ✅ `manage_gallery.php` - Gestion galerie
- ✅ `manage_axes.php` - Gestion axes
- ✅ `manage_slider.php` - Gestion slider (admin/slider)
- ✅ `manage_funfacts.php` - Gestion fun facts
- ✅ `add-actualites.php` - Ajout actualités
- ✅ `add-documentation.php` - Ajout documentation
- ✅ `change_password.php` - Changement mot de passe
- ✅ `install_partenaires.php` - Script d'installation (admin seulement)

### Pages avec CSRF:
- ✅ `manage_users.php` - Toutes les actions (create/edit/delete/reset)
- ⚠️ **À FAIRE**: Ajouter CSRF aux autres pages de gestion

### Pages avec Rate Limiting:
- ✅ `authentification.php` - 5 tentatives / 15 minutes

## 🛡️ Meilleures Pratiques

### Pour créer une nouvelle page admin:

```php
<?php
// 1. Authentification
require_once __DIR__ . '/auth_check.php';
require_role('admin'); // ou ['admin', 'user']

// 2. CSRF si formulaires POST
require_once __DIR__ . '/csrf.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate(true);
}

// 3. Votre logique...
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Votre code...
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ma Page Admin</title>
</head>
<body>
    <form method="post">
        <?php csrf_field(); ?>
        <!-- Vos champs -->
        <button type="submit">Envoyer</button>
    </form>
</body>
</html>
```

### Pour les requêtes SQL:
```php
// ✅ BON - Prepared statement
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);

// ❌ MAUVAIS - Injection SQL possible
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $pdo->query($sql);
```

### Pour l'affichage HTML:
```php
// ✅ BON - Échappement
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// ❌ MAUVAIS - XSS possible
echo $user_input;
```

## 🔧 Configuration Recommandée

### Dans php.ini ou .htaccess:

```ini
# Session sécurisée
session.cookie_httponly = On
session.cookie_secure = On
session.cookie_samesite = Strict
session.use_only_cookies = On

# Headers de sécurité
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set Strict-Transport-Security "max-age=31536000"
```

## 📊 Audit de Sécurité

### Checklist avant mise en production:

- [ ] Tous les formulaires ont la protection CSRF
- [ ] Toutes les pages admin ont `require_once __DIR__ . '/auth_check.php';`
- [ ] Les rôles sont correctement vérifiés avec `require_role()`
- [ ] Toutes les requêtes SQL utilisent des prepared statements
- [ ] Tous les affichages utilisent `htmlspecialchars()`
- [ ] Rate limiting activé sur login et actions sensibles
- [ ] HTTPS activé en production
- [ ] Sessions sécurisées configurées
- [ ] Mot de passe par défaut changé
- [ ] Logs d'audit en place (optionnel)

## 🚨 En Cas d'Incident

### Si un compte est compromis:
1. Désactiver le compte via `manage_users.php`
2. Réinitialiser le mot de passe
3. Vérifier les logs pour activité suspecte

### Si attaque brute-force détectée:
- Le rate limiter bloque automatiquement après 5 tentatives
- Bloquer l'IP au niveau firewall si nécessaire
- Augmenter le délai: modifier `rate_limit_check('login', $ip, 5, 1800)` (30 min)

## 📞 Support

Pour toute question de sécurité, consultez:
- OWASP Top 10: https://owasp.org/www-project-top-ten/
- PHP Security Guide: https://phptherightway.com/#security

---

**Dernière mise à jour:** 2026-02-07
**Version:** 1.0
