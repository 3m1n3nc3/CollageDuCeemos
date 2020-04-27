SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"; 

ALTER TABLE `configuration` ADD `allow_reg` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `allow_login`;

ALTER TABLE `configuration` ADD `welcome_email` INT(4) NOT NULL DEFAULT '0' AFTER `allow_reg`;

ALTER TABLE `configuration` ADD `email_verification` INT(4) NOT NULL DEFAULT '0' AFTER `welcome_email`;
