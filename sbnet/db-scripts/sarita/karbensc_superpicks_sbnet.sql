-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 29, 2014 at 05:44 AM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `karbensc_superpicks_sbnet`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer_choices`
--

CREATE TABLE IF NOT EXISTS `customer_choices` (
  `cust_choice_id` int(11) NOT NULL AUTO_INCREMENT,
  `ec_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `choice_type` tinyint(1) NOT NULL,
  PRIMARY KEY (`cust_choice_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `customer_choices`
--

INSERT INTO `customer_choices` (`cust_choice_id`, `ec_id`, `customer_id`, `choice_type`) VALUES
(1, 2867, 108, 0);

-- --------------------------------------------------------

--
-- Table structure for table `register_user`
--

CREATE TABLE IF NOT EXISTS `register_user` (
  `register_id` int(20) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`register_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `register_user`
--

INSERT INTO `register_user` (`register_id`, `firstname`, `lastname`, `email`, `username`, `password`, `date_created`) VALUES
(1, 'Raj', '', '', '', 'd41d8cd98f00b204e9800998ecf8427e', '2014-01-24 04:15:45'),
(2, 'Meena', '', '', '', 'd41d8cd98f00b204e9800998ecf8427e', '2014-01-24 04:28:40'),
(3, 'Manish', 'Mathore', 'manish@gmail.com', 'manish', 'b2fd8301040ce1dae22f4bfcd54017b0', '2014-01-24 04:56:17'),
(24, 'Mehek', 'Kurana', 'mehek', 'mehek', 'c598ef0dca28e03d5c2a0ba1e718a5f0', '2014-01-24 07:32:19'),
(25, 'Mohit', 'Meher', 'mohit@gmail.com', 'mohit', 'cf3b27ef58e8421ad18556857077d39f', '2014-01-24 07:39:34'),
(26, 'Mahi', 'Gill', 'mahi@gm', 'mahi', 'a6c5293bfc34fe0643b360bb007d948d', '2014-01-24 07:43:55'),
(27, 'Satish', 'Kamat', 'satish@gmail.com', 'satish', '948425aa3407a45c286531158d9e95d2', '2014-01-24 07:46:39'),
(28, 'Pranay', 'Raikar', 'pranay@gmail.com', 'pranay', '1229730a374a5b5883240371478298dd', '2014-01-24 07:52:01'),
(29, 'Minal', 'Kurana', 'minal@gmail.com', 'minal', '84f0df1b3bb451e62ba96989254d1134', '2014-01-24 09:09:51'),
(30, 'Rahul', 'Mehta', 'rahul@yahoo.com', 'rahul', '2acb7811397a5c3bea8cba57b0388b79', '2014-01-25 06:41:26'),
(31, 'Raj', 'Mehta', 'raj@gmail.com', 'raj', 'cac5ff630494aa784ce97b9fafac2500', '2014-01-27 10:18:24');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
