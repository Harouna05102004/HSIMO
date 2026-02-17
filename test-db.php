<?php
// Fichier de test pour vérifier la connexion à la base de données

echo "<h1>Test de connexion à la base de données</h1>";

// Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'hsimo');
define('DB_USER', 'root');
define('DB_PASS', '');

echo "<p><strong>Configuration :</strong></p>";
echo "<ul>";
echo "<li>Host: " . DB_HOST . "</li>";
echo "<li>Database: " . DB_NAME . "</li>";
echo "<li>User: " . DB_USER . "</li>";
echo "<li>Password: " . (empty(DB_PASS) ? '(vide)' : '******') . "</li>";
echo "</ul>";

// Test de connexion
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "<p style='color: green; font-weight: bold;'>✅ Connexion réussie à la base de données !</p>";
    
    // Vérifier si la table users existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ La table 'users' existe</p>";
        
        // Compter les utilisateurs
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "<p>Nombre d'utilisateurs dans la table : " . $result['count'] . "</p>";
    } else {
        echo "<p style='color: red;'>❌ La table 'users' n'existe PAS</p>";
        echo "<p>Execute cette requête SQL dans phpMyAdmin :</p>";
        echo "<pre style='background: #f0f0f0; padding: 10px;'>
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
</pre>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ Erreur de connexion !</p>";
    echo "<p style='color: red;'>Message d'erreur : " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='signup.php'>Retour à l'inscription</a></p>";
?>
