SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"; 

ALTER TABLE `users` ADD `access_token` varchar(255) DEFAULT NULL AFTER `reg_date`;;

ALTER TABLE `admin` ADD `access_token` varchar(255) DEFAULT NULL AFTER `admin_user`;
ALTER TABLE `admin` ADD `email` varchar(128) DEFAULT NULL AFTER `username`;

ALTER TABLE `configuration` ADD `smtp_debug` enum('0','1','2') NOT NULL DEFAULT '0' AFTER `smtp_password`;

ALTER TABLE `categories` ADD `restricted` enum('0','1') NOT NULL DEFAULT '0' AFTER `info`;

ALTER TABLE `static_pages` ADD `restricted` enum('0','1') NOT NULL DEFAULT '0' AFTER `header`;
