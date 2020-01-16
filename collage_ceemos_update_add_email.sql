SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"; 

ALTER TABLE `users` ADD `access_token` varchar(255) DEFAULT NULL;

ALTER TABLE `admin` ADD `access_token` varchar(255) DEFAULT NULL;

ALTER TABLE `configuration` ADD `smtp_debug` enum('0','1','2') NOT NULL DEFAULT '0';
