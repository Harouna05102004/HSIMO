<?php
session_start();
require_once 'config.php';

// ── Mot de passe admin dédié ──
define('ADMIN_LOGIN', 'hsimo');
define('ADMIN_PASSWORD', 'hsimo');

// ── Connexion admin ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    if ($_POST['admin_login_input'] === ADMIN_LOGIN && $_POST['admin_password'] === ADMIN_PASSWORD) {
        $_SESSION['is_admin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $login_error = "Mot de passe incorrect.";
    }
}

// ── Déconnexion admin ──
if (isset($_GET['logout'])) {
    $_SESSION['is_admin'] = false;
    header('Location: admin.php');
    exit;
}

// ── Page de connexion si pas admin ──
if (empty($_SESSION['is_admin'])) {
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - HSimo</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Outfit',sans-serif; background:#f0f4f8; display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .login-box { background:white; padding:3rem; border-radius:20px; box-shadow:0 8px 30px rgba(0,0,0,0.1); width:100%; max-width:420px; text-align:center; }
        .login-logo { font-family:'Playfair Display',serif; font-size:2.5rem; color:#1a3a52; margin-bottom:0.5rem; }
        .login-logo span { color:#d4a574; }
        .login-sub { color:#888; margin-bottom:2rem; font-size:0.95rem; }
        .form-group { text-align:left; margin-bottom:1.5rem; }
        label { font-weight:500; display:block; margin-bottom:0.5rem; color:#333; }
        input[type=password] { width:100%; padding:1rem; border:2px solid #e0e0e0; border-radius:10px; font-family:'Outfit',sans-serif; font-size:1rem; outline:none; }
        input[type=password]:focus { border-color:#1a3a52; }
        .btn-login { background:#1a3a52; color:white; border:none; padding:1rem; width:100%; border-radius:10px; font-size:1rem; font-family:'Outfit',sans-serif; font-weight:600; cursor:pointer; transition:all 0.2s; }
        .btn-login:hover { background:#d4a574; }
        .error { background:#fee2e2; color:#b91c1c; padding:0.8rem 1rem; border-radius:8px; margin-bottom:1.5rem; font-size:0.9rem; }
        .back { margin-top:1.5rem; }
        .back a { color:#888; font-size:0.9rem; text-decoration:none; }
        .back a:hover { color:#1a3a52; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo">HS<span>imo</span></div>
        <p class="login-sub">Espace Administration</p>

        <?php if (!empty($login_error)): ?>
            <div class="error">❌ <?= htmlspecialchars($login_error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="admin_login" value="1">
            <div class="form-group">
                <label>Identifiant</label>
                <input type="text" name="admin_login_input" placeholder="Identifiant" autofocus style="width:100%;padding:1rem;border:2px solid #e0e0e0;border-radius:10px;font-family:'Outfit',sans-serif;font-size:1rem;outline:none;margin-bottom:0;">
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="admin_password" placeholder="Mot de passe">
            </div>
            <button type="submit" class="btn-login">🔐 Accéder</button>
        </form>
        <div class="back"><a href="index.php">← Retour au site</a></div>
    </div>
</body>
</html>
<?php
    exit;
}

// ── Actions POST ──
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_bien'])) {
        $id = intval($_POST['id_bien']);
        $pdo->prepare("DELETE FROM bien WHERE id_bien = ?")->execute([$id]);
        $success = "Annonce supprimée.";
    }
    if (isset($_POST['delete_user'])) {
        $id = intval($_POST['id_user']);
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        $success = "Utilisateur supprimé.";
    }
    if (isset($_POST['delete_message'])) {
        $id = intval($_POST['id_message']);
        $pdo->prepare("DELETE FROM messages WHERE id_message = ?")->execute([$id]);
        $success = "Message supprimé.";
    }
    if (isset($_POST['delete_estimation'])) {
        $id = intval($_POST['id_estimation']);
        $pdo->prepare("DELETE FROM estimations WHERE id_estimation = ?")->execute([$id]);
        $success = "Estimation supprimée.";
    }
}

// ── Données ──
$biens       = $pdo->query("SELECT b.*, u.username as vendeur FROM bien b LEFT JOIN users u ON b.id_vendeur = u.id ORDER BY b.date_creation DESC")->fetchAll();
$users       = $pdo->query("SELECT u.*, (SELECT COUNT(*) FROM bien WHERE id_vendeur = u.id) as nb_biens FROM users u ORDER BY u.created_at DESC")->fetchAll();
$messages    = $pdo->query("SELECT m.*, b.titre as titre_bien, u.username as expediteur FROM messages m JOIN bien b ON m.id_bien = b.id_bien JOIN users u ON m.id_expediteur = u.id ORDER BY m.date_envoi DESC")->fetchAll();
$estimations = $pdo->query("SELECT e.*, u.username FROM estimations e LEFT JOIN users u ON e.id_user = u.id ORDER BY e.date_demande DESC")->fetchAll();

$nb_biens    = count($biens);
$nb_users    = count($users);
$nb_messages = count($messages);
$nb_estim    = count($estimations);
$nb_favoris  = $pdo->query("SELECT COUNT(*) FROM favoris")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - HSimo</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Outfit',sans-serif; background:#f0f4f8; color:#2c2c2c; }
        .layout { display:flex; min-height:100vh; }

        /* Sidebar */
        .sidebar { width:240px; background:linear-gradient(180deg,#1a3a52,#0d2233); color:white; padding:2rem 0; position:fixed; height:100vh; overflow-y:auto; }
        .sidebar-logo { font-family:'Playfair Display',serif; font-size:1.6rem; padding:0 1.5rem 1.5rem; border-bottom:1px solid rgba(255,255,255,0.1); margin-bottom:1.5rem; }
        .sidebar-logo span { color:#d4a574; }
        .sidebar-logo small { display:block; font-family:'Outfit',sans-serif; font-size:0.7rem; opacity:0.5; margin-top:0.2rem; }
        .nav-item a { display:flex; align-items:center; gap:0.7rem; padding:0.85rem 1.5rem; color:rgba(255,255,255,0.7); text-decoration:none; font-size:0.92rem; border-left:3px solid transparent; transition:all 0.2s; }
        .nav-item a:hover, .nav-item a.active { background:rgba(255,255,255,0.08); color:white; border-left-color:#d4a574; }
        .nav-count { margin-left:auto; background:rgba(255,255,255,0.15); padding:0.15rem 0.5rem; border-radius:10px; font-size:0.72rem; }
        .sidebar-footer { position:absolute; bottom:1.5rem; left:0; right:0; padding:0 1.5rem; border-top:1px solid rgba(255,255,255,0.1); padding-top:1rem; }
        .sidebar-footer a { display:block; color:rgba(255,255,255,0.6); text-decoration:none; font-size:0.88rem; padding:0.4rem 0; }
        .sidebar-footer a:hover { color:white; }

        /* Main */
        .main { margin-left:240px; flex:1; padding:2.5rem; }
        .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; flex-wrap:wrap; gap:1rem; }
        .page-header h1 { font-family:'Playfair Display',serif; font-size:1.8rem; color:#1a3a52; }
        .admin-tag { background:#1a3a52; color:white; padding:0.4rem 1rem; border-radius:50px; font-size:0.82rem; }

        /* Alert */
        .alert { background:#d1fae5; border:1px solid #10b981; color:#065f46; padding:1rem 1.5rem; border-radius:10px; margin-bottom:1.5rem; }

        /* Stats */
        .stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:1.2rem; margin-bottom:2.5rem; }
        .stat { background:white; border-radius:14px; padding:1.5rem; text-align:center; box-shadow:0 2px 10px rgba(0,0,0,0.06); border-top:4px solid #d4a574; }
        .stat-icon { font-size:2rem; margin-bottom:0.6rem; }
        .stat-num { font-size:2.2rem; font-weight:700; color:#1a3a52; font-family:'Playfair Display',serif; }
        .stat-lbl { color:#888; font-size:0.85rem; }

        /* Sections */
        .section { background:white; border-radius:14px; padding:1.8rem; margin-bottom:1.5rem; box-shadow:0 2px 10px rgba(0,0,0,0.06); display:none; }
        .section.active { display:block; }
        .section-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:2px solid #f0f4f8; }
        .section-head h2 { font-family:'Playfair Display',serif; font-size:1.4rem; color:#1a3a52; }
        .section-badge { background:#d4a574; color:white; padding:0.25rem 0.75rem; border-radius:20px; font-size:0.82rem; }

        /* Table */
        table { width:100%; border-collapse:collapse; }
        th { background:#f8f9fa; padding:0.8rem 1rem; text-align:left; font-size:0.8rem; color:#666; font-weight:600; text-transform:uppercase; letter-spacing:0.4px; }
        td { padding:0.9rem 1rem; border-bottom:1px solid #f5f5f5; font-size:0.9rem; vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#fafafa; }

        .thumb { width:55px; height:40px; object-fit:cover; border-radius:6px; }
        .no-thumb { width:55px; height:40px; background:#f0f4f8; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; }
        .badge { padding:0.25rem 0.7rem; border-radius:20px; font-size:0.75rem; font-weight:600; }
        .badge-vente { background:#dbeafe; color:#1d4ed8; }
        .badge-unread { background:#fee2e2; color:#b91c1c; }
        .badge-read { background:#f0f0f0; color:#666; }

        .btn-del { background:#fee2e2; color:#e05a7a; border:none; padding:0.45rem 0.9rem; border-radius:8px; font-size:0.8rem; cursor:pointer; font-family:'Outfit',sans-serif; transition:all 0.2s; }
        .btn-del:hover { background:#e05a7a; color:white; }
        .link-view { color:#1a3a52; text-decoration:none; font-size:0.83rem; font-weight:500; }
        .link-view:hover { color:#d4a574; }

        .empty { text-align:center; padding:3rem; color:#aaa; }
        .empty-icon { font-size:2.5rem; margin-bottom:0.8rem; }

        @media(max-width:768px) {
            .sidebar { width:200px; }
            .main { margin-left:200px; padding:1.5rem; }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-logo">HS<span>imo</span><small>Administration</small></div>
        <nav>
            <div class="nav-item"><a href="#" class="active" onclick="show('dashboard',this)">📊 Tableau de bord</a></div>
            <div class="nav-item"><a href="#" onclick="show('biens',this)">🏠 Annonces <span class="nav-count"><?= $nb_biens ?></span></a></div>
            <div class="nav-item"><a href="#" onclick="show('users',this)">👥 Utilisateurs <span class="nav-count"><?= $nb_users ?></span></a></div>
            <div class="nav-item"><a href="#" onclick="show('messages',this)">✉️ Messages <span class="nav-count"><?= $nb_messages ?></span></a></div>
            <div class="nav-item"><a href="#" onclick="show('estimations',this)">📋 Estimations <span class="nav-count"><?= $nb_estim ?></span></a></div>
        </nav>
        <div class="sidebar-footer">
            <a href="index.php">🏠 Voir le site</a>
            <a href="admin.php?logout=1">🚪 Déconnexion</a>
        </div>
    </aside>

    <main class="main">
        <div class="page-header">
            <h1 id="page-title">Tableau de bord</h1>
            <span class="admin-tag">🔐 Admin</span>
        </div>

        <?php if ($success): ?>
            <div class="alert">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- DASHBOARD -->
        <div id="s-dashboard" class="section active">
            <div class="stats">
                <div class="stat"><div class="stat-icon">🏠</div><div class="stat-num"><?= $nb_biens ?></div><div class="stat-lbl">Annonces</div></div>
                <div class="stat"><div class="stat-icon">👥</div><div class="stat-num"><?= $nb_users ?></div><div class="stat-lbl">Utilisateurs</div></div>
                <div class="stat"><div class="stat-icon">✉️</div><div class="stat-num"><?= $nb_messages ?></div><div class="stat-lbl">Messages</div></div>
                <div class="stat"><div class="stat-icon">❤️</div><div class="stat-num"><?= $nb_favoris ?></div><div class="stat-lbl">Favoris</div></div>
                <div class="stat"><div class="stat-icon">📋</div><div class="stat-num"><?= $nb_estim ?></div><div class="stat-lbl">Estimations</div></div>
            </div>
            <div style="background:#f8f9fa;border-radius:12px;padding:1.5rem;">
                <h3 style="color:#1a3a52;margin-bottom:1rem;font-size:1.1rem;">🏠 Dernières annonces</h3>
                <?php foreach (array_slice($biens, 0, 5) as $b): ?>
                <div style="display:flex;justify-content:space-between;padding:0.7rem 0;border-bottom:1px solid #eee;">
                    <span><?= htmlspecialchars($b['titre']) ?> — <em style="color:#888;"><?= htmlspecialchars($b['ville']) ?></em></span>
                    <span style="color:#d4a574;font-weight:600;"><?= number_format($b['prix'],0,',',' ') ?> €</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- BIENS -->
        <div id="s-biens" class="section">
            <div class="section-head">
                <h2>🏠 Annonces</h2>
                <span class="section-badge"><?= $nb_biens ?> annonce<?= $nb_biens>1?'s':'' ?></span>
            </div>
            <?php if (empty($biens)): ?>
                <div class="empty"><div class="empty-icon">🏠</div><p>Aucune annonce.</p></div>
            <?php else: ?>
            <div style="overflow-x:auto;">
                <table>
                    <thead><tr><th>Photo</th><th>Titre</th><th>Ville</th><th>Prix</th><th>Vendeur</th><th>Date</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($biens as $b):
                        $photos = !empty($b['photos']) ? explode(',', $b['photos']) : [];
                    ?>
                    <tr>
                        <td><?php if (!empty($photos)): ?><img src="uploads/<?= htmlspecialchars($photos[0]) ?>" class="thumb"><?php else: ?><div class="no-thumb">🏠</div><?php endif; ?></td>
                        <td><strong><?= htmlspecialchars($b['titre']) ?></strong></td>
                        <td><?= htmlspecialchars($b['ville']) ?></td>
                        <td style="color:#d4a574;font-weight:600;"><?= number_format($b['prix'],0,',',' ') ?> €</td>
                        <td><?= htmlspecialchars($b['vendeur'] ?? '—') ?></td>
                        <td style="color:#888;font-size:0.82rem;"><?= date('d/m/Y', strtotime($b['date_creation'])) ?></td>
                        <td>
                            <div style="display:flex;gap:0.5rem;align-items:center;">
                                <a href="detail.php?id=<?= $b['id_bien'] ?>" class="link-view">👁️</a>
                                <form method="POST" onsubmit="return confirm('Supprimer cette annonce définitivement ?')">
                                    <input type="hidden" name="delete_bien" value="1">
                                    <input type="hidden" name="id_bien" value="<?= $b['id_bien'] ?>">
                                    <button type="submit" class="btn-del">🗑️ Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- USERS -->
        <div id="s-users" class="section">
            <div class="section-head">
                <h2>👥 Utilisateurs</h2>
                <span class="section-badge"><?= $nb_users ?> utilisateur<?= $nb_users>1?'s':'' ?></span>
            </div>
            <?php if (empty($users)): ?>
                <div class="empty"><div class="empty-icon">👥</div><p>Aucun utilisateur.</p></div>
            <?php else: ?>
            <div style="overflow-x:auto;">
                <table>
                    <thead><tr><th>ID</th><th>Username</th><th>Annonces</th><th>Inscrit le</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td style="color:#aaa;">#<?= $u['id'] ?></td>
                        <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                        <td><?= $u['nb_biens'] ?></td>
                        <td style="color:#888;font-size:0.82rem;"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Supprimer cet utilisateur et toutes ses données ?')">
                                <input type="hidden" name="delete_user" value="1">
                                <input type="hidden" name="id_user" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn-del">🗑️ Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- MESSAGES -->
        <div id="s-messages" class="section">
            <div class="section-head">
                <h2>✉️ Messages</h2>
                <span class="section-badge"><?= $nb_messages ?> message<?= $nb_messages>1?'s':'' ?></span>
            </div>
            <?php if (empty($messages)): ?>
                <div class="empty"><div class="empty-icon">✉️</div><p>Aucun message.</p></div>
            <?php else: ?>
            <div style="overflow-x:auto;">
                <table>
                    <thead><tr><th>De</th><th>Annonce</th><th>Message</th><th>Statut</th><th>Date</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($messages as $m): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($m['expediteur']) ?></strong></td>
                        <td><a href="detail.php?id=<?= $m['id_bien'] ?>" class="link-view"><?= htmlspecialchars($m['titre_bien']) ?></a></td>
                        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($m['message']) ?></td>
                        <td><span class="badge <?= $m['lu']==0?'badge-unread':'badge-read' ?>"><?= $m['lu']==0?'Non lu':'Lu' ?></span></td>
                        <td style="color:#888;font-size:0.82rem;"><?= date('d/m/Y H:i', strtotime($m['date_envoi'])) ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Supprimer ce message ?')">
                                <input type="hidden" name="delete_message" value="1">
                                <input type="hidden" name="id_message" value="<?= $m['id_message'] ?>">
                                <button type="submit" class="btn-del">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- ESTIMATIONS -->
        <div id="s-estimations" class="section">
            <div class="section-head">
                <h2>📋 Estimations</h2>
                <span class="section-badge"><?= $nb_estim ?> demande<?= $nb_estim>1?'s':'' ?></span>
            </div>
            <?php if (empty($estimations)): ?>
                <div class="empty"><div class="empty-icon">📋</div><p>Aucune estimation.</p></div>
            <?php else: ?>
            <div style="overflow-x:auto;">
                <table>
                    <thead><tr><th>Utilisateur</th><th>Adresse</th><th>Ville</th><th>Type</th><th>Surface</th><th>État</th><th>Date</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($estimations as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['username'] ?? 'Anonyme') ?></td>
                        <td><?= htmlspecialchars($e['adresse']) ?></td>
                        <td><?= htmlspecialchars($e['ville']) ?></td>
                        <td><?= ucfirst(htmlspecialchars($e['type_bien'])) ?></td>
                        <td><?= $e['surface'] ?> m²</td>
                        <td><?= ucfirst(htmlspecialchars($e['etat'])) ?></td>
                        <td style="color:#888;font-size:0.82rem;"><?= date('d/m/Y', strtotime($e['date_demande'])) ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Supprimer cette estimation ?')">
                                <input type="hidden" name="delete_estimation" value="1">
                                <input type="hidden" name="id_estimation" value="<?= $e['id_estimation'] ?>">
                                <button type="submit" class="btn-del">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </main>
</div>

<script>
const titles = { dashboard:'Tableau de bord', biens:'Annonces', users:'Utilisateurs', messages:'Messages', estimations:'Estimations' };
function show(name, el) {
    event.preventDefault();
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-item a').forEach(a => a.classList.remove('active'));
    document.getElementById('s-' + name).classList.add('active');
    document.getElementById('page-title').textContent = titles[name];
    el.classList.add('active');
}
</script>
</body>
</html>