SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `b6fb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;
USE `b6fb`;

CREATE TABLE IF NOT EXISTS `admin_accounts` (
  `username` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `password_hash` varchar(60) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS `games` (
  `id` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `name` varchar(160) COLLATE utf8mb4_bin NOT NULL,
  `description` varchar(1024) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS `games_to_genres` (
  `game_id` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `genre_id` varchar(16) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`game_id`,`genre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS `genres` (
  `id` varchar(16) COLLATE utf8mb4_bin NOT NULL,
  `name` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS `user_accounts` (
  `username` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `password_hash` varchar(60) COLLATE utf8mb4_bin NOT NULL,
  `fullname` varchar(24) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS `user_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `game_id` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `parent_comment_id` int(11) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS `user_favourite_games` (
  `username` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  `game_id` varchar(32) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`username`,`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

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
