-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 26, 2014 at 06:40 PM
-- Server version: 5.5.32-cll-lve
-- PHP Version: 5.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `stovot_trentondb`
--

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE IF NOT EXISTS `movies` (
  `title`     VARCHAR(120)
              COLLATE ascii_bin NOT NULL,
  `director`  VARCHAR(120)
              COLLATE ascii_bin NOT NULL,
  `shakiness` FLOAT DEFAULT NULL,
  `timestamp` TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`title`),
  KEY `shakiness` (`shakiness`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `director` (`director`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =ascii
  COLLATE =ascii_bin;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`title`, `director`, `shakiness`, `timestamp`) VALUES
  ('Batman Begins', 'Cristopher Nolan', 4.5, '2014-02-11 05:00:00'),
  ('Groundhog Day', 'Harold Ramis', 0.3, '2014-02-12 05:00:00'),
  ('Terminator 2: Judgment Day', 'James Cameron', 4.5, '2014-02-12 05:00:00'),
  ('The Terminator', 'James Cameron', 5.7, '2014-02-12 05:00:00'),
  ('The Dark Night', 'Christopher Nolan', 5.4, '2014-02-12 05:00:00'),
  ('Doctor Who', 'Stephen Moffatt', 1, '2014-02-12 05:00:00'),
  ('A title', 'A director', 4.44444, '2014-02-14 00:21:34'),
  ('AAA', 'AAA', 90001, '2014-02-14 23:23:28'),
  ('BBB', 'BBB', 76, '2014-02-14 23:27:01'),
  ('CCC', 'CCC', 77, '2014-02-14 23:27:20'),
  ('DDDD', 'DDD', 444, '2014-02-14 23:27:49'),
  ('EEE', 'EEE', 776, '2014-02-14 23:29:00'),
  ('People', 'Things', 65, '2014-02-14 23:33:06'),
  ('New Movie', 'New Director', 53, '2014-02-19 00:39:00'),
  ('Feb 17 Movie', 'A Director', 55, '2014-02-19 01:25:48'),
  ('Another Feb17 Movie', 'pErson', 6783, '2014-02-19 01:26:02'),
  ('MOVIE TITLE!', 'MOVIE DIRECTOR!', 6531110, '2014-02-19 01:26:15'),
  ('TRE', 'NTON', 4, '2014-02-19 22:42:54'),
  ('Opera Movie', 'By Opera!', 812, '2014-02-21 03:00:45'),
  ('Solaris', 'Soderbergh', 4, '2014-02-23 23:20:32');

/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
