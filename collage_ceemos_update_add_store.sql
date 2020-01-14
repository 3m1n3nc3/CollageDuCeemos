SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

INSERT INTO `categories` (`title`, `value`, `info`) VALUES 
('Store', 'store', 'Pick from beautifully crafted artworks, buy and get them delivered to you in the comfort of your home.');

INSERT INTO `static_pages` (`title`, `jarallax`, `icon`, `content`, `parent`, `safelink`, `footer`, `header`, `priority`, `date`) VALUES
('Art Store', '', 'fa-shopping-cart', '<p>Shop beautiful artifacts created by we and our partners, buy and get it delivered to you in the comfort of your home</p>', 'store', 'art-store', '0', '0', '3', '2020-01-06 19:29:19');

CREATE TABLE IF NOT EXISTS `extend_views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `by` int(11) NOT NULL,
  `item` int(11) NOT NULL,
  `type` enum('0','1') NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `by` (`by`) USING BTREE,
  KEY `time` (`time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(128) DEFAULT NULL,
  `lname` varchar(128) DEFAULT NULL,
  `username` varchar(128) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `reference` varchar(128) DEFAULT NULL,
  `address` text,
  `total` decimal(10,2) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('0','1','2') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) DEFAULT NULL,
  `artist` varchar(128) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `discount` int(11) DEFAULT NULL,
  `shipping` decimal(10,2) DEFAULT NULL,
  `description` text,
  `available_qty` int(11) DEFAULT NULL,
  `image1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `sold_date` datetime DEFAULT NULL,
  `public` enum('0','1') NOT NULL DEFAULT '1',
  `featured` enum('0','1') NOT NULL DEFAULT '0',
  `promoted` enum('0','1') NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `safelink` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `store` ADD FULLTEXT KEY `description` (`description`);
 
ALTER TABLE `configuration` ADD `enable_store` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `mode`;
