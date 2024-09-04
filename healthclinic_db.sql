-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 01, 2024 at 12:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `healthclinic_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `asthenis`
--

CREATE TABLE `asthenis` (
  `AT` varchar(255) NOT NULL,
  `P_Name` varchar(255) DEFAULT NULL,
  `P_Surname` varchar(255) DEFAULT NULL,
  `P_DateOfEntry` varchar(255) DEFAULT NULL,
  `P_AMKA` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id_books` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contains`
--

CREATE TABLE `contains` (
  `id_contains` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `defines`
--

CREATE TABLE `defines` (
  `id_defines` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `diathesimotita`
--

CREATE TABLE `diathesimotita` (
  `slot` varchar(255) DEFAULT NULL,
  `DI_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eggrafi`
--

CREATE TABLE `eggrafi` (
  `id_entry` varchar(255) NOT NULL,
  `e_doc` varchar(255) DEFAULT NULL,
  `e_healthproblems` varchar(255) DEFAULT NULL,
  `e_cure` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `has`
--

CREATE TABLE `has` (
  `id_has` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `iatros`
--

CREATE TABLE `iatros` (
  `specialty` varchar(255) DEFAULT NULL,
  `D_Name` varchar(255) DEFAULT NULL,
  `D_Surname` varchar(255) DEFAULT NULL,
  `D_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `istoriko`
--

CREATE TABLE `istoriko` (
  `id_history` varchar(255) NOT NULL,
  `id_inserts` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `is_a`
--

CREATE TABLE `is_a` (
  `id_isa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rantevou`
--

CREATE TABLE `rantevou` (
  `id_appointment` varchar(255) NOT NULL,
  `a_date` varchar(255) DEFAULT NULL,
  `a_time` varchar(255) DEFAULT NULL,
  `a_desc` varchar(255) DEFAULT NULL,
  `a_doc` varchar(255) DEFAULT NULL,
  `a_state` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `xristis`
--

CREATE TABLE `xristis` (
  `AT` varchar(255) NOT NULL,
  `FirstName` varchar(255) DEFAULT NULL,
  `LastName` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `asthenis`
--
ALTER TABLE `asthenis`
  ADD PRIMARY KEY (`AT`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id_books`);

--
-- Indexes for table `contains`
--
ALTER TABLE `contains`
  ADD PRIMARY KEY (`id_contains`);

--
-- Indexes for table `defines`
--
ALTER TABLE `defines`
  ADD PRIMARY KEY (`id_defines`);

--
-- Indexes for table `diathesimotita`
--
ALTER TABLE `diathesimotita`
  ADD PRIMARY KEY (`DI_id`);

--
-- Indexes for table `eggrafi`
--
ALTER TABLE `eggrafi`
  ADD PRIMARY KEY (`id_entry`);

--
-- Indexes for table `has`
--
ALTER TABLE `has`
  ADD PRIMARY KEY (`id_has`);

--
-- Indexes for table `iatros`
--
ALTER TABLE `iatros`
  ADD PRIMARY KEY (`D_id`);

--
-- Indexes for table `istoriko`
--
ALTER TABLE `istoriko`
  ADD PRIMARY KEY (`id_history`);

--
-- Indexes for table `is_a`
--
ALTER TABLE `is_a`
  ADD PRIMARY KEY (`id_isa`);

--
-- Indexes for table `rantevou`
--
ALTER TABLE `rantevou`
  ADD PRIMARY KEY (`id_appointment`);

--
-- Indexes for table `xristis`
--
ALTER TABLE `xristis`
  ADD PRIMARY KEY (`AT`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
