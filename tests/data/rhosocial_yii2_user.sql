-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2017-03-12 00:00:59
-- 服务器版本： 8.0.0-dmr
-- PHP Version: 7.1.2

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
-- 表的结构 `auth_assignment`
--
-- 创建时间： 2017-03-11 13:21:06
--

DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE IF NOT EXISTS `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_guid` varbinary(16) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`item_name`,`user_guid`),
  KEY `user_assignment_fk` (`user_guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `auth_item`
--
-- 创建时间： 2017-03-11 13:21:06
--

DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE IF NOT EXISTS `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name_fk` (`rule_name`),
  KEY `idx-auth_item-type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `auth_item`
--

INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('admin', 1, 'Administrator', NULL, NULL, 1489238405, 1489238405),
('createAdminUser', 2, 'Create an administrator user', NULL, NULL, 1489238405, 1489238405),
('createUser', 2, 'Create a user', 'canCreateUser', NULL, 1489238405, 1489238405),
('deleteAdminUser', 2, 'Delete an administrator user', NULL, NULL, 1489238405, 1489238405),
('deleteMyself', 2, 'Delete myself', 'canDeleteMyself', NULL, 1489238405, 1489238405),
('deleteUser', 2, 'Delete a user', NULL, NULL, 1489238405, 1489238405),
('updateAdminUser', 2, 'Update an administrator user', NULL, NULL, 1489238405, 1489238405),
('updateMyself', 2, 'Update myself', 'canUpdateMyself', NULL, 1489238405, 1489238405),
('updateUser', 2, 'Update a user', 'canUpdateUser', NULL, 1489238405, 1489238405),
('user', 1, 'User', NULL, NULL, 1489238405, 1489238405);

-- --------------------------------------------------------

--
-- 表的结构 `auth_item_child`
--
-- 创建时间： 2017-03-11 13:21:06
--

DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE IF NOT EXISTS `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child_name_fk` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `auth_item_child`
--

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('admin', 'createUser'),
('user', 'deleteMyself'),
('admin', 'deleteUser'),
('user', 'updateMyself'),
('admin', 'updateUser'),
('admin', 'user');

-- --------------------------------------------------------

--
-- 表的结构 `auth_rule`
--
-- 创建时间： 2017-03-11 13:21:06
--

DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE IF NOT EXISTS `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `auth_rule`
--

INSERT INTO `auth_rule` (`name`, `data`, `created_at`, `updated_at`) VALUES
('canCreateUser', 0x4f3a34303a2272686f736f6369616c5c757365725c726261635c72756c65735c4372656174655573657252756c65223a333a7b733a343a226e616d65223b733a31333a2263616e43726561746555736572223b733a393a22637265617465644174223b693a313438393233383430353b733a393a22757064617465644174223b693a313438393233383430353b7d, 1489238405, 1489238405),
('canDeleteMyself', 0x4f3a34323a2272686f736f6369616c5c757365725c726261635c72756c65735c44656c6574654d7973656c6652756c65223a333a7b733a343a226e616d65223b733a31353a2263616e44656c6574654d7973656c66223b733a393a22637265617465644174223b693a313438393233383430353b733a393a22757064617465644174223b693a313438393233383430353b7d, 1489238405, 1489238405),
('canDeleteUser', 0x4f3a34303a2272686f736f6369616c5c757365725c726261635c72756c65735c44656c6574655573657252756c65223a333a7b733a343a226e616d65223b733a31333a2263616e44656c65746555736572223b733a393a22637265617465644174223b693a313438393233383430353b733a393a22757064617465644174223b693a313438393233383430353b7d, 1489238405, 1489238405),
('canUpdateMyself', 0x4f3a34323a2272686f736f6369616c5c757365725c726261635c72756c65735c5570646174654d7973656c6652756c65223a333a7b733a343a226e616d65223b733a31353a2263616e5570646174654d7973656c66223b733a393a22637265617465644174223b693a313438393233383430353b733a393a22757064617465644174223b693a313438393233383430353b7d, 1489238405, 1489238405),
('canUpdateUser', 0x4f3a34303a2272686f736f6369616c5c757365725c726261635c72756c65735c5570646174655573657252756c65223a333a7b733a343a226e616d65223b733a31333a2263616e55706461746555736572223b733a393a22637265617465644174223b693a313438393233383430353b733a393a22757064617465644174223b693a313438393233383430353b7d, 1489238405, 1489238405);

-- --------------------------------------------------------

--
-- 表的结构 `password_history`
--
-- 创建时间： 2017-03-08 05:23:13
--

DROP TABLE IF EXISTS `password_history`;
CREATE TABLE IF NOT EXISTS `password_history` (
  `guid` varbinary(16) NOT NULL COMMENT 'Password GUID',
  `user_guid` varbinary(16) NOT NULL COMMENT 'Created By',
  `created_at` datetime NOT NULL COMMENT 'Created At',
  `pass_hash` varchar(80) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Password Hash',
  PRIMARY KEY (`guid`),
  KEY `user_password_fk` (`user_guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Profile';

-- --------------------------------------------------------

--
-- 表的结构 `profile`
--
-- 创建时间： 2017-03-06 10:54:24
--

DROP TABLE IF EXISTS `profile`;
CREATE TABLE IF NOT EXISTS `profile` (
  `guid` varbinary(16) NOT NULL COMMENT 'User GUID',
  `nickname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nickname',
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'First Name',
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Last Name',
  `gender` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Gender',
  `individual_sign` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Individual Sign',
  `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Created At',
  `updated_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Updated At',
  PRIMARY KEY (`guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Profile';

-- --------------------------------------------------------

--
-- 表的结构 `user`
--
-- 创建时间： 2017-03-06 10:54:15
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `guid` varbinary(16) NOT NULL COMMENT 'User GUID',
  `id` varchar(16) COLLATE utf8_unicode_ci NOT NULL COMMENT 'User ID No.',
  `pass_hash` varchar(80) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Password Hash',
  `ip` varbinary(16) NOT NULL DEFAULT '0' COMMENT 'IP Address',
  `ip_type` tinyint(3) NOT NULL DEFAULT '4' COMMENT 'IP Address Type',
  `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Created At',
  `updated_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Updated At',
  `auth_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Authentication Key',
  `access_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Access Token',
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Password Reset Token',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT 'Status',
  `type` tinyint(3) NOT NULL DEFAULT '0' COMMENT 'Type',
  `source` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Source',
  PRIMARY KEY (`guid`),
  UNIQUE KEY `user_id_unique` (`id`),
  KEY `user_auth_key_normal` (`auth_key`),
  KEY `user_access_token_normal` (`access_token`),
  KEY `user_password_reset_token_normal` (`password_reset_token`),
  KEY `user_created_at_normal` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User';

--
-- 限制导出的表
--

--
-- 限制表 `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`user_guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `password_history`
--
ALTER TABLE `password_history`
  ADD CONSTRAINT `password_history_ibfk_1` FOREIGN KEY (`user_guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `profile`
--
ALTER TABLE `profile`
  ADD CONSTRAINT `user_profile_fk` FOREIGN KEY (`guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
