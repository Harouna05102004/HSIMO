<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id_user = $_SESSION['user_id'];

// Mes annonces
$stmt = $pdo->prepare("SELECT * FROM bien WHERE id_vendeur = ? ORDER BY date_creation DESC");
$stmt->execute([$id_user]);
$mes_annonces = $stmt->fetchAll();

// Mes favoris
$stmt = $pdo->prepare("
    SELECT b.*, f.date_ajout 
    FROM favoris f 
    JOIN bien b ON f.id_bien = b.id_bien 
    WHERE f.id_user = ? 
    ORDER BY f.date_ajout DESC
");
$stmt->execute([$id_user]);
$mes_favoris = $stmt->fetchAll();

// Nombre de messages non lus
$stmt = $pdo->prepare("
    SELECT COUNT(*) as nb 
    FROM messages m 
    JOIN bien b ON m.id_bien = b.id_bien 
    WHERE b.id_vendeur = ? AND m.lu = 0
");
$stmt->execute([$id_user]);
$nb_messages = $stmt->fetch()['nb'];

// Supprimer une annonce
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_bien'])) {
    $id_bien = intval($_POST['id_bien'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM bien WHERE id_bien = ? AND id_vendeur = ?");
    $stmt->execute([$id_bien, $id_user]);
    header('Location: profil.php?tab=annonces&deleted=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - HSimo</title>
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
                <li><a href="estimation.php">Estimation</a></li>
            </ul>
            <a href="messages.php" style="color:white;margin-right:1rem;font-family:'Outfit',sans-serif;position:relative;">
                📬 Messages
                <?php if ($nb_messages > 0): ?>
                    <span class="badge-notif"><?= $nb_messages ?></span>
                <?php endif; ?>
            </a>
            <a href="logout.php" class="btn-login">Déconnexion</a>
        </div>
    </nav>

    <main style="max-width:1300px;margin:0 auto;padding:4rem 3rem;min-height:80vh;">

        <!-- En-tête profil -->
        <div class="profil-header">
            <div class="profil-avatar">
                <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
            </div>
            <div class="profil-info">
                <h1 class="profil-username"><?= htmlspecialchars($_SESSION['username']) ?></h1>
                <p class="profil-meta">
                    <span>🏠 <?= count($mes_annonces) ?> annonce<?= count($mes_annonces) > 1 ? 's' : '' ?></span>
                    <span>❤️ <?= count($mes_favoris) ?> favori<?= count($mes_favoris) > 1 ? 's' : '' ?></span>
                    <?php if ($nb_messages > 0): ?>
                        <span>📬 <?= $nb_messages ?> message<?= $nb_messages > 1 ? 's' : '' ?> non lu<?= $nb_messages > 1 ? 's' : '' ?></span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="profil-actions">
                <a href="vendre.php" class="profil-btn-primary">+ Publier une annonce</a>
                <a href="messages.php" class="profil-btn-secondary">📬 Mes messages</a>
            </div>
        </div>

        <?php if (isset($_GET['deleted'])): ?>
            <div style="background:#10b981;color:white;padding:1rem 1.5rem;border-radius:10px;margin-bottom:2rem;text-align:center;">
                ✅ Annonce supprimée avec succès.
            </div>
        <?php endif; ?>

        <!-- Onglets -->
        <div class="profil-tabs" style="margin-bottom:2.5rem;gap:0.5rem;">
            <button style="padding:1.2rem 2.5rem;font-size:1.05rem;" class="profil-tab <?= ($_GET['tab'] ?? 'annonces') === 'annonces' ? 'active' : '' ?>" onclick="showTab('annonces')">
                🏠 Mes annonces <span class="tab-count"><?= count($mes_annonces) ?></span>
            </button>
            <button style="padding:1.2rem 2.5rem;font-size:1.05rem;" class="profil-tab <?= ($_GET['tab'] ?? '') === 'favoris' ? 'active' : '' ?>" onclick="showTab('favoris')">
                ❤️ Mes favoris <span class="tab-count"><?= count($mes_favoris) ?></span>
            </button>
        </div>

        <!-- Onglet Annonces -->
        <div id="tab-annonces" class="tab-content <?= ($_GET['tab'] ?? 'annonces') !== 'favoris' ? 'active' : '' ?>">
            <?php if (count($mes_annonces) === 0): ?>
                <div class="empty-state">
                    <div class="empty-icon">🏡</div>
                    <h3>Vous n'avez pas encore d'annonces</h3>
                    <p>Commencez par publier votre premier bien immobilier.</p>
                    <a href="vendre.php" class="profil-btn-primary" style="display:inline-block;margin-top:1.5rem;">Publier une annonce</a>
                </div>
            <?php else: ?>
                <div class="profil-grid" style="gap:2.5rem;margin-top:1.5rem;">
                    <?php foreach ($mes_annonces as $bien): ?>
                        <?php
                        $photos = !empty($bien['photos']) ? explode(',', $bien['photos']) : [];
                        ?>
                        <div class="profil-card">
                            <div class="profil-card-img" style="height:240px;">
                                <?php if (!empty($photos)): ?>
                                    <img src="uploads/<?= htmlspecialchars($photos[0]) ?>" alt="<?= htmlspecialchars($bien['titre']) ?>">
                                <?php else: ?>
                                    <div class="profil-card-no-img">🏠</div>
                                <?php endif; ?>
                                <span class="profil-card-badge"><?= htmlspecialchars($bien['categorie'] ?? 'Vente') ?></span>
                            </div>
                            <div class="profil-card-body" style="padding:2rem;">
                                <h3><?= htmlspecialchars($bien['titre']) ?></h3>
                                <p class="profil-card-loc">📍 <?= htmlspecialchars($bien['ville']) ?></p>
                                <div class="profil-card-features">
                                    <span>🛏️ <?= $bien['nb_chambres'] ?></span>
                                    <span>🚿 <?= $bien['nb_salles_bain'] ?></span>
                                    <span>📐 <?= $bien['surface'] ?> m²</span>
                                </div>
                                <p class="profil-card-price"><?= number_format($bien['prix'], 0, ',', ' ') ?> €</p>
                                <p class="profil-card-date">Publié le <?= date('d/m/Y', strtotime($bien['date_creation'])) ?></p>
                                <div class="profil-card-actions">
                                    <a href="detail.php?id=<?= $bien['id_bien'] ?>" class="profil-btn-outline">Voir</a>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette annonce ?')">
                                        <input type="hidden" name="delete_bien" value="1">
                                        <input type="hidden" name="id_bien" value="<?= $bien['id_bien'] ?>">
                                        <button type="submit" class="profil-btn-danger">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Onglet Favoris -->
        <div id="tab-favoris" class="tab-content <?= ($_GET['tab'] ?? '') === 'favoris' ? 'active' : '' ?>">
            <?php if (count($mes_favoris) === 0): ?>
                <div class="empty-state">
                    <div class="empty-icon">❤️</div>
                    <h3>Vous n'avez pas encore de favoris</h3>
                    <p>Ajoutez des biens à vos favoris en cliquant sur le cœur sur les fiches.</p>
                    <a href="acheter.php" class="profil-btn-primary" style="display:inline-block;margin-top:1.5rem;">Voir les annonces</a>
                </div>
            <?php else: ?>
                <div class="profil-grid" style="gap:2.5rem;margin-top:1.5rem;">
                    <?php foreach ($mes_favoris as $bien): ?>
                        <?php
                        $photos = !empty($bien['photos']) ? explode(',', $bien['photos']) : [];
                        ?>
                        <div class="profil-card" id="favori-card-<?= $bien['id_bien'] ?>">
                            <div class="profil-card-img" style="height:240px;">
                                <?php if (!empty($photos)): ?>
                                    <img src="uploads/<?= htmlspecialchars($photos[0]) ?>" alt="<?= htmlspecialchars($bien['titre']) ?>">
                                <?php else: ?>
                                    <div class="profil-card-no-img">🏠</div>
                                <?php endif; ?>
                                <button class="btn-fav active" 
                                        onclick="toggleFavoriProfil(<?= $bien['id_bien'] ?>, this)"
                                        title="Retirer des favoris">❤️</button>
                            </div>
                            <div class="profil-card-body" style="padding:2rem;">
                                <h3><?= htmlspecialchars($bien['titre']) ?></h3>
                                <p class="profil-card-loc">📍 <?= htmlspecialchars($bien['ville']) ?></p>
                                <div class="profil-card-features">
                                    <span>🛏️ <?= $bien['nb_chambres'] ?></span>
                                    <span>🚿 <?= $bien['nb_salles_bain'] ?></span>
                                    <span>📐 <?= $bien['surface'] ?> m²</span>
                                </div>
                                <p class="profil-card-price"><?= number_format($bien['prix'], 0, ',', ' ') ?> €</p>
                                <p class="profil-card-date">Ajouté le <?= date('d/m/Y', strtotime($bien['date_ajout'])) ?></p>
                                <div class="profil-card-actions">
                                    <a href="detail.php?id=<?= $bien['id_bien'] ?>" class="profil-btn-outline">Voir l'annonce</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section"><p>&copy; 2026 HSimo. Tous droits réservés.</p></div>
        </div>
    </footer>

    <script>
    function showTab(tab) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.profil-tab').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + tab).classList.add('active');
        event.currentTarget.classList.add('active');
        history.replaceState(null, '', '?tab=' + tab);
    }

    function toggleFavoriProfil(idBien, btn) {
        const formData = new FormData();
        formData.append('id_bien', idBien);

        fetch('toggle-favori.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'removed') {
                const card = document.getElementById('favori-card-' + idBien);
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                card.style.transition = 'all 0.3s ease';
                setTimeout(() => card.remove(), 300);
            }
        })
        .catch(err => console.error('Erreur:', err));
    }
    </script>
</body>
</html>