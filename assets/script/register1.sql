-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2025 at 08:16 AM
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `pass`, `phone`, `address`, `created_at`) VALUES
(1, 'ryan', 'gabutin', 'test1@gmail.com', '$2y$10$xmuPruKT5pJpkV4fWzchPOIqM2.al37xXhH5SyXn.LJ/v5Pobkx5S', '0975195715', 'Hipolito St. Sitio Sandayong', '2025-02-13 06:10:10'),
(2, 'Anna Marie', 'Calvario', 'annamarie9@gmail.com', '$2y$10$fRIzvJrHn7bqkMGUjr2r5.Y3qer2QGodhioYirj.vKWdX/eDBV6Gi', '09292626010', 'Lorega San Miguel', '2025-02-13 06:12:29'),
(3, 'Jundel', 'Malazarte', 'test3@gmail.com', '$2y$10$9lNvkCLNyYkjm/gSj5I3xOHeFaqc6fDZjl51jGl4MT/55SYl5gnTK', '09292626010', 'Sambag 1 Cebu City', '2025-02-13 06:39:24'),
(4, 'ryan2', 'ryan2', 'ryan123@gmail.com', '$2y$10$mRuFD0/2KX0gN2gmnxhcfepEzIkr7b0PV4Kj3lZn4Hmom1zxz.TRG', '095150561', 'Sambag 1 Cebu City', '2025-02-13 07:00:15'),
(5, 'Jundel', 'Malazarte', 'test4@gmail.com', '$2y$10$Ejqk34qMnAqhPmv/OljKK.gFu9GucgpBqSPG/YYhQ.V3lp8urq9SK', '981237123', 'Sambag 1 Cebu City', '2025-02-13 07:05:00');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
