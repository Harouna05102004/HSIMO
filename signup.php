<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - ImmoLux</title>
    <link rel="stylesheet" href="styles.css">
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
            <a href="login.php" class="btn-login">Se connecter</a>
        </div>
    </nav>
    <main class="auth-container">
        <div class="auth-box">
            <h2>Créer un compte</h2>
            <form class="auth-form">
                <input type="text" placeholder="Nom d'utilisateur" required>
                <input type="email" placeholder="Email" required>
                <input type="password" placeholder="Mot de passe" required>
                <button type="submit" class="btn-submit">S'inscrire</button>
            </form>
            <p class="auth-link">Déjà inscrit ? <a href="login.php">Connectez-vous</a></p>
        </div>
    </main>
</body>
</html>