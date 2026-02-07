# Système de Paramètres du Site - SN1325

## 🎯 Vue d'Ensemble

Un système complet de gestion des paramètres du site accessible depuis le dashboard administrateur. Permet de configurer tous les aspects du site sans modifier le code.

## 📁 Fichiers Créés

1. **pagesweb/manage_settings.php** - Interface d'administration des paramètres
2. **pagesweb/settings_helper.php** - Fonctions helper pour récupérer les paramètres
3. **Table `site_settings`** - Stockage en base de données

## 🗂️ Catégories de Paramètres

### 1. Général 🔧
- **Nom du site** - Titre principal
- **Slogan** - Tagline/sous-titre
- **Description** - Description du site
- **Mots-clés** - Keywords pour SEO

### 2. Contact 📧
- **Email** - Email de contact principal
- **Téléphone** - Numéro de téléphone
- **Adresse** - Adresse postale

### 3. Réseaux Sociaux 🔗
- Facebook
- Twitter/X
- Instagram
- LinkedIn
- YouTube

### 4. SEO 🔍
- **Meta title** - Titre par défaut pour Google
- **Meta description** - Description pour Google
- **Google Analytics** - Code de tracking (GA4)

### 5. Fonctionnalités ⚡
- **Commentaires** - Activer/désactiver les commentaires
- **Newsletter** - Inscription newsletter
- **Mode maintenance** - Mettre le site hors ligne

## 🗄️ Structure de la Base de Données

```sql
CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_group VARCHAR(50) DEFAULT 'general',
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Champs:
- **setting_key** - Identifiant unique (ex: `site_name`)
- **setting_value** - Valeur du paramètre
- **setting_group** - Catégorie (general, contact, social, seo, features)
- **description** - Description affichée dans l'interface
- **updated_at** - Date de dernière modification

## 🔧 Utilisation des Fonctions Helper

### Inclure le fichier helper
```php
<?php
require_once __DIR__ . '/pagesweb/settings_helper.php';
```

### Récupérer un paramètre
```php
// Syntaxe: get_setting($key, $default_value)
$site_name = get_setting('site_name', 'SN1325');
$contact_email = get_setting('contact_email', 'contact@example.com');
```

### Fonctions pratiques
```php
// Nom du site
echo get_site_name(); // "SN1325"

// Slogan
echo get_site_tagline(); // "Plateforme Nationale 1325"

// Email de contact
echo get_contact_email(); // "contact@sn1325.cd"

// Vérifier si une fonctionnalité est activée
if (is_feature_enabled('enable_comments')) {
    // Afficher les commentaires
}

// Mode maintenance
if (is_maintenance_mode()) {
    // Rediriger vers page maintenance
}

// Récupérer tous les liens sociaux
$socials = get_social_links();
// ['facebook' => 'url', 'twitter' => 'url', ...]
```

### Afficher les icônes de réseaux sociaux
```php
// Dans votre footer ou header
<div class="social-icons">
    <?php display_social_icons('fs-4 text-white'); ?>
</div>
```

### Mettre à jour un paramètre (en PHP)
```php
update_setting('site_name', 'Nouveau Nom du Site');
```

## 💡 Exemples d'Utilisation

### Dans le header
```php
<?php require_once __DIR__ . '/pagesweb/settings_helper.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars(get_site_name()) ?> - <?= htmlspecialchars(get_site_tagline()) ?></title>
    <meta name="description" content="<?= htmlspecialchars(get_setting('site_description')) ?>">
    <meta name="keywords" content="<?= htmlspecialchars(get_setting('site_keywords')) ?>">

    <!-- Google Analytics -->
    <?php $ga_code = get_setting('seo_google_analytics'); ?>
    <?php if ($ga_code): ?>
        <!-- Google Analytics tracking code -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($ga_code) ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?= htmlspecialchars($ga_code) ?>');
        </script>
    <?php endif; ?>
</head>
<body>
    <h1><?= htmlspecialchars(get_site_name()) ?></h1>
</body>
</html>
```

### Dans le footer
```php
<footer>
    <div class="container">
        <h3><?= htmlspecialchars(get_site_name()) ?></h3>
        <p><?= htmlspecialchars(get_site_tagline()) ?></p>

        <!-- Contact -->
        <p>
            <strong>Contact:</strong>
            <a href="mailto:<?= htmlspecialchars(get_contact_email()) ?>">
                <?= htmlspecialchars(get_contact_email()) ?>
            </a><br>
            <?= htmlspecialchars(get_setting('contact_phone')) ?><br>
            <?= htmlspecialchars(get_setting('contact_address')) ?>
        </p>

        <!-- Réseaux sociaux -->
        <div class="social-icons">
            <?php display_social_icons('fs-3'); ?>
        </div>
    </div>
</footer>
```

### Page de commentaires conditionnelle
```php
<?php if (is_feature_enabled('enable_comments')): ?>
    <div class="comments-section">
        <h3>Commentaires</h3>
        <!-- Votre système de commentaires -->
    </div>
<?php endif; ?>
```

### Mode maintenance
```php
<?php
require_once __DIR__ . '/pagesweb/settings_helper.php';

