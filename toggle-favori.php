<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'non_connecte', 'message' => 'Vous devez être connecté.']);
    exit;
}

// Récupérer id_bien depuis POST (FormData ou urlencoded ou body brut)
$id_bien = 0;
if (isset($_POST['id_bien'])) {
    $id_bien = intval($_POST['id_bien']);
} else {
    $input = file_get_contents('php://input');
    parse_str($input, $data);
    $id_bien = intval($data['id_bien'] ?? 0);
}

$id_user = $_SESSION['user_id'];

if ($id_bien === 0) {
    echo json_encode(['error' => 'invalid', 'post' => $_POST]);
    exit;
}

// Vérifier si déjà en favoris
$stmt = $pdo->prepare("SELECT id_favori FROM favoris WHERE id_user = ? AND id_bien = ?");
$stmt->execute([$id_user, $id_bien]);
$existing = $stmt->fetch();

if ($existing) {
    // Supprimer le favori
    $stmt = $pdo->prepare("DELETE FROM favoris WHERE id_user = ? AND id_bien = ?");
    $stmt->execute([$id_user, $id_bien]);
    echo json_encode(['status' => 'removed', 'message' => 'Retiré des favoris']);
} else {
    // Ajouter le favori
    $stmt = $pdo->prepare("INSERT INTO favoris (id_user, id_bien) VALUES (?, ?)");
    $stmt->execute([$id_user, $id_bien]);
    echo json_encode(['status' => 'added', 'message' => 'Ajouté aux favoris']);
}