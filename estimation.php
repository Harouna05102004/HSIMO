<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimation - ImmoLux</title>
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
                <li><a href="estimation.php" class="active">Estimation</a></li>
            </ul>
            <a href="login.php" class="btn-login">Se connecter</a>
        </div>
    </nav>
    <main class="form-page">
        <div class="form-container">
            <h1 class="form-title">Estimez votre maison gratuitement</h1>
            <form action="resultat-estimation.php" method="POST" class="estimation-form">
                <div class="form-group">
                    <label for="address">Adresse du bien</label>
                    <input type="text" id="address" name="address" placeholder="Ex: 12 rue du Palais, Nice" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Surface (m²)</label>
                        <input type="number" name="surface" placeholder="Ex: 120" required>
                    </div>
                    <div class="form-group">
                        <label>Nombre de pièces</label>
                        <input type="number" name="pieces" placeholder="Ex: 5" required>
                    </div>
                </div>
                <button type="submit" class="btn-submit">Calculer l'estimation</button>
            </form>
        </div>
    </main>
</body>
</html>