// Vérifier si le site est en maintenance (sauf pour les admins)
if (is_maintenance_mode() && !isset($_SESSION['user'])) {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Maintenance - <?= htmlspecialchars(get_site_name()) ?></title>
    </head>
    <body>
        <h1>Site en maintenance</h1>
        <p><?= htmlspecialchars(get_site_name()) ?> est actuellement en maintenance. Merci de revenir plus tard.</p>
    </body>
    </html>
    <?php
    exit;
}
?>
```

## 🎨 Interface d'Administration

### Accès
1. Connectez-vous au dashboard admin
2. Cliquez sur "⚙️ Paramètres" dans le menu
3. Accessible uniquement aux administrateurs

### Onglets Disponibles
- **Général** 🔧 - Informations de base du site
- **Contact** 📧 - Coordonnées de contact
- **Réseaux Sociaux** 🔗 - Liens vers vos profils sociaux
- **SEO** 🔍 - Optimisation pour moteurs de recherche
- **Fonctionnalités** ⚡ - Activer/désactiver des fonctions

### Sauvegarde
- Bouton "Enregistrer les Paramètres" en bas
- Protected par CSRF token
- Modifications instantanées sur le site

## 🔒 Sécurité

### Protection Implémentée
- ✅ Protection CSRF sur tous les formulaires
- ✅ Accès restreint aux administrateurs seulement
- ✅ Échappement HTML de toutes les valeurs
- ✅ Requêtes SQL préparées (PDO)

### Code de Sécurité
```php
// Dans manage_settings.php
if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ' . URL_AUTHENTIFICATION);
    exit;
}

// Validation CSRF obligatoire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate(true);
}
```

## 📊 Gestion Avancée

### Ajouter un Nouveau Paramètre

**Méthode 1: Via SQL**
```sql
INSERT INTO site_settings (setting_key, setting_value, setting_group, description)
VALUES ('mon_parametre', 'ma_valeur', 'general', 'Description de mon paramètre');
```

**Méthode 2: Via PHP**
```php
$pdo->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_group, description)
               VALUES (:key, :value, :group, :desc)")
    ->execute([
        ':key' => 'mon_parametre',
        ':value' => 'ma_valeur',
        ':group' => 'general',
        ':desc' => 'Description'
    ]);
```

### Récupérer Tous les Paramètres
```php
$all_settings = get_all_settings();
foreach ($all_settings as $key => $value) {
    echo "$key = $value\n";
}
```

### Récupérer par Groupe
```php
$general_settings = get_settings_by_group('general');
$contact_settings = get_settings_by_group('contact');
```

## 🎯 Cas d'Usage

### 1. Personnaliser le site sans toucher au code
Changez le nom, le slogan, les couleurs via l'interface admin.

### 2. Gérer les réseaux sociaux
Ajoutez/modifiez vos liens sociaux facilement.

### 3. Mode maintenance rapide
Un clic pour mettre le site hors ligne pendant une intervention.

### 4. Analytics
Ajoutez votre code Google Analytics sans modifier les templates.

### 5. Contact centralisé
Un seul endroit pour gérer toutes vos coordonnées.

## 🛠️ Dépannage

### Les paramètres ne s'affichent pas
1. Vérifiez que la table `site_settings` existe:
   ```sql
   SHOW TABLES LIKE 'site_settings';
   ```
2. Vérifiez qu'il y a des valeurs par défaut:
   ```sql
   SELECT * FROM site_settings;
   ```

### Les modifications ne sont pas prises en compte
- Videz le cache du navigateur (Ctrl+F5)
- Vérifiez les logs d'erreur PHP
- Assurez-vous que le formulaire se soumet correctement

### Erreur CSRF
- Assurez-vous que les cookies sont activés
- Vérifiez que la session est démarrée

## 📚 Résumé des Fonctions

| Fonction | Description | Exemple |
|----------|-------------|---------|
| `get_setting($key, $default)` | Récupère un paramètre | `get_setting('site_name', 'Default')` |
| `update_setting($key, $value)` | Met à jour un paramètre | `update_setting('site_name', 'Nouveau')` |
| `get_all_settings()` | Tous les paramètres | `$all = get_all_settings()` |
| `get_settings_by_group($group)` | Paramètres d'un groupe | `get_settings_by_group('social')` |
| `is_feature_enabled($key)` | Vérifie si activé | `if (is_feature_enabled('enable_comments'))` |
| `get_site_name()` | Nom du site | `echo get_site_name()` |
| `get_site_tagline()` | Slogan du site | `echo get_site_tagline()` |
| `get_contact_email()` | Email de contact | `echo get_contact_email()` |
| `get_social_links()` | Liens sociaux (array) | `$links = get_social_links()` |
| `display_social_icons($class)` | Affiche icônes sociales | `display_social_icons('fs-4')` |
| `is_maintenance_mode()` | Mode maintenance actif? | `if (is_maintenance_mode())` |

## 🚀 Prochaines Étapes

### Améliorations Possibles
1. **Upload de logo** - Ajouter un logo personnalisable
2. **Paramètres de couleurs** - Personnaliser le thème
3. **Multilingue** - Support de plusieurs langues
4. **Import/Export** - Sauvegarder/restaurer les paramètres
5. **Validation avancée** - Vérifier les URLs, emails, etc.

---

**Version:** 1.0
**Date:** 2026-02-07
**Auteur:** Système de paramètres SN1325
