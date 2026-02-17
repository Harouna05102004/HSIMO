-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 17 fév. 2026 à 22:34
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `hsimo`
--

-- --------------------------------------------------------

--
-- Structure de la table `bien`
--

CREATE TABLE `bien` (
  `id_bien` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `surface` int(11) NOT NULL,
  `nb_chambres` int(11) NOT NULL,
  `nb_salles_bain` int(11) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `categorie` varchar(50) DEFAULT NULL,
  `photos` text DEFAULT NULL,
  `id_vendeur` int(11) NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `bien`
--

INSERT INTO `bien` (`id_bien`, `titre`, `description`, `prix`, `surface`, `nb_chambres`, `nb_salles_bain`, `ville`, `adresse`, `categorie`, `photos`, `id_vendeur`, `date_creation`) VALUES
(1, 'belle villa avec piscine', 'belle villa', 450000.00, 120, 4, 3, 'creiteil', NULL, NULL, '', 3, '2026-02-17 09:23:19'),
(2, 'villa', 'venez belle villa', 450000.00, 130, 4, 3, 'montmagny', NULL, NULL, '69943913262dd_Design sans titre.png', 4, '2026-02-17 09:46:59'),
(4, 'villa', 'ffxhghdtfnfgj', 5000000.00, 130, 3, 4, 'creteil', NULL, NULL, '6994846bd06e7_bfbbb1f27b41bbbbfd200f6c82b38f18.jpg', 3, '2026-02-17 15:08:27');

-- --------------------------------------------------------

--
-- Structure de la table `estimations`
--

CREATE TABLE `estimations` (
  `id_estimation` int(11) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `code_postal` varchar(10) NOT NULL,
  `type_bien` varchar(50) NOT NULL,
  `nb_pieces` int(11) NOT NULL,
  `surface` int(11) NOT NULL,
  `annee_construction` int(11) NOT NULL,
  `etat` varchar(50) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `date_demande` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `estimations`
--

INSERT INTO `estimations` (`id_estimation`, `adresse`, `ville`, `code_postal`, `type_bien`, `nb_pieces`, `surface`, `annee_construction`, `etat`, `id_user`, `date_demande`) VALUES
(1, '5 rue baumarchais', 'paris', '75015', 'maison', 4, 100, 2000, 'bon', 4, '2026-02-17 10:21:54'),
(2, '5 rue gallieni', 'Montmagny', '95360', 'maison', 4, 130, 2003, 'bon', 3, '2026-02-17 15:42:21');

-- --------------------------------------------------------

--
-- Structure de la table `favoris`
--

CREATE TABLE `favoris` (
  `id_favori` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_bien` int(11) NOT NULL,
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id_message` int(11) NOT NULL,
  `id_bien` int(11) NOT NULL,
  `id_expediteur` int(11) NOT NULL,
  `message` text NOT NULL,
  `date_envoi` timestamp NOT NULL DEFAULT current_timestamp(),
  `lu` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id_message`, `id_bien`, `id_expediteur`, `message`, `date_envoi`, `lu`) VALUES
(1, 2, 4, 'je veux cette maison !!', '2026-02-17 10:01:28', 0),
(2, 2, 4, 'je veux cette maison !!', '2026-02-17 10:01:44', 0),
(3, 2, 4, 'je veux cette maison !!', '2026-02-17 10:03:59', 0),
(4, 2, 4, 'je veux cette maison !!', '2026-02-17 10:06:33', 0),
(5, 2, 4, 'je veux cette maison !!', '2026-02-17 10:08:30', 0),
(6, 2, 4, 'je veux cette maison !!', '2026-02-17 10:08:46', 0),
(7, 2, 4, 'gdrhhrtht', '2026-02-17 10:08:50', 0),
(9, 4, 3, 'bvb', '2026-02-17 15:08:40', 0);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'user2', '$2y$10$8UCqdW3yDjxw1yWsBwZ6beXsjc.55h8OB/tjiZhYYtG4Hc1xir94K', '2026-02-13 17:02:53'),
(2, 'admin', '$2y$10$U1nz9Utt4GiK8xoZJ.EyMuG.qESavicdq34ZaMxAq11NHOZK/52T6', '2026-02-13 17:03:30'),
(3, 'harouna1', '$2y$10$YGJzjfa7o2cEVKBukRTXS./HH/RasKxeLsbE92fNQJ5elw0g2dVvy', '2026-02-13 17:06:56'),
(4, 'sevan', '$2y$10$50JcxgeLLZHJGVeBFfvWa.Rso5Ei5DDdNIz.oZ58Ux8HLA3GKWtOa', '2026-02-17 09:36:04');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `bien`
--
ALTER TABLE `bien`
  ADD PRIMARY KEY (`id_bien`),
  ADD KEY `id_vendeur` (`id_vendeur`);

--
-- Index pour la table `estimations`
--
ALTER TABLE `estimations`
  ADD PRIMARY KEY (`id_estimation`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD PRIMARY KEY (`id_favori`),
  ADD UNIQUE KEY `unique_favori` (`id_user`,`id_bien`),
  ADD KEY `id_bien` (`id_bien`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `id_bien` (`id_bien`),
  ADD KEY `id_expediteur` (`id_expediteur`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `bien`
--
ALTER TABLE `bien`
  MODIFY `id_bien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `estimations`
--
ALTER TABLE `estimations`
  MODIFY `id_estimation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `favoris`
--
ALTER TABLE `favoris`
  MODIFY `id_favori` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `bien`
--
ALTER TABLE `bien`
  ADD CONSTRAINT `bien_ibfk_1` FOREIGN KEY (`id_vendeur`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `estimations`
--
ALTER TABLE `estimations`
  ADD CONSTRAINT `estimations_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD CONSTRAINT `favoris_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favoris_ibfk_2` FOREIGN KEY (`id_bien`) REFERENCES `bien` (`id_bien`) ON DELETE CASCADE;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`id_bien`) REFERENCES `bien` (`id_bien`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`id_expediteur`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
