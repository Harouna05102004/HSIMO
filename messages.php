<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id_user = $_SESSION['user_id'];
$success = '';
$error   = '';

// ── Ajouter colonnes si elles n'existent pas ──
try {
    $pdo->exec("ALTER TABLE messages ADD COLUMN id_destinataire INT DEFAULT NULL");
} catch (Exception $e) {}
try {
    $pdo->exec("ALTER TABLE messages ADD COLUMN id_parent INT DEFAULT NULL");
} catch (Exception $e) {}

// ── Répondre à un message ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['repondre'])) {
    $id_bien        = intval($_POST['id_bien'] ?? 0);
    $id_destinataire = intval($_POST['id_destinataire'] ?? 0);
    $id_parent      = intval($_POST['id_parent'] ?? 0);
    $message        = trim($_POST['message'] ?? '');

    if (empty($message)) {
        $error = "Le message ne peut pas être vide.";
    } elseif ($id_bien === 0 || $id_destinataire === 0) {
        $error = "Données invalides.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO messages (id_bien, id_expediteur, id_destinataire, id_parent, message) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$id_bien, $id_user, $id_destinataire, $id_parent ?: null, $message])) {
            $success = "Réponse envoyée !";
        } else {
            $error = "Erreur lors de l'envoi.";
        }
    }
}

// ── Marquer comme lu ──
if (isset($_GET['mark_read'])) {
    $id_msg = intval($_GET['mark_read']);
    $pdo->prepare("UPDATE messages m JOIN bien b ON m.id_bien = b.id_bien SET m.lu = 1 WHERE m.id_message = ? AND b.id_vendeur = ?")->execute([$id_msg, $id_user]);
}

