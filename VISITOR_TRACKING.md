# Système de Comptage des Visiteurs - SN1325

## 🎯 Vue d'Ensemble

Système complet de tracking des visiteurs avec statistiques en temps réel affichées dans le dashboard administrateur.

## 📊 Fonctionnalités

### Statistiques Disponibles

- **Visiteurs aujourd'hui** - Total et visiteurs uniques
- **Visiteurs cette semaine** - Comptage hebdomadaire
- **Visiteurs ce mois** - Comptage mensuel
- **Total des visites** - Depuis le début du tracking
- **Visiteurs uniques totaux** - Basé sur les cookies
- **Moyenne quotidienne** - Sur les 30 derniers jours
- **Graphique d'évolution** - 14 derniers jours avec Chart.js

### Données Collectées

Pour chaque visite:
- ID visiteur unique (cookie 1 an)
- Adresse IP
- User Agent (navigateur)
- URL de la page visitée
- Référent (page précédente)
- Date et heure de la visite
- Marqueur "visite unique du jour"

## 📁 Fichiers Créés

### 1. `pagesweb/track_visitor.php`
**Script principal de tracking des visiteurs**

Fonctions disponibles:
```php
// Obtenir les statistiques complètes
$stats = get_visitor_stats($pdo);

// Affiche:
// - total_visits: Nombre total de visites
// - unique_visitors: Nombre de visiteurs uniques
// - today: Visites aujourd'hui
// - today_unique: Visiteurs uniques aujourd'hui
// - this_week: Visites cette semaine
// - this_month: Visites ce mois
// - avg_daily: Moyenne quotidienne (30j)
// - recent_visitors: Dernières visites

// Obtenir les données pour un graphique
$chart_data = get_daily_stats_chart($pdo, 14); // 14 derniers jours
```

### 2. `pagesweb/visitor_stats_widget.php`
**Widget d'affichage des statistiques pour le dashboard**

Contient:
- 4 cartes de statistiques colorées
- Graphique Chart.js responsive
- Informations complémentaires
- Design Bootstrap 5 responsive

### 3. Tables de Base de Données

#### Table `visits`
```sql
CREATE TABLE visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_id VARCHAR(64) NOT NULL,           -- ID unique du visiteur
    ip_address VARCHAR(45) NOT NULL,           -- Adresse IP
    user_agent TEXT,                           -- Navigateur
    page_url VARCHAR(512),                     -- Page visitée
    referrer VARCHAR(512),                     -- D'où vient le visiteur
    visit_date DATE NOT NULL,                  -- Date de visite
    visit_time DATETIME DEFAULT CURRENT_TIMESTAMP, -- Heure exacte
    is_unique TINYINT(1) DEFAULT 1,           -- Première visite du jour?
    country VARCHAR(2) DEFAULT NULL,           -- Code pays (optionnel)
    INDEX idx_visitor_id (visitor_id),
    INDEX idx_visit_date (visit_date),
    INDEX idx_ip_address (ip_address)
);
```

#### Table `visit_stats`
```sql
CREATE TABLE visit_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_date DATE NOT NULL UNIQUE,            -- Date
    total_visits INT DEFAULT 0,                -- Total visites
    unique_visits INT DEFAULT 0,               -- Visiteurs uniques
    page_views INT DEFAULT 0,                  -- Pages vues
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 🔧 Installation

### Étape 1: Inclusion Automatique ✅
Les tables sont créées automatiquement au premier chargement de `track_visitor.php`.

### Étape 2: Intégration sur les Pages Publiques

#### Sur la page d'accueil (index.php) ✅
```php
<?php
require_once $dateDbConnect; // Connexion DB

// Tracker le visiteur
require_once __DIR__ . '/pagesweb/track_visitor.php';
?>
```

#### Sur les autres pages publiques
**Ajoutez le même code dans:**
- `pagesweb/actualites.php`
- `pagesweb/documentation.php`
- `pagesweb/resolution.php`
- `pagesweb/secretariat.php`
- `pagesweb/contact.php`
- `pagesweb/gallery.php`

**Exemple d'intégration:**
```php
<?php
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect;

// Tracker le visiteur sur cette page
require_once __DIR__ . '/track_visitor.php';
?>
<!DOCTYPE html>
<html>
<!-- Votre contenu -->
</html>
```

### Étape 3: Dashboard Admin ✅
Le widget est déjà intégré dans `administrateur.php`.

## 🎨 Personnalisation

### Modifier les Couleurs des Cartes

Dans `visitor_stats_widget.php`, modifiez les classes Bootstrap:
```php
<div class="card text-white bg-primary">   <!-- Bleu -->
<div class="card text-white bg-info">      <!-- Cyan -->
<div class="card text-white bg-success">   <!-- Vert -->
<div class="card text-white bg-dark">      <!-- Noir -->

