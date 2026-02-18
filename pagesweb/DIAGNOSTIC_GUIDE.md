# Guide de Dépannage - Documentation Tracking

## Problème Signalé
L'utilisateur reçoit une erreur en cliquant sur "Voir" ou "Télécharger" un document.

## Actions Prises

### 1. Bug SQL Corrigé ✓
- **Problème**: Paramètre `:date` dupliqué dans `update_daily_stats()`
- **Solution**: Renommé en `:stat_date` dans la clause VALUES
- **Fichier**: `pagesweb/track_visitor.php` ligne ~245
- **Impact**: Devrait réduire les erreurs de logging

### 2. Amélioration de la Gestion des Erreurs ✓
- **Fichier**: `pagesweb/documentation_event.php`
- **Changements**:
  - Ajout de logs détaillés pour chaque étape du traitement
  - Messages d'erreur plus spécifiques
  - Validation supplémentaire pour les chemins de fichiers
- **Résultat**: Les erreurs auront maintenant des messages plus explicites dans les logs PHP

### 3. Scripts de Diagnostic Créés ✓

#### test_documentation.php
**Accès**: http://localhost/info1325.cd/pagesweb/test_documentation.php
**Vérifie**:
  - L'existence de la table `documentations`
  - Le chemin des fichiers PDF
  - L'existence réelle des fichiers PDF sur le disque
  - Les actions disponibles (Voir/Télécharger)

#### test_tracking.php
**Accès**: http://localhost/info1325.cd/pagesweb/test_tracking.php
**Vérifie**:
  - Le fonctionnement de la fonction `track_documentation_event()`
  - L'existence de la table `documentation_events`
  - Les événements enregistrés récemment

#### test_filenames.php
**Accès**: http://localhost/info1325.cd/pagesweb/test_filenames.php
**Vérifie**:
  - La correspondance exacte entre les noms dans la BD et les fichiers réels
  - L'encodage des caractères spéciaux
  - Les discordances éventuelles

## Étapes de Diagnostic Recommandées

### Étape 1: Vérifier la Correspondance des Fichiers
1. Ouvrez **test_filenames.php** dans votre navigateur
2. Vérifiez si tous les fichiers en base de données existent en rose et ont une correspondance exacte
3. Si certains sont marqués "✗ NO", les fichiers manquent ou les noms ne correspondent pas exactement

### Étape 2: Tester Directement un Document
1. Ouvrez **test_documentation.php**
2. Essayez de cliquer sur "View" ou "Download" pour un document test
3. Notez le message d'erreur exact affiché

### Étape 3: Vérifier les Logs PHP
1. Vérifiez `C:\wamp64\logs\php_error.log`
2. Cherchez les messages de `documentation_event.php` et `track_documentation_event`
3. Les nouveaux messages d'erreur seront plus détaillés

## Messages d'Erreur Possibles et Solutions

| Erreur | Cause | Solution |
|--------|-------|----------|
| "Document introuvable." | Document ID n'existe pas en BD | Vérifier test_filenames.php |
| "Ce document n'a pas de fichier PDF associé." | `fichier_pdf` vide en BD | Mettre à jour la BD |
| "Fichier PDF introuvable: [nom]" | Fichier manquant ou nom incorrect | Vérifier le fichier existe dans `/img/documentations/` |
| "Nom de fichier invalide." | Tentative de path traversal détectée | Normal (sécurité) |
| "Erreur serveur." | Exception PDO | Vérifier test_tracking.php et logs PHP |

## Fichiers Validés
- ✓ `documentation_event.php` - Syntaxe OK
- ✓ `track_visitor.php` - Syntaxe OK, bug SQL fixé
- ✓ `documentation.php` - Syntaxe OK
- ✓ `resolution.php` - Les URLs sont correctement formées
- ✓ 17 fichiers PDF présents dans `/img/documentations/`

## Prochaines Étapes
1. Exécutez les scripts de diagnostic
2. Partagez les résultats/erreurs affiches
3. Consultez les logs PHP pour les messages détaillés
4. Si besoin, je peux approfondir l'investigation

## Note Importante
Les fichiers ont été modifiés et la configuration est correcte en théorie. 
L'erreur de l'utilisateur peut être due à :
- Une discordance entre les noms en BD et les fichiers réels
- Un problème de permissions/accès aux fichiers
- Un problème d'encodage de caractères spéciaux

**Les scripts de diagnostic devraient identifier le problème exact.**
