-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 03, 2015 at 12:21 PM
-- Server version: 5.1.73
-- PHP Version: 5.3.2-1ubuntu4.27

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
  `quantity` int(11) NOT NULL,
  `manufacturer` varchar(50) NOT NULL,
  `manufacturerpart` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `distributor` varchar(50) NOT NULL,
  `distributorpart` varchar(50) NOT NULL,
  `notes` varchar(255) NOT NULL,
  `location` varchar(50) NOT NULL,
  `url` varchar(100) NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  `reference` varchar(255) NOT NULL,
  PRIMARY KEY (`id`,`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=343 ;
