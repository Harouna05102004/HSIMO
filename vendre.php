<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre       = trim($_POST['title'] ?? '');
    $ville       = trim($_POST['city'] ?? '');
    $prix        = floatval($_POST['price'] ?? 0);
    $surface     = intval($_POST['surface'] ?? 0);
    $chambres    = intval($_POST['rooms'] ?? 0);
    $salles_bain = intval($_POST['bathrooms'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $categorie   = $_POST['categorie'] ?? 'Vente';
    $id_vendeur  = $_SESSION['user_id'];

    // Validations
    if (empty($titre))
        $errors['title'] = "Le titre est obligatoire.";
    elseif (strlen($titre) < 5)
        $errors['title'] = "Titre trop court (min 5 caractères).";

    if (empty($ville))
        $errors['city'] = "La ville est obligatoire.";

    if ($prix <= 0)
        $errors['price'] = "Le prix doit être supérieur à 0.";

    if ($surface < 10)
        $errors['surface'] = "Surface minimum 10 m².";
    elseif ($surface > 10000)
        $errors['surface'] = "Surface maximum 10 000 m².";

    if ($chambres <= 0)
        $errors['rooms'] = "Nombre de chambres invalide.";

    if ($salles_bain <= 0)
        $errors['bathrooms'] = "Nombre de salles de bain invalide.";

    if (empty($description) || strlen($description) < 20)
        $errors['description'] = "Description trop courte (min 20 caractères).";

    if (!in_array($categorie, ['Vente', 'Location']))
        $categorie = 'Vente';

    if (empty($errors)) {
        $photos_names = [];
        if (isset($_FILES['photos']) && $_FILES['photos']['error'][0] !== UPLOAD_ERR_NO_FILE) {
            $upload_dir = 'uploads/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = uniqid() . '_' . basename($_FILES['photos']['name'][$key]);
                    if (move_uploaded_file($tmp_name, $upload_dir . $file_name)) {
                        $photos_names[] = $file_name;
                    }
                }
            }
        }
        $photos_string = implode(',', $photos_names);
        $stmt = $pdo->prepare("INSERT INTO bien (titre, description, prix, surface, nb_chambres, nb_salles_bain, ville, photos, id_vendeur, categorie) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$titre, $description, $prix, $surface, $chambres, $salles_bain, $ville, $photos_string, $id_vendeur, $categorie])) {
            $success = "Annonce publiée avec succès ! Redirection...";
            header("refresh:2;url=confirmation.php");
        } else {
            $errors['global'] = "Erreur lors de la publication.";
        }
    }
}

