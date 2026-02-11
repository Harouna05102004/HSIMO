<?php
/**
 * Script PHP pour l'envoi d'emails d'estimation
 * 
 * IMPORTANT: Ce fichier nécessite un serveur PHP avec la fonction mail() activée
 * Pour tester en local, utilisez EmailJS ou un service SMTP
 */

// Configuration
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Votre email où recevoir les demandes d'estimation
define('RECIPIENT_EMAIL', 'votre-email@example.com');

// Fonction de validation
function validateData($data) {
    $required = ['address', 'city', 'postal', 'type', 'rooms', 'surface', 'year', 'condition'];
    
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return false;
        }
    }
    
    return true;
}

// Traitement de la requête
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données JSON
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    // Valider les données
    if (!validateData($data)) {
        http_response_code(400);
        echo json_encode(['error' => 'Données invalides']);
        exit;
    }
    
    // Préparer l'email
    $subject = 'Nouvelle demande d\'estimation immobilière';
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #1a3a52; color: white; padding: 20px; text-align: center; }
            .content { background-color: #f8f9fa; padding: 20px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #1a3a52; }
            .value { color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Demande d'Estimation</h1>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='label'>Adresse :</span>
                    <span class='value'>" . htmlspecialchars($data['address']) . "</span>
                </div>
                <div class='field'>
                    <span class='label'>Ville :</span>
                    <span class='value'>" . htmlspecialchars($data['city']) . "</span>
                </div>
                <div class='field'>
                    <span class='label'>Code postal :</span>
                    <span class='value'>" . htmlspecialchars($data['postal']) . "</span>
                </div>
                <div class='field'>
                    <span class='label'>Type de bien :</span>
                    <span class='value'>" . htmlspecialchars($data['type']) . "</span>
                </div>
                <div class='field'>
                    <span class='label'>Nombre de pièces :</span>
                    <span class='value'>" . htmlspecialchars($data['rooms']) . "</span>
                </div>
                <div class='field'>
                    <span class='label'>Surface :</span>
                    <span class='value'>" . htmlspecialchars($data['surface']) . " m²</span>
                </div>
                <div class='field'>
                    <span class='label'>Année de construction :</span>
                    <span class='value'>" . htmlspecialchars($data['year']) . "</span>
                </div>
                <div class='field'>
                    <span class='label'>État :</span>
                    <span class='value'>" . htmlspecialchars($data['condition']) . "</span>
                </div>
                <hr>
                <p style='color: #666; font-size: 12px;'>
                    Cette demande a été soumise le " . date('d/m/Y à H:i') . "
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Headers pour l'email HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: noreply@votre-site.com" . "\r\n";
    
    // Envoyer l'email
    $success = mail(RECIPIENT_EMAIL, $subject, $message, $headers);
    
    if ($success) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Email envoyé avec succès']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de l\'envoi de l\'email']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
}
?>
