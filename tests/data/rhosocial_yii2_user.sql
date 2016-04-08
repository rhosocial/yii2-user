-- phpMyAdmin SQL Dump
-- version 4.6.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 08, 2016 at 03:13 PM
-- Server version: 5.7.11
-- PHP Version: 5.6.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rhosocial_yii2_user`
--
DROP DATABASE IF EXISTS `rhosocial_yii2_user`;
CREATE DATABASE IF NOT EXISTS `rhosocial_yii2_user` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rhosocial_yii2_user`;

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--
-- Creation: Apr 08, 2016 at 06:21 AM
--

DROP TABLE IF EXISTS `profile`;
CREATE TABLE IF NOT EXISTS `profile` (
  `guid` varchar(36) COLLATE utf8_unicode_ci NOT NULL COMMENT 'User GUID',
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nickname',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Email',
  `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Phone',
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'First Name',
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Last Name',
  `individual_sign` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Individual Sign',
  `create_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Create Time',
  `update_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Update Time',
  PRIMARY KEY (`guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Profile';

-- --------------------------------------------------------

--
-- Table structure for table `user`
--
-- Creation: Apr 08, 2016 at 06:20 AM
-- Last update: Apr 08, 2016 at 06:38 AM
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `guid` varchar(36) COLLATE utf8_unicode_ci NOT NULL COMMENT 'GUID',
  `id` varchar(16) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ID',
  `pass_hash` varchar(80) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Password Hash',
  `ip_1` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'IP 1',
  `ip_2` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'IP 2',
  `ip_3` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'IP 3',
  `ip_4` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'IP 4',
  `ip_type` tinyint(3) UNSIGNED NOT NULL DEFAULT '4' COMMENT 'IP Address Type',
  `create_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Create Time',
  `update_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Update Time',
  `auth_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Authentication Key',
  `access_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Access Token',
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Password Reset Token',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Status',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Type',
  `source` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Source',
  PRIMARY KEY (`guid`),
  UNIQUE KEY `user_id_unique` (`id`),
  KEY `user_auth_key_normal` (`auth_key`),
  KEY `user_access_token_normal` (`access_token`),
  KEY `user_password_reset_token` (`password_reset_token`),
  KEY `user_create_time_normal` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User';

--
-- Constraints for dumped tables
--

--
-- Constraints for table `profile`
--
ALTER TABLE `profile`
  ADD CONSTRAINT `user_profile_fkey` FOREIGN KEY (`guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
