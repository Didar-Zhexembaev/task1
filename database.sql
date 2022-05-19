CREATE DATABASE `web_form`;
USE `web_form`;
CREATE TABLE `users` (`id` INT AUTO_INCREMENT, `email` VARCHAR(255) NOT NULL, `phone` CHAR(18) NOT NULL, `message` VARCHAR(300), PRIMARY KEY (`id`));