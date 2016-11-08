-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 07, 2016 at 08:34 PM
-- Server version: 5.1.73
-- PHP Version: 5.3.2-1ubuntu4.30

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `electronicparts`
--

CREATE TABLE IF NOT EXISTS `electronicparts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `quantity` int(11) NOT NULL,
  `manufacturer` varchar(255) NOT NULL,
  `partnumber` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `distributor` varchar(255) NOT NULL,
  `distributorsku` varchar(255) NOT NULL,
  `distributorurl` varchar(255) NOT NULL,
  `octoparturl` varchar(255) NOT NULL,
  `parturl` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `notes` varchar(255) NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  `reference` varchar(255) NOT NULL,
  PRIMARY KEY (`id`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `electronicparts`
--

