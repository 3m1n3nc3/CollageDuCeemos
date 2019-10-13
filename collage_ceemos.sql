-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 11, 2019 at 10:04 AM
-- Server version: 5.7.27-0ubuntu0.19.04.1
-- PHP Version: 7.2.19-0ubuntu0.19.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


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

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(128) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `auth_token` varchar(128) DEFAULT NULL,
  `level` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `auth_token`, `level`) VALUES
(1, 'super', 'ce09a544f587f961dc7797bfc3781d6b', NULL, '1'),
(2, 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', NULL, '0');

-- --------------------------------------------------------

--
-- Table structure for table `allowed_config`
--

CREATE TABLE `allowed_config` (
  `id` int(11) NOT NULL,
  `name` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `allowed_config`
--

INSERT INTO `allowed_config` (`id`, `name`) VALUES
(1, 'logo'),
(2, 'intro_logo'),
(3, 'banner'),
(4, 'site_name'),
(19, 'intro_banner'),
(20, 'slug'),
(21, 'site_phone'),
(22, 'site_office'),
(23, 'twitter'),
(24, 'instagram'),
(25, 'whatsapp'),
(26, 'facebook'),
(27, 'email'),
(28, 'ads_1'),
(29, 'ads_off');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `title` varchar(128) DEFAULT NULL,
  `value` varchar(128) DEFAULT NULL,
  `info` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `title`, `value`, `info`) VALUES
(2, 'Art Event', 'event', 'See beautiful creations from people like you, working every day to make life even more beautiful'),
(3, 'Collage', 'collage', 'See beautiful creations from people like you, working every day to make life even more beautiful'),
(4, 'Painting', 'painting', 'See beautiful creations from people like you, working every day to make life even more beautiful'),
(5, 'Digital Art', 'digital-art', 'See beautiful creations from people like you, working every day to make life even more beautiful'),
(6, 'Photography', 'photography', 'See beautiful creations from people like you, working every day to make life even more beautiful'),
(7, 'Sculpture', 'sculpture', 'See beautiful creations from people like you, working every day to make life even more beautiful'),
(8, 'Exhibition', 'exhibition', 'See beautiful creations from people like you, working every day to make life even more beautiful'),
(14, 'Portfolio', 'portfolio', 'Perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam');

-- --------------------------------------------------------

--
-- Table structure for table `configuration`
--

CREATE TABLE `configuration` (
  `site_name` varchar(128) NOT NULL DEFAULT 'Passcontest',
  `logo` varchar(128) DEFAULT NULL,
  `intro_logo` varchar(128) DEFAULT NULL,
  `banner` varchar(128) DEFAULT NULL,
  `intro_banner` varchar(128) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `site_phone` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `site_office` text,
  `facebook` varchar(128) DEFAULT NULL,
  `twitter` varchar(128) DEFAULT NULL,
  `instagram` varchar(128) DEFAULT NULL,
  `whatsapp` varchar(128) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `template` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default',
  `skin` varchar(128) NOT NULL DEFAULT 'mdb-skin',
  `tracking` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'english',
  `cleanurl` enum('0','1') NOT NULL DEFAULT '0',
  `mode` enum('live','offline','debug') NOT NULL DEFAULT 'live',
  `per_page` int(11) NOT NULL DEFAULT '5',
  `per_featured` int(11) NOT NULL DEFAULT '5',
  `fbacc` enum('0','1') NOT NULL DEFAULT '1',
  `fb_appid` varchar(128) DEFAULT NULL,
  `fb_secret` varchar(128) DEFAULT NULL,
  `twilio_sid` varchar(128) DEFAULT NULL,
  `twilio_token` varchar(128) DEFAULT NULL,
  `twilio_phone` varchar(128) DEFAULT NULL,
  `captcha` enum('0','1') NOT NULL DEFAULT '0',
  `smtp` enum('0','1') NOT NULL DEFAULT '0',
  `sms` enum('0','1') NOT NULL DEFAULT '0',
  `smtp_server` varchar(128) NOT NULL,
  `smtp_port` int(6) NOT NULL,
  `smtp_secure` enum('0','ssl','tls') NOT NULL DEFAULT '0',
  `smtp_auth` enum('0','1') NOT NULL DEFAULT '0',
  `smtp_username` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `smtp_password` varchar(128) NOT NULL,
  `rave_mode` enum('0','1') NOT NULL,
  `rave_public_key` varchar(128) NOT NULL,
  `rave_private_key` varchar(128) NOT NULL,
  `rave_encryption_key` varchar(128) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `ads_1` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ads_2` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ads_3` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ads_4` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ads_off` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `configuration`
--

INSERT INTO `configuration` (`site_name`, `logo`, `intro_logo`, `banner`, `intro_banner`, `slug`, `site_phone`, `site_office`, `facebook`, `twitter`, `instagram`, `whatsapp`, `email`, `template`, `skin`, `tracking`, `language`, `cleanurl`, `mode`, `per_page`, `per_featured`, `fbacc`, `fb_appid`, `fb_secret`, `twilio_sid`, `twilio_token`, `twilio_phone`, `captcha`, `smtp`, `sms`, `smtp_server`, `smtp_port`, `smtp_secure`, `smtp_auth`, `smtp_username`, `smtp_password`, `rave_mode`, `rave_public_key`, `rave_private_key`, `rave_encryption_key`, `currency`, `ads_1`, `ads_2`, `ads_3`, `ads_4`, `ads_off`) VALUES
('Collage Du Ceemos', '1379626937_140441866_n.jpeg', '2102881324_6952862_n.jpg', '', '2103528816_2139861658_n.jpg', 'NIGERIA\'S NO.1 ART BLOG', '09031983482', 'No. 31 Your street Address, Somewhere in, One State, Nigeria', 'collageduceemos', 'cceemos', 'collageduceemos_', '+ 2347089593153', 'collageduceemos2096@gmail.com', 'default', 'mdb-skin', '<!-- Global site tag (gtag.js) - Google Analytics -->\n<script async src=\"https://www.googletagmanager.com/gtag/js?id=UA-112185838-1\"></script>\n<script> \n  window.dataLayer = window.dataLayer || [];\n  function gtag(){dataLayer.push(arguments);}\n  gtag(\'js\', new Date());\n\n  gtag(\'config\', \'UA-112185838-1\');\n</script>', 'english', '0', 'live', 120, 21, '0', '283872735659168', 'e2c51f61d42a9074fc61e2d9a208d300', 'AC5cf08b88620d4b3ea0672ff6bcf0aa00', '634140af0f8319a503809b1b9ce88276', '18327304145', '0', '1', '0', 'passcontest.cf', 25, 'tls', '1', 'support@passcontest.cf', 'friendship1A@', '1', 'FLWPUBK-0e0942dd8d63fe28b759b277e22a9c7a-X', 'FLWSECK-074771e108effaab8df36d22b74271b7-X', 'a95e1e4267f556ed1651603d', 'USD', '<!-- Content -->\n  <div class=\"text-white text-center d-flex align-items-center rgba-black-strong py-5 px-4\">\n    <div>\n      <h5 class=\"pink-text\"><i class=\"fa fa-pie-chart\"></i> Marketing</h5>\n      <h3 class=\"card-title pt-2\"><strong>This is card title</strong></h3>\n      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repellat fugiat, laboriosam, voluptatem,\n        optio vero odio nam sit officia accusamus minus error nisi architecto nulla ipsum dignissimos.\n        Odit sed qui, dolorum!.</p>\n      <a class=\"btn btn-pink\"><i class=\"fa fa-clone left\"></i> View project</a>\n    </div>\n  </div>', '<!-- Card content -->\n  <div class=\"card-body card-body-cascade text-center\">\n\n    <!-- Title -->\n    <h4 class=\"card-title\"><strong>My adventure</strong></h4>\n    <!-- Subtitle -->\n    <h6 class=\"font-weight-bold indigo-text py-2\">This is not Photography</h6>\n    <!-- Text -->\n    <p class=\"card-text\">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Exercitationem perspiciatis voluptatum a, quo nobis, non commodi quia repellendus sequi nulla voluptatem dicta reprehenderit, placeat laborum ut beatae ullam suscipit veniam.\n    </p>\n\n    <!-- Linkedin -->\n    <a class=\"px-2 fa-lg li-ic\"><i class=\"fa fa-linkedin\"></i></a>\n    <!-- Twitter -->\n    <a class=\"px-2 fa-lg tw-ic\"><i class=\"fa fa-twitter\"></i></a>\n    <!-- Dribbble -->\n    <a class=\"px-2 fa-lg fb-ic\"><i class=\"fa fa-facebook\"></i></a>\n\n  </div>', '<!-- Content -->\n  <div class=\"text-white text-center d-flex align-items-center rgba-black-strong py-5 px-4\">\n    <div>\n      <h5 class=\"pink-text\"><i class=\"fa fa-pie-chart\"></i> Marketing</h5>\n      <h3 class=\"card-title pt-2\"><strong>This is another card title</strong></h3>\n      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repellat fugiat, laboriosam, voluptatem,\n        optio vero odio nam sit officia accusamus minus error nisi architecto nulla ipsum dignissimos.\n        Odit sed qui, dolorum!.</p>\n      <a class=\"btn btn-pink\"><i class=\"fa fa-clone left\"></i> Viewadv</a>\n    </div>\n  </div>', '<!-- Content -->\n  <div class=\"text-white text-center d-flex align-items-center rgba-black-strong py-5 px-4\">\n    <div>\n      <h5 class=\"pink-text\"><i class=\"fa fa-pie-chart\"></i> Marketing</h5>\n      <h3 class=\"card-title pt-2\"><strong>This is another card title for ad </strong></h3>\n      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repellat fugiat, laboriosam, voluptatem,\n        optio vero odio nam sit officia accusamus minus error nisi architecto nulla ipsum dignissimos.\n        Odit sed qui, dolorum!.</p>\n      <a class=\"btn btn-pink\"><i class=\"fa fa-clone left\"></i> View this project</a>\n    </div>\n  </div>', '1');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `post_id` bigint(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `category` varchar(128) DEFAULT NULL,
  `safelink` varchar(128) DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  `sub_title` varchar(128) DEFAULT NULL,
  `quote` text,
  `details` text,
  `image` varchar(128) DEFAULT NULL,
  `public` enum('0','1') NOT NULL DEFAULT '1',
  `featured` enum('0','1') NOT NULL DEFAULT '0',
  `promoted` enum('0','1') NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `event_date` datetime DEFAULT NULL,
  `promo_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `post_id`, `user_id`, `category`, `safelink`, `title`, `sub_title`, `quote`, `details`, `image`, `public`, `featured`, `promoted`, `date`, `event_date`, `promo_date`) VALUES
(2, NULL, 1, NULL, NULL, 'Little knowledge here', 'What should we do now', 'totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Ut enim ad minima veniam', 'Perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi.\r\n\r\nOmnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi.Perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque.', 'spider.jpg', '1', '0', '1', '2019-10-10 22:11:42', '2019-10-05 08:21:32', NULL),
(3, NULL, 1, NULL, NULL, 'Weldone good and faithfulll', ' Ut enim ad minima veniam, quis nostrum exercitationem ullam', 'Aque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo', 'Perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi.\\r\\n\\r\\nOmnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi.Perspiciatis unde omn', 'DSC_0057-2.JPG', '1', '0', '0', '2019-07-02 15:56:16', '2019-10-05 08:21:32', NULL),
(5, NULL, 1, NULL, NULL, 'One more thing', 'that was another thing altogher', 'Totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi', 'Perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi.\\r\\n\\r\\nOmnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi.Perspiciatis unde omn', 'dead.png', '1', '0', '0', '2019-09-01 15:56:16', '2019-10-05 08:21:32', NULL),
(6, NULL, 1, 'event', NULL, 'House Party', 'Welcome to grove home', 'unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam', '<p>Perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi.\r\n\r\nOmnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi.Perspiciatis unde omn</p>', 'robot.jpg', '1', '1', '0', '2019-10-10 22:15:58', '2019-10-05 08:21:00', '2019-10-10 00:00:00'),
(8, 506314013408304, 1, 'portfolio', 'welcome-to-africa', 'Welcome to africa', 'The world is a little crazy', 'If the post author is not entered the the site admin will be credited as post author', 'If the post author is not entered the the site admin will be credited as post authorIf the post author is not entered the the site admin will be credited as post author', NULL, '1', '0', '0', '2019-10-07 06:59:40', '2019-10-06 08:02:59', NULL),
(9, 494525965052938, 1, 'portfolio', 'making-up', 'Making up', 'Make the world a little better place', 'Perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo', 'Perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicaboPerspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicaboPerspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo', '645154288_1967552835_n.jpg', '1', '0', '0', '2019-10-10 21:47:40', '2019-10-07 05:22:21', '2019-10-10 00:00:00'),
(10, 473403871361618, 1, 'event', 'sddf-445', 'Social Welfare', 'Event or Exhibition do well to add a date and time', 'You can use the image button on the editor to add more images to the postYou can use the image button on the editor to add more images to the post', '<figure class=\"image image-style-align-left\"><img src=\"http://collageduceemos.te/uploads/davidson_1/1464112845_404563028_n.jpeg\"></figure><p>You can use the image button You can use the image button on the editor to add more images to the post You can use the image button on the editor to add more images to the post You can use the image button on the editor to add more images to the poston the editor to add more images to the post You can use the image button on the editor to add more images to the post You can use the image button on the editor to add more images to the postYou can use the image button on the editor to add more images to the post You can use the image button on the editor to add more images to the postYou can use the image button on the editor to add more images to the post You can use the image button on the editor to add more images to the post You can use the image button on the editor to add more images to the post You can use the image button on the editor to add more images to the post&nbsp;</p><figure class=\"image\"><img src=\"http://collageduceemos.te/uploads/davidson_1/1877161554_2053869834_n.png\"></figure><p>You can use the image button on the editor to add more images to the post You can use the image button on the editor to add more images to the post</p>', '1665422583_345941493_n.jpg', '1', '0', '1', '2019-10-10 23:00:22', '2019-10-07 01:00:00', '2019-10-11 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `static_pages`
--

CREATE TABLE `static_pages` (
  `id` int(11) NOT NULL,
  `title` varchar(128) DEFAULT NULL,
  `jarallax` varchar(128) DEFAULT NULL,
  `icon` varchar(128) DEFAULT NULL,
  `content` text,
  `parent` varchar(128) DEFAULT NULL,
  `safelink` varchar(128) DEFAULT NULL,
  `footer` enum('0','1') NOT NULL DEFAULT '0',
  `header` enum('0','1') NOT NULL DEFAULT '0',
  `priority` enum('0','1','2','3') NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `static_pages`
--

INSERT INTO `static_pages` (`id`, `title`, `jarallax`, `icon`, `content`, `parent`, `safelink`, `footer`, `header`, `priority`, `date`) VALUES
(1, 'But, It\'s mostly about you!', 'rhapsody2.JPG', '500px', 'COLLAGE DU CEEMOS is a platform designed to showcase Elegant, Exquisite artworks of diverse genres and specifications (Paintings, Sculptures, and Photographs) from talented Artists to the general art community and enthusiasts of arts and culture through a well grounded platform designed to promote the awareness, popularity and appreciation of Arts and the likes in the Nigerian public and beyond. Art is universal and COLLAGE DU CEEMOS aims to provide Art for collectors of art, Exhibitors, home settings, and any kind of workspace be it offices, receptions, waiting rooms, lounges, bars and the likes.', 'about', NULL, '0', '0', '3', '2019-10-09 11:26:16'),
(3, 'Creative', NULL, 'fa-paint-brush', 'We are creative, we know what you want, we know how to create what you want and we go the extra mile, do the dirty job just to get you that clean plate you\'ve been longing to see.', 'about', NULL, '0', '0', '2', '2019-10-09 11:26:16'),
(4, 'Exhibit', NULL, 'fa-magic', 'We create, but don\'t know if you create too; well, if you do, we could help you get your creations out there for the world to see and help you get those prospects you seek.', 'about', NULL, '0', '0', '2', '2019-10-09 11:26:16'),
(5, 'Submissions and advertising', NULL, NULL, 'Submissions are welcome from artists who wish to get their works featured on the platform. Simply contact us through the information on the contact page to discuss further terms and conditions. Art Exhibitions and events organizers can also publish details of their upcoming events on the platform through the same process. We look forward to hearing from you!', 'about', NULL, '0', '0', '1', '2019-10-09 11:26:16'),
(6, 'About The Founder', '', 'fa-bug', '<p>Suleiman Olaide Semiu is a 23 year old graduate of the university of Ilorin with a First degree in Geology and Mineral Sciences with a Professional certification in Project management from the Project Management Academy after which he obtained a Masters degree in Energy Security Management from the Nigerian Defense Academy post graduate school. He has a drive for problem solving, entrepreneurship, personal career development and an eye for the arts, especially contemporary African art.&nbsp;</p><p>&nbsp;{$texp-&gt;davidson}</p>', 'about', NULL, '0', '0', '1', '2019-10-11 01:07:21'),
(7, 'Contact us', NULL, NULL, 'Do you want to talk doing business with us or you have a suggestion or an idea that could make us better, or you simply need answers to questions you think we have answers to, please do not hesitate to contact us! You can instantly send us a message using the contact form below or use our contact info.', 'contact', NULL, '0', '0', '3', '2019-10-09 11:26:16'),
(8, 'Today is a new day', '555740678_1598032979_n.jpeg', NULL, 'Ex sunt eu ullamco ullamco nostrud cillum aliquip officia dolor fugiat elit officia reprehenderit adipisicing reprehenderit excepteur sit cillum sit culpa elit cupidatat aute mollit do deserunt velit consequat sed aliquip dolore duis laboris aliqua est enim eu proident sed consequat amet amet in incididunt nisi deserunt pariatur duis esse minim velit labore duis aute et reprehenderit tempor esse magna quis reprehenderit sed dolore sunt aute aliqua eiusmod id ullamco ex mollit occaecat officia fugiat laboris commodo sunt est in veniam reprehenderit laboris est occaecat ut laborum non fugiat cillum occaecat cupidatat est excepteur pariatur ea in mollit aliquip dolor culpa eu cupidatat ullamco ad culpa dolor minim dolor laborum qui aute deserunt ut magna ad do tempor nulla et nisi in adipisicing fugiat magna incididunt deserunt eiusmod irure elit id occaecat elit minim ad quis do cupidatat fugiat dolore laboris irure.', 'static', 'occaecat-elit-minim', '1', '0', '3', '2019-10-09 11:26:16'),
(9, 'Welcome to africa', '', 'fa-500px', '<p>button on the editor to add more images to this pubutton on the editor to add more images to this pubutton on the editor to add more images to this pubutton on the editor to add more images to this pu</p>', 'about', 'welcome-to-africa', '0', '0', '1', '2019-10-09 11:26:16'),
(12, 'How do you like yours', '555740678_1598032979_n.jpeg', 'fa-bug', '<p>Ex sunt eu ullamco ullamco nostrud cillum aliquip officia dolor fugiat elit officia reprehenderit adipisicing reprehenderit excepteur sit cillum sit culpa elit cupidatat aute mollit do deserunt velit consequat sed aliquip dolore duis laboris aliqua est enim eu proident sed consequat amet amet in incididunt nisi deserunt pariatur duis esse minim velit labore duis aute et reprehenderit tempor esse magna quis reprehenderit sed dolore sunt aute aliqua eiusmod id ullamco ex mollit occaecat officia fugiat laboris commodo sunt est in veniam reprehenderit laboris est occaecat ut laborum non fugiat cillum occaecat cupidatat est excepteur pariatur ea in mollit aliquip dolor culpa eu cupidatat ullamco ad culpa dolor minim dolor laborum qui aute deserunt ut magna ad do tempor nulla et nisi in adipisicing fugiat magna incididunt deserunt eiusmod irure elit id occaecat elit minim ad quis do cupidatat fugiat dolore laboris irure.</p>', 'static', 'like-minim', '1', '1', '3', '2019-10-10 23:32:42'),
(13, 'What a friend we have in jesus', '', NULL, 'Ex sunt eu ullamco ullamco nostrud cillum aliquip officia dolor fugiat elit officia reprehenderit adipisicing reprehenderit excepteur sit cillum sit culpa elit cupidatat aute mollit do deserunt velit consequat sed aliquip dolore duis laboris aliqua est enim eu proident sed consequat amet amet in incididunt nisi deserunt pariatur duis esse minim velit labore duis aute et reprehenderit tempor esse magna quis reprehenderit sed dolore sunt aute aliqua eiusmod id ullamco ex mollit occaecat officia fugiat laboris commodo sunt est in veniam reprehenderit laboris est occaecat ut laborum non fugiat cillum occaecat cupidatat est excepteur pariatur ea in mollit aliquip dolor culpa eu cupidatat ullamco ad culpa dolor minim dolor laborum qui aute deserunt ut magna ad do tempor nulla et nisi in adipisicing fugiat magna incididunt deserunt eiusmod irure elit id occaecat elit minim ad quis do cupidatat fugiat dolore laboris irure.', 'static', 'like-salama', '1', '0', '3', '2019-10-08 11:26:16'),
(14, 'Saalaam Aleikum', '555740678_1598032979_n.jpeg', NULL, 'Ex sunt eu ullamco ullamco nostrud cillum aliquip officia dolor fugiat elit officia reprehenderit adipisicing reprehenderit excepteur sit cillum sit culpa elit cupidatat aute mollit do deserunt velit consequat sed aliquip dolore duis laboris aliqua est enim eu proident sed consequat amet amet in incididunt nisi deserunt pariatur duis esse minim velit labore duis aute et reprehenderit tempor esse magna quis reprehenderit sed dolore sunt aute aliqua eiusmod id ullamco ex mollit occaecat officia fugiat laboris commodo sunt est in veniam reprehenderit laboris est occaecat ut laborum non fugiat cillum occaecat cupidatat est excepteur pariatur ea in mollit aliquip dolor culpa eu cupidatat ullamco ad culpa dolor minim dolor laborum qui aute deserunt ut magna ad do tempor nulla et nisi in adipisicing fugiat magna incididunt deserunt eiusmod irure elit id occaecat elit minim ad quis do cupidatat fugiat dolore laboris irure.', 'static', 'like-salam-aleikum', '1', '0', '3', '2019-10-01 11:26:16'),
(15, 'Saalaam Aleikum Is back', '555740678_1598032979_n.jpeg', NULL, 'Ex sunt eu ullamco ullamco nostrud cillum aliquip officia dolor fugiat elit officia reprehenderit adipisicing reprehenderit excepteur sit cillum sit culpa elit cupidatat aute mollit do deserunt velit consequat sed aliquip dolore duis laboris aliqua est enim eu proident sed consequat amet amet in incididunt nisi deserunt pariatur duis esse minim velit labore duis aute et reprehenderit tempor esse magna quis reprehenderit sed dolore sunt aute aliqua eiusmod id ullamco ex mollit occaecat officia fugiat laboris commodo sunt est in veniam reprehenderit laboris est occaecat ut laborum non fugiat cillum occaecat cupidatat est excepteur pariatur ea in mollit aliquip dolor culpa eu cupidatat ullamco ad culpa dolor minim dolor laborum qui aute deserunt ut magna ad do tempor nulla et nisi in adipisicing fugiat magna incididunt deserunt eiusmod irure elit id occaecat elit minim ad quis do cupidatat fugiat dolore laboris irure.', 'static', 'back-salam-aleikum', '1', '0', '3', '2019-10-09 11:26:16'),
(16, 'Scillum sit culpa elit cupidatat', '555740678_1598032979_n.jpeg', NULL, 'Ex sunt eu ullamco ullamco nostrud cillum aliquip officia dolor fugiat elit officia reprehenderit adipisicing reprehenderit excepteur sit cillum sit culpa elit cupidatat aute mollit do deserunt velit consequat sed aliquip dolore duis laboris aliqua est enim eu proident sed consequat amet amet in incididunt nisi deserunt pariatur duis esse minim velit labore duis aute et reprehenderit tempor esse magna quis reprehenderit sed dolore sunt aute aliqua eiusmod id ullamco ex mollit occaecat officia fugiat laboris commodo sunt est in veniam reprehenderit laboris est occaecat ut laborum non fugiat cillum occaecat cupidatat est excepteur pariatur ea in mollit aliquip dolor culpa eu cupidatat ullamco ad culpa dolor minim dolor laborum qui aute deserunt ut magna ad do tempor nulla et nisi in adipisicing fugiat magna incididunt deserunt eiusmod irure elit id occaecat elit minim ad quis do cupidatat fugiat dolore laboris irure.', 'static', 'aute-deserunt', '1', '0', '3', '2019-10-09 11:26:16'),
(17, 'Svelit labore duis aute et', '555740678_1598032979_n.jpeg', 'fa-500px', '<p>Ex sunt eu ullamco ullamco nostrud cillum aliquip officia dolor fugiat elit officia reprehenderit adipisicing reprehenderit excepteur sit cillum sit culpa elit cupidatat aute mollit do deserunt velit consequat sed aliquip dolore duis laboris aliqua est enim eu proident sed consequat amet amet in incididunt nisi deserunt pariatur duis esse minim velit labore duis aute et reprehenderit tempor esse magna quis reprehenderit sed dolore sunt aute aliqua eiusmod id ullamco ex mollit occaecat officia fugiat laboris commodo sunt est in veniam reprehenderit laboris est occaecat ut laborum non fugiat cillum occaecat cupidatat est excepteur pariatur ea in mollit aliquip dolor culpa eu cupidatat ullamco ad culpa dolor minim dolor laborum qui aute deserunt ut magna ad do tempor nulla et nisi in adipisicing fugiat magna incididunt deserunt eiusmod irure elit id occaecat elit minim ad quis do cupidatat fugiat dolore laboris irure.</p>', 'static', 'velit-labore', '1', '1', '3', '2019-10-09 11:26:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(11) NOT NULL,
  `username` varchar(128) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(128) DEFAULT NULL,
  `fname` varchar(128) DEFAULT NULL,
  `lname` varchar(128) DEFAULT NULL,
  `photo` varchar(128) DEFAULT NULL,
  `cover` varchar(128) DEFAULT NULL,
  `intro` text,
  `qualification` varchar(128) DEFAULT NULL,
  `label` varchar(128) DEFAULT NULL,
  `verified` enum('0','1') NOT NULL DEFAULT '0',
  `founder` enum('0','1') NOT NULL DEFAULT '0',
  `role` enum('1','2','3','4','5') NOT NULL DEFAULT '1',
  `reg_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `auth_token` varchar(128) DEFAULT NULL,
  `token_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `username`, `password`, `email`, `fname`, `lname`, `photo`, `cover`, `intro`, `qualification`, `label`, `verified`, `founder`, `role`, `reg_date`, `auth_token`, `token_date`) VALUES
(1, 'davidson', 'a1fa59e79bba1a38bb0684d3298c9ddd', 'mygame@gmail.com', 'Suleiman Olaide ', 'Semiu', 'PROFILE PICTUE.JPG', NULL, 'Driven by the need to provide solutions to problems, entrepreneurship, personal career development and an eye for the arts.', 'M.Sc. Energy Security Management', 'newnify', '0', '1', '4', '2019-08-10 04:42:04', NULL, '2019-08-14 04:42:04'),
(2, 'wilson', 'a1fa59e79bba1a38bb0684d3298c9ddd', 'mygame@gmail.com', 'Wilson', 'Good', '', NULL, NULL, NULL, 'pass', '1', '0', '1', '2019-09-28 00:00:00', NULL, '2019-09-28 19:09:24'),
(3, 'western', '42bf85196c63fadb97cc4123d7ecf834', NULL, 'Western', '', '2000988763_368205464_1673384145_n.jpg', NULL, '', NULL, 'Xper1mentall Music', '0', '0', '2', '2019-09-30 15:13:19', NULL, '2019-09-30 15:13:19');

-- --------------------------------------------------------

--
-- Table structure for table `views`
--

CREATE TABLE `views` (
  `id` int(11) NOT NULL,
  `by` int(11) NOT NULL,
  `post` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `views`
--

INSERT INTO `views` (`id`, `by`, `post`, `time`) VALUES
(1, 1, 3, '2019-10-01 20:53:30'),
(2, 1, 3, '2019-10-16 20:53:22'),
(3, 1, 2, '2019-10-12 20:53:17'),
(4, 3, 2, '2019-10-29 20:53:09'),
(5, 3, 2, '2019-10-22 20:53:05'),
(8, 4, 2, '2019-10-04 14:21:18'),
(9, 1, 3, '2019-02-16 20:53:22'),
(11, 1, 10, '2019-10-09 00:18:16'),
(12, 1, 10, '2019-10-09 00:18:56'),
(13, 1, 10, '2019-10-09 00:19:27'),
(14, 1, 10, '2019-10-09 00:19:36'),
(15, 1, 10, '2019-10-09 00:19:42'),
(16, 1, 10, '2019-10-09 00:20:00'),
(17, 1, 10, '2019-10-09 00:20:18'),
(18, 1, 10, '2019-10-09 00:20:31'),
(19, 1, 8, '2019-10-09 11:21:24'),
(20, 1, 9, '2019-10-09 11:21:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `allowed_config`
--
ALTER TABLE `allowed_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `posts` ADD FULLTEXT KEY `title` (`title`);
ALTER TABLE `posts` ADD FULLTEXT KEY `details` (`details`);

--
-- Indexes for table `static_pages`
--
ALTER TABLE `static_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `uid` (`uid`) USING BTREE;

--
-- Indexes for table `views`
--
ALTER TABLE `views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `by` (`by`) USING BTREE,
  ADD KEY `time` (`time`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `allowed_config`
--
ALTER TABLE `allowed_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `static_pages`
--
ALTER TABLE `static_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `views`
--
ALTER TABLE `views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
