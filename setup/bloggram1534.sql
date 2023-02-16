-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2023 at 03:18 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bloggram1534`
--

-- --------------------------------------------------------

--
-- Table structure for table `doyouknow`
--

CREATE TABLE `doyouknow` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userdetails`
--

CREATE TABLE `userdetails` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `useremail` varchar(60) NOT NULL,
  `usergender` varchar(7) NOT NULL,
  `userposition` varchar(100) NOT NULL DEFAULT 'Bloggram User',
  `phone_no` varchar(15) DEFAULT NULL,
  `username` varchar(35) NOT NULL,
  `password` varchar(225) NOT NULL,
  `securitycode` varchar(225) DEFAULT NULL,
  `createdtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `userdetails` (`name`, `useremail`, `usergender`, `userposition`, `phone_no`, `username`, `password`, `securitycode`, `createdtime`) VALUES ('Bloggram', 'bloggram@gmail.com', 'Male', 'developer | admin', '9801234567', 'admin', '$2y$10$oHDHbVtmfM2Pp21x0OcyX.LAp6GHDleVsAz9HuGhdug5Vpgo5oeoW', '$2y$10$oHDHbVtmfM2Pp21x0OcyX.LAp6GHDleVsAz9HuGhdug5Vpgo5oeoW', current_timestamp());
-- --------------------------------------------------------

--
-- Table structure for table `userfollowfollowing`
--

CREATE TABLE `userfollowfollowing` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL,
  `follow` varchar(500) NOT NULL,
  `following` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `userfollowfollowing` (`username`, `follow`, `following`) VALUES ('admin', 'a:0:{}', 'a:0:{}');
-- --------------------------------------------------------

--
-- Table structure for table `userposts`
--

CREATE TABLE `userposts` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL,
  `title` varchar(60) NOT NULL,
  `description` mediumtext NOT NULL,
  `likes` varchar(500) DEFAULT NULL,
  `createdtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