<!-- Autres couleurs disponibles: -->
<!-- bg-danger (rouge), bg-warning (jaune), bg-secondary (gris) -->
```

### Modifier la Période du Graphique

Par défaut: 14 derniers jours
```php
// Dans visitor_stats_widget.php, ligne ~14
$daily_chart_data = get_daily_stats_chart($pdo, 14); // Changer 14 à 30 pour un mois
```

### Changer la Durée du Cookie Visiteur

Par défaut: 1 an (365 jours)
```php
// Dans track_visitor.php, fonction get_visitor_id()
$cookie_lifetime = 365 * 24 * 60 * 60; // Modifier selon vos besoins
```

## 📈 Analyse des Données

### Voir Toutes les Visites
```sql
SELECT * FROM visits
ORDER BY visit_time DESC
LIMIT 100;
```

### Visiteurs Uniques par Jour
```sql
SELECT
    visit_date,
    COUNT(DISTINCT visitor_id) as unique_visitors
FROM visits
GROUP BY visit_date
ORDER BY visit_date DESC;
```

### Pages les Plus Visitées
```sql
SELECT
    page_url,
    COUNT(*) as visits
FROM visits
GROUP BY page_url
ORDER BY visits DESC
LIMIT 10;
```

### Trafic par Heure de la Journée
```sql
SELECT
    HOUR(visit_time) as hour,
    COUNT(*) as visits
FROM visits
WHERE visit_date = CURDATE()
GROUP BY HOUR(visit_time)
ORDER BY hour;
```

## 🔒 Sécurité et Confidentialité

### Protection de la Vie Privée
- Les adresses IP sont stockées mais pas affichées publiquement
- Les cookies utilisent le flag `HttpOnly` pour éviter l'accès JavaScript
- Pas de tracking sur les pages d'administration
- Conformité RGPD: informez vos visiteurs du tracking

### Recommandations RGPD

Ajoutez dans votre politique de confidentialité:
```
Nous utilisons des cookies pour mesurer l'audience de notre site.
Ces données sont anonymisées et utilisées uniquement à des fins statistiques.
```

## 🚀 Optimisation

### Nettoyage des Anciennes Données

Créez un cron job pour supprimer les visites de plus de 1 an:
```sql
-- À exécuter mensuellement
DELETE FROM visits
WHERE visit_date < DATE_SUB(CURDATE(), INTERVAL 365 DAY);
```

### Index pour Performance

Les index sont déjà créés automatiquement:
- `idx_visitor_id` - Recherche par visiteur
- `idx_visit_date` - Recherche par date
- `idx_ip_address` - Recherche par IP

## 📊 Dashboard Admin - Ce qui s'Affiche

### Cartes Statistiques (4 cartes)
1. **Aujourd'hui** (Bleu) - Visites du jour + visiteurs uniques
2. **Cette Semaine** (Cyan) - Total semaine en cours
3. **Ce Mois** (Vert) - Total mois en cours
4. **Total** (Noir) - Depuis le début + visiteurs uniques

### Graphique
- Ligne bleue: Toutes les visites
- Ligne cyan: Visiteurs uniques
- Période: 14 derniers jours
- Responsive et interactif (Chart.js)

### Indicateurs
- Moyenne quotidienne sur 30 jours
- Barre de progression
- Date de début du suivi

## 🛠️ Dépannage

### Les statistiques sont toutes à 0
1. Vérifiez que les tables sont créées:
   ```sql
   SHOW TABLES LIKE 'visits';
   SHOW TABLES LIKE 'visit_stats';
   ```
2. Visitez la page d'accueil du site
3. Vérifiez qu'il y a des entrées:
   ```sql
   SELECT COUNT(*) FROM visits;
   ```

### Le graphique ne s'affiche pas
- Vérifiez que Chart.js est chargé (F12 > Console)
- Assurez-vous qu'il y a au moins 2 jours de données

### Les visiteurs sont comptés plusieurs fois
- Les cookies doivent être activés
- Vérifiez que le cookie `sn1325_visitor_id` est bien créé (F12 > Application > Cookies)

## 📞 Support

### Fichiers à Vérifier
1. `pagesweb/track_visitor.php` - Script de tracking
2. `pagesweb/visitor_stats_widget.php` - Widget dashboard
3. `pagesweb/administrateur.php` - Intégration dashboard
4. `index.php` - Tracking page d'accueil

### Logs d'Erreur
Les erreurs sont loguées via `error_log()`. Vérifiez:
- `error_log` de PHP
- Console navigateur (F12)
- Logs MySQL

---

**Version:** 1.0
**Date:** 2026-02-07
**Auteur:** Système de tracking visiteurs SN1325
