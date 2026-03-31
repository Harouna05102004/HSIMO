<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation - HSimo</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">HSimo</div>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="acheter.php">Acheter</a></li>
                <li><a href="louer.php">Louer</a></li>
                <li><a href="vendre.php">Vendre</a></li>
                <li><a href="estimation.php">Estimation</a></li>
            </ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="btn-login">Déconnexion</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Se connecter</a>
            <?php endif; ?>
        </div>
    </nav>
    <main class="result-page" style="min-height:80vh;display:flex;align-items:center;justify-content:center;">
        <div style="text-align:center;max-width:600px;padding:3rem;">
            <div style="font-size:5rem;margin-bottom:1.5rem;">✅</div>
            <h1 style="font-family:'Playfair Display',serif;color:#1a3a52;font-size:2rem;margin-bottom:1rem;">
                Votre demande de vente a bien été prise en compte !
            </h1>
            <p style="color:#666;margin-bottom:0.5rem;">Nous vous contacterons sous peu.</p>
            <p style="color:#888;font-size:0.9rem;margin-bottom:2rem;">Si vous avez besoin d'aide, n'hésitez pas à nous contacter.</p>
            <a href="index.php" style="background:#1a3a52;color:white;padding:1rem 2.5rem;border-radius:50px;text-decoration:none;font-family:'Outfit',sans-serif;font-weight:500;">
                Retour à l'accueil
            </a>
        </div>
    </main>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section"><p>&copy; 2026 HSimo. Tous droits réservés.</p></div>
        </div>
    </footer>
</body>
</html>