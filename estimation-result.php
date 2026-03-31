<?php
session_start();

// Récupérer les données de l'estimation
$min = intval($_GET['min'] ?? 0);
$max = intval($_GET['max'] ?? 0);
$moy = intval($_GET['moy'] ?? 0);
$surface = intval($_GET['surface'] ?? 0);
$ville = $_GET['ville'] ?? '';
$type = $_GET['type'] ?? '';
$etat = $_GET['etat'] ?? '';

// Si pas de données, rediriger
if ($moy === 0) {
    header('Location: estimation.php');
    exit;
}

$prix_m2 = $surface > 0 ? round($moy / $surface) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat de l'estimation - HSimo</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .result-page {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
        }
        .result-container {
            max-width: 700px;
            width: 100%;
            text-align: center;
        }
        .result-title {
            font-family: 'Playfair Display', serif;
            color: #1a3a52;
            font-size: 2rem;
            margin-bottom: 2rem;
        }
        .estimation-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .estimation-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .estimation-price {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            color: #d4a574;
            font-weight: 700;
            margin: 1rem 0;
        }
        .estimation-range {
            color: #888;
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }
        .estimation-details {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin: 1.5rem 0;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 15px;
        }
        .detail-item {
            text-align: center;
        }
        .detail-label {
            color: #888;
            font-size: 0.85rem;
            display: block;
            margin-bottom: 0.25rem;
        }
        .detail-value {
            color: #1a3a52;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .disclaimer {
            color: #888;
            font-size: 0.85rem;
            margin: 1rem 0;
            font-style: italic;
        }
        .btn-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        .btn-primary {
            background: #1a3a52;
            color: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-family: 'Outfit', sans-serif;
            font-weight: 500;
            transition: opacity 0.3s;
        }
        .btn-primary:hover { opacity: 0.85; }
        .btn-secondary {
            background: #d4a574;
            color: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-family: 'Outfit', sans-serif;
            font-weight: 500;
            transition: opacity 0.3s;
        }
        .btn-secondary:hover { opacity: 0.85; }
    </style>
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

    <main class="result-page">
        <div class="result-container">
            <h1 class="result-title">Résultat de votre estimation</h1>

            <div class="estimation-card">
                <div class="estimation-icon">🏠</div>

                <p style="color:#888; margin-bottom:0.5rem;">Estimation pour votre <?= htmlspecialchars($type) ?> à <strong><?= htmlspecialchars($ville) ?></strong></p>

                <div class="estimation-price">
                    <?= number_format($moy, 0, ',', ' ') ?> €
                </div>

                <div class="estimation-range">
                    Fourchette estimée : <strong><?= number_format($min, 0, ',', ' ') ?> €</strong> — <strong><?= number_format($max, 0, ',', ' ') ?> €</strong>
                </div>

                <div class="estimation-details">
                    <div class="detail-item">
                        <span class="detail-label">Prix au m²</span>
                        <span class="detail-value"><?= number_format($prix_m2, 0, ',', ' ') ?> €/m²</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Surface</span>
                        <span class="detail-value"><?= $surface ?> m²</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Type</span>
                        <span class="detail-value"><?= ucfirst(htmlspecialchars($type)) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">État</span>
                        <span class="detail-value"><?= ucfirst(htmlspecialchars($etat)) ?></span>
                    </div>
                </div>

                <p class="disclaimer">
                    ⚠️ Cette estimation est indicative et basée sur les données du marché. 
                    Un expert immobilier vous contactera pour affiner cette évaluation.
                </p>
            </div>

            <div class="btn-actions">
                <a href="estimation.php" class="btn-primary">🔄 Nouvelle estimation</a>
                <a href="vendre.php" class="btn-secondary">🏷️ Mettre en vente</a>
                <a href="index.php" class="btn-primary">🏠 Retour à l'accueil</a>
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
                <p>&copy; 2026 HSimo. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>