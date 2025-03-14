-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 14, 2025 at 11:53 AM
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
(2, 'The Hacienda', '1740032179_book2.jpg', 'Isabel Cañas', 2002, 0, 299, 2990),
(5, 'El filibusterismo', '1740450422_book4.jpg', 'José Rizal', 1891, 0, 249, 2490),
(6, 'Legend of Mariang Makiling', '1740701783_book6.jpg', 'Nick Joaquin', 1997, 0, 280, 2800),
(7, 'Alamat ng Sampalok', '1740702817_book7.jpg', 'Virgilio S. Almario', 2008, 5, 280, 3360),
(8, 'Alamat ng Bahaghari', '1740703364_book8.png', 'Rene O. Villanueva', 2003, 5, 250, 2500);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `isbn` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `isbn`, `quantity`) VALUES
(103, 1, 2, 1),
(114, 11, 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gcash_users2`
--

CREATE TABLE `gcash_users2` (
  `id` int(11) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `balance` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gcash_users2`
--

INSERT INTO `gcash_users2` (`id`, `mobile_number`, `email`, `balance`) VALUES
(1, '981237123', 'test1@gmail.com', 1000000.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `shipping_address` text NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `email` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `payment_receipt` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `shipping_address`, `payment_method`, `order_date`, `status`, `email`, `first_name`, `last_name`, `mobile`, `address`, `city`, `state`, `zipcode`, `payment_receipt`) VALUES
(1, 11, 3785.00, 'Hipolito St. Sitio Sandayong, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-03 01:36:21', 'completed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 11, 330.00, 'Hipolito St. Sitio Sandayong, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-03 02:02:15', 'completed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 11, 349.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-03 02:14:32', 'canceled', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 11, 548.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-03 03:19:00', 'canceled', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 11, 330.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'credit_card', '2025-03-03 03:19:48', 'canceled', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 11, 1170.00, 'Hipolito St. Sitio Sandayong, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-04 06:52:35', 'completed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 11, 349.00, 'Hipolito St. Sitio Sandayong, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-04 07:26:46', 'canceled', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 11, 300.00, 'Hipolito St. Sitio Sandayong, Cebu, CEBU, 6000', 'credit_card', '2025-03-04 07:27:56', 'canceled', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 11, 299.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-06 06:47:18', 'canceled', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 11, 330.00, 'Hipolito St. Sitio Sandayong, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-06 06:52:25', 'canceled', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 12, 859.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'credit_card', '2025-03-06 07:23:57', 'completed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 11, 399.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-10 02:51:15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 11, 349.00, 'Cebu, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-10 02:51:58', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 11, 349.00, 'Lorega San Miguel, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-10 03:01:50', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 11, 349.00, 'Hipolito St. Sitio Sandayong, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-10 03:02:07', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 11, 349.00, 'Hipolito St. Sitio Sandayong, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-10 03:05:20', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 11, 349.00, 'Hipolito St. Sitio Sandayong, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-10 03:06:44', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 11, 350.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-11 00:56:12', 'completed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 11, 380.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-11 01:15:39', 'completed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 11, 399.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-12 00:43:29', 'canceled', 'ryan123@gmail.com', 'mario', 'hapon', '+639500146972', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', 'Cebu', 'CEBU', '6000', NULL),
(27, 11, 350.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-13 01:26:01', 'completed', 'test1@gmail.com', 'mario', 'hapon', '+63981237123', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', 'Cebu', 'CEBU', '6000', NULL),
(28, 11, 399.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-13 02:29:08', 'pending', 'test1@gmail.com', 'mario', 'hapon', '+63981237123', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', 'Cebu', 'CEBU', '6000', NULL),
(29, 11, 399.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-13 02:36:26', 'pending', 'test1@gmail.com', 'mario', 'hapon', '+63981237123', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', 'Cebu', 'CEBU', '6000', NULL),
(30, 11, 399.00, 'Hipolito St. Sitio Sandayong, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-13 02:45:25', 'pending', 'test1@gmail.com', 'mario', 'hapon', '+63981237123', 'Hipolito St. Sitio Sandayong', 'Cebu', 'CEBU', '6000', NULL),
(31, 11, 399.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-13 02:46:56', 'pending', 'test1@gmail.com', 'mario', 'hapon', '+63981237123', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', 'Cebu', 'CEBU', '6000', NULL),
(32, 11, 380.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-13 02:53:38', 'pending', 'test1@gmail.com', 'mario', 'hapon', '+63981237123', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', 'Cebu', 'CEBU', '6000', NULL),
(33, 11, 349.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-13 03:10:47', 'pending', 'test1@gmail.com', 'mario', 'hapon', '+63981237123', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', 'Cebu', 'CEBU', '6000', NULL),
(34, 11, 380.00, 'Hipolito St. Sitio Sandayong, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-13 03:15:36', 'pending', 'test1@gmail.com', 'mario', 'hapon', '+63981237123', 'Hipolito St. Sitio Sandayong', 'Cebu', 'CEBU', '6000', NULL),
(35, 11, 399.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-13 03:19:01', 'pending', 'test1@gmail.com', 'mario', 'hapon', '+63981237123', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', 'Cebu', 'CEBU', '6000', NULL),
(36, 11, 399.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-13 03:24:55', 'pending', 'test1@gmail.com', 'mario', 'hapon', '+63981237123', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', 'Cebu', 'CEBU', '6000', NULL),
(37, 11, 380.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-14 05:25:24', 'completed', 'test1@gmail.com', 'mario', 'hapon', '+63981237123', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', 'Cebu', 'CEBU', '6000', 'receipt_37_1741933349.png');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `book_id`, `quantity`, `price`) VALUES
(1, 1, 5, 15, 249.00),
(2, 2, 6, 1, 280.00),
(3, 3, 2, 1, 299.00),
(4, 4, 5, 2, 249.00),
(5, 5, 7, 1, 280.00),
(6, 6, 6, 3, 280.00),
(7, 6, 7, 1, 280.00),
(8, 7, 2, 1, 299.00),
(9, 8, 8, 1, 250.00),
(10, 9, 5, 1, 249.00),
(11, 10, 7, 1, 280.00),
(12, 11, 5, 1, 249.00),
(13, 11, 6, 1, 280.00),
(14, 11, 7, 1, 280.00),
(15, 12, 2, 1, 299.00),
(16, 13, 5, 1, 249.00),
(17, 14, 5, 1, 249.00),
(18, 15, 5, 1, 249.00),
(19, 16, 5, 1, 249.00),
(20, 17, 5, 1, 249.00),
(31, 24, 8, 1, 250.00),
(32, 25, 7, 1, 280.00),
(33, 26, 2, 1, 299.00),
(34, 27, 8, 1, 250.00),
(35, 28, 2, 1, 299.00),
(36, 29, 2, 1, 299.00),
(37, 30, 2, 1, 299.00),
(38, 31, 2, 1, 299.00),
(39, 32, 6, 1, 280.00),
(40, 33, 5, 1, 249.00),
(41, 34, 6, 1, 280.00),
(42, 35, 2, 1, 299.00),
(43, 36, 2, 1, 299.00),
(44, 37, 6, 1, 280.00);

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
(1, 'ryan', 'gabutin', 'test1@gmail.com', 'ryan1234@', '0975195715', 'Hipolito St. Sitio Sandayong', '2025-02-13 06:10:10', NULL),
(2, 'Anna Marie', 'Calvario', 'annamarie9@gmail.com', '$2y$10$fRIzvJrHn7bqkMGUjr2r5.Y3qer2QGodhioYirj.vKWdX/eDBV6Gi', '09292626010', 'Lorega San Miguel', '2025-02-13 06:12:29', NULL),
(3, 'Jundel', 'Malazarte', 'test3@gmail.com', '$2y$10$9lNvkCLNyYkjm/gSj5I3xOHeFaqc6fDZjl51jGl4MT/55SYl5gnTK', '09292626010', 'Sambag 1 Cebu City', '2025-02-13 06:39:24', NULL),
(5, 'Jundel', 'Malazarte', 'test4@gmail.com', '$2y$10$Ejqk34qMnAqhPmv/OljKK.gFu9GucgpBqSPG/YYhQ.V3lp8urq9SK', '981237123', 'Sambag 1 Cebu City', '2025-02-13 07:05:00', NULL),
(6, 'Ronald', 'Pacquiao', 'test5@gmail.com', '$2y$10$2JKQ82GiS9/SskNxqHEOLuv8nPTmYZp9SaXRDrzUVeyvHof1gajVK', '981237123', 'Sambag 1 Cebu City', '2025-02-14 05:31:46', NULL),
(7, 'jenjie', 'igot', 'igot23@gmail.com', '$2y$10$sIN.QxVxHEGBZQijoTbzxeEScqC8StI7CwIKySUzMuwbkYqofvZGW', '0975195715', 'Hipolito St. Sitio Sandayong', '2025-02-17 01:43:41', 'uploads/default-profile.png'),
(8, 'manny ', 'Pacquiao', 'turyak2@gmail.com', '$2y$10$7WPCDV3nd8O5xO/QD/cG3.wqSCmANgviwfGMPDyq9RvG1mNZvyAS6', '0975195715', 'Hipolito St. Sitio Sandayong', '2025-02-17 01:50:09', 'uploads/67b3e9e3bd7e1_man.jpg'),
(9, 'test', 'test', 'test6@gmail.com', '$2y$10$HcK17YDB1gDJU5wb/4uNhehbPNc./su9uVnBTjqI1SWlFHH2dMYBy', '09428013424', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', '2025-02-18 03:28:08', 'uploads/67b3fe4889550_man.jpg'),
(10, 'mario', 'friolo', 'mario@gmail.com', '$2y$10$Pc8sW216gFIwI7vDNN2G6OWcbXzrlUTDm9aP3sjAGwThZ72pAG/Za', '09812371239', '123 Main St, City, 1234', '2025-02-20 07:35:16', 'uploads/67b6db69e587d_book_icon.png'),
(11, 'mario', 'hapon', 'mario1@gmail.com', '$2y$10$eNG.E5IG81V8VCbLEO4V2.BjgTIp2Z2X/JeEmvFXoB6wUik/Xj2HK', '09812371231', '312 Hipolito St. Sitio Sandayong, Cebu, Cebu 6000', '2025-02-28 04:03:49', 'uploads/67c148493dbe3_Man-PNG-Free-Download.png'),
(12, 'Manny', 'Pacs', 'manny@gmail.com', '$2y$10$SfNLIyK3i7DEFDALyMZNCO089blWCCAZuE1lD2HBQVawOAs9hZNG.', '09121231234', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', '2025-03-06 07:22:46', 'uploads/67c94d462951b_Man-PNG-Free-Download.png'),
(13, 'Jundel', 'Malazarte', 'jundelmalazarte348@gmail.com', '$2y$10$HVIbYCfBvpprnRC367OxNOPsFr4MvqxrE3LVUf6KRE/SAEM.wJF5q', '09812371231', 'Sambag 1 Cebu City', '2025-03-10 02:03:40', 'uploads/67ce487c82fe8_default.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`isbn`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`isbn`),
  ADD KEY `isbn` (`isbn`);

--
-- Indexes for table `gcash_users2`
--
ALTER TABLE `gcash_users2`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `book_id` (`book_id`);

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
  MODIFY `isbn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `gcash_users2`
--
ALTER TABLE `gcash_users2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`isbn`) REFERENCES `books` (`isbn`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`isbn`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
