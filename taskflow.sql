-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : mer. 27 mai 2026 à 12:22
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `taskflow`
--

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260526101451', '2026-05-26 12:15:47', 99);

-- --------------------------------------------------------

--
-- Structure de la table `group`
--

CREATE TABLE IF NOT EXISTS `group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL UNIQUE,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `group`
--

INSERT INTO `group` (`id`, `name`, `created_at`) VALUES
(1, 'Development Alpha', '2026-05-27 09:33:07'),
(2, 'UI/UX Design Team', '2026-05-27 09:33:07'),
(3, 'QA Testing Squad', '2026-05-27 09:33:07'),
(4, 'Backend Team', '2026-05-27 09:33:07'),
(5, 'DevOps', '2026-05-27 09:33:07');

-- --------------------------------------------------------

--
-- Structure de la table `task`
--

CREATE TABLE IF NOT EXISTS `task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `priority` varchar(10) NOT NULL,
  `movement` varchar(20) NOT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`tags`)),
  `due_date` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `task`
--

INSERT INTO `task` (`id`, `title`, `priority`, `movement`, `tags`, `due_date`) VALUES
(5, 'Setup project repository', 'high', 'allegro', '[\"setup\",\"git\"]', '2026-04-01'),
(6, 'Design database schema', 'high', 'allegro', '[\"database\",\"design\"]', '2026-04-05'),
(7, 'Create login page UI', 'medium', 'allegro', '[\"ui\",\"login\"]', '2026-04-08'),
(8, 'Implement authentication', 'high', 'allegro', '[\"auth\",\"security\"]', '2026-04-10'),
(9, 'Build task CRUD API', 'high', 'allegro', '[\"api\",\"backend\"]', '2026-04-12'),
(10, 'Design kanban board layout', 'medium', 'allegro', '[\"ui\",\"kanban\"]', '2026-04-15'),
(11, 'Connect frontend to API', 'high', 'moderato', '[\"frontend\",\"api\"]', '2026-05-01'),
(12, 'Add drag and drop support', 'medium', 'moderato', '[\"ui\",\"kanban\"]', '2026-05-05'),
(13, 'Implement task filtering', 'low', 'moderato', '[\"frontend\",\"filter\"]', '2026-05-08'),
(14, 'Write unit tests for API', 'medium', 'moderato', '[\"testing\",\"api\"]', '2026-05-10'),
(15, 'Add priority color indicators', 'low', 'moderato', '[\"ui\",\"design\"]', '2026-05-15'),
(16, 'Implement due date warnings', 'medium', 'andante', '[\"frontend\",\"ux\"]', '2026-05-18'),
(17, 'Add user profile page', 'medium', 'andante', '[\"ui\",\"profile\"]', '2026-05-20'),
(18, 'Implement role-based access', 'high', 'andante', '[\"security\",\"roles\"]', '2026-05-22'),
(19, 'Add task search feature', 'low', 'moderato', '[\"frontend\",\"search\"]', '2026-05-25'),
(20, 'Fix mobile responsiveness', 'medium', 'andante', '[\"css\",\"responsive\"]', '2026-05-28'),
(21, 'Add toast notifications', 'low', 'moderato', '[\"ui\",\"ux\"]', '2026-05-30'),
(22, 'Optimize database queries', 'high', 'andante', '[\"backend\",\"performance\"]', '2026-06-01'),
(23, 'Write API documentation', 'medium', 'andante', '[\"docs\",\"api\"]', '2026-06-03'),
(24, 'Deploy to production server', 'high', 'andante', '[\"devops\",\"deploy\"]', '2026-06-05'),
(25, 'Add dark mode toggle', 'low', 'moderato', '[\"ui\",\"design\"]', '2026-06-08'),
(28, 'Create admin dashboard', 'high', 'andante', '[\"admin\",\"ui\"]', '2026-06-15'),
(29, 'Add CSV export for tasks', 'low', 'moderato', '[\"feature\",\"export\"]', '2026-06-18');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `role` enum('Admin','Regular') NOT NULL DEFAULT 'Regular',
  `group_id` int(11) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT 'default_avatar.png', -- FIXED: Storing clean file names only
  `motto` varchar(255) DEFAULT 'Stay focused.',
  `current_streak` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_user_group` (`group_id`),
  CONSTRAINT `fk_user_group` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- AUTO_INCREMENT pour les tables déchargées
--

-- AUTO_INCREMENT pour la table `group`
ALTER TABLE `group` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

-- AUTO_INCREMENT pour la table `task`
ALTER TABLE `task` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

-- AUTO_INCREMENT pour la table `user`
ALTER TABLE `user` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;