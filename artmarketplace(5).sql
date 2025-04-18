-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2025 at 06:01 PM
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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_auction_statuses` ()   BEGIN
    -- Mark auctions that should be active
    UPDATE auctions 
    SET status = 'active'
    WHERE status = 'pending' 
    AND start_time <= NOW() 
    AND end_time > NOW();
    
    -- Mark expired auctions
    UPDATE auctions 
    SET status = 'ended'
    WHERE status IN ('pending', 'active') 
    AND end_time <= NOW();
END$$

DELIMITER ;

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

--
-- Dumping data for table `adminactions`
--

INSERT INTO `adminactions` (`action_id`, `admin_id`, `target_user_id`, `action_taken`, `reason`, `created_at`) VALUES
(1, 5, 5, 'approve_artwork', 'Artwork meets guidelines', '2025-04-10 19:32:09'),
(2, 5, 5, 'approve_artwork', 'Artwork meets guidelines', '2025-04-10 19:32:58');

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
-- Table structure for table `artist_subscription_settings`
--

CREATE TABLE `artist_subscription_settings` (
  `setting_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `tier_id` int(11) NOT NULL,
  `is_enabled` tinyint(1) DEFAULT 0,
  `custom_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archived` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artist_subscription_settings`
--

INSERT INTO `artist_subscription_settings` (`setting_id`, `artist_id`, `tier_id`, `is_enabled`, `custom_description`, `created_at`, `updated_at`, `archived`) VALUES
(1, 2, 2, 1, 'Subscribe to get 15% off all my artworks plus early access to new collections', '2025-04-04 12:19:07', '2025-04-04 12:22:04', 0),
(2, 2, 5, 1, 'Annual subscribers get exclusive behind-the-scenes content and 18% off all purchases', '2025-04-04 12:19:07', '2025-04-04 12:22:04', 0),
(3, 2, 8, 1, 'Become a lifetime patron and enjoy 25% off forever plus personalized thank you sketch', '2025-04-04 12:19:07', '2025-04-04 12:22:04', 0),
(4, 19, 8, 1, 'Become a lifetime patron and enjoy 25% off forever plus personalized thank you sketch', '2025-04-04 12:19:07', '2025-04-04 12:22:04', 0),
(5, 19, 5, 1, 'Annual subscribers get exclusive behind-the-scenes content and 18% off all purchases', '2025-04-04 12:19:07', '2025-04-04 12:22:04', 0),
(6, 19, 2, 1, 'Subscribe to get 15% off all my artworks plus early access to new collections', '2025-04-04 12:19:07', '2025-04-04 12:22:04', 0);

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `moderation_status` varchar(255) NOT NULL DEFAULT 'pending',
  `is_for_auction` tinyint(1) DEFAULT 0,
  `is_for_sale` tinyint(1) DEFAULT 1,
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artworks`
--

