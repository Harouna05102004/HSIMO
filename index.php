<!DOCTYPE html>
<?php
session_start();
require_once 'config.php';
$isLoggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';

// Suppression d'un bien
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_bien']) && isset($_SESSION['user_id'])) {
    $id_del = intval($_POST['id_bien'] ?? 0);
    $stmt_del = $pdo->prepare("DELETE FROM bien WHERE id_bien = ? AND id_vendeur = ?");
    $stmt_del->execute([$id_del, $_SESSION['user_id']]);
    header('Location: index.php');
    exit;
}

// Recherche
$search = trim($_GET['q'] ?? '');
$type   = trim($_GET['type'] ?? '');
$prix_max = intval($_GET['prix_max'] ?? 0);

$where = [];
$params = [];

if ($search !== '') {
    $where[] = "(ville LIKE ? OR titre LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($type !== '') {
    // Recherche flexible : dans categorie OU dans titre
    $where[] = "(categorie = ? OR titre LIKE ?)";
    $params[] = $type;
    $params[] = "%$type%";
}
if ($prix_max > 0) {
    $where[] = "prix <= ?";
    $params[] = $prix_max;
}

$sql = "SELECT * FROM bien";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY date_creation DESC LIMIT 6";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$biens = $stmt->fetchAll();
?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HSimo - Trouvez votre maison de rêve</title>
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
                <a href="profil.php" style="color:white;margin-right:1rem;font-family:'Outfit',sans-serif;">👤 <?= htmlspecialchars($username) ?></a>
                <a href="logout.php" class="btn-login">Déconnexion</a>
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

            <form method="GET" action="index.php" class="search-form">
                <div class="search-bar-advanced">
                    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                           placeholder="Ville, quartier, titre..." class="search-input-main">
                    <select name="type" class="search-select">
                        <option value="">Tous types</option>
                        <option value="Vente" <?= $type === 'Vente' ? 'selected' : '' ?>>Vente</option>
                        <option value="Location" <?= $type === 'Location' ? 'selected' : '' ?>>Location</option>
                        <option value="Villa" <?= $type === 'Villa' ? 'selected' : '' ?>>Villa</option>
                    </select>
                    <input type="number" name="prix_max" value="<?= $prix_max ?: '' ?>"
                           placeholder="Budget max (€)" class="search-price" min="0">
                    <button type="submit" class="search-btn">🔍 Rechercher</button>
                </div>
            </form>
        </div>
    </section>

    <main class="container">
        <section class="featured-section">
            <?php if ($search || $type || $prix_max): ?>
                <h2 class="section-title">
                    Résultats de recherche
                    <span style="font-size:1.2rem;color:#888;display:block;margin-top:0.5rem;">
                        <?= count($biens) ?> bien<?= count($biens) > 1 ? 's' : '' ?> trouvé<?= count($biens) > 1 ? 's' : '' ?>
                        <?= $search ? ' pour "' . htmlspecialchars($search) . '"' : '' ?>
                    </span>
                </h2>
                <a href="index.php" style="display:inline-block;margin-bottom:2rem;color:#6b8ca8;">← Effacer la recherche</a>
            <?php else: ?>
                <h2 class="section-title">Nos coups de cœur</h2>
            <?php endif; ?>

            <?php if (count($biens) === 0): ?>
                <div style="text-align:center;padding:4rem;color:#888;">
                    <div style="font-size:4rem;margin-bottom:1rem;">🔍</div>
                    <p style="font-size:1.3rem;margin-bottom:1rem;">Aucun bien ne correspond à votre recherche.</p>
                    <a href="index.php" style="color:#d4a574;">Voir tous les biens →</a>
                </div>
            <?php else: ?>
                <div class="property-grid">
                    <?php foreach ($biens as $bien):
                        $photos = !empty($bien['photos']) ? explode(',', $bien['photos']) : [];
                    ?>
                    <div class="property-card">
                        <div class="property-image">
                            <?php if (!empty($photos)): ?>
                                <img src="uploads/<?= htmlspecialchars($photos[0]) ?>" alt="<?= htmlspecialchars($bien['titre']) ?>">
                            <?php else: ?>
                                <div style="width:100%;height:280px;background:#f0f4f8;display:flex;align-items:center;justify-content:center;font-size:3rem;">🏠</div>
                            <?php endif; ?>
                            <span class="property-badge"><?= htmlspecialchars($bien['categorie'] ?? 'Vente') ?></span>
                        </div>
                        <div class="property-info">
                            <h3 class="property-title"><?= htmlspecialchars($bien['titre']) ?></h3>
                            <p class="property-location">📍 <?= htmlspecialchars($bien['ville']) ?></p>
                            <div class="property-features">
                                <span>🛏️ <?= $bien['nb_chambres'] ?> chambres</span>
                                <span>🚿 <?= $bien['nb_salles_bain'] ?> sdb</span>
                                <span>📐 <?= $bien['surface'] ?> m²</span>
                            </div>
                            <div class="property-footer">
                                <span class="property-price"><?= number_format($bien['prix'], 0, ',', ' ') ?> €</span>
                                <div style="display:flex;gap:0.5rem;align-items:center;">
                                    <a href="detail.php?id=<?= $bien['id_bien'] ?>" class="btn-view">Voir plus</a>
                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $bien['id_vendeur']): ?>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette annonce ?')">
                                        <input type="hidden" name="delete_bien" value="1">
                                        <input type="hidden" name="id_bien" value="<?= $bien['id_bien'] ?>">
                                        <button type="submit" class="btn-delete-card">🗑️</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!$search && !$type && !$prix_max): ?>
                <div style="text-align:center;margin-top:3rem;">
                    <a href="acheter.php" style="background:#1a3a52;color:white;padding:1rem 2.5rem;border-radius:50px;font-family:'Outfit',sans-serif;font-weight:500;">Voir toutes les annonces →</a>
                </div>
            <?php endif; ?>
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
                <p>&copy; 2026 HSimo. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>