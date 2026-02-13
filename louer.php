<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Louer - Les plus belles maisons de la côte</title>
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
                <li><a href="louer.php" class="active">Louer</a></li>
                <li><a href="vendre.php">Vendre</a></li>
                <li><a href="estimation.php">Estimation</a></li>
            </ul>
            <a href="login.php" class="btn-login">Se connecter</a>
        </div>
    </nav>

    <main class="container listing-page">
        <div class="page-header">
            <h1 class="page-title">Louez les Plus belles maisons de la côte</h1>
            <div class="filter-tabs">
                <button class="filter-tab active">Moderne</button>
                <button class="filter-tab">Rustique</button>
                <button class="filter-tab">Appartement</button>
            </div>
        </div>

        <div class="property-grid-large" id="propertyContainer">
            <!-- Les annonces seront chargées dynamiquement par JavaScript -->
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
    <script src="script.js"></script>
</body>
</html>
