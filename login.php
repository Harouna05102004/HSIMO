<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Immobilier Luxe</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">IMMOLUX</div>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="acheter.php">Acheter</a></li>
                <li><a href="louer.php">Louer</a></li>
                <li><a href="vendre.php">Vendre</a></li>
                <li><a href="estimation.php">Estimation</a></li>
            </ul>
            <a href="login.php" class="btn-login active">Se connecter</a>
        </div>
    </nav>

    <main class="auth-container">
        <div class="auth-box">
            <h2 class="auth-title">Se connecter</h2>
            <p class="auth-subtitle">Votre place dans un coin du paradis</p>
            
            <div class="auth-form">
                <div class="form-group">
                    <label for="email">Nom d'utilisateur</label>
                    <input type="text" id="email" placeholder="Entrez votre nom d'utilisateur" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" placeholder="Entrez votre mot de passe" required>
                </div>
                
                <button class="btn-submit">Se connecter</button>
                
                <p class="auth-link">
                    Pas encore de compte ? <a href="signup.php">Créer un compte</a>
                </p>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h4>Nous contacter</h4>
                <div class="social-links">
                    <a href="#" aria-label="TikTok">📱</a>
                    <a href="#" aria-label="Instagram">📷</a>
                    <a href="#" aria-label="Facebook">👍</a>
                </div>
            </div>
            <div class="footer-section">
                <p>&copy; 2026 ImmoLux. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>
