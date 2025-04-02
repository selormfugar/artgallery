-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2025 at 10:52 AM
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
-- Database: `artmarketplace`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminactions`
--

CREATE TABLE `adminactions` (
  `action_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `target_user_id` int(11) DEFAULT NULL,
  `action_taken` varchar(255) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `artists`
--

CREATE TABLE `artists` (
  `artist_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `social_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_links`)),
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artists`
--

INSERT INTO `artists` (`artist_id`, `user_id`, `bio`, `profile_picture`, `social_links`, `archived`) VALUES
(1, 1, 'Abstract artist specializing in modern art.', 'profile1.jpg', '{\"instagram\": \"@artist1\"}', 0),
(2, 2, 'Realist painter focusing on landscapes.', 'profile2.jpg', '{\"twitter\": \"@artist2\"}', 0),
(3, 3, 'Digital artist working with surreal themes.', 'profile3.jpg', '{\"website\": \"artist3.com\"}', 0),
(4, 4, 'Minimalist sculptor creating small-scale pieces.', 'profile4.jpg', '{\"linkedin\": \"artist4\"}', 0),
(5, 5, 'Street artist blending graffiti and fine art.', 'profile5.jpg', '{\"instagram\": \"@artist5\"}', 0);

-- --------------------------------------------------------

--
-- Table structure for table `artworks`
--

CREATE TABLE `artworks` (
  `artwork_id` int(11) NOT NULL,
  `artist_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `moderation_status` varchar(255) NOT NULL DEFAULT 'pending',
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artworks`
--

INSERT INTO `artworks` (`artwork_id`, `artist_id`, `title`, `description`, `price`, `category`, `image_url`, `created_at`, `moderation_status`, `archived`) VALUES
(1, 1, 'Sunset Overdrive', 'A stunning abstract piece.', 500.00, 'Abstract', 'sunset.jpg', '2025-03-20 09:17:57', '\'pending\',\'commpleed\'', 0),
(2, 2, 'Mountain View', 'A realistic painting of a mountain.', 750.00, 'Landscape', 'mountain.jpg', '2025-03-20 09:17:57', '\'pending\',\'commpleed\'', 0),
(3, 3, 'Cyber Dream', 'A futuristic digital artwork.', 300.00, 'Digital', 'cyber.jpg', '2025-03-20 09:17:57', '\'pending\',\'commpleed\'', 0),
(4, 4, 'Marble Form', 'A minimalist sculpture in marble.', 1200.00, 'Sculpture', 'marble.jpg', '2025-03-20 09:17:57', '\'pending\',\'commpleed\'', 0),
(5, 5, 'City Lights', 'A graffiti-inspired street art piece.', 450.00, 'Street Art', 'city.jpg', '2025-03-20 09:17:57', '\'pending\',\'commpleed\'', 0),
(6, 2, 'Mona Lisa', 'By Leonardo DaVinci', 1000.00, 'Digital', '67dbfe40944b9.png', '2025-03-20 11:38:40', '\'pending\',\'commpleed\'', 0),
(7, 2, 'La Vance', 'Lance Vance', 1200.00, 'Abstract', '67e04100ee637.jpeg', '2025-03-23 17:12:32', '\'pending\',\'commpleed\'', 0);

-- --------------------------------------------------------

--
-- Table structure for table `artwork_views`
--

CREATE TABLE `artwork_views` (
  `view_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `artwork_id` int(11) NOT NULL,
  `viewed_at` datetime NOT NULL,
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`, `archived`) VALUES
(1, 'Abstract', 'Non-representational and expressive artworks', 0),
(2, 'Landscape', 'Depictions of natural scenes and environments', 0),
(3, 'Digital', 'Art created using digital tools and techniques', 0),
(4, 'Sculpture', 'Three-dimensional art forms', 0),
(5, 'Street Art', 'Urban-inspired graffiti and public art', 0);

-- --------------------------------------------------------

--
-- Table structure for table `content_flags`
--

CREATE TABLE `content_flags` (
  `flag_id` int(11) NOT NULL,
  `content_type` enum('artwork','comment','message') NOT NULL,
  `content_id` int(11) NOT NULL,
  `reporter_id` int(11) DEFAULT NULL,
  `reason` text NOT NULL,
  `severity` enum('low','medium','high') DEFAULT 'low',
  `status` enum('pending','resolved') DEFAULT 'pending',
  `resolution` text DEFAULT NULL,
  `archived` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resolved_at` datetime DEFAULT NULL,
  `resolved_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `seen` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `sender_id`, `receiver_id`, `content`, `seen`, `created_at`, `archived`) VALUES
(1, 3, 2, 'try again', 1, '2025-03-20 11:31:08', 0),
(2, 2, 4, 'thaan you for your service', 0, '2025-03-20 11:43:12', 0),
(3, 3, 2, 'try again', 1, '2025-03-20 11:31:08', 0),
(4, 4, 2, 'try again', 1, '2025-03-20 11:31:08', 0),
(5, 2, 4, 'hey', 0, '2025-03-20 11:51:21', 0),
(6, 2, 4, 'hi', 0, '2025-03-23 15:06:45', 0),
(7, 2, 3, 'hello', 0, '2025-03-23 15:06:55', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `seen` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `buyer_id` int(11) DEFAULT NULL,
  `artwork_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `buyer_id`, `artwork_id`, `total_price`, `payment_status`, `created_at`, `archived`) VALUES
(1, 3, 1, 500.00, 'completed', '2025-03-20 09:18:23', 0),
(2, 4, 2, 750.00, 'completed', '2025-03-20 09:18:23', 0),
(3, 3, 3, 300.00, 'pending', '2025-03-20 09:18:23', 0),
(4, 4, 4, 1200.00, 'failed', '2025-03-20 09:18:23', 0),
(5, 3, 5, 450.00, 'completed', '2025-03-20 09:18:23', 0);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `payment_gateway` enum('Stripe','PayPal','Flutterwave') DEFAULT NULL,
  `status` enum('pending','successful','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `artwork_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `email`, `password_hash`, `role`, `created_at`, `archived`) VALUES
(1, 'artist1', 'artist1', 'artist1@example.com', '$2y$10$eXvFkyVIf9JVPdGK.CNDe.Y4Mz3RFtcM.BsvPASZapm21IG9nJKXe', 'artist', '2025-03-20 09:17:05', 0),
(2, 'artist2', 'artist1', 'artist2@example.com', '$2y$10$eXvFkyVIf9JVPdGK.CNDe.Y4Mz3RFtcM.BsvPASZapm21IG9nJKXe', 'artist', '2025-03-20 09:17:05', 0),
(3, 'buyer1', 'artist1', 'buyer1@example.com', '$2y$10$eXvFkyVIf9JVPdGK.CNDe.Y4Mz3RFtcM.BsvPASZapm21IG9nJKXe', 'buyer', '2025-03-20 09:17:05', 0),
(4, 'buyer2', 'artist1', 'buyer2@example.com', '$2y$10$eXvFkyVIf9JVPdGK.CNDe.Y4Mz3RFtcM.BsvPASZapm21IG9nJKXe', 'buyer', '2025-03-20 09:17:05', 0),
(5, 'admin1', 'artist1', 'admin1@example.com', '$2y$10$eXvFkyVIf9JVPdGK.CNDe.Y4Mz3RFtcM.BsvPASZapm21IG9nJKXe', 'admin', '2025-03-20 09:17:05', 0),
(7, 'kwame', 'kwame', 'kwame@gmail.com', '$2y$10$/yslo.JecxuCcEEfcz5lqO3a4W2cBCZLWsONnGWnwac6sAsHKBd2G', 'buyer', '2025-03-23 13:58:45', 0),
(9, 'kwame', 'kwame', 'kwame11@gmail.com', '$2y$10$UHhCIO00EjOLJQzfHVb0zu2HTx1xvzmOJBu3ltABCZTohrLVE564.', 'artist', '2025-03-23 14:03:42', 0),
(11, 'kwame', 'kwamena', 'kwamena12@s.com', '$2y$10$C5ci44DEcUwPuzmE790sCuD8A0XcdD2jit9SS4K1Ur4DyBYjClthi', 'buyer', '2025-03-23 14:05:33', 0),
(12, 'qwe', 'qwe', 'q@gmail.com', '$2y$10$F3BtdXA.dfesNLW.zfyRZePlU01ku.2vtwEprK9xQtD2EDpaNpYda', 'buyer', '2025-03-23 14:10:00', 0),
(14, 'adwoa', 'mansa', 'amanas@gmail.com', '$2y$10$fPxYbDhxjege1z3rPfZov.AqVvnzx7WQUjO8SHk7NGSapjPRPT8he', 'buyer', '2025-03-23 14:16:21', 0),
(15, 'artist', '12', 'artist12@gmail.com', '$2y$10$eXvFkyVIf9JVPdGK.CNDe.Y4Mz3RFtcM.BsvPASZapm21IG9nJKXe', 'buyer', '2025-03-23 14:24:08', 0);

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `artwork_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`wishlist_id`, `user_id`, `artwork_id`, `created_at`, `archived`) VALUES
(1, 3, 1, '2025-03-20 09:18:37', 0),
(2, 4, 2, '2025-03-20 09:18:37', 0),
(3, 3, 3, '2025-03-20 09:18:37', 0),
(4, 4, 4, '2025-03-20 09:18:37', 0),
(5, 3, 5, '2025-03-20 09:18:37', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminactions`
--
ALTER TABLE `adminactions`
  ADD PRIMARY KEY (`action_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `target_user_id` (`target_user_id`);

--
-- Indexes for table `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`artist_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `artworks`
--
ALTER TABLE `artworks`
  ADD PRIMARY KEY (`artwork_id`),
  ADD KEY `artist_id` (`artist_id`);

--
-- Indexes for table `artwork_views`
--
ALTER TABLE `artwork_views`
  ADD PRIMARY KEY (`view_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `artwork_id` (`artwork_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `content_flags`
--
ALTER TABLE `content_flags`
  ADD PRIMARY KEY (`flag_id`),
  ADD KEY `reporter_id` (`reporter_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `artwork_id` (`artwork_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `artwork_id` (`artwork_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `artwork_id` (`artwork_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminactions`
--
ALTER TABLE `adminactions`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `artists`
--
ALTER TABLE `artists`
  MODIFY `artist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `artworks`
--
ALTER TABLE `artworks`
  MODIFY `artwork_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `artwork_views`
--
ALTER TABLE `artwork_views`
  MODIFY `view_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `content_flags`
--
ALTER TABLE `content_flags`
  MODIFY `flag_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adminactions`
--
ALTER TABLE `adminactions`
  ADD CONSTRAINT `adminactions_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `adminactions_ibfk_2` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `artists`
--
ALTER TABLE `artists`
  ADD CONSTRAINT `artists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `artworks`
--
ALTER TABLE `artworks`
  ADD CONSTRAINT `artworks_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`artist_id`);

--
-- Constraints for table `artwork_views`
--
ALTER TABLE `artwork_views`
  ADD CONSTRAINT `artwork_views_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `artwork_views_ibfk_2` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`artwork_id`);

--
-- Constraints for table `content_flags`
--
ALTER TABLE `content_flags`
  ADD CONSTRAINT `content_flags_ibfk_1` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`artwork_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`artwork_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `wishlists_ibfk_2` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`artwork_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
