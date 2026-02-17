<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT m.*, b.titre as titre_bien, u.username as expediteur
    FROM messages m
    JOIN bien b ON m.id_bien = b.id_bien
    JOIN users u ON m.id_expediteur = u.id
    WHERE b.id_vendeur = ?
    ORDER BY m.date_envoi DESC
");
$stmt->execute([$_SESSION['user_id']]);
$messages_recus = $stmt->fetchAll();

$stmt2 = $pdo->prepare("
    SELECT m.*, b.titre as titre_bien
    FROM messages m
    JOIN bien b ON m.id_bien = b.id_bien
    WHERE m.id_expediteur = ?
    ORDER BY m.date_envoi DESC
");
$stmt2->execute([$_SESSION['user_id']]);
$messages_envoyes = $stmt2->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Messages - HSimo</title>
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
            <a href="messages.php" style="color:white;margin-right:1rem;font-family:'Outfit',sans-serif;">📬 Messages</a>
            <a href="logout.php" class="btn-login">Déconnexion</a>
        </div>
    </nav>
    <main class="container" style="padding:3rem 2rem;max-width:900px;margin:0 auto;">
        <h1 style="font-family:'Playfair Display',serif;color:#1a3a52;margin-bottom:2rem;">📬 Mes Messages</h1>

        <h2 style="color:#1a3a52;margin-bottom:1rem;font-size:1.5rem;border-bottom:2px solid #d4a574;padding-bottom:0.5rem;">
            📥 Messages reçus (<?= count($messages_recus) ?>)
        </h2>
        <?php if (count($messages_recus) === 0): ?>
            <div style="background:#f8f9fa;padding:2rem;border-radius:15px;text-align:center;margin-bottom:2rem;">
                <p style="color:#888;">Aucun message reçu pour le moment.</p>
            </div>
        <?php else: ?>
            <?php foreach ($messages_recus as $msg): ?>
                <div style="background:white;padding:1.5rem;border-radius:15px;margin-bottom:1rem;box-shadow:0 2px 10px rgba(0,0,0,0.08);<?= $msg['lu'] == 0 ? 'border-left:4px solid #d4a574;' : 'border-left:4px solid #e0e0e0;' ?>">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
                        <strong style="color:#1a3a52;"><?= $msg['lu'] == 0 ? '🔵 ' : '✅ ' ?>De : <?= htmlspecialchars($msg['expediteur']) ?></strong>
                        <span style="color:#888;font-size:0.85rem;"><?= date('d/m/Y à H:i', strtotime($msg['date_envoi'])) ?></span>
                    </div>
                    <p style="color:#d4a574;font-size:0.9rem;margin-bottom:0.75rem;">
                        🏠 <a href="detail.php?id=<?= $msg['id_bien'] ?>" style="color:#d4a574;text-decoration:underline;"><?= htmlspecialchars($msg['titre_bien']) ?></a>
                    </p>
                    <p style="color:#333;line-height:1.6;"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <h2 style="color:#1a3a52;margin:2rem 0 1rem;font-size:1.5rem;border-bottom:2px solid #d4a574;padding-bottom:0.5rem;">
            📤 Messages envoyés (<?= count($messages_envoyes) ?>)
        </h2>
        <?php if (count($messages_envoyes) === 0): ?>
            <div style="background:#f8f9fa;padding:2rem;border-radius:15px;text-align:center;">
                <p style="color:#888;">Vous n'avez pas encore envoyé de messages.</p>
            </div>
        <?php else: ?>
            <?php foreach ($messages_envoyes as $msg): ?>
                <div style="background:white;padding:1.5rem;border-radius:15px;margin-bottom:1rem;box-shadow:0 2px 10px rgba(0,0,0,0.08);border-left:4px solid #1a3a52;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
                        <strong style="color:#1a3a52;">🏠 <a href="detail.php?id=<?= $msg['id_bien'] ?>" style="color:#1a3a52;text-decoration:underline;"><?= htmlspecialchars($msg['titre_bien']) ?></a></strong>
                        <span style="color:#888;font-size:0.85rem;"><?= date('d/m/Y à H:i', strtotime($msg['date_envoi'])) ?></span>
                    </div>
                    <p style="color:#333;line-height:1.6;"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section"><p>&copy; 2026 HSimo. Tous droits réservés.</p></div>
        </div>
    </footer>
</body>
</html>