<?php
session_start();
require_once 'config.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (empty($username))
        $errors['username'] = "Le nom d'utilisateur est obligatoire.";
    elseif (strlen($username) < 3)
        $errors['username'] = "Minimum 3 caractères.";
    elseif (strlen($username) > 30)
        $errors['username'] = "Maximum 30 caractères.";
    elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username))
        $errors['username'] = "Uniquement lettres, chiffres et _.";

    if (empty($email))
        $errors['email'] = "L'adresse email est obligatoire.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email'] = "Adresse email invalide.";

    if (empty($password))
        $errors['password'] = "Le mot de passe est obligatoire.";
    elseif (strlen($password) < 6)
        $errors['password'] = "Minimum 6 caractères.";
    elseif (!preg_match('/[0-9]/', $password))
        $errors['password'] = "Doit contenir au moins un chiffre.";

    if (empty($confirm))
        $errors['confirm'] = "Veuillez confirmer le mot de passe.";
    elseif ($password !== $confirm)
        $errors['confirm'] = "Les mots de passe ne correspondent pas.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors['username'] = "Ce nom d'utilisateur est déjà pris.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($stmt->execute([$username, $hashed])) {
                $success = "Compte créé avec succès ! Redirection...";
                header("refresh:2;url=login.php");
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte - HSimo</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .field-error { color:#e05a7a; font-size:0.82rem; margin-top:0.3rem; }
        .input-error { border-color:#e05a7a !important; background:#fff8f9 !important; }
        .input-ok    { border-color:#10b981 !important; }
        .pwd-bar     { height:5px; border-radius:3px; margin-top:0.4rem; background:#e0e0e0; transition:all 0.3s; }
        .pwd-weak    { background:#e05a7a; width:30%; }
        .pwd-medium  { background:#f59e0b; width:65%; }
        .pwd-strong  { background:#10b981; width:100%; }
        .pwd-label   { font-size:0.78rem; margin-top:0.2rem; }
        .required-star { color:#e05a7a; }
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
            <a href="login.php" class="btn-login">Se connecter</a>
        </div>
    </nav>

    <main class="auth-container">
        <div class="auth-box">
            <h2 class="auth-title">Créer un compte</h2>
            <p class="auth-subtitle">Rejoignez-nous pour trouver votre propriété de rêve</p>

            <?php if ($success): ?>
                <div style="background:#10b981;color:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center;">
                    ✅ <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="signup.php" class="auth-form" novalidate>

                <div class="form-group">
                    <label for="username">Nom d'utilisateur <span class="required-star">*</span></label>
                    <input type="text" id="username" name="username"
                           placeholder="Ex: jean_dupont"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           class="<?= isset($errors['username']) ? 'input-error' : '' ?>"
                           maxlength="30">
                    <?php if (isset($errors['username'])): ?>
                        <div class="field-error">⚠️ <?= $errors['username'] ?></div>
                    <?php else: ?>
                        <div style="font-size:0.78rem;color:#888;margin-top:0.2rem;">3 à 30 caractères, lettres, chiffres et _ uniquement</div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Adresse email <span class="required-star">*</span></label>
                    <input type="email" id="email" name="email"
                           placeholder="exemple@email.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           class="<?= isset($errors['email']) ? 'input-error' : '' ?>">
                    <?php if (isset($errors['email'])): ?>
                        <div class="field-error">⚠️ <?= $errors['email'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe <span class="required-star">*</span></label>
                    <input type="password" id="password" name="password"
                           placeholder="Minimum 6 caractères avec un chiffre"
                           class="<?= isset($errors['password']) ? 'input-error' : '' ?>"
                           oninput="checkStrength(this.value)">
                    <div class="pwd-bar" id="pwdBar"></div>
                    <div class="pwd-label" id="pwdLabel"></div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="field-error">⚠️ <?= $errors['password'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="confirm">Confirmer le mot de passe <span class="required-star">*</span></label>
                    <input type="password" id="confirm" name="confirm_password"
                           placeholder="Répétez votre mot de passe"
                           class="<?= isset($errors['confirm']) ? 'input-error' : '' ?>"
                           oninput="checkConfirm(this.value)">
                    <?php if (isset($errors['confirm'])): ?>
                        <div class="field-error">⚠️ <?= $errors['confirm'] ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-submit">S'inscrire</button>
                <p class="auth-link">Déjà un compte ? <a href="login.php">Se connecter</a></p>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section"><p>&copy; 2026 HSimo. Tous droits réservés.</p></div>
        </div>
    </footer>

    <script>
    function checkStrength(val) {
        const bar = document.getElementById('pwdBar');
        const lbl = document.getElementById('pwdLabel');
        bar.className = 'pwd-bar';
        if (!val) { lbl.textContent = ''; return; }
        let score = 0;
        if (val.length >= 6) score++;
        if (val.length >= 10) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[^a-zA-Z0-9]/.test(val)) score++;
        if (score <= 2) {
            bar.classList.add('pwd-weak');
            lbl.innerHTML = '<span style="color:#e05a7a">🔴 Faible</span>';
        } else if (score <= 3) {
            bar.classList.add('pwd-medium');
            lbl.innerHTML = '<span style="color:#f59e0b">🟡 Moyen</span>';
        } else {
            bar.classList.add('pwd-strong');
            lbl.innerHTML = '<span style="color:#10b981">🟢 Fort</span>';
        }
    }
    function checkConfirm(val) {
        const pwd = document.getElementById('password').value;
        const el  = document.getElementById('confirm');
        if (!val) return;
        if (val !== pwd) {
            el.classList.add('input-error');
            el.classList.remove('input-ok');
        } else {
            el.classList.remove('input-error');
            el.classList.add('input-ok');
        }
    }
    </script>
</body>
</html>