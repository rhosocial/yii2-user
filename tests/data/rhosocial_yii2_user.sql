-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2017-01-07 16:27:22
-- 服务器版本： 8.0.0-dmr
-- PHP Version: 7.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rhosocial_yii2_user`
--
CREATE DATABASE IF NOT EXISTS `rhosocial_yii2_user` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rhosocial_yii2_user`;

-- --------------------------------------------------------

--
-- 表的结构 `profile`
--
-- 创建时间： 2017-01-07 08:23:54
--

DROP TABLE IF EXISTS `profile`;
CREATE TABLE IF NOT EXISTS `profile` (
  `guid` varbinary(16) NOT NULL,
  `nickname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `individual_sign` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  PRIMARY KEY (`guid`),
  KEY `user_nickname_normal` (`nickname`) USING BTREE,
  KEY `user_email_normal` (`email`) USING BTREE,
  KEY `user_phone_normal` (`phone`) USING BTREE,
  KEY `user_name_normal` (`first_name`,`last_name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--
-- 创建时间： 2017-01-07 08:26:59
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `guid` varbinary(16) NOT NULL,
  `id` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `pass_hash` varchar(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ip` varbinary(16) NOT NULL DEFAULT '0',
  `ip_type` tinyint(3) UNSIGNED NOT NULL DEFAULT '4',
  `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `auth_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `access_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`guid`),
  UNIQUE KEY `user_id_unique` (`id`) USING BTREE,
  UNIQUE KEY `user_auth_key_unique` (`auth_key`) USING BTREE,
  UNIQUE KEY `user_access_token_unique` (`access_token`) USING BTREE,
  KEY `user_password_reset_token_unique` (`password_reset_token`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 限制导出的表
--

--
-- 限制表 `profile`
--
ALTER TABLE `profile`
  ADD CONSTRAINT `user_guid_fkey` FOREIGN KEY (`guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
