-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 11, 2019 at 10:04 AM
-- Server version: 5.7.27-0ubuntu0.19.04.1
-- PHP Version: 7.2.19-0ubuntu0.19.04.2 

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `collage_ceemos`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

ALTER TABLE `admin` ADD `admin_user` int(11) DEFAULT NULL AFTER `password`; 
ALTER TABLE `configuration` ADD `company` varchar(128) DEFAULT NULL AFTER `email`;
ALTER TABLE `configuration` ADD `company_url` varchar(128) DEFAULT NULL AFTER `company`;
ALTER TABLE `configuration` ADD `allow_login` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `ads_off`;
 
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
