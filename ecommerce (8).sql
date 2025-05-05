-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2025 at 07:01 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `category_name`) VALUES
(39, NULL, 'Books'),
(40, NULL, 'Bookmarks'),
(41, NULL, 'Book Journals'),
(43, NULL, 'Book Nook'),
(44, 41, 'Thirty books'),
(46, 40, 'Metal BookMarks'),
(47, 40, 'Painted BookMarks'),
(48, 40, 'Rubber Bookmarks'),
(49, 39, 'Fiction'),
(50, 39, 'Non Fiction'),
(51, 49, 'Fantasy'),
(53, 49, 'Romance'),
(54, 49, 'SciFi'),
(55, 50, 'Biography and Memoires'),
(56, 50, 'Course Books'),
(57, 41, 'Sixty Books'),
(59, NULL, 'Character Cards'),
(61, NULL, 'Test');

-- --------------------------------------------------------

--
-- Table structure for table `discount_coupons`
--

CREATE TABLE `discount_coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('fixed','percentage') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT NULL,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `valid_from` datetime DEFAULT NULL,
  `valid_until` datetime DEFAULT NULL,
  `status` enum('active','expired','used','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discount_coupons`
--

INSERT INTO `discount_coupons` (`id`, `code`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount`, `usage_limit`, `used_count`, `valid_from`, `valid_until`, `status`, `created_at`, `updated_at`) VALUES
(1, 'HELLO15', 'percentage', 10.00, 1500.00, 200.00, 100, 5, '2025-04-21 14:03:00', '2025-06-21 14:03:00', 'active', '2025-04-21 08:18:50', '2025-05-02 09:30:31'),
(2, 'LAB', 'percentage', 10.00, 1000.00, 300.00, 50, 12, '2025-04-24 20:17:00', '2025-05-24 20:17:00', 'active', '2025-04-24 14:32:51', '2025-04-27 08:09:31'),
(3, 'HELLO20', 'percentage', 20.00, 1500.00, 250.00, 10, 2, '2025-04-25 22:54:00', '2025-04-30 22:54:00', 'active', '2025-04-25 17:09:44', '2025-04-26 14:22:53'),
(4, 'WELCOME', 'fixed', 150.00, 1000.00, NULL, 10, 0, '2025-04-27 13:59:00', '2025-05-27 13:59:00', 'active', '2025-04-27 08:14:31', '2025-04-27 08:14:31');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `order_status` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `payment_method`, `shipping_address`, `order_status`, `created_at`, `transaction_id`) VALUES
(1, 1, 1800.00, 'COD', 'Baneshwor', 'Processing', '2025-04-21 14:18:07', NULL),
(2, 17, 2100.00, 'COD', 'Baneshwor', 'Shipped', '2025-04-21 21:27:18', NULL),
(12, 18, 900.00, 'Khalti', 'Baneshwor', 'Processing', '2025-04-25 21:33:19', NULL),
(21, 18, NULL, NULL, NULL, 'Processing', '2025-04-25 21:48:00', NULL),
(22, 18, 900.00, 'Khalti', 'Baneshwor', 'Pending', '2025-04-25 21:49:10', NULL),
(34, 1, 900.00, 'COD', 'Baneshwor', 'Processing', '2025-04-25 22:25:29', NULL),
(50, 1, 958.00, 'Khalti', 'Baneshwor', 'Pending', '2025-04-26 16:56:28', NULL),
(57, 1, 1800.00, 'Khalti', 'Baneshwor', 'Processing', '2025-05-02 15:15:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 3, 2, 900.00),
(2, 2, 2, 1, 1200.00),
(13, 12, 4, 1, 900.00),
(20, 21, 4, 1, 900.00),
(21, 22, 3, 1, 900.00),
(33, 34, 4, 1, 900.00),
(47, 50, 14, 0, 958.00),
(53, 57, 3, 2, 900.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `image_path`, `price`, `description`, `quantity`, `name`) VALUES
(1, 57, 'uploads/6805ffc1c1800.jpeg', 1200.00, 'Book joural for sixty books', 0, 'Book Journal Sixty'),
(2, 46, 'uploads/6805fff3d3a55.jpeg', 1200.00, 'Stainless steel bookmark', 9, 'Metal Fairy Bookmark'),
(3, 51, 'uploads/68060010450c5.jpeg', 900.00, 'Rebecca Ross', 1, 'A Fire Endless'),
(4, 51, 'uploads/6806002c3d00c.jpeg', 900.00, 'Rebecca Ross', 2, 'A River Enchanted'),
(5, 55, 'uploads/6806004bdeeb3.jpg', 758.00, 'Phil Knight', 13, 'shoe dog'),
(6, 53, 'uploads/680600665f913.jpeg', 958.00, 'Abby Jimenez', 1200, 'Just For The summer'),
(7, 53, 'uploads/6806008faaafb.jpeg', 958.00, 'Abby Jiminez', 12, 'Part of Your World'),
(8, 54, 'uploads/680600ae56c69.jpeg', 958.00, 'Neal Shusterman', 1, 'Scythe'),
(9, 54, 'uploads/680600cb4198c.jpeg', 3000.00, 'Neal Shusterman', 11, 'Scythe BoxSet'),
(10, 59, 'uploads/680600ff3aaa1.jpeg', 120.00, 'Kai and Pae', 11, 'Powerless Character Cards'),
(11, 55, 'uploads/6806011f3897d.jpeg', 1200.00, 'Jenette McCurdy', 19, 'I\'m Glad My Mom Died'),
(12, 53, 'uploads/6806014fce1f0.jpeg', 1300.00, 'Stephanie Garber', 10, 'Once Upon A Broken Heart'),
(13, 48, 'uploads/68060175d9e0c.jpeg', 300.00, 'Bookmarks that keeps track of pages as well as line.', 22, 'Rubber Bookmark'),
(14, 49, 'uploads/6806019e2fb37.jpeg', 958.00, 'Buddhisagar', 3, 'Eklo'),
(15, 59, 'uploads/680601c44111d.jpeg', 700.00, 'Character Cards', 15, 'Six of Crows'),
(17, 53, 'uploads/680bc1a1eddb8.jpeg', 758.00, 'Jenny Hann', 4, 'To All The Boys I Loved Before');

-- --------------------------------------------------------

--
-- Table structure for table `shopping_cart`
--

CREATE TABLE `shopping_cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shopping_cart`
--

INSERT INTO `shopping_cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(46, 17, 14, 2, '2025-04-27 13:56:49', '2025-04-28 20:20:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `firstname`, `lastname`, `phone`, `address`, `created_at`, `updated_at`, `role`) VALUES
(1, 'Admin', '$2y$10$M9QHtXJZjhL48C1SooCZB.zaQsXKCRhvlWt6qiJkKNDVH5XNW4knW', 'admin@gmail.com', 'Nutan', 'Gyawali', '9861815910', 'Khahare chowk,Pepsicola\r\nRURU-01 ,GULMI', '2024-12-21 13:01:58', '2025-05-02 14:51:33', 'admin'),
(2, 'BibekGyawali', '$2y$10$Pv37AI5nntEMJ3ffaJW89.Ao8rZY.xdiX4ECRALxvwJOKKhznZ5Am', 'bibekgyawali01@gmail.com', 'Bibek', 'Gyawali', '9860846688', 'Khahare chowk,Pepsicola\r\nRURU-01 ,GULMI', '2024-12-21 13:05:42', '2024-12-21 13:05:42', 'customer'),
(3, 'SandhyaGPanthi', '$2y$10$oF8rDswCEE7LoyKugCuUKOnL29XHOL.duK2Y/pefbLA7IrwULrNES', 'sandhya01@gmail.com', 'Sandhya', 'Gyawali', '9861239857', 'Khahare chowk,Pepsicola\r\nRURU-01 ,GULMI', '2024-12-21 13:10:13', '2024-12-21 13:10:13', 'customer'),
(6, 'Shatakshi Shiwakoti', '$2y$10$A00YQhgRWSByjGA.dXAIzuizD/e3EOskO.lp.DlXnxrkMaZ20WAyi', 'shatakshishiwakoti@gmail.com', 'Shatakshi', 'Shiwakoti', '9841767766', 'Mulpani, Kathmandu\r\n', '2024-12-22 13:06:17', '2024-12-22 13:06:17', 'customer'),
(10, 'Gauri', '$2y$10$rHNENyt.ciBH.igHcg.TmuE/N4OGadowjhMMnqaEJM2qCRYm99zs2', 'gaurabdahal@gmail.com', 'Gaurab', 'Dahal', '9840340590', 'Thimi, Bhaktapur', '2024-12-25 20:40:17', '2024-12-25 20:40:17', 'customer'),
(15, 'nutan', '$2y$10$Xqh2YAdZIIO26/PQldsDNOc2chXhr4yFAAP.8WRzEO6Dcd6EFgvwq', 'nutangyawali02@gmail.com', 'Nutan', 'Gyawali', '9860846688', 'Khahare chowk,Pepsicola\r\nRURU-01 ,GULMI', '2025-01-31 17:25:54', '2025-01-31 17:25:54', 'customer'),
(16, 'Bibek Gyawali', '$2y$10$dG79uRy8mKDLdT/ZwJEjguLjnPwRZcym28XlbRLEWUqG2hRDzwQhi', 'bibek01@gmail.com', 'Bibek', 'Gyawali', '9860846688', 'Khahare chowk,Pepsicola\r\nRURU-01 ,GULMI', '2025-02-04 20:29:14', '2025-02-04 20:29:14', 'customer'),
(17, 'anukc', '$2y$10$yc7o7emo8igUrftJzuGHR.JAplqN/gAvP9syjGufDPtfFOVaOkSEi', 'anukc@gmail.com', 'Anu', 'KC', '9861815910', 'Khahare chowk,Pepsicola\r\nRURU-01 ,GULMI', '2025-04-21 21:23:58', '2025-04-21 21:23:58', 'customer'),
(18, 'ShreyaRajlawat', '$2y$10$9s.jhgpGxrUUf6J1SK9aV.wAXpknk2q2.6nsJeSo7uxOsY207H776', 'shreyarajlawat@gmail.com', 'Shreya', 'Rajlawat', '9862766567', 'Khahare chowk,Pepsicola\r\nRURU-01 ,GULMI', '2025-04-25 21:32:41', '2025-04-25 21:32:41', 'customer');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(2, 17, 14, '2025-04-21 21:26:46'),
(3, 1, 13, '2025-04-22 16:30:41'),
(5, 1, 1, '2025-04-27 14:05:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `discount_coupons`
--
ALTER TABLE `discount_coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

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
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `discount_coupons`
--
ALTER TABLE `discount_coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD CONSTRAINT `shopping_cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shopping_cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
