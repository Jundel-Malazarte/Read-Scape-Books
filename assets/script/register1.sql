-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 25, 2025 at 03:43 AM
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
-- Database: `register1`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `isbn` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `book_image` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `copyright` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` float NOT NULL,
  `total` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`isbn`, `title`, `book_image`, `author`, `copyright`, `qty`, `price`, `total`) VALUES
(2, 'The Hacienda', '../images/1740032179_book2.jpg', 'Isabel Cañas', 2002, 10, 299, 2990),
(5, 'El filibusterismo', '1740450422_book4.jpg', 'José Rizal', 1891, 60, 249, 14940);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `pass`, `phone`, `address`, `created_at`, `profile_image`) VALUES
(1, 'ryan', 'gabutin', 'test1@gmail.com', '$2y$10$xmuPruKT5pJpkV4fWzchPOIqM2.al37xXhH5SyXn.LJ/v5Pobkx5S', '0975195715', 'Hipolito St. Sitio Sandayong', '2025-02-13 06:10:10', NULL),
(2, 'Anna Marie', 'Calvario', 'annamarie9@gmail.com', '$2y$10$fRIzvJrHn7bqkMGUjr2r5.Y3qer2QGodhioYirj.vKWdX/eDBV6Gi', '09292626010', 'Lorega San Miguel', '2025-02-13 06:12:29', NULL),
(3, 'Jundel', 'Malazarte', 'test3@gmail.com', '$2y$10$9lNvkCLNyYkjm/gSj5I3xOHeFaqc6fDZjl51jGl4MT/55SYl5gnTK', '09292626010', 'Sambag 1 Cebu City', '2025-02-13 06:39:24', NULL),
(5, 'Jundel', 'Malazarte', 'test4@gmail.com', '$2y$10$Ejqk34qMnAqhPmv/OljKK.gFu9GucgpBqSPG/YYhQ.V3lp8urq9SK', '981237123', 'Sambag 1 Cebu City', '2025-02-13 07:05:00', NULL),
(6, 'Ronald', 'Pacquiao', 'test5@gmail.com', '$2y$10$2JKQ82GiS9/SskNxqHEOLuv8nPTmYZp9SaXRDrzUVeyvHof1gajVK', '981237123', 'Sambag 1 Cebu City', '2025-02-14 05:31:46', NULL),
(7, 'jenjie', 'igot', 'igot23@gmail.com', '$2y$10$sIN.QxVxHEGBZQijoTbzxeEScqC8StI7CwIKySUzMuwbkYqofvZGW', '0975195715', 'Hipolito St. Sitio Sandayong', '2025-02-17 01:43:41', 'uploads/default-profile.png'),
(8, 'manny ', 'Pacquiao', 'turyak2@gmail.com', '$2y$10$7WPCDV3nd8O5xO/QD/cG3.wqSCmANgviwfGMPDyq9RvG1mNZvyAS6', '0975195715', 'Hipolito St. Sitio Sandayong', '2025-02-17 01:50:09', 'uploads/67b3e9e3bd7e1_man.jpg'),
(9, 'test', 'test', 'test6@gmail.com', '$2y$10$HcK17YDB1gDJU5wb/4uNhehbPNc./su9uVnBTjqI1SWlFHH2dMYBy', '09428013424', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', '2025-02-18 03:28:08', 'uploads/67b3fe4889550_man.jpg'),
(10, 'mario', 'friolo', 'mario@gmail.com', '$2y$10$Pc8sW216gFIwI7vDNN2G6OWcbXzrlUTDm9aP3sjAGwThZ72pAG/Za', '09812371239', '123 Main St, City, 1234', '2025-02-20 07:35:16', 'uploads/67b6db69e587d_book_icon.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`isbn`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `isbn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
