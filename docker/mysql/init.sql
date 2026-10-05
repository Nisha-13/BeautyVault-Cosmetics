-- MySQL initialization script for BeautyVault
-- Run automatically on first container start

CREATE DATABASE IF NOT EXISTS `beautyvault` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS `beautyvault_testing` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON `beautyvault`.* TO 'beautyvault'@'%';
GRANT ALL PRIVILEGES ON `beautyvault_testing`.* TO 'beautyvault'@'%';
FLUSH PRIVILEGES;
