-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2024 at 10:56 PM
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

--
-- Dumping data for table `asthenis`
--

INSERT INTO `asthenis` (`AT`, `P_Name`, `P_Surname`, `P_DateOfEntry`, `P_AMKA`) VALUES
('AN67554', 'User1', 'user', '2024-09-24 23:56:23', '12345');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id_books` int(11) NOT NULL,
  `AT` varchar(255) NOT NULL,
  `id_appointment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contains`
--

CREATE TABLE `contains` (
  `history_id` int(11) NOT NULL,
  `entry_id` int(11) NOT NULL
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
  `id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `slot` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eggrafi`
--

CREATE TABLE `eggrafi` (
  `id_entry` int(11) NOT NULL,
  `e_doc` int(11) NOT NULL,
  `e_healthproblems` text NOT NULL,
  `e_cure` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `has`
--

CREATE TABLE `has` (
  `history_id` int(11) NOT NULL,
  `patient_id` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `history_id` int(11) NOT NULL,
  `patient_id` varchar(12) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `iatros`
--

CREATE TABLE `iatros` (
  `specialty` varchar(255) DEFAULT NULL,
  `D_Name` varchar(255) DEFAULT NULL,
  `D_Surname` varchar(255) DEFAULT NULL,
  `D_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `iatros`
--

INSERT INTO `iatros` (`specialty`, `D_Name`, `D_Surname`, `D_id`) VALUES
('Καρδιολόγος', 'Doctor1', 'doc', 3);

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
-- Table structure for table `rantevou`
--

CREATE TABLE `rantevou` (
  `a_date` varchar(255) DEFAULT NULL,
  `a_time` varchar(255) DEFAULT NULL,
  `a_desc` varchar(255) DEFAULT NULL,
  `a_doc` varchar(255) DEFAULT NULL,
  `a_state` varchar(255) DEFAULT NULL,
  `id_appointment` int(11) NOT NULL
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
  `Password` varchar(255) DEFAULT NULL,
  `Role` enum('Doctor','Secretary','Patient') NOT NULL DEFAULT 'Patient'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `xristis`
--

INSERT INTO `xristis` (`AT`, `FirstName`, `LastName`, `Email`, `Password`, `Role`) VALUES
('AN32190', 'Doctor1', 'doc', 'doctor@doc.com', '$2y$10$GOQ6rkKHcNbRoFfLiXoblufxheVL9nnXur5xnaNg7KFziTCm2mP2i', 'Doctor'),
('AN567866', 'Γραμματέας', 'γρ', 'sec@sec.com', '$2y$10$ogGD/0HSt8N5LgdoQ/M4ausrsRj3qbzSXiLI5s6jj9t1ya8Ok/3ci', 'Secretary'),
('AN67554', 'User1', 'user', 'user1@gmail.com', '$2y$10$y65RE5z2CuRTbwm0inMSRegqJP3sx5WxDwduhkbAF2LqDnm6w5EAK', 'Patient');

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
  ADD PRIMARY KEY (`history_id`,`entry_id`),
  ADD KEY `entry_id` (`entry_id`);

--
-- Indexes for table `defines`
--
ALTER TABLE `defines`
  ADD PRIMARY KEY (`id_defines`);

--
-- Indexes for table `diathesimotita`
--
ALTER TABLE `diathesimotita`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `eggrafi`
--
ALTER TABLE `eggrafi`
  ADD PRIMARY KEY (`id_entry`),
  ADD KEY `e_doc` (`e_doc`);

--
-- Indexes for table `has`
--
ALTER TABLE `has`
  ADD PRIMARY KEY (`history_id`,`patient_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `patient_id` (`patient_id`);

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
-- Indexes for table `rantevou`
--
ALTER TABLE `rantevou`
  ADD PRIMARY KEY (`id_appointment`);

--
-- Indexes for table `xristis`
--
ALTER TABLE `xristis`
  ADD PRIMARY KEY (`AT`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id_books` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `diathesimotita`
--
ALTER TABLE `diathesimotita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eggrafi`
--
ALTER TABLE `eggrafi`
  MODIFY `id_entry` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `iatros`
--
ALTER TABLE `iatros`
  MODIFY `D_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rantevou`
--
ALTER TABLE `rantevou`
  MODIFY `id_appointment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contains`
--
ALTER TABLE `contains`
  ADD CONSTRAINT `contains_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`history_id`),
  ADD CONSTRAINT `contains_ibfk_2` FOREIGN KEY (`entry_id`) REFERENCES `eggrafi` (`id_entry`);

--
-- Constraints for table `diathesimotita`
--
ALTER TABLE `diathesimotita`
  ADD CONSTRAINT `diathesimotita_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `iatros` (`D_id`);

--
-- Constraints for table `eggrafi`
--
ALTER TABLE `eggrafi`
  ADD CONSTRAINT `eggrafi_ibfk_1` FOREIGN KEY (`e_doc`) REFERENCES `iatros` (`D_id`) ON DELETE CASCADE;

--
-- Constraints for table `has`
--
ALTER TABLE `has`
  ADD CONSTRAINT `has_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`history_id`),
  ADD CONSTRAINT `has_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `asthenis` (`AT`);

--
-- Constraints for table `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `history_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `asthenis` (`AT`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
