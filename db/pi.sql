-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 02, 2014 at 03:22 AM
-- Server version: 5.5.38-1~dotdeb.0
-- PHP Version: 5.4.32-1~dotdeb.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pi`
--
CREATE DATABASE IF NOT EXISTS `pi` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `pi`;

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
CREATE TABLE IF NOT EXISTS `address` (
`id` int(4) unsigned NOT NULL,
  `pi_channel` bit(4) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `pi_type` tinyint(1) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `app`
--

DROP TABLE IF EXISTS `app`;
CREATE TABLE IF NOT EXISTS `app` (
`id` int(4) unsigned NOT NULL,
  `name` varchar(63) CHARACTER SET latin1 NOT NULL,
  `description` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `app$user`
--

DROP TABLE IF EXISTS `app$user`;
CREATE TABLE IF NOT EXISTS `app$user` (
  `pi_app` int(4) unsigned NOT NULL,
  `pi_user` int(4) unsigned NOT NULL,
  `permissions` bit(12) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `channel`
--

DROP TABLE IF EXISTS `channel`;
CREATE TABLE IF NOT EXISTS `channel` (
  `id` bit(4) NOT NULL,
  `name` varchar(63) CHARACTER SET utf8 COLLATE utf8_danish_ci NOT NULL,
  `pi_type` tinyint(1) DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_danish_ci DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `channel`
--

INSERT INTO `channel` (`id`, `name`, `pi_type`, `description`) VALUES
(b'0000', 'pi', NULL, 'base channel'),
(b'0001', 'auth', NULL, 'permissions channel'),
(b'0010', 'chat', NULL, 'chat channel'),
(b'0011', 'debug', NULL, 'debug channel'),
(b'0100', 'warning', NULL, 'warning channel'),
(b'0101', 'error', NULL, 'error channel'),
(b'0110', 'log', NULL, 'log channel'),
(b'0111', 'type', NULL, 'type channel'),
(b'1000', 'db', NULL, 'db channel'),
(b'1001', 'ping', NULL, 'ping channel'),
(b'1010', 'ctrl', NULL, 'control channel'),
(b'1011', 'admin', NULL, 'admin channel'),
(b'1100', 'sys', NULL, 'system channel'),
(b'1110', 'push', NULL, 'push channel'),
(b'1111', 'zmq', NULL, 'ZeroMQ channel');

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
`id` int(4) NOT NULL,
  `name` varchar(63) COLLATE utf8_danish_ci NOT NULL,
  `description` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `json` text CHARACTER SET latin1,
  `apikey` varchar(31) CHARACTER SET latin1 DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`id`, `name`, `description`, `json`, `apikey`, `timestamp`) VALUES
(1, 'xman', NULL, NULL, 'tsxx', '2014-08-31 19:23:56'),
(2, 'views', NULL, NULL, NULL, '2014-08-31 19:24:17');

-- --------------------------------------------------------

--
-- Table structure for table `email`
--

DROP TABLE IF EXISTS `email`;
CREATE TABLE IF NOT EXISTS `email` (
`id` bigint(8) NOT NULL,
  `value` varchar(255) CHARACTER SET latin1 NOT NULL,
  `verified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `email`
--

