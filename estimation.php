<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adresse = trim($_POST['address'] ?? '');
    $ville = trim($_POST['city'] ?? '');
    $code_postal = trim($_POST['postal'] ?? '');
    $type_bien = $_POST['type'] ?? '';
    $nb_pieces = intval($_POST['rooms'] ?? 0);
    $surface = intval($_POST['surface'] ?? 0);
    $annee = intval($_POST['year'] ?? 0);
    $etat = $_POST['condition'] ?? '';
    $id_user = $_SESSION['user_id'] ?? null;

    if (empty($adresse) || empty($ville) || empty($code_postal) || empty($type_bien) || $surface <= 0) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        // Sauvegarder en base de données
        $stmt = $pdo->prepare("
            INSERT INTO estimations (adresse, ville, code_postal, type_bien, nb_pieces, surface, annee_construction, etat, id_user) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$adresse, $ville, $code_postal, $type_bien, $nb_pieces, $surface, $annee, $etat, $id_user]);

        // Calcul de l'estimation
        // Prix de base par m² selon la ville
        $prix_base = 3000; // prix par défaut
        $ville_lower = strtolower($ville);
        if (str_contains($ville_lower, 'paris')) $prix_base = 10000;
        elseif (str_contains($ville_lower, 'nice') || str_contains($ville_lower, 'cannes')) $prix_base = 5500;
        elseif (str_contains($ville_lower, 'lyon') || str_contains($ville_lower, 'bordeaux')) $prix_base = 4500;
        elseif (str_contains($ville_lower, 'marseille') || str_contains($ville_lower, 'toulouse')) $prix_base = 3500;
        else $prix_base = 3000;

        // Coefficient selon le type
        $coef_type = 1.0;
        if ($type_bien === 'villa') $coef_type = 1.3;
        elseif ($type_bien === 'maison') $coef_type = 1.1;
        elseif ($type_bien === 'appartement') $coef_type = 1.0;

        // Coefficient selon l'état
        $coef_etat = 1.0;
        if ($etat === 'neuf') $coef_etat = 1.2;
        elseif ($etat === 'excellent') $coef_etat = 1.1;
        elseif ($etat === 'bon') $coef_etat = 1.0;
        elseif ($etat === 'renover') $coef_etat = 0.75;

        // Coefficient selon l'année
        $annee_actuelle = 2026;
        $age = $annee_actuelle - $annee;
        $coef_age = 1.0;
        if ($age <= 5) $coef_age = 1.15;
        elseif ($age <= 15) $coef_age = 1.05;
        elseif ($age <= 30) $coef_age = 1.0;
        elseif ($age <= 50) $coef_age = 0.95;
        else $coef_age = 0.85;

        // Calcul final
        $estimation = $surface * $prix_base * $coef_type * $coef_etat * $coef_age;
        $estimation_min = round($estimation * 0.9 / 1000) * 1000;
        $estimation_max = round($estimation * 1.1 / 1000) * 1000;
        $estimation_moy = round($estimation / 1000) * 1000;

        // Rediriger vers la page résultat avec les données
        header("Location: estimation-result.php?min=$estimation_min&max=$estimation_max&moy=$estimation_moy&surface=$surface&ville=" . urlencode($ville) . "&type=$type_bien&etat=$etat");
        exit;
    }
}
?>
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
            <div class="logo">HSimo</div>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="acheter.php">Acheter</a></li>
                <li><a href="louer.php">Louer</a></li>
                <li><a href="vendre.php">Vendre</a></li>
                <li><a href="estimation.php" class="active">Estimation</a></li>
            </ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="messages.php" style="color:white; margin-right:1rem; font-family:'Outfit',sans-serif;">📬 Messages</a>
                <a href="logout.php" class="btn-login">Déconnexion</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Se connecter</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="form-page">
        <div class="form-container">
            <h1 class="form-title">Estimez votre maison !</h1>

            <?php if ($error): ?>
                <div style="background:#ff004f; color:white; padding:15px; border-radius:10px; margin-bottom:20px; text-align:center;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="estimation.php" class="estimation-form">
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
                <p>&copy; 2026 HSimo. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>