INSERT INTO `artworks` (`artwork_id`, `artist_id`, `title`, `description`, `price`, `category`, `image_url`, `created_at`, `updated_at`, `moderation_status`, `is_for_auction`, `is_for_sale`, `archived`) VALUES
(1, 1, 'Sunset Overdrive', 'A stunning abstract piece.', 500.00, 'Abstract', 'art6.jpg', '2025-03-20 09:17:57', '2025-03-29 11:16:22', 'completed', 0, 0, 0),
(2, 2, 'Mountain View', 'A realistic painting of a mountain.', 750.00, '', '67e7d6d6d7ae4_art21.jpg', '2025-03-20 09:17:57', '2025-04-07 15:14:01', 'completed', 1, 0, 0),
(3, 3, 'Cyber Dream', 'A futuristic digital artwork.', 300.00, 'Digital', 'cyber.jpg', '2025-03-20 09:17:57', '2025-03-29 11:16:22', 'completed', 0, 0, 0),
(4, 4, 'Marble Form', 'A minimalist sculpture in marble.', 1200.00, 'Sculpture', 'marble.jpg', '2025-03-20 09:17:57', '2025-03-29 11:16:22', 'completed', 0, 0, 0),
(5, 5, 'City Lights', 'A graffiti-inspired street art piece.', 450.00, 'Street Art', 'city.jpg', '2025-03-20 09:17:57', '2025-04-10 19:32:58', 'completed', 0, 0, 0),
(6, 2, 'Mona Lisa', 'By Leonardo DaVinci', 1000.00, '', '67dbfe40944b9.png', '2025-03-20 11:38:40', '2025-03-29 12:56:10', 'completed', 1, 1, 0),
(7, 2, 'La Vance', 'Lance Vance', 1200.00, 'Abstract', '67e04100ee637.jpeg', '2025-03-23 17:12:32', '2025-03-29 11:16:22', 'completed', 0, 0, 0),
(9, 1, 'something new', 'something i havent thought off', 500.00, 'Abstract', '67e56c57310e0.jpg', '2025-03-27 15:18:47', '2025-03-29 11:16:22', 'completed', 0, 0, 0),
(10, 2, 'new hang', 'renesance', 3500.00, '4', 'img_67e7cb0c9ab659.87107436.jpg', '2025-03-29 10:27:24', '2025-04-07 15:20:48', 'completed', 1, 0, 0),
(11, 2, 'something newer', 'try', 400.00, '3', 'img_67e833e9c7fc75.46697250.png', '2025-03-29 17:54:49', '2025-03-29 18:25:46', 'completed', 1, 0, 0),
(12, 2, 'something newer', '', 400.00, 'Abstract', 'img_67e839025715e9.90047290.png', '2025-03-29 18:16:34', '2025-03-29 18:16:34', 'completed', 0, 0, 0),
(13, 2, 'Devil May Cry', 'Dante', 500.00, 'Abstract', 'img_67f51cb4ec0209.81661931.jpeg', '2025-04-08 12:55:16', '2025-04-08 12:55:16', 'Completed', 0, 0, 0),
(14, 2, 'Devil May Cry 2', 'Devil May Cry 2', 1000.00, '1', 'img_67f51df9721571.01921571.jpeg', '2025-04-08 13:00:41', '2025-04-08 13:01:44', 'completed', 1, 0, 0);

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

--
-- Dumping data for table `artwork_views`
--

INSERT INTO `artwork_views` (`view_id`, `user_id`, `artwork_id`, `viewed_at`, `archived`) VALUES
(1, 18, 11, '2025-04-07 10:29:19', 0),
(2, 18, 11, '2025-04-07 10:39:50', 0),
(3, 18, 11, '2025-04-07 10:41:58', 0),
(4, 18, 11, '2025-04-07 10:42:34', 0),
(30, 18, 12, '2025-04-07 14:38:58', 0),
(31, 18, 12, '2025-04-07 14:45:36', 0),
(32, 18, 12, '2025-04-07 14:50:21', 0),
(33, 18, 11, '2025-04-07 15:38:16', 0),
(34, 18, 13, '2025-04-08 12:58:14', 0),
(35, 18, 13, '2025-04-08 13:02:17', 0),
(36, 21, 11, '2025-04-09 22:08:42', 0),
(37, 18, 5, '2025-04-11 17:30:00', 0),
(38, 18, 5, '2025-04-11 17:30:14', 0),
(39, 18, 5, '2025-04-11 17:30:18', 0),
(40, 18, 5, '2025-04-11 17:30:27', 0),
(41, 18, 5, '2025-04-11 17:30:52', 0),
(42, 18, 5, '2025-04-11 17:31:23', 0),
(43, 18, 5, '2025-04-11 17:32:17', 0),
(44, 18, 5, '2025-04-11 17:32:45', 0),
(45, 18, 5, '2025-04-11 17:34:16', 0),
(46, 18, 5, '2025-04-11 17:34:42', 0),
(47, 18, 5, '2025-04-11 17:35:48', 0),
(48, 18, 5, '2025-04-11 17:36:12', 0),
(49, 18, 5, '2025-04-11 17:36:20', 0),
(50, 18, 5, '2025-04-11 17:53:31', 0),
(51, 18, 5, '2025-04-11 17:54:12', 0),
(52, 18, 5, '2025-04-11 18:01:19', 0),
(53, 18, 5, '2025-04-11 18:01:50', 0),
(54, 18, 9, '2025-04-11 18:02:32', 0);

-- --------------------------------------------------------

--
-- Table structure for table `auctions`
--