INSERT INTO `email` (`id`, `value`, `verified`, `created`, `updated`) VALUES
(1, 'jt@viewshq.no', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

DROP TABLE IF EXISTS `file`;
CREATE TABLE IF NOT EXISTS `file` (
`id` bigint(8) unsigned NOT NULL,
  `pi_address` int(4) NOT NULL,
  `pi_permissions` int(4) NOT NULL,
  `permissions` bit(12) DEFAULT NULL,
  `pi_user` int(4) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `group`
--

DROP TABLE IF EXISTS `group`;
CREATE TABLE IF NOT EXISTS `group` (
  `id` int(4) unsigned NOT NULL,
  `name` varchar(63) CHARACTER SET utf8 COLLATE utf8_danish_ci NOT NULL,
  `description` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `json` text CHARACTER SET latin1,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `group`
--

INSERT INTO `group` (`id`, `name`, `description`, `json`, `timestamp`) VALUES
(1, 'user', 'Normal user account', NULL, '2013-09-24 19:09:13'),
(2, 'admin', 'Administrator account', NULL, '2013-09-24 19:10:35'),
(3, 'app', 'Accounts for apps', NULL, '2013-09-24 19:11:22'),
(4, 'service', 'Accounts for services', NULL, '2013-09-24 19:11:54'),
(5, 'channel', 'Accounts for channels', NULL, '2013-09-24 19:12:25'),
(6, 'superuser', 'Superuser account', NULL, '2013-09-24 19:12:59'),
(7, 'dev', 'Developer account', NULL, '2013-10-24 14:52:59'),
(8, 'sysadmin', 'System administrator account', NULL, '2014-08-23 17:08:01'),
(9, 'client', 'Client account', NULL, '2014-08-23 17:08:01');

-- --------------------------------------------------------

--
-- Table structure for table `group$user`
--

DROP TABLE IF EXISTS `group$user`;
CREATE TABLE IF NOT EXISTS `group$user` (
  `pi_group` int(4) unsigned NOT NULL,
  `pi_user` int(4) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `host`
--

DROP TABLE IF EXISTS `host`;
CREATE TABLE IF NOT EXISTS `host` (
`id` int(4) unsigned NOT NULL,
  `pi_url` int(4) unsigned NOT NULL,
  `pi_address` int(4) unsigned DEFAULT NULL,
  `pi_group` int(4) unsigned DEFAULT NULL,
  `pi_client` int(4) unsigned DEFAULT NULL,
  `pi_service` int(4) unsigned DEFAULT NULL,
  `pi_type` int(4) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

DROP TABLE IF EXISTS `image`;
CREATE TABLE IF NOT EXISTS `image` (
`id` bigint(8) unsigned NOT NULL,
  `pi_file` bigint(8) unsigned DEFAULT NULL,
  `pi_url` bigint(8) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `object`
--

DROP TABLE IF EXISTS `object`;
CREATE TABLE IF NOT EXISTS `object` (
`id` bigint(8) unsigned NOT NULL,
  `pi_channel` bit(4) NOT NULL,
  `pi_user` int(4) unsigned DEFAULT NULL,
  `pi_address` int(4) unsigned DEFAULT NULL,
  `pi_type` tinyint(1) unsigned NOT NULL,
  `permissions` bit(12) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `object$property`
--

DROP TABLE IF EXISTS `object$property`;
CREATE TABLE IF NOT EXISTS `object$property` (
`id` bigint(20) unsigned NOT NULL,
  `name` varchar(63) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
`id` bigint(8) unsigned NOT NULL,
  `pi_address` int(4) unsigned NOT NULL,
  `pi_group` int(4) unsigned NOT NULL,
  `pi_user` int(4) unsigned NOT NULL,
  `value` bit(12) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pi`
--

DROP TABLE IF EXISTS `pi`;
CREATE TABLE IF NOT EXISTS `pi` (
  `name` varchar(255) NOT NULL COMMENT 'property name',
  `value` text NOT NULL COMMENT 'JSON',
  `created` datetime NOT NULL,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
CREATE TABLE IF NOT EXISTS `service` (
`id` int(4) unsigned NOT NULL,
  `name` varchar(63) CHARACTER SET latin1 NOT NULL,
  `description` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `token`
--

DROP TABLE IF EXISTS `token`;
CREATE TABLE IF NOT EXISTS `token` (
  `id` bigint(8) NOT NULL,
  `value` char(40) NOT NULL,
  `usage` bigint(20) unsigned NOT NULL DEFAULT '0',
  `issued` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `type`
--

DROP TABLE IF EXISTS `type`;
CREATE TABLE IF NOT EXISTS `type` (
  `id` tinyint(1) unsigned NOT NULL,
  `name` varchar(63) NOT NULL,
  `pi_numeric` bit(1) NOT NULL DEFAULT b'0',
  `pi_int` bit(1) NOT NULL DEFAULT b'0',
  `pi_signed` bit(1) NOT NULL DEFAULT b'0',
  `pi_float` bit(1) NOT NULL DEFAULT b'0',
  `pi_string` bit(1) NOT NULL DEFAULT b'0',
  `pi_datetime` bit(1) NOT NULL DEFAULT b'0',
  `pi_object` bit(1) NOT NULL DEFAULT b'0',
  `pi_binary` bit(1) NOT NULL DEFAULT b'1',
  `bits` tinyint(1) unsigned NOT NULL DEFAULT '8',
  `size` tinyint(1) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `type`
--

INSERT INTO `type` (`id`, `name`, `pi_numeric`, `pi_int`, `pi_signed`, `pi_float`, `pi_string`, `pi_datetime`, `pi_object`, `pi_binary`, `bits`, `size`) VALUES
(1, 'STR', b'0', b'0', b'0', b'0', b'1', b'0', b'0', b'1', 8, NULL),
(2, 'NUMBER', b'1', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, 4),
(5, 'FLOAT32', b'1', b'0', b'1', b'1', b'0', b'0', b'0', b'1', 8, 4),
(6, 'FLOAT64', b'1', b'0', b'1', b'1', b'0', b'0', b'0', b'1', 8, 8),
(7, 'FLOAT32ARRAY', b'0', b'0', b'0', b'0', b'0', b'0', b'1', b'1', 8, NULL),
(8, 'FLOAT64ARRAY', b'0', b'0', b'0', b'0', b'0', b'0', b'1', b'1', 8, NULL),
(9, 'UINT8', b'0', b'1', b'0', b'0', b'0', b'0', b'0', b'1', 8, 1),
(10, 'UINT16', b'0', b'1', b'0', b'0', b'0', b'0', b'0', b'1', 8, 2),
(11, 'UINT32', b'0', b'1', b'0', b'0', b'0', b'0', b'0', b'1', 8, 4),
(12, 'UINT64', b'0', b'1', b'0', b'0', b'0', b'0', b'0', b'1', 8, 8),
(17, 'INT8', b'0', b'1', b'1', b'0', b'0', b'0', b'0', b'1', 8, 1),
(18, 'INT16', b'0', b'1', b'1', b'0', b'0', b'0', b'0', b'1', 8, 2),
(19, 'INT32', b'0', b'1', b'1', b'0', b'0', b'0', b'0', b'1', 8, 4),
(20, 'INT64', b'0', b'1', b'1', b'0', b'0', b'0', b'0', b'1', 8, 8),
(31, 'UINT8ARRAY', b'0', b'0', b'0', b'0', b'0', b'0', b'1', b'1', 8, NULL),
(32, 'UINT16ARRAY', b'0', b'0', b'0', b'0', b'0', b'0', b'1', b'1', 8, NULL),
(33, 'UINT32ARRAY', b'0', b'0', b'0', b'0', b'0', b'0', b'1', b'1', 8, NULL),
(34, 'UINT64ARRAY', b'0', b'0', b'0', b'0', b'0', b'0', b'1', b'1', 8, NULL),
(50, 'DAY', b'0', b'0', b'0', b'0', b'0', b'1', b'0', b'1', 8, NULL),
(51, 'WEEK', b'0', b'0', b'0', b'0', b'0', b'1', b'0', b'1', 8, NULL),
(52, 'TIME', b'0', b'0', b'0', b'0', b'0', b'1', b'0', b'1', 8, NULL),
(53, 'DATE', b'0', b'0', b'0', b'0', b'0', b'1', b'0', b'1', 8, NULL),
(54, 'DATETIME', b'0', b'0', b'0', b'0', b'0', b'1', b'0', b'1', 8, NULL),
(55, 'DATETIME_LOCAL', b'0', b'0', b'0', b'0', b'0', b'1', b'0', b'1', 8, NULL),
(56, 'HOUR', b'1', b'1', b'0', b'0', b'0', b'1', b'0', b'1', 8, NULL),
(57, 'MINUTE', b'0', b'1', b'0', b'0', b'0', b'1', b'0', b'1', 8, NULL),
(58, 'SECOND', b'0', b'1', b'0', b'0', b'0', b'1', b'0', b'1', 8, NULL),
(59, 'UNIXTIME', b'0', b'1', b'1', b'0', b'0', b'1', b'0', b'1', 8, NULL),
(60, 'MILLITIME', b'0', b'1', b'1', b'0', b'0', b'1', b'0', b'1', 8, 8),
(61, 'MICROTIME', b'1', b'0', b'0', b'1', b'0', b'1', b'0', b'1', 8, 8),
(65, 'INT8ARRAY', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(66, 'INT16ARRAY', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(67, 'INT32ARRAY', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(68, 'INT64ARRAY', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(100, 'USER', b'0', b'0', b'0', b'0', b'0', b'0', b'1', b'1', 8, NULL),
(101, 'USERGROUP', b'0', b'1', b'0', b'0', b'0', b'0', b'1', b'1', 8, 4),
(102, 'PERMISSIONS', b'0', b'0', b'0', b'0', b'0', b'0', b'1', b'1', 12, 1),
(103, 'TOKEN', b'0', b'0', b'0', b'0', b'0', b'0', b'1', b'1', 8, NULL),
(104, 'JSON', b'0', b'0', b'0', b'0', b'1', b'0', b'0', b'0', 8, NULL),
(105, 'MYSQL', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(106, 'REDIS', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(107, 'LIST', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(108, 'IP', b'1', b'1', b'0', b'0', b'0', b'0', b'0', b'1', 8, 4),
(109, 'IPV6', b'1', b'1', b'0', b'0', b'0', b'0', b'0', b'1', 8, 16),
(110, 'SHORTSTRING', b'0', b'0', b'0', b'0', b'1', b'0', b'0', b'0', 8, 255),
(111, 'ANSISTRING', b'0', b'0', b'0', b'0', b'1', b'0', b'0', b'0', 8, NULL),
(112, 'UTF8', b'0', b'0', b'0', b'0', b'1', b'0', b'0', b'0', 8, NULL),
(123, 'RANGE', b'1', b'1', b'1', b'0', b'0', b'0', b'0', b'1', 8, 8),
(124, 'ARRAY', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(125, 'BYTEARRAY', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(127, 'STRUCT', b'0', b'0', b'0', b'0', b'0', b'0', b'1', b'1', 8, NULL),
(128, 'FILE', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(129, 'IMAGE', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(130, 'DATA', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(131, 'TEL', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(132, 'GEO', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(133, 'EMAIL', b'0', b'0', b'0', b'0', b'1', b'0', b'0', b'1', 8, 127),
(134, 'URL', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(135, 'FORMAT', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(136, 'CHANNEL', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 4, 1),
(137, 'ADDRESS', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, 255),
(200, 'SET', b'0', b'0', b'0', b'0', b'0', b'0', b'1', b'1', 8, NULL),
(201, 'SORTEDSET', b'0', b'0', b'0', b'0', b'0', b'0', b'1', b'1', 8, NULL),
(240, 'IGBINARY', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(241, 'BASE64', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, NULL),
(254, 'NAN', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, 1),
(255, 'NULL', b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'1', 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `url`
--

DROP TABLE IF EXISTS `url`;
CREATE TABLE IF NOT EXISTS `url` (
`id` bigint(8) unsigned NOT NULL,
  `value` varchar(767) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(4) unsigned NOT NULL,
  `name` varchar(63) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `_password` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `pi_email` int(4) unsigned NOT NULL,
  `pi_group` int(4) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `_password`, `pi_email`, `pi_group`, `created`) VALUES
(6, 'views', '81f33187147211336a4984d8ab8fccdfeb11a42c', 0, NULL, '2013-09-24 21:08:57'),
(5, 'xman', '33766502368bd2126ba9edb4711995f3435054c3', 1, 2, '0000-00-00 00:00:00'),
(7, 'demo', '89e495e7941cf9e40e6980d14a16bf023ccd4c91', 0, NULL, '2013-09-24 21:09:21'),
(8, 'are', '26354c0464bca1faadbd50a79943de17f4d60124', 0, NULL, '2013-09-24 21:10:07'),
(9, 'ole', '061f1391acab0fc6cbcc2795668edc3a5ae071af', 0, NULL, '2013-09-24 21:13:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
 ADD PRIMARY KEY (`id`), ADD KEY `channel` (`pi_channel`,`name`,`pi_type`);

--
-- Indexes for table `app`
--
ALTER TABLE `app`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`), ADD KEY `created` (`created`);

--
-- Indexes for table `app$user`
--
ALTER TABLE `app$user`
 ADD KEY `created` (`created`), ADD KEY `pi_user` (`pi_user`), ADD KEY `pi_app` (`pi_app`), ADD KEY `permissions` (`permissions`);

--
-- Indexes for table `channel`
--
ALTER TABLE `channel`
 ADD UNIQUE KEY `name` (`name`), ADD UNIQUE KEY `id` (`id`), ADD KEY `pi_type` (`pi_type`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`), ADD UNIQUE KEY `apikey` (`apikey`), ADD KEY `timestamp` (`timestamp`);

--
-- Indexes for table `email`
--
ALTER TABLE `email`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `value` (`value`), ADD KEY `verified` (`verified`);

--
-- Indexes for table `file`
--
ALTER TABLE `file`
 ADD PRIMARY KEY (`id`), ADD KEY `permissions` (`permissions`), ADD KEY `pi_user` (`pi_user`);

--
-- Indexes for table `group`
--
ALTER TABLE `group`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`), ADD KEY `timestamp` (`timestamp`);

--
-- Indexes for table `group$user`
--
ALTER TABLE `group$user`
 ADD KEY `pi_group` (`pi_group`,`pi_user`);

--
-- Indexes for table `host`
--
ALTER TABLE `host`
 ADD PRIMARY KEY (`id`), ADD KEY `pi_address` (`pi_address`,`pi_group`,`pi_client`,`pi_service`,`pi_type`);

--
-- Indexes for table `image`
--
ALTER TABLE `image`
 ADD PRIMARY KEY (`id`), ADD KEY `created` (`created`);

--
-- Indexes for table `object`
--
ALTER TABLE `object`
 ADD PRIMARY KEY (`id`), ADD KEY `pi_channel` (`pi_channel`,`pi_user`,`pi_address`,`pi_type`,`permissions`,`created`);

--
-- Indexes for table `object$property`
--
ALTER TABLE `object$property`
 ADD PRIMARY KEY (`id`), ADD KEY `pi_channel` (`name`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pi`
--
ALTER TABLE `pi`
 ADD PRIMARY KEY (`name`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`), ADD KEY `created` (`created`);

--
-- Indexes for table `token`
--
ALTER TABLE `token`
 ADD UNIQUE KEY `value` (`value`), ADD KEY `usage` (`usage`);

--
-- Indexes for table `type`
--
ALTER TABLE `type`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`), ADD KEY `string` (`pi_string`), ADD KEY `pi_object` (`pi_object`), ADD KEY `pi_datetime` (`pi_datetime`), ADD KEY `pi_int` (`pi_int`), ADD KEY `pi_binary` (`pi_binary`);

--
-- Indexes for table `url`
--
ALTER TABLE `url`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `value` (`value`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `username` (`name`), ADD KEY `timestamp` (`created`), ADD KEY `pi_email` (`pi_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
MODIFY `id` int(4) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `app`
--
ALTER TABLE `app`
MODIFY `id` int(4) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `channel`
--
ALTER TABLE `channel`
AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
MODIFY `id` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `email`
--
ALTER TABLE `email`
MODIFY `id` bigint(8) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
MODIFY `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `group`
--
ALTER TABLE `group`
AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `host`
--
ALTER TABLE `host`
MODIFY `id` int(4) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
MODIFY `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `object`
--
ALTER TABLE `object`
MODIFY `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `object$property`
--
ALTER TABLE `object$property`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
MODIFY `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
MODIFY `id` int(4) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `url`
--
ALTER TABLE `url`
MODIFY `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
AUTO_INCREMENT=10;--
-- Database: `pidata`
--
CREATE DATABASE IF NOT EXISTS `pidata` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `pidata`;

-- --------------------------------------------------------

--
-- Table structure for table `collection`
--

DROP TABLE IF EXISTS `collection`;
CREATE TABLE IF NOT EXISTS `collection` (
`id` bigint(8) unsigned NOT NULL,
  `name` varchar(52) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `pi_address` int(4) unsigned NOT NULL,
  `pi_app` int(4) unsigned DEFAULT NULL,
  `pi_group` int(4) unsigned DEFAULT NULL,
  `pi_channel` bit(4) DEFAULT NULL,
  `pi_user` int(4) unsigned DEFAULT NULL,
  `permissions` bit(12) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `collection`
--
ALTER TABLE `collection`
 ADD PRIMARY KEY (`id`), ADD KEY `pi_app` (`pi_app`,`pi_group`,`pi_channel`,`pi_user`,`created`), ADD KEY `name` (`name`), ADD KEY `pi_address` (`pi_address`), ADD KEY `permissions` (`permissions`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `collection`
--
ALTER TABLE `collection`
MODIFY `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
