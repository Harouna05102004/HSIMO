<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['title'] ?? '');
    $ville = trim($_POST['city'] ?? '');
    $prix = floatval($_POST['price'] ?? 0);
    $surface = intval($_POST['surface'] ?? 0);
    $chambres = intval($_POST['rooms'] ?? 0);
    $salles_bain = intval($_POST['bathrooms'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $id_vendeur = $_SESSION['user_id'];

    if (empty($titre) || empty($ville) || $prix <= 0 || $surface <= 0) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
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
        $stmt = $pdo->prepare("INSERT INTO bien (titre, description, prix, surface, nb_chambres, nb_salles_bain, ville, photos, id_vendeur) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$titre, $description, $prix, $surface, $chambres, $salles_bain, $ville, $photos_string, $id_vendeur])) {
            $success = "Votre annonce a été publiée avec succès ! Redirection...";
            header("refresh:2;url=confirmation.php");
        } else {
            $error = "Erreur lors de la publication de l'annonce.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendre - HSimo</title>
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
                <li><a href="vendre.php" class="active">Vendre</a></li>
                <li><a href="estimation.php">Estimation</a></li>
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
            <h1 class="form-title">Vendez votre maison !</h1>
            <?php if ($error): ?>
                <div style="background:#ff004f;color:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div style="background:#10b981;color:white;padding:15px;border-radius:10px;margin-bottom:20px;text-align:center;"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <form method="POST" action="vendre.php" enctype="multipart/form-data" class="sell-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Titre de l'annonce</label>
                        <input type="text" id="title" name="title" placeholder="Ex: Belle villa avec piscine" required>
                    </div>
                    <div class="form-group">
                        <label for="city">Ville</label>
                        <input type="text" id="city" name="city" placeholder="Ville" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Prix (€)</label>
                        <input type="number" id="price" name="price" placeholder="Ex: 450000" required>
                    </div>
                    <div class="form-group">
                        <label for="surface">Surface (m²)</label>
                        <input type="number" id="surface" name="surface" placeholder="Ex: 120" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="rooms">Nombre de chambres</label>
                        <input type="number" id="rooms" name="rooms" placeholder="Ex: 3" required>
                    </div>
                    <div class="form-group">
                        <label for="bathrooms">Nombre de salles de bain</label>
                        <input type="number" id="bathrooms" name="bathrooms" placeholder="Ex: 2" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5" placeholder="Décrivez votre bien..." required></textarea>
                </div>
                <div class="form-group">
                    <label for="photos">Photos</label>
                    <input type="file" id="photos" name="photos[]" multiple accept="image/*"
                           style="display:block;width:100%;padding:1rem;border:2px dashed #6b8ca8;border-radius:10px;cursor:pointer;background:#f8f9fa;font-family:'Outfit',sans-serif;box-sizing:border-box;">
                    <p style="color:#888;font-size:0.85rem;margin-top:5px;">Vous pouvez sélectionner plusieurs photos</p>
                </div>
                <button type="submit" class="btn-submit">Soumettre l'annonce</button>
            </form>
        </div>
    </main>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section"><p>&copy; 2026 HSimo. Tous droits réservés.</p></div>
        </div>
    </footer>
</body>
</html>