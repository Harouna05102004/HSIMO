# HSimo 🏠

Plateforme immobilière en ligne permettant la vente, la location et l'estimation de biens immobiliers.

## 📋 Description

HSimo est un site e-commerce immobilier développé dans le cadre du projet de développement web à l'EFREI. Il permet aux utilisateurs de consulter, publier et gérer des annonces immobilières.

## ✨ Fonctionnalités

- 🔐 Inscription / Connexion sécurisée
- 🏡 Consultation des annonces (vente & location)
- 📝 Publication d'annonces
- 💰 Achat et location de biens
- 📊 Estimation de biens immobiliers
- 💬 Messagerie entre utilisateurs
- ⚙️ Back-office administrateur

## 🛠️ Technologies utilisées

| Couche | Technologies |
|--------|-------------|
| Frontend | HTML5, CSS3, JavaScript, Bootstrap 5 |
| Backend | PHP 8 |
| Base de données | MySQL 8, PhpMyAdmin |
| Hébergement | Microsoft Azure VM (Debian + Apache) |
| Outils | VS Code, GitHub, Figma, XAMPP |

## 🚀 Installation locale

1. Clone le repo :
```bash
git clone https://github.com/Harouna05102004/HSIMO.git
```

2. Copie les fichiers dans ton dossier XAMPP :
```bash
cp -r HSIMO/ C:/xampp/htdocs/HSimo
```

3. Importe la base de données dans PhpMyAdmin :
- Ouvre `http://localhost/phpmyadmin`
- Crée une base de données `hsimo`
- Importe le fichier `hsimo.sql`

4. Configure la connexion dans `config.php` :
```php
$host = "localhost";
$dbname = "hsimo";
$user = "root";
$password = "";
```

5. Lance XAMPP et ouvre `http://localhost/HSimo`

## 🌐 Démo en ligne

> http://158.158.0.191

## 👥 Équipe

- **Harouna Diakité** — EFREI 2025

## 📄 Licence

Projet académique — EFREI 2025
