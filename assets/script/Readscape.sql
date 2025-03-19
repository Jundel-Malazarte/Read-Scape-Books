-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2025 at 08:28 AM
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
(2, 'The Hacienda', '../images/../images/../images/1740032179_book2.jpg', 'Isabel Cañas', 2002, 10, 299, 2990),
(5, 'El filibusterismo', '../images/1740450422_book4.jpg', 'José Rizal', 1891, 9, 249, 2490),
(6, 'Legend of Mariang Makiling', '../images/1740701783_book6.jpg', 'Nick Joaquin', 1997, 9, 280, 2800),
(7, 'Alamat ng Sampalok', '../images/1740702817_book7.jpg', 'Virgilio S. Almario', 2008, 10, 280, 2800),
(8, 'Alamat ng Bahaghari', '../images/../images/../images/1740703364_book8.png', 'Rene O. Villanueva', 2003, 10, 250, 2500);

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
(125, 11, 7, 1),
(131, 15, 2, 1);

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
(1, '981237123', 'test1@gmail.com', 1000000.00),
(2, '9123456789', 'jundel@gmail.com', 1000000.00);

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
(37, 11, 380.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-14 05:25:24', 'completed', 'test1@gmail.com', 'mario', 'hapon', '+63981237123', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', 'Cebu', 'CEBU', '6000', 'receipt_37_1741933349.png'),
(38, 11, 380.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-17 06:33:52', 'completed', 'test1@gmail.com', 'mario', 'hapon', '+63981237123', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', 'Cebu', 'CEBU', '6000', 'receipt_38_1742193265.png'),
(39, 11, 380.00, '123 Hipolito St. Sitio Sandayong, Cebu City, 6000, Cebu, CEBU, 6000', 'cash_on_delivery', '2025-03-18 01:43:01', 'completed', 'test1@gmail.com', 'mario', 'hapon', '+63981237123', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', 'Cebu', 'CEBU', '6000', 'receipt_39_1742262305.png'),
(40, 13, 1630.00, 'Sambag 1 Cebu City, Cebu CIty, Cebu, 6000', 'cash_on_delivery', '2025-03-18 02:33:43', 'completed', 'jundel@gmail.com', 'Jundel', 'Malazarte', '639123456789', 'Sambag 1 Cebu City', 'Cebu CIty', 'Cebu', '6000', 'receipt_40_1742265283.png'),
(42, 16, 380.00, 'Don Juan Climaco Sr. Toledo City, Toledo, Cebu, 6038', 'cash_on_delivery', '2025-03-19 07:10:49', 'canceled', 'heroship@gmail.com', 'Heroshi', 'Paro', '09055565546', 'Don Juan Climaco Sr. Toledo City', 'Toledo', 'Cebu', '6038', NULL);

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
(44, 37, 6, 1, 280.00),
(45, 38, 7, 1, 280.00),
(46, 39, 7, 1, 280.00),
(47, 40, 7, 1, 280.00),
(48, 40, 8, 5, 250.00),
(50, 42, 6, 1, 280.00);

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
  `profile_image` varchar(255) DEFAULT NULL,
  `role` varchar(10) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `pass`, `phone`, `address`, `created_at`, `profile_image`, `role`) VALUES
(2, 'Anna Marie', 'Calvario', 'annamarie9@gmail.com', '$2y$10$fRIzvJrHn7bqkMGUjr2r5.Y3qer2QGodhioYirj.vKWdX/eDBV6Gi', '09292626010', 'Lorega San Miguel', '2025-02-13 06:12:29', NULL, 'user'),
(6, 'Ronald Jhun', 'Pacquiao', 'test5@gmail.com', '$2y$10$2JKQ82GiS9/SskNxqHEOLuv8nPTmYZp9SaXRDrzUVeyvHof1gajVK', '981237123', 'Sambag 1 Cebu City', '2025-02-14 05:31:46', NULL, 'user'),
(11, 'mario', 'hapon', 'mario1@gmail.com', '$2y$10$eNG.E5IG81V8VCbLEO4V2.BjgTIp2Z2X/JeEmvFXoB6wUik/Xj2HK', '09812371231', 'Hipolito St. Sitio Sandayong, Cebu, Cebu', '2025-02-28 04:03:49', 'uploads/67d796730059b_book9.jpg', 'user'),
(12, 'Manny', 'Pacs', 'manny@gmail.com', '$2y$10$SfNLIyK3i7DEFDALyMZNCO089blWCCAZuE1lD2HBQVawOAs9hZNG.', '09121231234', '123 Hipolito St. Sitio Sandayong, Cebu City, 6000', '2025-03-06 07:22:46', 'uploads/67c94d462951b_Man-PNG-Free-Download.png', 'user'),
(13, 'Jundel', 'Malazarte', 'jundelmalazarte348@gmail.com', '$2y$10$y5zBZXOjVIze9Y4Ns0Ai5OmID9MH55JOj0AcPxSl7HrWenaDTKt6i', '09812371231', 'Sambag 1 Cebu City', '2025-03-10 02:03:40', 'uploads/67ce487c82fe8_default.jpg', 'user'),
(14, 'Admin', 'User', 'admin@gmail.com', 'admin123', '', '', '2025-03-18 00:36:04', 'uploads/67ce487c82fe8_default.jpg', 'admin'),
(15, 'Anna Marie', 'Calvario', 'calvarioannamarie9@gmail.com', '$2y$10$KHl6bMraFYlOWv3k6EddMespMPeraa76GMNQgP6MXYSrHB.VDrELy', '09052973120', 'Lorega San Miguel Cebu City', '2025-03-18 05:59:45', 'uploads/67d90bd102201_profile2.png', 'user'),
(16, 'Heroshi', 'Paro', 'heroship@gmail.com', '$2y$10$DVPhs/G3GHS8PS9tzm/5PuqyIh9xVYDqBuZ4RGHLWrs0UT7da9B8m', '09055565546', 'Don Juan Climaco Sr. Toledo City', '2025-03-19 07:08:24', 'uploads/default.jpg', 'user');

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
  MODIFY `isbn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `gcash_users2`
--
ALTER TABLE `gcash_users2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
