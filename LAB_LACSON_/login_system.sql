CREATE DATABASE IF NOT EXISTS `login_system`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `login_system`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Pre-seeded Accounts:
-- 1. Username: admin | Password: password123 (Role: admin)
-- 2. Username: user1 | Password: password123 (Role: user)
INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$4y9pB/Uj89iSgq0d1c1Ueup9OaH8z3wTqE7bB12c2ZgMpsN4r12ye', 'admin'),
(2, 'user1', '$2y$10$4y9pB/Uj89iSgq0d1c1Ueup9OaH8z3wTqE7bB12c2ZgMpsN4r12ye', 'user')
ON DUPLICATE KEY UPDATE `id`=`id`;
