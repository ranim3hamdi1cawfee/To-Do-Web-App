SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- 1. Create the Group Table first so the user table can reference it
CREATE TABLE IF NOT EXISTS `group` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `group` (`id`, `name`) VALUES 
(1, 'Development Alpha'), 
(2, 'UI/UX Design Team'),
(3, 'QA Testing Squad');

-- 2. Create the expanded User Table
CREATE TABLE IF NOT EXISTS `user` (
  `id`             INT(11)       NOT NULL AUTO_INCREMENT,
  `username`       VARCHAR(50)   NOT NULL UNIQUE,
  `password`       VARCHAR(255)  NOT NULL,
  `birthday`       DATE          NOT NULL,                         -- Added field
  `role`           ENUM('Admin','Regular') NOT NULL DEFAULT 'Regular', -- Handled via fallback or form
  `group_id`       INT(11)       DEFAULT NULL,                     -- Added link
  `profile_image`  VARCHAR(255)  DEFAULT 'default_avatar.png',
  `motto`          VARCHAR(255)  DEFAULT 'Stay focused.',
  `current_streak` INT(11)       DEFAULT 0,
  `created_at`     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_user_group_reg` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;