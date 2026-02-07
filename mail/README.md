Instructions d'installation de PHPMailer (Windows WAMP)

But : activer l'envoi SMTP via PHPMailer pour utiliser Gmail (App Password) plutôt que le fallback mail().

Étapes rapides :

1) Installer Composer (Windows)
- Téléchargez et exécutez l'installateur : https://getcomposer.org/Composer-Setup.exe
- Suivez l'assistant (il ajoute `composer` au PATH).
- Vérifiez dans PowerShell :

```powershell
composer --version
```

2) Depuis la racine du projet, installer PHPMailer

```powershell
cd C:\wamp64\www\info1325.cd
composer require phpmailer/phpmailer
```

Cela créera `vendor/` et `vendor/autoload.php` que `mail/mail.php` détecte automatiquement.

3) Configurer SMTP pour Gmail
- Ouvrez `mail/smtp_config.php` et modifiez :
  - `SMTP_USER` -> votre adresse Gmail (ex: projet1325@gmail.com)
  - `SMTP_PASS` -> Mot de passe d'application (App Password)
  - Laisser `SMTP_HOST` = `smtp.gmail.com`, `SMTP_PORT` = 587, `SMTP_SECURE` = 'tls'

Remarques Gmail :
- Activez l'authentification à deux facteurs pour le compte Gmail, puis créez un App Password (Courrier > App Passwords).
- N'utilisez pas votre mot de passe principal.

4) Tester le formulaire
- Accédez à : http://localhost/info1325.cd/pagesweb/contact.php
- Envoyez un message et vérifiez la page de confirmation (`mail/wa_redirect.php`) et la boîte Gmail.

Alternative sans Composer
- Téléchargez PHPMailer depuis GitHub (release zip) et placez les sources dans `vendor/phpmailer/phpmailer` puis assurez-vous que `vendor/autoload.php` existe (ou incluez manuellement `src/PHPMailer.php` etc.).

Si vous voulez, je peux :
- Vous guider pas-à-pas pour installer Composer sur votre machine (commandes et vérifications).
- Télécharger manuellement PHPMailer dans le projet si vous m'autorisez à ajouter les fichiers (mais c'est assez volumineux).

