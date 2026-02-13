<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimation - Estimez votre maison</title>
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
                <li><a href="estimation.php" class="active">Estimation</a>
</li>
            </ul>
            <a href="login.php" class="btn-login">Se connecter</a>
        </div>
    </nav>

    <main class="form-page">
        <div class="form-container">
            <h1 class="form-title">Estimez votre maison !</h1>
            
            <form id="estimationForm" class="estimation-form">
                <div class="form-group">
                    <label for="address-est">Adresse</label>
                    <input type="text" id="address-est" name="address" placeholder="Entrez l'adresse de votre bien" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="city-est">Ville</label>
                        <input type="text" id="city-est" name="city" placeholder="Ville" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="postal">Code postal</label>
                        <input type="text" id="postal" name="postal" placeholder="Code postal" required>
                    </div>
                </div>
                
                <div class="form-row triple">
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select id="type" name="type" required>
                            <option value="">Sélectionner</option>
                            <option value="maison">Maison</option>
                            <option value="appartement">Appartement</option>
                            <option value="villa">Villa</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="rooms-est">Pièces</label>
                        <input type="number" id="rooms-est" name="rooms" placeholder="Ex: 4" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="surface-est">Surface (m²)</label>
                        <input type="number" id="surface-est" name="surface" placeholder="Ex: 100" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="year">Année de construction</label>
                        <input type="number" id="year" name="year" placeholder="Ex: 1990" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="condition">État</label>
                        <select id="condition" name="condition" required>
                            <option value="">Sélectionner</option>
                            <option value="neuf">Neuf</option>
                            <option value="excellent">Excellent</option>
                            <option value="bon">Bon</option>
                            <option value="renover">À rénover</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">Obtenir l'estimation</button>
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
