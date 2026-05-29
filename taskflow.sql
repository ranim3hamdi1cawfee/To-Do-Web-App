-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 29, 2026 at 09:02 PM
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
  `user_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'in progress'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`id`, `title`, `priority`, `movement`, `tags`, `due_date`, `group_id`, `user_id`, `status`) VALUES
(5, 'Setup project repository', 'high', 'allegro', '[\"setup\",\"git\"]', '2026-04-01', 2, 2, 'in progress'),
(6, 'Design database schema', 'high', 'allegro', '[\"database\",\"design\"]', '2026-04-05', 2, 2, 'in progress'),
(7, 'Create login page UI', 'medium', 'allegro', '[\"ui\",\"login\"]', '2026-04-08', 2, 2, 'in progress'),
(8, 'Implement authentication', 'high', 'allegro', '[\"auth\",\"security\"]', '2026-04-10', 1, 3, 'in progress'),
(9, 'Build task CRUD API', 'high', 'allegro', '[\"api\",\"backend\"]', '2026-04-12', 1, 3, 'in progress'),
(10, 'Design kanban board layout', 'medium', 'allegro', '[\"ui\",\"kanban\"]', '2026-04-15', 1, 3, 'in progress'),
(11, 'Connect frontend to API', 'high', 'allegro', '[\"frontend\",\"api\"]', '2026-05-01', 3, 4, 'in progress'),
(12, 'Add drag and drop support', 'medium', 'allegro', '[\"ui\",\"kanban\"]', '2026-05-05', 3, 4, 'in progress'),
(13, 'Implement task filtering', 'low', 'moderato', '[\"frontend\",\"filter\"]', '2026-05-08', 3, 4, 'in progress'),
(14, 'Write unit tests for API', 'medium', 'moderato', '[\"testing\",\"api\"]', '2026-05-10', 4, 5, 'in progress'),
(15, 'Add priority color indicators', 'low', 'moderato', '[\"ui\",\"design\"]', '2026-05-15', 4, 5, 'in progress'),
(16, 'Implement due date warnings', 'medium', 'moderato', '[\"frontend\",\"ux\"]', '2026-05-18', 4, 5, 'in progress'),
(17, 'Add user profile page', 'medium', 'moderato', '[\"ui\",\"profile\"]', '2026-05-20', 2, 1, 'in progress'),
(18, 'Implement role-based access', 'high', 'allegro', '[\"security\",\"roles\"]', '2026-05-22', 2, 1, 'in progress'),
(20, 'Fix mobile responsiveness part2', 'high', 'allegro', '[\"css, responsive,html\"]', '2026-05-28', 2, 1, 'in progress'),
(25, 'Add dark mode toggle', 'low', 'allegro', '[\"ui\",\"design\"]', '2026-06-08', 2, 1, 'in progress'),
(29, 'Add CSV export for tasks', 'low', 'allegro', '[\"feature\",\"export\"]', '2026-06-18', 2, 1, 'in progress'),
(35, 'CCNA REVISION', 'high', 'moderato', '[\"CISCO LABS (SLAAC-DHCPv6)\"]', '2026-05-30', 2, 1, 'in progress'),
(37, 'CCNA REVISION', 'high', 'allegro', '[\"labs cisco\"]', '2026-05-31', 2, 1, 'in progress'),
(38, 'task1', 'high', 'allegro', '[]', '2026-05-29', 2, 1, 'in progress'),
(39, 'task2', 'medium', 'allegro', '[]', '2026-05-29', 2, 1, 'in progress'),
(40, 'task1', 'low', 'moderato', '[\"task1 today\"]', '2026-05-29', 2, 1, 'in progress'),
(41, 'task1', 'high', 'allegro', '[]', '2026-05-29', 2, 1, 'in progress'),
(42, 'task1 - REVISION CCNA', 'high', 'allegro', '[\"labs cisco\"]', '2026-05-30', 2, 1, 'in progress'),
(43, 'CCNA REVISION', 'high', 'moderato', '[\"cisco labs\"]', '2026-05-30', 2, 1, 'in progress'),
(47, 'CCNA REVISION 2', 'high', 'allegro', '[\"LABS DHCPv6-SLAAC\"]', '2026-05-31', 2, 2, 'in progress'),
(48, 'CCNP REVISION', 'high', 'allegro', '[\"STP\"]', '2026-05-31', 2, 1, 'in progress'),
(50, 'migration Doctrine', 'pp', 'allegro', '[\"Symfony\"]', '2025-12-2', NULL, 2, 'in progress'),
(51, 'CCNP REVISION', 'high', 'allegro', '[\"labs CISCO\"]', '2026-05-30', 2, 2, 'in progress'),
(52, 'CCNA REVISION', 'high', 'allegro', '[\"labs cisco\"]', '2026-05-30', 2, 1, 'in progress'),
(53, 'DHCPv6-SLAAC', 'high', 'allegro', '[\"LAB cisco boson NetSim\"]', '2026-05-30', 2, 1, 'in progress'),
(54, 'DHCPv6', 'high', 'moderato', '[\"lab\"]', '2026-05-30', 2, 1, 'in progress'),
(55, 'SLAAC', 'high', 'andante', '[\"labs cisco 1 and 2\"]', '2026-05-30', 2, 2, 'in progress'),
(56, 'FHRP', 'high', 'allegro', '[\"LAB CISCO VRRP\\/HSRP\"]', '2026-05-30', 2, 1, 'in progress'),
(57, 'FHRP', 'high', 'allegro', '[\"HSRP\\/VRRP\"]', '2026-05-30', 2, 1, 'in progress'),
(58, 'Merging work', 'high', 'andante', '[\"rather important\"]', '2026-05-05', 5, 6, 'in progress');

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
  `current_streak` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `birthday`, `role`, `group_id`, `profile_image`, `motto`, `current_streak`, `created_at`) VALUES
(1, 'admin', '$2y$13$uCPmON1zshFeBPDQ8s4qR.lA9lYSrQXB2djG4Gmi5jJ3AD2FeZ2Ka', '1995-03-15', 'Admin', 2, 'default_avatar.png', 'Lead with purpose.', 0, '2026-05-27 09:33:08'),
(2, 'zeineb', '$2y$13$wDN0xpI1cgwnLL0PCoJ2Huf5EC03qaFOGHtp9MlCW4SOdArKlZjiO', '2000-05-20', 'Regular', 2, 'default_avatar.png', 'Design is everything.', 0, '2026-05-27 09:33:08'),
(3, 'ranim', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1999-08-10', 'Regular', 1, 'default_avatar.png', 'Code never lies.', 0, '2026-05-27 09:33:08'),
(4, 'hamdi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2001-12-01', 'Regular', 3, 'default_avatar.png', 'Test everything.', 0, '2026-05-27 09:33:08'),
(5, 'cawfee', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1998-07-25', 'Regular', 4, 'default_avatar.png', 'Ship it.', 0, '2026-05-27 09:33:08'),
(6, 'josh', '$2y$10$.lIAgLFVveb1jaYp6aotT.0A/R5nBfQXy3DOnmVlLmv1zS0dXS.pG', '2005-05-05', 'Admin', 5, 'default_avatar.png', 'Stay focused.', 0, '2026-05-29 12:56:21'),
(7, 'roudayna', '$2y$13$f5c7rJg7OrzymdCBwoj6ZuysmDUCnfuDUADmRU99MQtin3XyIlzRi', '2005-05-05', 'Regular', NULL, 'default_avatar.png', 'Stay focused.', 0, '2026-05-29 12:04:02'),
(8, 'rouda', '$2y$10$3umv6iyAcd5GgV1alrLsXeGZ2MH4DCnBmT62Z0M0LbolFVo3Y8VD.', '2005-05-05', 'Regular', 3, '1780065715_Screenshot 2026-02-12 202209.png', 'Stay focused.', 0, '2026-05-29 13:09:38'),
(9, 'jo', '$2y$13$1J5x2.kS6C.ks9TbI5Ptwegm3ji0h8guer2hWpM8i5LoYLzRdLoWe', '2005-05-05', 'Regular', 5, 'default_avatar.png', 'Stay focused.', 0, '2026-05-29 12:10:58'),
(15, 'riri', '$2y$10$97nwlQ1e12szFv52mp9rB.Z3G6rVs7mIblKSVVIjXVQaldIvnL.36', '2005-05-05', 'Regular', 1, 'default_avatar.png', 'Stay focused.', 0, '2026-05-29 14:49:01'),
(17, 'rawen', '$2y$10$69U8vpmiCTKnINVNxUdwxueGqbwXF7o5xbQQR/lARgwAkVhvrOFsa', '2008-08-08', 'Admin', 5, '1780066378_Screenshot 2026-02-12 202857.png', 'Stay focused.', 0, '2026-05-29 14:52:35');

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
  ADD KEY `FK_527EDB25A76ED395` (`user_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `FK_527EDB25A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_group` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
