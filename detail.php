<?php
session_start();
require_once 'config.php';

// Récupérer l'ID du bien
$id = intval($_GET['id'] ?? 0);

if ($id === 0) {
    header('Location: acheter.php');
    exit;
}

// Récupérer le bien depuis la base de données
$stmt = $pdo->prepare("SELECT * FROM bien WHERE id_bien = ?");
$stmt->execute([$id]);
$bien = $stmt->fetch();

if (!$bien) {
    header('Location: acheter.php');
    exit;
}

// Gestion du formulaire de contact
$contact_success = '';
$contact_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact'])) {
    if (!isset($_SESSION['user_id'])) {
        $contact_error = "Vous devez être connecté pour contacter le vendeur.";
    } else {
        $message = trim($_POST['message'] ?? '');
        if (empty($message)) {
            $contact_error = "Veuillez écrire un message.";
        } else {
            // Sauvegarder le message en base de données
            $stmt_msg = $pdo->prepare("
                INSERT INTO messages (id_bien, id_expediteur, message) 
                VALUES (?, ?, ?)
            ");
            if ($stmt_msg->execute([$id, $_SESSION['user_id'], $message])) {
                $contact_success = "Votre message a été envoyé au vendeur !";
            } else {
                $contact_error = "Erreur lors de l'envoi du message.";
            }
        }
    }
}

// Récupérer les photos
$photos = [];
if (!empty($bien['photos'])) {
    $photos = explode(',', $bien['photos']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($bien['titre']) ?> - HSimo</title>
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="mes-messages.php" style="color:white; margin-right:1rem; font-family:'Outfit',sans-serif;">📬 Messages</a>
                <a href="logout.php" class="btn-login">Déconnexion</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Se connecter</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="detail-page">
        <div class="detail-container">

            <!-- IMAGE -->
            <div class="detail-image">
                <?php if (!empty($photos)): ?>
                    <img src="uploads/<?= htmlspecialchars($photos[0]) ?>" 
                         alt="<?= htmlspecialchars($bien['titre']) ?>"
                         style="width:100%; height:600px; object-fit:cover;">
                    
                    <!-- Galerie si plusieurs photos -->
                    <?php if (count($photos) > 1): ?>
                        <div style="display:flex; gap:10px; margin-top:10px; overflow-x:auto; padding:5px;">
                            <?php foreach ($photos as $photo): ?>
                                <img src="uploads/<?= htmlspecialchars($photo) ?>" 
                                     alt="Photo"
                                     style="width:100px; height:70px; object-fit:cover; border-radius:8px; cursor:pointer; flex-shrink:0;"
                                     onclick="document.querySelector('.detail-image img').src=this.src">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="width:100%; height:600px; background:#f0f4f8; display:flex; align-items:center; justify-content:center; border-radius:20px;">
                        <span style="font-size:4rem;">🏠</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- INFOS -->
            <div class="detail-content">
                <h1 class="detail-title"><?= htmlspecialchars($bien['titre']) ?></h1>
                <p class="detail-location">📍 <?= htmlspecialchars($bien['ville']) ?><?= !empty($bien['adresse']) ? ' - ' . htmlspecialchars($bien['adresse']) : '' ?></p>

                <div class="detail-features">
                    <div class="feature-item">
                        <span class="feature-icon">🛏️</span>
                        <span class="feature-text"><?= $bien['nb_chambres'] ?> chambres</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">🚿</span>
                        <span class="feature-text"><?= $bien['nb_salles_bain'] ?> salles de bain</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">📐</span>
                        <span class="feature-text"><?= $bien['surface'] ?> m²</span>
                    </div>
                </div>

                <p class="detail-price"><?= number_format($bien['prix'], 0, ',', ' ') ?> €</p>

                <div class="detail-description">
                    <h2>Description</h2>
                    <p><?= nl2br(htmlspecialchars($bien['description'])) ?></p>
                </div>

                <!-- FORMULAIRE CONTACT -->
                <div style="margin-top:2rem; background:#f8f9fa; padding:2rem; border-radius:15px;">
                    <h3 style="font-family:'Playfair Display',serif; color:#1a3a52; margin-bottom:1rem;">
                        Contacter le vendeur
                    </h3>

                    <?php if ($contact_success): ?>
                        <div style="background:#10b981; color:white; padding:15px; border-radius:10px; margin-bottom:15px; text-align:center;">
                            ✅ <?= htmlspecialchars($contact_success) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($contact_error): ?>
                        <div style="background:#ff004f; color:white; padding:15px; border-radius:10px; margin-bottom:15px; text-align:center;">
                            ❌ <?= htmlspecialchars($contact_error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="contact" value="1">
                            <div class="form-group" style="margin-bottom:1rem;">
                                <label style="font-weight:500; display:block; margin-bottom:0.5rem;">Votre message</label>
                                <textarea name="message" rows="4" 
                                          placeholder="Bonjour, je suis intéressé par ce bien..."
                                          style="width:100%; padding:1rem; border:2px solid #e0e0e0; border-radius:10px; font-family:'Outfit',sans-serif; resize:vertical; box-sizing:border-box;"
                                          required></textarea>
                            </div>
                            <button type="submit" class="btn-contact" style="width:100%;">
                                Envoyer le message
                            </button>
                        </form>
                    <?php else: ?>
                        <div style="text-align:center; padding:1rem;">
                            <p style="color:#666; margin-bottom:1rem;">Connectez-vous pour contacter le vendeur</p>
                            <a href="login.php" class="btn-contact">Se connecter</a>
                        </div>
                    <?php endif; ?>
                </div>

                <p style="color:#888; font-size:0.85rem; margin-top:1rem;">
                    Publié le <?= date('d/m/Y', strtotime($bien['date_creation'])) ?>
                </p>
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