<?php

session_start();



// 🔹 Inclusion des fichiers de configuration

require_once __DIR__ . '/../configUrl.php';

require_once __DIR__ . '/../defConstLiens.php';

require_once $dateDbConnect; // Fichier qui contient $pdo (connexion PDO)



// 🔒 Vérification de la session (protection)

if (!isset($_SESSION['user'])) {

    header('Location:' . URL_AUTHENTIFICATION);

    exit;

}



?>



<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>Espace Administrateur | SN1325</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body {

            background: linear-gradient(135deg, #007bff, #6610f2);

            height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;

            color: white;

        }

        .card {

            background-color: rgba(255, 255, 255, 0.1);

            backdrop-filter: blur(10px);

            border: none;

            border-radius: 20px;

            padding: 40px;

            width: 400px;

            text-align: center;

        }

        .btn-custom {

            width: 100%;

            margin: 10px 0;

            padding: 15px;

            font-size: 18px;

            border-radius: 10px;

            transition: transform 0.2s;

        }

        .btn-custom:hover {

            transform: scale(1.05);

        }

        .logout {

            position: absolute;

            top: 20px;

            right: 30px;

        }

    </style>

</head>

<body>

    <a href="<?= URL_LOGOUT; ?>" class="btn btn-light logout">Déconnexion</a>

    <div class="card shadow-lg">

        <h3 class="mb-4">Bienvenue dans l’espace d’administration</h3>

        <a href="<?=URL_ADDACTUALITES; ?>" class="btn btn-warning btn-custom">📰 Espace Actualités</a>

        <a href="<?=URL_ADDDOCUMENTATIONS; ?>" class="btn btn-info btn-custom">📚 Espace Documentation</a>

        <a href="<?= URL_MANAGE_SLIDER; ?>" class="btn btn-secondary btn-custom">🎞️ Gérer le slider</a>


    </div>

</body>

</html>

