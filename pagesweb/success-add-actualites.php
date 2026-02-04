<?php
session_start();
require_once __DIR__ . '/../configUrl.php'; // pour avoir URL_ADDSPACEADMIN, etc.

if (!isset($_SESSION['user'])) {
    header('Location:' . URL_AUTHENTIFICATION);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajout réussi - Documentation 1325</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }
        .success-container {
            text-align: center;
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            padding: 50px;
            max-width: 500px;
            width: 90%;
        }
        .success-gif {
            width: 120px;
            margin-bottom: 20px;
        }
        .countdown {
            font-size: 1.2rem;
            font-weight: 500;
            color: #0d6efd;
        }
        .title {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="success-container">
    <img src="https://media3.giphy.com/media/v1.Y2lkPTc5MGI3NjExcHQ5YTd0c2xwb2N2aWd2ZGhpZnh5N3k4ZXdrYzVxZ2dlM3l3aDczaCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/3ohhwfAa9rbXaZe86c/giphy.gif" alt="Succès" class="success-gif">
    
    <h3 class="title">Actualité ajoutée avec succès 🎉</h3>
    <p class="text-secondary mb-4">Votre enregistrement a été effectué avec succès.</p>
    <p class="countdown">Redirection dans <span id="counter">10</span> secondes...</p>
</div>

<script>
    let counter = 10;
    const interval = setInterval(() => {
        counter--;
        document.getElementById('counter').textContent = counter;
        if (counter <= 0) {
            clearInterval(interval);
            window.location.href = "<?=URL_ADDACTUALITES; ?>"; // retour vers le formulaire
        }
    }, 1000);
</script>

</body>
</html>
