<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImmoLux - Accueil</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">IMMOLUX</div>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Accueil</a></li>
                <li><a href="acheter.php">Acheter</a></li>
                <li><a href="louer.php">Louer</a></li>
                <li><a href="vendre.php">Vendre</a></li>
                <li><a href="estimation.php">Estimation</a></li>
            </ul>
            <a href="login.php" class="btn-login">Se connecter</a>
        </div>
    </nav>
    <main>
        <section class="hero">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">Votre place dans un coin du paradis</h1>
                    <p class="hero-subtitle">Les plus belles maisons de la côte vous attendent</p>
                    <div class="hero-buttons">
                        <a href="acheter.php" class="btn-primary">Acheter</a>
                        <a href="louer.php" class="btn-secondary">Louer</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=800&q=80" alt="Villa de luxe">
                </div>
            </div>
        </section>
    </main>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h4>Nous contacter</h4>
                <div class="social-links">
                    <a href="#">📱</a> <a href="#">📷</a> <a href="#">👍</a>
                </div>
            </div>
            <div class="footer-section">
                <p>&copy; 2026 ImmoLux. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>