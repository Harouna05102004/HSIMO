<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendre - ImmoLux</title>
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
            <h1 class="form-title">Vendez votre bien d'exception</h1>
            <form action="confirmation.php" method="POST" class="estimation-form">
                <div class="form-group">
                    <label>Description du bien</label>
                    <textarea placeholder="Décrivez votre propriété..." required></textarea>
                </div>
                <button type="submit" class="btn-submit">Envoyer l'annonce</button>
            </form>
        </div>
    </main>
</body>
</html>