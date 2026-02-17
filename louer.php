<?php
session_start();
require_once 'config.php';

$stmt = $pdo->query("SELECT * FROM bien ORDER BY date_creation DESC");
$biens = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Louer - HSimo</title>
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
                <li><a href="louer.php" class="active">Louer</a></li>
                <li><a href="vendre.php">Vendre</a></li>
                <li><a href="estimation.php">Estimation</a></li>
            </ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="messages.php" style="color:white;margin-right:1rem;font-family:'Outfit',sans-serif;">📬 Messages</a>
                <a href="logout.php" class="btn-login">Déconnexion</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Se connecter</a>
            <?php endif; ?>
        </div>
    </nav>
    <main class="container listing-page">
        <div class="page-header">
            <h1 class="page-title">Louez les plus belles maisons</h1>
            <div class="filter-tabs">
                <button class="filter-tab active">Toutes</button>
                <button class="filter-tab">Moderne</button>
                <button class="filter-tab">Rustique</button>
                <button class="filter-tab">Appartement</button>
            </div>
        </div>
        <div class="property-grid-large">
            <?php if (count($biens) === 0): ?>
                <div style="text-align:center;padding:3rem;color:#666;grid-column:1/-1;">
                    <p style="font-size:1.5rem;">Aucune annonce disponible pour le moment.</p>
                    <a href="vendre.php" style="color:#d4a574;">Publier une annonce →</a>
                </div>
            <?php else: ?>
                <?php foreach ($biens as $bien): ?>
                    <div class="property-card-large">
                        <div class="property-image-large">
                            <?php if (!empty($bien['photos'])):
                                $photos = explode(',', $bien['photos']); ?>
                                <img src="uploads/<?= htmlspecialchars($photos[0]) ?>" alt="<?= htmlspecialchars($bien['titre']) ?>">
                            <?php else: ?>
                                <div style="width:100%;height:250px;background:#f0f4f8;display:flex;align-items:center;justify-content:center;font-size:3rem;">🏠</div>
                            <?php endif; ?>
                            <span class="property-label"><?= htmlspecialchars($bien['categorie'] ?? 'Location') ?></span>
                        </div>
                        <div class="property-details">
                            <h3><?= htmlspecialchars($bien['titre']) ?></h3>
                            <p class="location">📍 <?= htmlspecialchars($bien['ville']) ?></p>
                            <div class="property-features" style="display:flex;gap:1rem;margin:1rem 0;flex-wrap:wrap;">
                                <span>🛏️ <?= $bien['nb_chambres'] ?> chambres</span>
                                <span>🚿 <?= $bien['nb_salles_bain'] ?> sdb</span>
                                <span>📐 <?= $bien['surface'] ?> m²</span>
                            </div>
                            <p style="color:#666;margin-bottom:1rem;font-size:0.95rem;"><?= htmlspecialchars(substr($bien['description'], 0, 100)) ?>...</p>
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span class="rental-price"><?= number_format($bien['prix'], 0, ',', ' ') ?> € /mois</span>
                                <a href="detail.php?id=<?= $bien['id_bien'] ?>" class="btn-view">Voir plus</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section"><p>&copy; 2026 HSimo. Tous droits réservés.</p></div>
        </div>
    </footer>
</body>
</html>