<?php
session_start();
require_once 'config.php';

// Suppression d'un bien
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_bien']) && isset($_SESSION['user_id'])) {
    $id_del = intval($_POST['id_bien'] ?? 0);
    $stmt_del = $pdo->prepare("DELETE FROM bien WHERE id_bien = ? AND id_vendeur = ?");
    $stmt_del->execute([$id_del, $_SESSION['user_id']]);
    header('Location: acheter.php');
    exit;
}

$filtre = $_GET['filtre'] ?? 'Toutes';
$allowed = ['Toutes', 'Maison', 'Villa', 'Appartement'];
if (!in_array($filtre, $allowed)) $filtre = 'Toutes';

if ($filtre === 'Toutes') {
    $stmt = $pdo->query("SELECT * FROM bien WHERE categorie = 'Vente' OR categorie IS NULL ORDER BY date_creation DESC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM bien WHERE (categorie = 'Vente' OR categorie IS NULL) AND (titre LIKE ? OR description LIKE ?) ORDER BY date_creation DESC");
    $stmt->execute(['%'.$filtre.'%', '%'.$filtre.'%']);
}
$biens = $stmt->fetchAll();

// Favoris de l'utilisateur connecté
$favoris_ids = [];
if (isset($_SESSION['user_id'])) {
    $stmt_fav = $pdo->prepare("SELECT id_bien FROM favoris WHERE id_user = ?");
    $stmt_fav->execute([$_SESSION['user_id']]);
    $favoris_ids = array_column($stmt_fav->fetchAll(), 'id_bien');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acheter - HSimo</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">HSimo</div>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="acheter.php" class="active">Acheter</a></li>
                <li><a href="louer.php">Louer</a></li>
                <li><a href="vendre.php">Vendre</a></li>
                <li><a href="estimation.php">Estimation</a></li>
            </ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="messages.php" style="color:white;margin-right:1rem;font-family:'Outfit',sans-serif;">📬 Messages</a>
                <a href="profil.php" style="color:white;margin-right:1rem;font-family:'Outfit',sans-serif;">👤 Mon profil</a>
                <a href="logout.php" class="btn-login">Déconnexion</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Se connecter</a>
            <?php endif; ?>
        </div>
    </nav>
    <main class="container listing-page">
        <div class="page-header">
            <h1 class="page-title">Achetez les plus belles maisons</h1>
            <div class="filter-tabs">
                <a href="acheter.php?filtre=Toutes" class="filter-tab <?= ($filtre==='Toutes')?'active':'' ?>">Toutes</a>
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
                            <span class="property-label"><?= htmlspecialchars($bien['categorie'] ?? 'Vente') ?></span>
                            <button type="button" class="btn-fav <?= in_array($bien['id_bien'], $favoris_ids) ? 'active' : '' ?>" 
                                        onclick="toggleFavoriCard(<?= $bien['id_bien'] ?>, this)"
                                        title="Ajouter aux favoris">
                                        <?= in_array($bien['id_bien'], $favoris_ids) ? '❤️' : '🤍' ?>
                                    </button>
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
                            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.5rem;">
                                <span class="sale-price"><?= number_format($bien['prix'], 0, ',', ' ') ?> €</span>
                                <div style="display:flex;gap:0.5rem;">
                                    <a href="detail.php?id=<?= $bien['id_bien'] ?>" class="btn-view">Voir plus</a>
                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $bien['id_vendeur']): ?>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette annonce ?')">
                                        <input type="hidden" name="delete_bien" value="1">
                                        <input type="hidden" name="id_bien" value="<?= $bien['id_bien'] ?>">
                                        <button type="submit" class="btn-delete-card">🗑️ Supprimer</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
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
    <script>
    function toggleFavoriCard(idBien, btn) {
        const formData = new FormData();
        formData.append('id_bien', idBien);

        fetch('toggle-favori.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.error === 'non_connecte') {
                window.location.href = 'login.php';
                return;
            }
            if (data.status === 'added') {
                btn.innerHTML = '❤️';
                btn.classList.add('active');
            } else {
                btn.innerHTML = '🤍';
                btn.classList.remove('active');
            }
        })
        .catch(err => console.error('Erreur:', err));
    }
    </script>
</body>
</html>