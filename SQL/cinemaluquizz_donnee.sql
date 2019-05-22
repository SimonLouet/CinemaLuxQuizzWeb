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

--
-- Contenu de la table `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('20190421113915', '2019-04-21 11:39:29');

--
-- Contenu de la table `partie`
--

INSERT INTO `partie` (`id`, `nom`, `date`, `imagefondname`, `description`, `theme`, `colortext`, `colortitre`, `colorfenetre`, `fontpolice`, `fontsize`, `modejeux`) VALUES
(2, 'Retour vers le futur', '2020-01-05', '102dad73f582cd14c01ef22d7c166e17.jpeg', NULL, 'sf', '#ffffff', '#ffffff', '#ea4335', 'Black Ops One', 40, 'TourParTour');

--
-- Contenu de la table `question`
--

INSERT INTO `question` (`id`, `partie_id`, `libelle`, `ouverte`, `piecejointe`, `numero`, `timer`, `videoyoutube`, `fontsize`, `cadeau`) VALUES
(3, 2, 'Dans quelle ville habite Marty McFly ?', 0, NULL, 1, 10000, NULL, 50, NULL),
(4, 2, 'Quel est le prénom de Doc Brown ?', 0, NULL, 2, 10000, NULL, 50, NULL);

--
-- Contenu de la table `reponse`
--

INSERT INTO `reponse` (`id`, `utilisateur_id`, `question_id`, `timereponse`) VALUES
(1, 1, 4, '1558419763.740');

--
-- Contenu de la table `reponse_possible`
--

INSERT INTO `reponse_possible` (`id`, `question_id`, `libelle`, `piecejointe`, `correct`, `fontsize`) VALUES
(5, 3, 'Kingstown Falls', NULL, 0, 40),
(6, 3, 'Hill Valley', NULL, 1, 40),
(7, 3, 'Tombstone', NULL, 0, 40),
(8, 4, 'Albert', NULL, 0, 40),
(9, 4, 'John', NULL, 0, 40),
(10, 4, 'Emmet', NULL, 1, 40);

--
-- Contenu de la table `reponse_reponse_possible`
--

INSERT INTO `reponse_reponse_possible` (`reponse_id`, `reponse_possible_id`) VALUES
(1, 10);

--
-- Contenu de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `login`, `mdp`, `mail`) VALUES
(1, 'Simon', '36e029791042c080a5e8cd9da51ef8ee', 'simon.louet.98@gmail.com');

--
-- Contenu de la table `utilisateur_partie`
--

INSERT INTO `utilisateur_partie` (`utilisateur_id`, `partie_id`) VALUES
(1, 2);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
