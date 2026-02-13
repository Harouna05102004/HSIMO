<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendre votre maison - Immobilier Luxe</title>
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
                <li><a href="vendre.php" class="active">Vendre</a></li>
                <li><a href="estimation.php">Estimation</a></li>
            </ul>
            <a href="login.php" class="btn-login">Se connecter</a>
        </div>
    </nav>

    <main class="form-page">
        <div class="form-container">
            <h1 class="form-title">Vendez votre maison !</h1>
            
            <form id="sellForm" class="sell-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Titre de l'annonce</label>
                        <input type="text" id="title" name="title" placeholder="Ex: Belle villa avec piscine" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="city">Ville</label>
                        <input type="text" id="city" name="city" placeholder="Ville" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Prix (€)</label>
                        <input type="number" id="price" name="price" placeholder="Ex: 450000" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="surface">Surface (m²)</label>
                        <input type="number" id="surface" name="surface" placeholder="Ex: 120" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="rooms">Nombre de chambres</label>
                        <input type="number" id="rooms" name="rooms" placeholder="Ex: 3" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="bathrooms">Nombre de salles de bain</label>
                        <input type="number" id="bathrooms" name="bathrooms" placeholder="Ex: 2" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5" placeholder="Décrivez votre bien..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="photos">Photos</label>
                    <div class="file-upload">
                        <input type="file" id="photos" name="photos" multiple accept="image/*">
                        <label for="photos" class="file-upload-label">
                            <span>📁 Choisir des photos</span>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">Soumettre l'annonce</button>
            </form>
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