// ── Messages reçus (vendeur) ──
$stmt = $pdo->prepare("
    SELECT m.*, b.titre as titre_bien, u.username as expediteur_nom, u.id as expediteur_id
    FROM messages m
    JOIN bien b ON m.id_bien = b.id_bien
    JOIN users u ON m.id_expediteur = u.id
    WHERE b.id_vendeur = ? AND m.id_expediteur != ?
    ORDER BY m.date_envoi DESC
");
$stmt->execute([$id_user, $id_user]);
$messages_recus = $stmt->fetchAll();

// ── Messages envoyés ──
$stmt2 = $pdo->prepare("
    SELECT m.*, b.titre as titre_bien,
           u.username as destinataire_nom
    FROM messages m
    JOIN bien b ON m.id_bien = b.id_bien
    LEFT JOIN users u ON m.id_destinataire = u.id
    WHERE m.id_expediteur = ?
    ORDER BY m.date_envoi DESC
");
$stmt2->execute([$id_user]);
$messages_envoyes = $stmt2->fetchAll();

// ── Conversations (réponses reçues à mes messages) ──
$stmt3 = $pdo->prepare("
    SELECT m.*, b.titre as titre_bien, u.username as expediteur_nom
    FROM messages m
    JOIN bien b ON m.id_bien = b.id_bien
    JOIN users u ON m.id_expediteur = u.id
    WHERE m.id_destinataire = ?
    ORDER BY m.date_envoi DESC
");
$stmt3->execute([$id_user]);
$reponses_recues = $stmt3->fetchAll();

$nb_non_lus = count(array_filter($messages_recus, fn($m) => $m['lu'] == 0));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Messages - HSimo</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .msg-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            transition: all 0.2s;
        }
        .msg-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.12); }
        .msg-unread { border-left: 4px solid #d4a574; }
        .msg-read   { border-left: 4px solid #e0e0e0; }
        .msg-sent   { border-left: 4px solid #1a3a52; }
        .msg-reply  { border-left: 4px solid #10b981; }

        .msg-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.6rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .msg-from   { font-weight: 600; color: #1a3a52; }
        .msg-date   { color: #aaa; font-size: 0.82rem; }
        .msg-bien   { color: #d4a574; font-size: 0.88rem; margin-bottom: 0.75rem; }
        .msg-body   { color: #333; line-height: 1.7; margin-bottom: 1rem; }

        .btn-reply {
            background: #1a3a52;
            color: white;
            border: none;
            padding: 0.55rem 1.2rem;
            border-radius: 25px;
            font-size: 0.85rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-reply:hover { background: #d4a574; }

        .btn-mark-read {
            background: transparent;
            color: #888;
            border: 1px solid #ddd;
            padding: 0.45rem 1rem;
            border-radius: 25px;
            font-size: 0.82rem;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-mark-read:hover { background: #f0f0f0; }

        .reply-form {
            display: none;
            margin-top: 1rem;
            padding: 1.2rem;
            background: #f8f9fa;
            border-radius: 10px;
            animation: fadeIn 0.2s ease;
        }
        .reply-form.open { display: block; }

        @keyframes fadeIn {
            from { opacity:0; transform:translateY(-5px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .reply-form textarea {
            width: 100%;
            padding: 0.9rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.95rem;
            resize: vertical;
            outline: none;
            margin-bottom: 0.75rem;
            box-sizing: border-box;
        }
        .reply-form textarea:focus { border-color: #6b8ca8; }

        .msg-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e0e0e0;
        }
        .msg-tab {
            background: none;
            border: none;
            padding: 1rem 1.5rem;
            font-family: 'Outfit', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            color: #888;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
        }
        .msg-tab:hover { color: #1a3a52; }
        .msg-tab.active { color: #1a3a52; border-bottom-color: #d4a574; }
        .msg-tab-count {
            background: #e0e0e0;
            padding: 0.15rem 0.55rem;
            border-radius: 10px;
            font-size: 0.75rem;
            margin-left: 0.4rem;
        }
        .msg-tab.active .msg-tab-count { background: #d4a574; color: white; }

        .tab-pane { display: none; }
        .tab-pane.active { display: block; }

        .empty-msg {
            text-align: center;
            padding: 3rem;
            background: #f8f9fa;
            border-radius: 15px;
            color: #888;
        }
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
                <li><a href="estimation.php">Estimation</a></li>
            </ul>
            <a href="profil.php" style="color:white;margin-right:1rem;font-family:'Outfit',sans-serif;">👤 Mon profil</a>
            <a href="logout.php" class="btn-login">Déconnexion</a>
        </div>
    </nav>

    <main class="container" style="max-width:900px;margin:0 auto;padding:3rem 2rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <h1 style="font-family:'Playfair Display',serif;color:#1a3a52;">📬 Mes Messages</h1>
            <?php if ($nb_non_lus > 0): ?>
                <span style="background:#e05a7a;color:white;padding:0.5rem 1.2rem;border-radius:50px;font-size:0.9rem;">
                    <?= $nb_non_lus ?> non lu<?= $nb_non_lus > 1 ? 's' : '' ?>
                </span>
            <?php endif; ?>
        </div>

        <?php if ($success): ?>
            <div style="background:#d1fae5;border:1px solid #10b981;color:#065f46;padding:1rem 1.5rem;border-radius:10px;margin-bottom:1.5rem;">
                ✅ <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div style="background:#fee2e2;border:1px solid #e05a7a;color:#b91c1c;padding:1rem 1.5rem;border-radius:10px;margin-bottom:1.5rem;">
                ❌ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Onglets -->
        <div class="msg-tabs">
            <button class="msg-tab active" onclick="showTab('recus', this)">
                📥 Reçus <span class="msg-tab-count"><?= count($messages_recus) ?></span>
            </button>
            <button class="msg-tab" onclick="showTab('envoyes', this)">
                📤 Envoyés <span class="msg-tab-count"><?= count($messages_envoyes) ?></span>
            </button>
            <button class="msg-tab" onclick="showTab('reponses', this)">
                💬 Réponses reçues <span class="msg-tab-count"><?= count($reponses_recues) ?></span>
            </button>
        </div>

        <!-- Messages reçus -->
        <div id="tab-recus" class="tab-pane active">
            <?php if (count($messages_recus) === 0): ?>
                <div class="empty-msg">
                    <div style="font-size:3rem;margin-bottom:1rem;">📭</div>
                    <p>Aucun message reçu pour le moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages_recus as $msg): ?>
                    <div class="msg-card <?= $msg['lu'] == 0 ? 'msg-unread' : 'msg-read' ?>">
                        <div class="msg-header">
                            <span class="msg-from">
                                <?= $msg['lu'] == 0 ? '🔵' : '✅' ?>
                                De : <strong><?= htmlspecialchars($msg['expediteur_nom']) ?></strong>
                            </span>
                            <span class="msg-date"><?= date('d/m/Y à H:i', strtotime($msg['date_envoi'])) ?></span>
                        </div>
                        <p class="msg-bien">
                            🏠 <a href="detail.php?id=<?= $msg['id_bien'] ?>" style="color:#d4a574;text-decoration:underline;">
                                <?= htmlspecialchars($msg['titre_bien']) ?>
                            </a>
                        </p>
                        <p class="msg-body"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                            <button class="btn-reply" type="button"
                                onclick="toggleReply('reply-<?= $msg['id_message'] ?>')">
                                💬 Répondre
                            </button>
                            <?php if ($msg['lu'] == 0): ?>
                                <a href="messages.php?mark_read=<?= $msg['id_message'] ?>" class="btn-mark-read">
                                    ✔ Marquer comme lu
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Formulaire de réponse -->
                        <div class="reply-form" id="reply-<?= $msg['id_message'] ?>">
                            <p style="font-size:0.85rem;color:#888;margin-bottom:0.75rem;">
                                Répondre à <strong><?= htmlspecialchars($msg['expediteur_nom']) ?></strong> concernant <em><?= htmlspecialchars($msg['titre_bien']) ?></em>
                            </p>
                            <form method="POST">
                                <input type="hidden" name="repondre" value="1">
                                <input type="hidden" name="id_bien" value="<?= $msg['id_bien'] ?>">
                                <input type="hidden" name="id_destinataire" value="<?= $msg['expediteur_id'] ?>">
                                <input type="hidden" name="id_parent" value="<?= $msg['id_message'] ?>">
                                <textarea name="message" rows="3"
                                    placeholder="Votre réponse..." required></textarea>
                                <div style="display:flex;gap:0.75rem;">
                                    <button type="submit" class="btn-reply">Envoyer la réponse</button>
                                    <button type="button" class="btn-mark-read"
                                        onclick="toggleReply('reply-<?= $msg['id_message'] ?>')">Annuler</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Messages envoyés -->
        <div id="tab-envoyes" class="tab-pane">
            <?php if (count($messages_envoyes) === 0): ?>
                <div class="empty-msg">
                    <div style="font-size:3rem;margin-bottom:1rem;">📤</div>
                    <p>Vous n'avez pas encore envoyé de messages.</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages_envoyes as $msg): ?>
                    <div class="msg-card msg-sent">
                        <div class="msg-header">
                            <span class="msg-from">
                                🏠 <a href="detail.php?id=<?= $msg['id_bien'] ?>"
                                    style="color:#1a3a52;text-decoration:underline;">
                                    <?= htmlspecialchars($msg['titre_bien']) ?>
                                </a>
                                <?php if (!empty($msg['destinataire_nom'])): ?>
                                    <span style="color:#888;font-weight:400;font-size:0.88rem;">
                                        → <?= htmlspecialchars($msg['destinataire_nom']) ?>
                                    </span>
                                <?php endif; ?>
                            </span>
                            <span class="msg-date"><?= date('d/m/Y à H:i', strtotime($msg['date_envoi'])) ?></span>
                        </div>
                        <p class="msg-body"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Réponses reçues -->
        <div id="tab-reponses" class="tab-pane">
            <?php if (count($reponses_recues) === 0): ?>
                <div class="empty-msg">
                    <div style="font-size:3rem;margin-bottom:1rem;">💬</div>
                    <p>Aucune réponse reçue pour le moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($reponses_recues as $msg): ?>
                    <div class="msg-card msg-reply">
                        <div class="msg-header">
                            <span class="msg-from">
                                💬 Réponse de <strong><?= htmlspecialchars($msg['expediteur_nom']) ?></strong>
                            </span>
                            <span class="msg-date"><?= date('d/m/Y à H:i', strtotime($msg['date_envoi'])) ?></span>
                        </div>
                        <p class="msg-bien">
                            🏠 <a href="detail.php?id=<?= $msg['id_bien'] ?>" style="color:#d4a574;text-decoration:underline;">
                                <?= htmlspecialchars($msg['titre_bien']) ?>
                            </a>
                        </p>
                        <p class="msg-body"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                        <button class="btn-reply" type="button"
                            onclick="toggleReply('reply-rep-<?= $msg['id_message'] ?>')">
                            💬 Répondre
                        </button>

                        <div class="reply-form" id="reply-rep-<?= $msg['id_message'] ?>">
                            <form method="POST">
                                <input type="hidden" name="repondre" value="1">
                                <input type="hidden" name="id_bien" value="<?= $msg['id_bien'] ?>">
                                <input type="hidden" name="id_destinataire" value="<?= $msg['id_expediteur'] ?>">
                                <input type="hidden" name="id_parent" value="<?= $msg['id_message'] ?>">
                                <textarea name="message" rows="3"
                                    placeholder="Votre réponse..." required></textarea>
                                <div style="display:flex;gap:0.75rem;">
                                    <button type="submit" class="btn-reply">Envoyer</button>
                                    <button type="button" class="btn-mark-read"
                                        onclick="toggleReply('reply-rep-<?= $msg['id_message'] ?>')">Annuler</button>
                                </div>
                            </form>
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
    function showTab(name, btn) {
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.msg-tab').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        btn.classList.add('active');
    }

    function toggleReply(id) {
        const form = document.getElementById(id);
        form.classList.toggle('open');
        if (form.classList.contains('open')) {
            form.querySelector('textarea').focus();
        }
    }
    </script>
</body>
</html>