-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 09, 2021 at 10:45 PM
-- Server version: 5.7.24
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coindesk`
--

-- --------------------------------------------------------

--
-- Table structure for table `coindeskemails`
--

CREATE TABLE `coindeskemails` (
  `ID` int(11) NOT NULL,
  `Date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UserEmail` varchar(320) NOT NULL,
  `address` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `coindeskemails`
--

INSERT INTO `coindeskemails` (`ID`, `Date`, `UserEmail`, `address`) VALUES
(1, '2021-04-10 00:04:26', 'koeln.kalk@gmail.com', '0x3f3Effe7578870E686cf334A06E19d816DdF1d6B'),
(4, '2021-04-10 00:44:50', '1@cd.com', ''),
(5, '2021-04-10 00:44:56', '2@cd.com', ''),
(6, '2021-04-10 00:45:03', '3@cd.com', ''),
(7, '2021-04-10 00:45:10', '4@cd.com', ''),
(8, '2021-04-10 00:45:18', '5@cd.com', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `coindeskemails`
--
ALTER TABLE `coindeskemails`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `UserEmail_2` (`UserEmail`),
  ADD KEY `UserEmail` (`UserEmail`),
  ADD KEY `address` (`address`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `coindeskemails`
--
ALTER TABLE `coindeskemails`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