$cat = $_POST['categorie'] ?? 'Vente';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publier une annonce - HSimo</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .field-error   { color:#e05a7a; font-size:0.82rem; margin-top:0.3rem; }
        .input-error   { border-color:#e05a7a !important; background:#fff8f9 !important; }
        .required-star { color:#e05a7a; }

        .toggle-wrapper {
            display: flex;
            background: #f0f4f8;
            border-radius: 60px;
            padding: 0.4rem;
            width: fit-content;
            margin: 0 auto 2.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .toggle-btn {
            padding: 0.9rem 2.8rem;
            border-radius: 50px;
            border: none;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #888;
            background: transparent;
        }
        .toggle-btn.active-vente {
            background: #1a3a52;
            color: white;
            box-shadow: 0 4px 15px rgba(26,58,82,0.3);
        }
        .toggle-btn.active-location {
            background: #d4a574;
            color: white;
            box-shadow: 0 4px 15px rgba(212,165,116,0.4);
        }
        .prix-hint { color:#888; font-size:0.82rem; margin-top:0.2rem; }
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
                <li><a href="vendre.php" class="active">Vendre</a></li>
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

    <main class="form-page">
        <div class="form-container">
            <h1 class="form-title">Publier une annonce</h1>
            <p style="text-align:center;color:#888;margin-top:-1.5rem;margin-bottom:2rem;">
                Les champs marqués <span style="color:#e05a7a;">*</span> sont obligatoires
            </p>

            <?php if (!empty($errors['global'])): ?>
                <div style="background:#fee2e2;color:#b91c1c;padding:1rem 1.5rem;border-radius:10px;margin-bottom:1.5rem;text-align:center;">
                    ❌ <?= htmlspecialchars($errors['global']) ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div style="background:#d1fae5;color:#065f46;padding:1rem 1.5rem;border-radius:10px;margin-bottom:1.5rem;text-align:center;">
                    ✅ <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <!-- Toggle Vente / Location -->
            <div class="toggle-wrapper">
                <button type="button" class="toggle-btn <?= $cat === 'Vente' ? 'active-vente' : '' ?>"
                        id="btn-vente" onclick="setCategorie('Vente')">
                    🏷️ Mettre en vente
                </button>
                <button type="button" class="toggle-btn <?= $cat === 'Location' ? 'active-location' : '' ?>"
                        id="btn-location" onclick="setCategorie('Location')">
                    🔑 Mettre en location
                </button>
            </div>

            <form method="POST" action="vendre.php" enctype="multipart/form-data" class="sell-form" novalidate>
                <input type="hidden" name="categorie" id="categorie-input" value="<?= htmlspecialchars($cat) ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Titre de l'annonce <span class="required-star">*</span></label>
                        <input type="text" id="title" name="title"
                               placeholder="Ex: Belle villa avec piscine"
                               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                               class="<?= isset($errors['title']) ? 'input-error' : '' ?>">
                        <?php if (isset($errors['title'])): ?>
                            <div class="field-error">⚠️ <?= $errors['title'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="city">Ville <span class="required-star">*</span></label>
                        <input type="text" id="city" name="city"
                               placeholder="Ex: Paris"
                               value="<?= htmlspecialchars($_POST['city'] ?? '') ?>"
                               class="<?= isset($errors['city']) ? 'input-error' : '' ?>">
                        <?php if (isset($errors['city'])): ?>
                            <div class="field-error">⚠️ <?= $errors['city'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price" id="prix-label">Prix (€) <span class="required-star">*</span></label>
                        <input type="number" id="price" name="price"
                               placeholder="Ex: 450000" min="1"
                               value="<?= htmlspecialchars($_POST['price'] ?? '') ?>"
                               class="<?= isset($errors['price']) ? 'input-error' : '' ?>">
                        <div class="prix-hint" id="prix-hint">Prix de vente total</div>
                        <?php if (isset($errors['price'])): ?>
                            <div class="field-error">⚠️ <?= $errors['price'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="surface">Surface (m²) <span class="required-star">*</span></label>
                        <input type="number" id="surface" name="surface"
                               placeholder="Ex: 120" min="10" max="10000"
                               value="<?= htmlspecialchars($_POST['surface'] ?? '') ?>"
                               class="<?= isset($errors['surface']) ? 'input-error' : '' ?>">
                        <?php if (isset($errors['surface'])): ?>
                            <div class="field-error">⚠️ <?= $errors['surface'] ?></div>
                        <?php else: ?>
                            <div class="prix-hint">Entre 10 et 10 000 m²</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="rooms">Chambres <span class="required-star">*</span></label>
                        <input type="number" id="rooms" name="rooms"
                               placeholder="Ex: 3" min="1" max="50"
                               value="<?= htmlspecialchars($_POST['rooms'] ?? '') ?>"
                               class="<?= isset($errors['rooms']) ? 'input-error' : '' ?>">
                        <?php if (isset($errors['rooms'])): ?>
                            <div class="field-error">⚠️ <?= $errors['rooms'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="bathrooms">Salles de bain <span class="required-star">*</span></label>
                        <input type="number" id="bathrooms" name="bathrooms"
                               placeholder="Ex: 2" min="1"
                               value="<?= htmlspecialchars($_POST['bathrooms'] ?? '') ?>"
                               class="<?= isset($errors['bathrooms']) ? 'input-error' : '' ?>">
                        <?php if (isset($errors['bathrooms'])): ?>
                            <div class="field-error">⚠️ <?= $errors['bathrooms'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description <span class="required-star">*</span></label>
                    <textarea id="description" name="description" rows="5"
                              placeholder="Décrivez votre bien (min 20 caractères)..."
                              class="<?= isset($errors['description']) ? 'input-error' : '' ?>"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="field-error">⚠️ <?= $errors['description'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Photos</label>
                    <input type="file" id="photos" name="photos[]" multiple accept="image/*"
                           style="display:block;width:100%;padding:1rem;border:2px dashed #6b8ca8;border-radius:10px;cursor:pointer;background:#f8f9fa;font-family:'Outfit',sans-serif;box-sizing:border-box;">
                    <div class="prix-hint">Formats acceptés : JPG, PNG, WEBP — Max 5MB par photo</div>
                </div>

                <button type="submit" class="btn-submit" id="submit-btn">
                    🏷️ Publier la vente
                </button>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section"><p>&copy; 2026 HSimo. Tous droits réservés.</p></div>
        </div>
    </footer>

    <script>
    // Initialiser le bon état au chargement
    const currentCat = document.getElementById('categorie-input').value;
    if (currentCat === 'Location') {
        activateLocation();
    } else {
        activateVente();
    }

    function setCategorie(cat) {
        document.getElementById('categorie-input').value = cat;
        if (cat === 'Vente') {
            activateVente();
        } else {
            activateLocation();
        }
    }

    function activateVente() {
        document.getElementById('btn-vente').className = 'toggle-btn active-vente';
        document.getElementById('btn-location').className = 'toggle-btn';
        document.getElementById('prix-label').innerHTML = 'Prix (€) <span style="color:#e05a7a;">*</span>';
        document.getElementById('prix-hint').textContent = 'Prix de vente total';
        document.getElementById('price').placeholder = 'Ex: 450 000';
        document.getElementById('submit-btn').innerHTML = '🏷️ Publier la vente';
    }

    function activateLocation() {
        document.getElementById('btn-location').className = 'toggle-btn active-location';
        document.getElementById('btn-vente').className = 'toggle-btn';
        document.getElementById('prix-label').innerHTML = 'Loyer mensuel (€) <span style="color:#e05a7a;">*</span>';
        document.getElementById('prix-hint').textContent = 'Loyer par mois';
        document.getElementById('price').placeholder = 'Ex: 1 500';
        document.getElementById('submit-btn').innerHTML = '🔑 Publier la location';
    }
    </script>
</body>
</html>