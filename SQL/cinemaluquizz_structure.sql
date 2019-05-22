-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Client :  localhost:3306
-- Généré le :  Mer 22 Mai 2019 à 10:33
-- Version du serveur :  5.7.26-0ubuntu0.18.04.1
-- Version de PHP :  7.2.17-0ubuntu0.18.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `cinemaluquizz`
--

-- --------------------------------------------------------

--
-- Structure de la table `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `partie`
--

CREATE TABLE `partie` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `imagefondname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `theme` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `colortext` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `colortitre` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `colorfenetre` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fontpolice` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fontsize` double NOT NULL,
  `modejeux` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `question`
--

CREATE TABLE `question` (
  `id` int(11) NOT NULL,
  `partie_id` int(11) NOT NULL,
  `libelle` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `ouverte` tinyint(1) NOT NULL DEFAULT '0',
  `piecejointe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` int(11) NOT NULL,
  `timer` int(11) NOT NULL,
  `videoyoutube` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fontsize` double NOT NULL,
  `cadeau` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reponse`
--

CREATE TABLE `reponse` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `timereponse` decimal(20,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reponse_possible`
--

CREATE TABLE `reponse_possible` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `libelle` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `piecejointe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correct` tinyint(1) NOT NULL,
  `fontsize` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reponse_reponse_possible`
--

CREATE TABLE `reponse_reponse_possible` (
  `reponse_id` int(11) NOT NULL,
  `reponse_possible_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `login` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mdp` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur_partie`
--

CREATE TABLE `utilisateur_partie` (
  `utilisateur_id` int(11) NOT NULL,
  `partie_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `partie`
--
ALTER TABLE `partie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B6F7494EE075F7A4` (`partie_id`);

--
-- Index pour la table `reponse`
--
ALTER TABLE `reponse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5FB6DEC7FB88E14F` (`utilisateur_id`),
  ADD KEY `IDX_5FB6DEC71E27F6BF` (`question_id`);

--
-- Index pour la table `reponse_possible`
--
ALTER TABLE `reponse_possible`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_21290E491E27F6BF` (`question_id`);

--
-- Index pour la table `reponse_reponse_possible`
--
ALTER TABLE `reponse_reponse_possible`
  ADD PRIMARY KEY (`reponse_id`,`reponse_possible_id`),
  ADD KEY `IDX_2AD38062CF18BB82` (`reponse_id`),
  ADD KEY `IDX_2AD38062C53BC6BC` (`reponse_possible_id`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateur_partie`
--
ALTER TABLE `utilisateur_partie`
  ADD PRIMARY KEY (`utilisateur_id`,`partie_id`),
  ADD KEY `IDX_643625B6FB88E14F` (`utilisateur_id`),
  ADD KEY `IDX_643625B6E075F7A4` (`partie_id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `partie`
--
ALTER TABLE `partie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `question`
--
ALTER TABLE `question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `reponse`
--
ALTER TABLE `reponse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `reponse_possible`
--
ALTER TABLE `reponse_possible`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `FK_B6F7494EE075F7A4` FOREIGN KEY (`partie_id`) REFERENCES `partie` (`id`);

--
-- Contraintes pour la table `reponse`
--
ALTER TABLE `reponse`
  ADD CONSTRAINT `FK_5FB6DEC71E27F6BF` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`),
  ADD CONSTRAINT `FK_5FB6DEC7FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `reponse_possible`
--
ALTER TABLE `reponse_possible`
  ADD CONSTRAINT `FK_21290E491E27F6BF` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`);

--
-- Contraintes pour la table `reponse_reponse_possible`
--
ALTER TABLE `reponse_reponse_possible`
  ADD CONSTRAINT `FK_2AD38062C53BC6BC` FOREIGN KEY (`reponse_possible_id`) REFERENCES `reponse_possible` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_2AD38062CF18BB82` FOREIGN KEY (`reponse_id`) REFERENCES `reponse` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `utilisateur_partie`
--
ALTER TABLE `utilisateur_partie`
  ADD CONSTRAINT `FK_643625B6E075F7A4` FOREIGN KEY (`partie_id`) REFERENCES `partie` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_643625B6FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
