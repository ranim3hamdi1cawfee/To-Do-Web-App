-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 29, 2026 at 12:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `taskflow`
--

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260526101451', '2026-05-26 12:15:47', 99);

-- --------------------------------------------------------

--
-- Table structure for table `group`
--

CREATE TABLE `group` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group`
--

INSERT INTO `group` (`id`, `name`, `created_at`) VALUES
(1, 'Development Alpha', '2026-05-27 09:33:07'),
(2, 'UI/UX Design Team', '2026-05-27 09:33:07'),
(3, 'QA Testing Squad', '2026-05-27 09:33:07'),
(4, 'Backend Team', '2026-05-27 09:33:07'),
(5, 'DevOps', '2026-05-27 09:33:07');

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `priority` varchar(10) NOT NULL,
  `movement` varchar(20) NOT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`tags`)),
  `due_date` varchar(20) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`id`, `title`, `priority`, `movement`, `tags`, `due_date`, `group_id`, `created_by`) VALUES
(1, 'Create login page UI', 'medium', 'andante', '[\"ui\",\"login\"]', '2026-05-27', 5, NULL),
(2, 'Implement authentication', 'high', 'moderato', '[\"auth\",\"security\"]', '2026-05-28', 5, NULL),
(3, 'Build task CRUD API', 'high', 'allegro', '[\"api\",\"backend\"]', '2026-05-29', 5, NULL),
(4, 'Fix responsive layout bugs', 'low', 'moderato', '[\"css\",\"responsive\"]', '2026-05-20', 5, NULL),
(5, 'Write API documentation', 'medium', 'moderato', '[\"docs\",\"api\"]', '2026-05-27', NULL, NULL),
(6, 'Optimize database queries', 'high', 'moderato', '[\"backend\",\"performance\"]', '2026-05-30', NULL, NULL),
(7, 'Design kanban board layout', 'medium', 'allegro', '[\"ui, kanban\"]', '2026-05-26', 1, NULL),
(13, 'Rather complicated task', 'medium', 'andante', '[\"backend\"]', '2026-05-28', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `role` enum('Admin','Regular') NOT NULL DEFAULT 'Regular',
  `group_id` int(11) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT 'default_avatar.png',
  `motto` varchar(255) DEFAULT 'Stay focused.',
  `current_streak` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `birthday`, `role`, `group_id`, `profile_image`, `motto`, `current_streak`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1995-03-15', 'Admin', 1, 'default_avatar.png', 'Lead with purpose.', 0, '2026-05-27 09:33:08'),
(2, 'zeineb', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2000-05-20', 'Regular', 2, 'default_avatar.png', 'Design is everything.', 0, '2026-05-27 09:33:08'),
(4, 'hamdi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2001-12-01', 'Regular', 3, 'default_avatar.png', 'Test everything.', 0, '2026-05-27 09:33:08'),
(5, 'cawfee', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1998-07-25', 'Regular', 4, 'default_avatar.png', 'Ship it.', 0, '2026-05-27 09:33:08'),
(6, 'ranime', '$2y$10$ByvuMth3XKTKIBN8R2dJxukOB3sOWA5Vz6/CY.Sa8o5edHLNjCY5y', '2005-09-30', 'Regular', 5, 'default_avatar.png', 'Stay focused.', 0, '2026-05-27 20:36:59'),
(7, 'emna', '$2y$10$d8qi0ekzdETQZ4ocwl6WkuBp8qEfdluCRCN/VyAxjo2vbN1KJWT16', '2004-02-02', 'Regular', 3, 'default_avatar.png', 'Stay focused.', 0, '2026-05-27 21:06:22'),
(8, 'rouda', '$2y$13$dyBqvMBVJx5KHivm0McsW.95NVjQR.TDDKXj8aZOw.rTC97Hc1aB2', '2005-12-11', 'Admin', 4, 'default_avatar.png', 'Stay focused.', 0, '2026-05-27 21:06:45'),
(10, 'khadija', '$2y$13$thZ.sSyn9/g7GyueV5AZTeDvzCdnqhyGO4vdAdm1PkkI5qY0wU5nO', '1980-09-01', 'Regular', 5, 'default_avatar.png', 'Stay focused.', 0, '2026-05-28 17:21:05'),
(11, 'roudayna', '$2y$13$LLp5/3nBS9w1X6/TyA96AOP8Z5zxODJ8VqRhE5oYAXqxuDrB90p9q', '2006-11-15', 'Regular', NULL, 'default_avatar.png', 'Stay focused.', 0, '2026-05-28 16:22:05'),
(13, 'josh', '$2y$10$dI7UgllO/zxLhXlF6K10zOKnI5qgj4g3kjPZgQhmZBnf97iDDEJs.', '2005-10-14', 'Admin', 5, 'default_avatar.png', 'Stay focused.', 0, '2026-05-28 19:18:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `group`
--
ALTER TABLE `group`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_task_user` (`created_by`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_user_group` (`group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `group`
--
ALTER TABLE `group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `fk_task_user` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_group` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
