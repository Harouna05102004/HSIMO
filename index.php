<!DOCTYPE html>
<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';
?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Immobilier Luxe - Trouvez votre maison de rêve</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <img src="Gemini_Generated_Image_xzd5pxzd5pxzd5px-removebg-preview.png" alt="Logo">
            </div>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Accueil</a></li>
                <li><a href="acheter.php">Acheter</a></li>
                <li><a href="louer.php">Louer</a></li>
                <li><a href="vendre.php">Vendre</a></li>
                <li><a href="estimation.php">Estimation</a></li>
            </ul>
           <?php if ($isLoggedIn): ?>
    <a href="logout.php" class="btn-login">Déconnexion (<?= htmlspecialchars($username) ?>)</a>
<?php else: ?>
    <a href="login.php" class="btn-login">Se connecter</a>
<?php endif; ?>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-overlay"></div>
        <img src="o0JZAPcnRsS64C5rFqGn_4840ec50-9d33-4aae-be2b-410c2dcd21f1.webp" alt="Luxury Home" class="hero-image">
        <div class="hero-content">
            <h1 class="hero-title">Votre place dans un coin du paradis</h1>
            <p class="hero-subtitle">Découvrez les plus belles propriétés de la côte</p>
            <div class="search-bar">
                <input type="text" placeholder="Rechercher une ville, un quartier..." class="search-input">
                <button class="search-btn">Rechercher</button>
            </div>
        </div>
    </section>

    <main class="container">
        <section class="featured-section">
            <h2 class="section-title">Nos coups de cœur</h2>
            <div class="property-grid">
                <div class="property-card">
                    <div class="property-image">
                        <img src="YOUR_PROPERTY_IMAGE_1_URL" alt="Villa moderne">
                        <span class="property-badge">Nouveau</span>
                    </div>
                    <div class="property-info">
                        <h3 class="property-title">Villa Moderne Vue Mer</h3>
                        <p class="property-location">Nice, Côte d'Azur</p>
                        <div class="property-features">
                            <span>🛏️ 4 chambres</span>
                            <span>🚿 3 salles de bain</span>
                            <span>📐 250 m²</span>
                        </div>
                        <div class="property-footer">
                            <span class="property-price">2 450 000 €</span>
                            <button class="btn-view">Voir plus</button>
                        </div>
                    </div>
                </div>

                <div class="property-card">
                    <div class="property-image">
                        <img src="YOUR_PROPERTY_IMAGE_2_URL" alt="Appartement luxe">
                        <span class="property-badge featured">Coup de cœur</span>
                    </div>
                    <div class="property-info">
                        <h3 class="property-title">Penthouse Centre-Ville</h3>
                        <p class="property-location">Cannes, Croisette</p>
                        <div class="property-features">
                            <span>🛏️ 3 chambres</span>
                            <span>🚿 2 salles de bain</span>
                            <span>📐 180 m²</span>
                        </div>
                        <div class="property-footer">
                            <span class="property-price">1 850 000 €</span>
                            <button class="btn-view">Voir plus</button>
                        </div>
                    </div>
                </div>

                <div class="property-card">
                    <div class="property-image">
                        <img src="YOUR_PROPERTY_IMAGE_3_URL" alt="Maison de campagne">
                    </div>
                    <div class="property-info">
                        <h3 class="property-title">Bastide Provençale</h3>
                        <p class="property-location">Grasse, Provence</p>
                        <div class="property-features">
                            <span>🛏️ 5 chambres</span>
                            <span>🚿 4 salles de bain</span>
                            <span>📐 320 m²</span>
                        </div>
                        <div class="property-footer">
                            <span class="property-price">3 200 000 €</span>
                            <button class="btn-view">Voir plus</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h4>Nous contacter</h4>
                <div class="social-links">
                    <a href="https://www.tiktok.com/@votre_compte" aria-label="TikTok">
                        <img src="illustration-icone-medias-sociaux-tiktok-icone-tiktok-illustration-vectorielle_561158-2136.avif" alt="TikTok">
                    </a>
                    <a href="https://www.instagram.com/votre_compte" aria-label="Instagram">
                        <img src="Instagram_icon.png.webp" alt="Instagram">
                    </a>
                    <a href="https://www.facebook.com/votre_page" aria-label="Facebook">
                        <img src="png-clipart-facebook-logo-computer-icons-facebook-logo-facebook-thumbnail-removebg-preview.png" alt="Facebook">
                    </a>
                </div>
            </div>
            <div class="footer-section">
                <p>&copy; 2026 ImmoLux. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>