CREATE TABLE `auctions` (
  `auction_id` int(11) NOT NULL,
  `artwork_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `starting_price` decimal(10,2) NOT NULL,
  `reserve_price` decimal(10,2) DEFAULT NULL,
  `current_bid` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','active','ended','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auctions`
--

INSERT INTO `auctions` (`auction_id`, `artwork_id`, `start_time`, `end_time`, `starting_price`, `reserve_price`, `current_bid`, `status`, `created_at`, `updated_at`, `archived`) VALUES
(1, 11, '2025-03-29 18:24:00', '2025-04-30 18:24:00', 300.00, 700.00, 4800.00, 'active', '2025-03-29 18:25:46', '2025-04-08 12:14:36', 0),
(2, 2, '2025-04-07 15:12:00', '2025-04-14 15:12:00', 300.00, 350.00, 350.00, 'active', '2025-04-07 15:14:01', '2025-04-08 12:31:15', 0),
(3, 10, '2025-04-07 15:20:00', '2025-04-14 15:20:00', 600.00, 800.00, 600.00, 'active', '2025-04-07 15:20:48', '2025-04-07 15:20:48', 0),
(4, 14, '2025-04-08 13:01:00', '2025-04-09 13:01:00', 1000.00, NULL, 1100.00, 'active', '2025-04-08 13:01:44', '2025-04-08 13:03:39', 0);

-- --------------------------------------------------------

--
-- Table structure for table `bids`
--

CREATE TABLE `bids` (
  `bid_id` int(11) NOT NULL,
  `auction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `bid_time` datetime NOT NULL DEFAULT current_timestamp(),
  `is_winning` tinyint(1) DEFAULT 0,
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bids`
--

INSERT INTO `bids` (`bid_id`, `auction_id`, `user_id`, `amount`, `bid_time`, `is_winning`, `archived`) VALUES
(1, 1, 18, 350.00, '2025-04-08 11:48:41', 0, 0),
(2, 1, 4, 400.00, '2025-04-08 11:48:41', 0, 0),
(3, 1, 18, 4600.00, '2025-04-08 11:48:41', 0, 0),
(4, 1, 18, 4700.00, '2025-04-08 12:14:04', 0, 0),
(5, 1, 18, 4800.00, '2025-04-08 12:14:36', 1, 0),
(6, 2, 18, 350.00, '2025-04-08 12:31:15', 1, 0),
(7, 4, 18, 1100.00, '2025-04-08 13:03:39', 1, 0);

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
-- Table structure for table `collection_folders`
--

CREATE TABLE `collection_folders` (
  `folder_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `folder_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `collection_folders`
--

INSERT INTO `collection_folders` (`folder_id`, `user_id`, `folder_name`, `description`, `is_public`, `created_at`, `updated_at`, `archived`) VALUES
(1, 3, 'Abstract Art', 'My favorite abstract pieces', 0, '2025-04-01 11:42:44', '2025-04-01 11:42:44', 0),
(2, 18, 'Abstract Art', 'My favorite abstract pieces', 0, '2025-04-01 11:42:44', '2025-04-01 11:42:44', 0),
(3, 18, 'Trial', 'Nice', 0, '2025-04-01 12:48:26', '2025-04-01 12:48:26', 0),
(4, 18, 'Trial', 'Nice', 0, '2025-04-01 12:48:39', '2025-04-01 12:48:39', 0),
(5, 18, 'second trial', 'trial', 0, '2025-04-01 12:49:04', '2025-04-01 12:49:04', 0);

-- --------------------------------------------------------

--
-- Table structure for table `collection_folder_items`
--

CREATE TABLE `collection_folder_items` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `collection_folder_items`
--

INSERT INTO `collection_folder_items` (`id`, `folder_id`, `collection_id`, `created_at`) VALUES
(1, 1, 1, '2025-04-01 11:42:44');

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
(7, 2, 3, 'hello', 1, '2025-03-23 15:06:55', 0),
(8, 2, 3, 'hello', 1, '2025-03-29 10:09:44', 0),
(9, 2, 18, 'hello', 1, '2025-03-29 10:09:44', 0),
(10, 2, 4, 'thank you', 0, '2025-04-11 16:28:46', 0),
(11, 3, 2, 'hii', 1, '2025-04-11 16:56:02', 0),
(12, 2, 3, 'whats up', 1, '2025-04-11 17:03:48', 0),
(13, 2, 3, 'how are you doing', 1, '2025-04-11 17:03:59', 0),
(14, 3, 2, 'good, yourslef?', 1, '2025-04-11 17:04:34', 0),
(15, 2, 3, 'not bad', 1, '2025-04-11 17:04:48', 0),
(16, 2, 18, 'hii', 1, '2025-04-11 17:22:32', 0),
(17, 18, 2, 'grateful', 1, '2025-04-11 17:25:21', 0),
(18, 2, 18, 'good', 1, '2025-04-11 17:25:39', 0);

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

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `seen`, `created_at`) VALUES
(1, 18, 'You purchased \'something newer\' for $392.00 (with your 2.00% subscription discount)', 0, '2025-04-07 11:20:46'),
(2, 18, 'You purchased \'something new\' for $500.00', 0, '2025-04-07 11:51:26'),
(3, 18, 'You purchased \'La Vance\' for $1,176.00 (with your 2.00% subscription discount)', 0, '2025-04-07 12:42:07'),
(4, 18, 'You purchased \'Sunset Overdrive\' for $500.00', 0, '2025-04-07 12:43:37'),
(5, 18, 'You purchased \'Cyber Dream\' for $300.00', 0, '2025-04-07 12:43:48'),
(6, 18, 'You purchased \'Mountain View\' for $735.00 (with your 2.00% subscription discount)', 0, '2025-04-07 12:57:22'),
(7, 18, 'You purchased \'City Lights\' for $450.00', 0, '2025-04-07 12:59:55'),
(8, 18, 'You purchased \'Marble Form\' for $1,200.00', 0, '2025-04-07 13:01:13'),
(9, 5, 'Your artwork \"City Lights\" has been approved and is now live.', 0, '2025-04-10 19:32:09'),
(10, 5, 'Your artwork \"City Lights\" has been approved and is now live.', 0, '2025-04-10 19:32:58'),
(11, 18, 'You purchased \'Devil May Cry\' for $490.00 (with your 2.00% subscription discount)', 0, '2025-04-14 11:59:19');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `buyer_id` int(11) DEFAULT NULL,
  `artwork_id` int(11) DEFAULT NULL,
  `is_auction_sale` tinyint(1) DEFAULT 0,
  `auction_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived` tinyint(1) DEFAULT 0,
  `subscription_discount` decimal(10,2) DEFAULT 0.00,
  `is_subscription_cancellation` tinyint(1) DEFAULT 0,
  `applied_subscription_id` int(11) DEFAULT NULL,
  `subscription_plan_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `buyer_id`, `artwork_id`, `is_auction_sale`, `auction_id`, `total_price`, `payment_status`, `created_at`, `archived`, `subscription_discount`, `is_subscription_cancellation`, `applied_subscription_id`, `subscription_plan_id`) VALUES
(1, 3, 1, 0, NULL, 500.00, 'completed', '2025-03-20 09:18:23', 0, 0.00, 0, NULL, NULL),
(2, 4, 2, 0, NULL, 750.00, 'completed', '2025-03-20 09:18:23', 0, 0.00, 0, NULL, NULL),
(3, 3, 3, 0, NULL, 300.00, 'pending', '2025-03-20 09:18:23', 0, 0.00, 0, NULL, NULL),
(4, 4, 4, 0, NULL, 1200.00, 'failed', '2025-03-20 09:18:23', 0, 0.00, 0, NULL, NULL),
(5, 3, 5, 0, NULL, 450.00, 'completed', '2025-03-20 09:18:23', 0, 0.00, 0, NULL, NULL),
(6, 18, 12, 0, NULL, 450.00, 'completed', '2025-04-01 09:18:23', 0, 0.00, 0, NULL, NULL),
(7, 18, 7, 0, NULL, 450.00, 'completed', '2025-04-01 09:18:23', 0, 0.00, 0, NULL, NULL),
(8, 18, 2, 0, NULL, 500.00, 'completed', '2025-04-01 09:18:23', 0, 0.00, 0, NULL, NULL),
(9, 18, 1, 0, NULL, 500.00, 'completed', '2025-04-01 09:18:23', 0, 0.00, 0, NULL, NULL),
(10, 18, 11, 0, NULL, 500.00, 'completed', '2025-04-01 09:18:23', 0, 0.00, 0, NULL, NULL),
(11, 18, NULL, 0, NULL, 0.00, 'pending', '2025-04-04 13:14:27', 0, 0.00, 0, NULL, 3),
(12, 18, NULL, 0, NULL, 0.00, 'pending', '2025-04-04 13:18:13', 0, 0.00, 0, NULL, 1),
(13, 18, NULL, 0, NULL, 0.00, 'pending', '2025-04-04 14:30:30', 0, 0.00, 0, NULL, 4),
(14, 18, 12, 0, NULL, 392.00, 'completed', '2025-04-07 11:20:46', 0, 2.00, 0, 2, 1),
(15, 18, 9, 0, NULL, 500.00, 'completed', '2025-04-07 11:51:26', 0, 0.00, 0, NULL, NULL),
(16, 18, 7, 0, NULL, 1176.00, 'completed', '2025-04-07 12:42:07', 0, 2.00, 0, 2, 1),
(17, 18, 1, 0, NULL, 500.00, 'completed', '2025-04-07 12:43:37', 0, 0.00, 0, NULL, NULL),
(18, 18, 3, 0, NULL, 300.00, 'completed', '2025-04-07 12:43:47', 0, 0.00, 0, NULL, NULL),
(19, 18, 2, 0, NULL, 735.00, 'completed', '2025-04-07 12:57:22', 0, 2.00, 0, 2, 1),
(20, 18, 5, 0, NULL, 450.00, 'completed', '2025-04-07 12:59:55', 0, 0.00, 0, NULL, NULL),
(21, 18, 4, 0, NULL, 1200.00, 'completed', '2025-04-07 13:01:13', 0, 0.00, 0, NULL, NULL),
(22, 18, 13, 0, NULL, 490.00, 'completed', '2025-04-14 11:59:19', 0, 2.00, 0, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'USD',
  `payment_gateway` enum('Stripe','PayPal','Flutterwave') DEFAULT NULL,
  `status` enum('pending','successful','failed') DEFAULT 'pending',
  `gateway_response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_items`
--

CREATE TABLE `portfolio_items` (
  `item_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
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
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `plan_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_type` enum('monthly','yearly','lifetime') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscription_plans`
--

INSERT INTO `subscription_plans` (`plan_id`, `artist_id`, `name`, `description`, `duration_type`, `price`, `discount_percentage`, `created_at`, `updated_at`, `archived`) VALUES
(1, 2, 'kwame', 'wet', 'monthly', 20.00, 2.00, '2025-04-04 12:13:59', '2025-04-04 12:31:59', 0);

-- --------------------------------------------------------

--
-- Table structure for table `subscription_tiers`
--

CREATE TABLE `subscription_tiers` (
  `tier_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_type` enum('monthly','yearly','lifetime') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscription_tiers`
--

INSERT INTO `subscription_tiers` (`tier_id`, `name`, `description`, `duration_type`, `price`, `discount_percentage`, `is_active`) VALUES
(1, 'Basic Monthly', 'Basic monthly subscription with exclusive discounts', 'monthly', 4.99, 10.00, 1),
(2, 'Premium Monthly', 'Premium monthly subscription with better discounts and early access', 'monthly', 9.99, 15.00, 1),
(3, 'VIP Monthly', 'VIP monthly subscription with maximum monthly benefits', 'monthly', 14.99, 20.00, 1),
(4, 'Basic Annual', 'Basic annual subscription - save compared to monthly', 'yearly', 49.99, 12.00, 1),
(5, 'Premium Annual', 'Premium annual subscription with enhanced benefits', 'yearly', 99.99, 18.00, 1),
(6, 'VIP Annual', 'VIP annual subscription with maximum yearly benefits', 'yearly', 149.99, 25.00, 1),
(7, 'Lifetime Supporter', 'One-time payment for lifetime basic discounts', 'lifetime', 199.99, 15.00, 1),
(8, 'Lifetime Patron', 'One-time payment for lifetime premium benefits', 'lifetime', 299.99, 25.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived` tinyint(1) DEFAULT 0,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `firstname`, `lastname`, `email`, `password_hash`, `role`, `created_at`, `archived`, `profile_image`) VALUES
(1, 'artist1a1', 'artist1', 'artist1', 'artist1@example.com', '$2y$10$eXvFkyVIf9JVPdGK.CNDe.Y4Mz3RFtcM.BsvPASZapm21IG9nJKXe', 'artist', '2025-03-20 09:17:05', 0, NULL),
(2, 'michaela2', 'Michael', 'Angelo', 'artist@gmail.com', '$2y$10$eXvFkyVIf9JVPdGK.CNDe.Y4Mz3RFtcM.BsvPASZapm21IG9nJKXe', 'artist', '2025-03-20 09:17:05', 0, NULL),
(3, 'buyer1a3', 'buyer1', 'artist1', 'buyer1@example.com', '$2y$10$eXvFkyVIf9JVPdGK.CNDe.Y4Mz3RFtcM.BsvPASZapm21IG9nJKXe', 'buyer', '2025-03-20 09:17:05', 0, NULL),
(4, 'buyer2a4', 'buyer2', 'artist1', 'buyer2@example.com', '$2y$10$eXvFkyVIf9JVPdGK.CNDe.Y4Mz3RFtcM.BsvPASZapm21IG9nJKXe', 'buyer', '2025-03-20 09:17:05', 0, NULL),
(5, 'joestarj5', 'Joestar', 'Jotaro', 'admin@gmail.com', '$2y$10$eXvFkyVIf9JVPdGK.CNDe.Y4Mz3RFtcM.BsvPASZapm21IG9nJKXe', 'admin', '2025-03-20 09:17:05', 0, NULL),
(7, 'kwamek7', 'kwame', 'kwame', 'kwame@gmail.com', '$2y$10$/yslo.JecxuCcEEfcz5lqO3a4W2cBCZLWsONnGWnwac6sAsHKBd2G', 'buyer', '2025-03-23 13:58:45', 0, NULL),
(9, 'kwamed9', 'kwame', 'dubois', 'kwame11@gmail.com', '$2y$10$itrU68GSgMpoQhjlVu1kHuW7G8KedZIN4EJa8qOMwc5bSN099Biq6', 'artist', '2025-03-23 14:03:42', 0, '/uploads/avatars/user_9_1744383237.jpg'),
(12, 'qweq12', 'qwe', 'qwe', 'q@gmail.com', '$2y$10$F3BtdXA.dfesNLW.zfyRZePlU01ku.2vtwEprK9xQtD2EDpaNpYda', 'buyer', '2025-03-23 14:10:00', 0, NULL),
(14, 'adwoam14', 'adwoa', 'mansa', 'amanas@gmail.com', '$2y$10$fPxYbDhxjege1z3rPfZov.AqVvnzx7WQUjO8SHk7NGSapjPRPT8he', 'buyer', '2025-03-23 14:16:21', 0, NULL),
(15, 'artist115', 'artist', '12', 'artist12@gmail.com', '$2y$10$eXvFkyVIf9JVPdGK.CNDe.Y4Mz3RFtcM.BsvPASZapm21IG9nJKXe', 'buyer', '2025-03-23 14:24:08', 0, NULL),
(17, 'ww17', 'w', 'w', 'admi@example.com', '$2y$10$BtgB7uKZnR9nOJYWEB/o9ugdUHkbpW6UO8bu8DpkFDC/TV3XB8fyy', 'buyer', '2025-03-27 13:33:45', 0, NULL),
(18, 'ament18', 'Amen', 'Thompson', 'buyer@gmail.com', '$2y$10$eXvFkyVIf9JVPdGK.CNDe.Y4Mz3RFtcM.BsvPASZapm21IG9nJKXe', 'buyer', '2025-03-27 13:41:27', 0, NULL),
(19, '2trial119', '2trial', '1', '1@gmail.com', '$2y$10$nT4za6iDbbavpmQ.F.qMw.bK9UhOCj2.qzoTkQwdAhx449yN1/.cy', 'artist', '2025-03-27 13:44:28', 0, NULL),
(20, 'kwamenal20', 'kwamena', 'Livrgogne', 'kwame@s.com', '$2y$10$jPP6hHQzjdCf.oLfAXWfc.HzBR/q0uU/B4G8vYoJAYKglb7xcxqW6', 'artist', '2025-04-09 21:49:20', 0, NULL),
(21, 'kwameb21', 'kwame', 'banga', 'banga@gmail.com', '$2y$10$iNFH6OsWaEz8uiqBIkaej./lqdeuXEZRvWnOCgnx/QrUV8nvodKK6', 'artist', '2025-04-09 21:53:30', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_collections`
--

CREATE TABLE `user_collections` (
  `collection_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `artwork_id` int(11) NOT NULL,
  `collection_name` varchar(255) DEFAULT 'My Collection',
  `is_public` tinyint(1) DEFAULT 0,
  `is_purchased` tinyint(1) DEFAULT 0,
  `purchase_date` datetime DEFAULT NULL,
  `purchase_order_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_collections`
--

INSERT INTO `user_collections` (`collection_id`, `user_id`, `artwork_id`, `collection_name`, `is_public`, `is_purchased`, `purchase_date`, `purchase_order_id`, `notes`, `created_at`, `updated_at`, `archived`) VALUES
(1, 3, 1, 'Favorites', 0, 1, NULL, 1, NULL, '2025-04-01 11:42:44', '2025-04-01 11:42:44', 0),
(2, 18, 12, 'My Collection', 0, 1, '2025-04-07 11:20:46', 14, NULL, '2025-04-07 11:20:46', '2025-04-07 11:20:46', 0),
(3, 18, 9, 'My Collection', 0, 1, '2025-04-07 11:51:26', 15, NULL, '2025-04-07 11:51:26', '2025-04-07 11:51:26', 0),
(4, 18, 7, 'My Collection', 0, 1, '2025-04-07 12:42:07', 16, NULL, '2025-04-07 12:42:07', '2025-04-07 12:42:07', 0),
(5, 18, 1, 'My Collection', 0, 1, '2025-04-07 12:43:37', 17, NULL, '2025-04-07 12:43:37', '2025-04-07 12:43:37', 0),
(6, 18, 3, 'My Collection', 0, 1, '2025-04-07 12:43:48', 18, NULL, '2025-04-07 12:43:48', '2025-04-07 12:43:48', 0),
(7, 18, 2, 'My Collection', 0, 1, '2025-04-07 12:57:22', 19, NULL, '2025-04-07 12:57:22', '2025-04-07 12:57:22', 0),
(8, 18, 5, 'My Collection', 0, 1, '2025-04-07 12:59:55', 20, NULL, '2025-04-07 12:59:55', '2025-04-07 12:59:55', 0),
(9, 18, 4, 'My Collection', 0, 1, '2025-04-07 13:01:13', 21, NULL, '2025-04-07 13:01:13', '2025-04-07 13:01:13', 0),
(10, 18, 13, 'My Collection', 0, 1, '2025-04-14 11:59:19', 22, NULL, '2025-04-14 11:59:19', '2025-04-14 11:59:19', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_subscriptions`
--

CREATE TABLE `user_subscriptions` (
  `subscription_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('active','expired','cancelled') DEFAULT 'active',
  `auto_renew` tinyint(1) DEFAULT 0,
  `payment_reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_subscriptions`
--

INSERT INTO `user_subscriptions` (`subscription_id`, `user_id`, `plan_id`, `start_date`, `end_date`, `status`, `auto_renew`, `payment_reference`, `created_at`, `updated_at`) VALUES
(2, 18, 1, '2025-04-04 13:18:13', '2025-05-04 13:18:13', 'active', 1, NULL, '2025-04-04 13:18:13', '2025-04-04 13:18:13'),
(3, 18, 4, '2025-04-04 14:30:30', '2125-04-04 14:30:30', 'active', 0, NULL, '2025-04-04 14:30:30', '2025-04-04 14:30:30');

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
(5, 3, 5, '2025-03-20 09:18:37', 0),
(6, 18, 7, '2025-04-01 10:00:30', 0),
(7, 18, 10, '2025-04-01 10:00:30', 0),
(11, 18, 12, '2025-04-07 11:05:21', 0),
(12, 21, 11, '2025-04-09 22:08:55', 0);

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
-- Indexes for table `artist_subscription_settings`
--
ALTER TABLE `artist_subscription_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `artist_tier_unique` (`artist_id`,`tier_id`),
  ADD KEY `artist_id` (`artist_id`),
  ADD KEY `tier_id` (`tier_id`);

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
-- Indexes for table `auctions`
--
ALTER TABLE `auctions`
  ADD PRIMARY KEY (`auction_id`),
  ADD KEY `artwork_id` (`artwork_id`),
  ADD KEY `idx_artwork` (`artwork_id`),
  ADD KEY `idx_status_time` (`status`,`end_time`);

--
-- Indexes for table `bids`
--
ALTER TABLE `bids`
  ADD PRIMARY KEY (`bid_id`),
  ADD KEY `auction_id` (`auction_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_auction` (`auction_id`),
  ADD KEY `idx_user_auction` (`user_id`,`auction_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `collection_folders`
--
ALTER TABLE `collection_folders`
  ADD PRIMARY KEY (`folder_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `collection_folder_items`
--
ALTER TABLE `collection_folder_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_folder_item` (`folder_id`,`collection_id`),
  ADD KEY `collection_id` (`collection_id`);

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
  ADD KEY `artwork_id` (`artwork_id`),
  ADD KEY `orders_ibfk_3` (`auction_id`),
  ADD KEY `orders_ibfk_4` (`applied_subscription_id`),
  ADD KEY `fk_orders_subscription_plan` (`subscription_plan_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `portfolio_items`
--
ALTER TABLE `portfolio_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `artist_id` (`artist_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `artwork_id` (`artwork_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`plan_id`),
  ADD KEY `artist_id` (`artist_id`);

--
-- Indexes for table `subscription_tiers`
--
ALTER TABLE `subscription_tiers`
  ADD PRIMARY KEY (`tier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_collections`
--
ALTER TABLE `user_collections`
  ADD PRIMARY KEY (`collection_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `artwork_id` (`artwork_id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`);

--
-- Indexes for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `plan_id` (`plan_id`);

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
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `artists`
--
ALTER TABLE `artists`
  MODIFY `artist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `artist_subscription_settings`
--
ALTER TABLE `artist_subscription_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `artworks`
--
ALTER TABLE `artworks`
  MODIFY `artwork_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `artwork_views`
--
ALTER TABLE `artwork_views`
  MODIFY `view_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `auctions`
--
ALTER TABLE `auctions`
  MODIFY `auction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bids`
--
ALTER TABLE `bids`
  MODIFY `bid_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `collection_folders`
--
ALTER TABLE `collection_folders`
  MODIFY `folder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `collection_folder_items`
--
ALTER TABLE `collection_folder_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `content_flags`
--
ALTER TABLE `content_flags`
  MODIFY `flag_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `portfolio_items`
--
ALTER TABLE `portfolio_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subscription_tiers`
--
ALTER TABLE `subscription_tiers`
  MODIFY `tier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user_collections`
--
ALTER TABLE `user_collections`
  MODIFY `collection_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  MODIFY `subscription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
-- Constraints for table `artist_subscription_settings`
--
ALTER TABLE `artist_subscription_settings`
  ADD CONSTRAINT `artist_subscription_settings_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `artist_subscription_settings_ibfk_2` FOREIGN KEY (`tier_id`) REFERENCES `subscription_tiers` (`tier_id`);

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
-- Constraints for table `auctions`
--
ALTER TABLE `auctions`
  ADD CONSTRAINT `auctions_ibfk_1` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`artwork_id`);

--
-- Constraints for table `bids`
--
ALTER TABLE `bids`
  ADD CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`auction_id`),
  ADD CONSTRAINT `bids_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `collection_folders`
--
ALTER TABLE `collection_folders`
  ADD CONSTRAINT `collection_folders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `collection_folder_items`
--
ALTER TABLE `collection_folder_items`
  ADD CONSTRAINT `collection_folder_items_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `collection_folders` (`folder_id`),
  ADD CONSTRAINT `collection_folder_items_ibfk_2` FOREIGN KEY (`collection_id`) REFERENCES `user_collections` (`collection_id`);

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
  ADD CONSTRAINT `fk_orders_subscription_plan` FOREIGN KEY (`subscription_plan_id`) REFERENCES `artist_subscription_settings` (`setting_id`),
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`artwork_id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`auction_id`),
  ADD CONSTRAINT `orders_ibfk_4` FOREIGN KEY (`applied_subscription_id`) REFERENCES `user_subscriptions` (`subscription_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `portfolio_items`
--
ALTER TABLE `portfolio_items`
  ADD CONSTRAINT `portfolio_items_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`artist_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`artwork_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD CONSTRAINT `subscription_plans_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_collections`
--
ALTER TABLE `user_collections`
  ADD CONSTRAINT `user_collections_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_collections_ibfk_2` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`artwork_id`),
  ADD CONSTRAINT `user_collections_ibfk_3` FOREIGN KEY (`purchase_order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD CONSTRAINT `user_subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_subscriptions_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `artist_subscription_settings` (`setting_id`);

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
