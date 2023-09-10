-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 04, 2022 at 06:11 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `votingniter`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `username` varchar(15) NOT NULL,
  `password` varchar(32) NOT NULL,
  `last_login` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `name`, `username`, `password`, `last_login`) VALUES
(1, 'Admin', 'admin', 'd00f5d5217896fb7fd601412cb890830', '2022-08-31 14:20:15');

-- --------------------------------------------------------

--
-- Table structure for table `candidate`
--

CREATE TABLE `candidate` (
  `candidate_id` int(11) NOT NULL,
  `candidate_name` varchar(50) NOT NULL DEFAULT '0',
  `explanation` text DEFAULT NULL,
  `photo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `candidate`
--

INSERT INTO `candidate` (`candidate_id`, `candidate_name`, `explanation`, `photo`) VALUES
(1, 'Sheikh Hasina', 'Awami League (AL)', '1.png'),
(2, 'Khaleda Zia', 'Bangladesh National Party(BNP)', '2.png'),
(3, ' Ghulam Muhammad Quader', 'Jatiya Party (JaPa)', '3.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `voter`
--

CREATE TABLE `voter` (
  `n_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT '0',
  `username` varchar(15) DEFAULT '0',
  `password` varchar(32) DEFAULT '0',
  `phone` varchar(11) NOT NULL,
  `verified` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `voter`
--

INSERT INTO `voter` (`n_id`, `name`, `username`, `password`, `phone`, `verified`) VALUES
(1, 'Nazia Tabassum Natasha', 'natasha', '32a030ae6159b1145051ce0a9b7569c1', '0', 0),
(2, 'Imran Fakir', 'imran', '798f73f70fd6f7064ed5c77afaee99f7', '0', 0),
(3, 'Sabiha Afsana Falguni', 'falguni', '03d5626ef3150d4e06e117a5ffd8ee22', '0', 0),
(4, 'Shamsur Rahman', 'shamsur', 'cf0f69025501a184d6532256682afa7f', '0', 0),
(5, 'Nahid Hasan', 'nahid', 'afca39a7d324c25989a6b57fa35b5ae2', '0', 0),
(6, 'Janay Alam', 'janay', '89218c5c8a5e939cc2928b0f28ffe390', '0', 0),
(7, 'Mahade Ahmed', 'mahade', 'ad326f0c5fa88b2e8397549415d1b441', '0', 0),
(8, 'Faria Tabassum', 'faria', '33c9ae4cfc956f3d82334b2950eb80a9', '0', 0),
(9, 'Karim Khan', 'karim', '86daa7cd600c9817465d4fde34446278', '0', 0),
(10, 'Rupa Islam', 'rupa', '896d90bc3cfd240b4c72c14b7221b54d', '0', 0),
(11, 'Zebin Akhter', 'zebin', '0cdce1fd11803e7a7f33327475830c7d', '0', 0),
(12, 'Sabuj Paul', 'sabuj', '5430aaea532f7af0b3b0c00f1e33a695', '0', 0);

-- --------------------------------------------------------

--
-- Table structure for table `voter_candidate`
--

CREATE TABLE `voter_candidate` (
  `id_voter_candidate` int(11) NOT NULL,
  `id_voting` int(11) DEFAULT NULL,
  `candidate_id` int(11) DEFAULT NULL,
  `vote` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `voter_candidate`
--

INSERT INTO `voter_candidate` (`id_voter_candidate`, `id_voting`, `candidate_id`, `vote`) VALUES
(3, 2, 1, 2),
(4, 2, 2, 0),
(5, 2, 3, 5);

-- --------------------------------------------------------

--
-- Table structure for table `voter_voting`
--

CREATE TABLE `voter_voting` (
  `id_voter` int(11) NOT NULL,
  `id_voting` int(11) DEFAULT NULL,
  `n_id` int(11) DEFAULT NULL,
  `time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `voter_voting`
--

INSERT INTO `voter_voting` (`id_voter`, `id_voting`, `n_id`, `time`) VALUES
(3, 2, 12, '2021-06-13 23:07:39'),
(4, 2, 3, '2021-06-13 23:08:17'),
(5, 2, 6, '2021-06-13 23:08:49'),
(6, 2, 8, '2021-06-13 23:09:15'),
(7, 2, 10, '2021-06-13 23:09:40'),
(8, 2, 11, '2021-06-13 23:10:09'),
(9, 2, 1, '2021-06-13 23:11:19');

-- --------------------------------------------------------

--
-- Table structure for table `voting`
--

CREATE TABLE `voting` (
  `id_voting` int(11) NOT NULL,
  `election` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `voting`
--

INSERT INTO `voting` (`id_voting`, `election`) VALUES
(2, 'National Election');

--
-- Triggers `voting`
--
DELIMITER $$
CREATE TRIGGER `delete_voting` BEFORE DELETE ON `voting` FOR EACH ROW BEGIN
	DELETE FROM voter_candidate WHERE voter_candidate.id_voting=OLD.id_voting;
	DELETE FROM voter_voting WHERE voter_voting.id_voting=OLD.id_voting;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `candidate`
--
ALTER TABLE `candidate`
  ADD PRIMARY KEY (`candidate_id`);

--
-- Indexes for table `voter`
--
ALTER TABLE `voter`
  ADD PRIMARY KEY (`n_id`);

--
-- Indexes for table `voter_candidate`
--
ALTER TABLE `voter_candidate`
  ADD PRIMARY KEY (`id_voter_candidate`);

--
-- Indexes for table `voter_voting`
--
ALTER TABLE `voter_voting`
  ADD PRIMARY KEY (`id_voter`);

--
-- Indexes for table `voting`
--
ALTER TABLE `voting`
  ADD PRIMARY KEY (`id_voting`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `candidate`
--
ALTER TABLE `candidate`
  MODIFY `candidate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `voter`
--
ALTER TABLE `voter`
  MODIFY `n_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `voter_candidate`
--
ALTER TABLE `voter_candidate`
  MODIFY `id_voter_candidate` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `voter_voting`
--
ALTER TABLE `voter_voting`
  MODIFY `id_voter` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `voting`
--
ALTER TABLE `voting`
  MODIFY `id_voting` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
