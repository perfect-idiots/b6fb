-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 07, 2017 at 04:59 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `b6fb`
--
CREATE DATABASE IF NOT EXISTS `b6fb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;
USE `b6fb`;

-- --------------------------------------------------------

--
-- Table structure for table `admin_accounts`
--

CREATE TABLE IF NOT EXISTS `admin_accounts` (
  `username` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `password_hash` varchar(60) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE IF NOT EXISTS `games` (
  `id` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `name` varchar(160) COLLATE utf8mb4_bin NOT NULL,
  `description` varchar(1024) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `games_to_genres`
--

CREATE TABLE IF NOT EXISTS `games_to_genres` (
  `game_id` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `genre_id` varchar(16) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`game_id`,`genre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE IF NOT EXISTS `genres` (
  `id` varchar(16) COLLATE utf8mb4_bin NOT NULL,
  `name` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user_accounts`
--

CREATE TABLE IF NOT EXISTS `user_accounts` (
  `username` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `password_hash` varchar(60) COLLATE utf8mb4_bin NOT NULL,
  `fullname` varchar(24) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user_comments`
--

CREATE TABLE IF NOT EXISTS `user_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `game_id` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `parent_comment_id` int(11) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user_favourite_games`
--

CREATE TABLE IF NOT EXISTS `user_favourite_games` (
  `username` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `game_id` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`username`,`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user_playing_history`
--

CREATE TABLE IF NOT EXISTS `user_playing_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `game_id` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
