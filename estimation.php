<?php
session_start();
require_once 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adresse    = trim($_POST['address'] ?? '');
    $ville      = trim($_POST['city'] ?? '');
    $code_postal = trim($_POST['postal'] ?? '');
    $type_bien  = $_POST['type'] ?? '';
    $nb_pieces  = intval($_POST['rooms'] ?? 0);
    $surface    = intval($_POST['surface'] ?? 0);
    $annee      = intval($_POST['year'] ?? 0);
    $etat       = $_POST['condition'] ?? '';
    $id_user    = $_SESSION['user_id'] ?? null;

    // Validations
    if (empty($adresse))
        $errors['address'] = "L'adresse est obligatoire.";

    if (empty($ville))
        $errors['city'] = "La ville est obligatoire.";
    elseif (strlen($ville) < 2)
        $errors['city'] = "Ville invalide.";

    if (empty($code_postal))
        $errors['postal'] = "Le code postal est obligatoire.";
    elseif (!preg_match('/^[0-9]{5}$/', $code_postal))
        $errors['postal'] = "Code postal invalide (5 chiffres).";

    if (empty($type_bien))
        $errors['type'] = "Veuillez choisir un type de bien.";

    if ($nb_pieces <= 0)
        $errors['rooms'] = "Nombre de pièces invalide.";
    elseif ($nb_pieces > 50)
        $errors['rooms'] = "Nombre de pièces trop élevé (max 50).";

    if ($surface <= 0)
        $errors['surface'] = "La surface est obligatoire.";
    elseif ($surface < 10)
        $errors['surface'] = "Surface trop petite (minimum 10 m²).";
    elseif ($surface > 10000)
        $errors['surface'] = "Surface trop grande (maximum 10 000 m²).";

    if ($annee <= 0)
        $errors['year'] = "L'année de construction est obligatoire.";
    elseif ($annee < 1800)
        $errors['year'] = "Année invalide (minimum 1800).";
    elseif ($annee > 2026)
        $errors['year'] = "Année invalide (ne peut pas être dans le futur).";

    if (empty($etat))
        $errors['condition'] = "Veuillez choisir l'état du bien.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO estimations (adresse, ville, code_postal, type_bien, nb_pieces, surface, annee_construction, etat, id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$adresse, $ville, $code_postal, $type_bien, $nb_pieces, $surface, $annee, $etat, $id_user]);

        $prix_base = 3000;
        $ville_lower = strtolower($ville);
        if (str_contains($ville_lower, 'paris')) $prix_base = 10000;
        elseif (str_contains($ville_lower, 'nice') || str_contains($ville_lower, 'cannes')) $prix_base = 5500;
        elseif (str_contains($ville_lower, 'lyon') || str_contains($ville_lower, 'bordeaux')) $prix_base = 4500;
        elseif (str_contains($ville_lower, 'marseille') || str_contains($ville_lower, 'toulouse')) $prix_base = 3500;

        $coef_type = match($type_bien) { 'villa' => 1.3, 'maison' => 1.1, default => 1.0 };
        $coef_etat = match($etat) { 'neuf' => 1.2, 'excellent' => 1.1, 'renover' => 0.75, default => 1.0 };
        $age = 2026 - $annee;
        $coef_age = match(true) { $age <= 5 => 1.15, $age <= 15 => 1.05, $age <= 30 => 1.0, $age <= 50 => 0.95, default => 0.85 };

        $estimation = $surface * $prix_base * $coef_type * $coef_etat * $coef_age;
        $min = round($estimation * 0.9 / 1000) * 1000;
        $max = round($estimation * 1.1 / 1000) * 1000;
        $moy = round($estimation / 1000) * 1000;

        header("Location: estimation-result.php?min=$min&max=$max&moy=$moy&surface=$surface&ville=" . urlencode($ville) . "&type=$type_bien&etat=$etat");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimation - HSimo</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .field-error { color:#e05a7a; font-size:0.82rem; margin-top:0.3rem; }
        .input-error { border-color:#e05a7a !important; background:#fff8f9 !important; }
        .required-star { color:#e05a7a; }
        .form-hint { font-size:0.78rem; color:#888; margin-top:0.2rem; }
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
                <a href="messages.php" style="color:white;margin-right:1rem;font-family:'Outfit',sans-serif;">📬 Messages</a>
                <a href="logout.php" class="btn-login">Déconnexion</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Se connecter</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="form-page">
        <div class="form-container">
            <h1 class="form-title">Estimez votre bien !</h1>
            <p style="text-align:center;color:#888;margin-top:-1.5rem;margin-bottom:2rem;">
                Les champs marqués <span style="color:#e05a7a;">*</span> sont obligatoires
            </p>

            <?php if (!empty($errors)): ?>
                <div style="background:#fff0f4;border:1px solid #e05a7a;color:#c0392b;padding:1rem 1.5rem;border-radius:10px;margin-bottom:1.5rem;">
                    ⚠️ Veuillez corriger les erreurs ci-dessous avant de continuer.
                </div>
            <?php endif; ?>

            <form method="POST" action="estimation.php" class="estimation-form" novalidate>

                <div class="form-group">
                    <label for="address-est">Adresse <span class="required-star">*</span></label>
                    <input type="text" id="address-est" name="address"
                           placeholder="Ex: 12 rue de la Paix"
                           value="<?= htmlspecialchars($_POST['address'] ?? '') ?>"
                           class="<?= isset($errors['address']) ? 'input-error' : '' ?>">
                    <?php if (isset($errors['address'])): ?>
                        <div class="field-error">⚠️ <?= $errors['address'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city-est">Ville <span class="required-star">*</span></label>
                        <input type="text" id="city-est" name="city"
                               placeholder="Ex: Paris"
                               value="<?= htmlspecialchars($_POST['city'] ?? '') ?>"
                               class="<?= isset($errors['city']) ? 'input-error' : '' ?>">
                        <?php if (isset($errors['city'])): ?>
                            <div class="field-error">⚠️ <?= $errors['city'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="postal">Code postal <span class="required-star">*</span></label>
                        <input type="text" id="postal" name="postal"
                               placeholder="Ex: 75001"
                               value="<?= htmlspecialchars($_POST['postal'] ?? '') ?>"
                               maxlength="5"
                               class="<?= isset($errors['postal']) ? 'input-error' : '' ?>">
                        <?php if (isset($errors['postal'])): ?>
                            <div class="field-error">⚠️ <?= $errors['postal'] ?></div>
                        <?php else: ?>
                            <div class="form-hint">5 chiffres</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row triple">
                    <div class="form-group">
                        <label for="type">Type <span class="required-star">*</span></label>
                        <select id="type" name="type" class="<?= isset($errors['type']) ? 'input-error' : '' ?>">
                            <option value="">Sélectionner</option>
                            <option value="maison"      <?= ($_POST['type'] ?? '') === 'maison'      ? 'selected' : '' ?>>Maison</option>
                            <option value="appartement" <?= ($_POST['type'] ?? '') === 'appartement' ? 'selected' : '' ?>>Appartement</option>
                            <option value="villa"       <?= ($_POST['type'] ?? '') === 'villa'       ? 'selected' : '' ?>>Villa</option>
                        </select>
                        <?php if (isset($errors['type'])): ?>
                            <div class="field-error">⚠️ <?= $errors['type'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="rooms-est">Pièces <span class="required-star">*</span></label>
                        <input type="number" id="rooms-est" name="rooms"
                               placeholder="Ex: 4" min="1" max="50"
                               value="<?= htmlspecialchars($_POST['rooms'] ?? '') ?>"
                               class="<?= isset($errors['rooms']) ? 'input-error' : '' ?>">
                        <?php if (isset($errors['rooms'])): ?>
                            <div class="field-error">⚠️ <?= $errors['rooms'] ?></div>
                        <?php else: ?>
                            <div class="form-hint">Entre 1 et 50</div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="surface-est">Surface (m²) <span class="required-star">*</span></label>
                        <input type="number" id="surface-est" name="surface"
                               placeholder="Ex: 100" min="10" max="10000"
                               value="<?= htmlspecialchars($_POST['surface'] ?? '') ?>"
                               class="<?= isset($errors['surface']) ? 'input-error' : '' ?>">
                        <?php if (isset($errors['surface'])): ?>
                            <div class="field-error">⚠️ <?= $errors['surface'] ?></div>
                        <?php else: ?>
                            <div class="form-hint">Entre 10 et 10 000 m²</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="year">Année de construction <span class="required-star">*</span></label>
                        <input type="number" id="year" name="year"
                               placeholder="Ex: 1990" min="1800" max="2026"
                               value="<?= htmlspecialchars($_POST['year'] ?? '') ?>"
                               class="<?= isset($errors['year']) ? 'input-error' : '' ?>">
                        <?php if (isset($errors['year'])): ?>
                            <div class="field-error">⚠️ <?= $errors['year'] ?></div>
                        <?php else: ?>
                            <div class="form-hint">Entre 1800 et 2026</div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="condition">État <span class="required-star">*</span></label>
                        <select id="condition" name="condition" class="<?= isset($errors['condition']) ? 'input-error' : '' ?>">
                            <option value="">Sélectionner</option>
                            <option value="neuf"      <?= ($_POST['condition'] ?? '') === 'neuf'      ? 'selected' : '' ?>>Neuf</option>
                            <option value="excellent" <?= ($_POST['condition'] ?? '') === 'excellent' ? 'selected' : '' ?>>Excellent</option>
                            <option value="bon"       <?= ($_POST['condition'] ?? '') === 'bon'       ? 'selected' : '' ?>>Bon</option>
                            <option value="renover"   <?= ($_POST['condition'] ?? '') === 'renover'   ? 'selected' : '' ?>>À rénover</option>
                        </select>
                        <?php if (isset($errors['condition'])): ?>
                            <div class="field-error">⚠️ <?= $errors['condition'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Obtenir l'estimation</button>
            </form>
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

    <script>
    // Forcer uniquement des chiffres pour le code postal
    document.getElementById('postal').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5);
    });
    </script>
</body>
</html>