-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2025 at 02:33 PM
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
-- Database: `blog_dashboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `username`, `phone`, `password`, `created_at`) VALUES
(5, 'Firky Kumar', 'admin@flexymarkets.com', 'admin', '07070465761', '$2y$10$msfFEW8v..zB/PvKlJTDjO8rlIFf0c3525TXR.Ux6wiZqyo8CHAEW', '2025-02-19 12:05:07');

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `status` enum('published','draft') NOT NULL,
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `content` text NOT NULL,
  `author` varchar(255) NOT NULL,
  `featured_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `status`, `views`, `created_at`, `content`, `author`, `featured_image`) VALUES
(20, 'Danny Ramirez Flies High as the Falcon in Captain America: Brave New World', 'published', 46, '2025-02-21 10:18:16', '<p>The actor suits up as&nbsp;<a href=\\\"\\\\&quot;https:/www.marvel.com/characters/joaquin-torres\\\\&quot;\\\" aria-label=\\\"\\\\&quot;Learn\\\">Joaquin Torres</a>, aka the Falcon, in Marvel Studios&rsquo; upcoming film&nbsp;<em><a href=\\\"\\\\&quot;https:/www.marvel.com/movies/captain-america-brave-new-world\\\\&quot;\\\" aria-label=\\\"\\\\&quot;Learn\\\">Captain America: Brave New World</a>.&nbsp;</em>Ramirez, 32, is no stranger to the Marvel Cinematic Universe, having made his debut in the 2021 Disney+ series&nbsp;<em><a href=\\\"\\\\&quot;https:/www.marvel.com/tv-shows/the-falcon-and-the-winter-soldier/1\\\\&quot;\\\" aria-label=\\\"\\\\&quot;Learn\\\">The Falcon and The Winter Soldier</a></em>. But with&nbsp;<em>Brave New World,&nbsp;</em>he&rsquo;s reaching new heights &mdash;&nbsp;literally. With&nbsp;<strong>Anthony Mackie&rsquo;s&nbsp;</strong><a href=\\\"\\\\&quot;https:/www.marvel.com/characters/sam-wilson\\\\&quot;\\\" aria-label=\\\"\\\\&quot;Learn\\\">Sam Wilson</a> taking up Captain America&rsquo;s shield, Joaquin follows in his footsteps as the new incarnation of the Falcon.</p>\\r\\n<p>Speaking to Marvel.com ahead of the film&rsquo;s premiere, Ramirez said he&rsquo;s been overwhelmed by the positive fan reaction to Joaquin so far.</p>\\r\\n<p>&ldquo;The part that&rsquo;s been really powerful has been the response I&rsquo;ve seen from kids that reminded me of myself,&rdquo; Ramirez tells Marvel.com. &ldquo;When I was their age, I didn&rsquo;t have anyone in the space to look up to in the same way. I was kind of saddened by that. For me, it was athletes and musicians mainly because that&rsquo;s who I could see myself as. But it&rsquo;s been the most amazing feeling seeing people get uplifted and their hope in seeing themselves on screen.&rdquo;</p>\\r\\n<p>&nbsp;</p>\\r\\n<p>He notes that even though the movie has yet to hit theaters, he&rsquo;s already seen scores of fans in flawless Falcon cosplay &mdash; wings and all. &ldquo;That&rsquo;s been beautiful to experience,&rdquo; he says with a smile.</p>\\r\\n<p>&nbsp;</p>', 'Firky', 'img_67b852e8847382.47792018.jpg'),
(46, 'Flexy Markets vs. Go Markets Which Broker Suits Trading', 'published', 2, '2025-02-26 12:23:44', '<p data-pm-slice=\"1 1 []\">When it comes to forex trading, choosing the right broker can make all the difference.&nbsp;<a href=\"https://flexymarkets.com/\" target=\"_blank\" rel=\"nofollow noopener\"><strong>Flexy Markets vs. Go Markets</strong></a>&nbsp;is a hot topic among traders who want a reliable and feature-rich trading experience. Both brokers have a strong reputation in the industry, but how do they compare? In this in-depth comparison, we&rsquo;ll analyze&nbsp;<strong>Flexy Markets vs. Go Markets</strong>&nbsp;based on trading conditions, features, regulations, and overall user experience to help you make an informed decision.</p>\r\n<h2>Overview of Flexy Markets vs. Go Markets</h2>\r\n<p><strong>Flexy Markets</strong></p>\r\n<p>Flexy Markets is a cutting-edge forex broker that aims to provide traders with seamless execution, advanced trading tools, and a wide range of assets. Operating from the UAE, Flexy Markets is rapidly gaining recognition as a go-to platform for traders who demand low spreads, high leverage, and transparency.</p>\r\n<p><strong>Go Markets</strong></p>\r\n<p>Go Markets, an Australian-based forex broker, has been in operation since 2006. It is regulated by ASIC (Australian Securities and Investments Commission) and is well known for its strong compliance, MetaTrader 4 (MT4) and MetaTrader 5 (MT5) platforms, and educational resources.</p>\r\n<h3>Trading Platforms: Flexy Markets vs. Go Markets</h3>\r\n<p><strong>Flexy Markets</strong></p>\r\n<ul data-spread=\"false\">\r\n<li>Offers a user-friendly web-based trading platform.</li>\r\n<li>Provides MT5 for professional traders.</li>\r\n<li>Seamless mobile trading experience.</li>\r\n<li>Advanced charting tools and real-time market analysis.</li>\r\n</ul>\r\n<p><strong>Go Markets</strong></p>\r\n<ul data-spread=\"false\">\r\n<li>Offers MT4 and MT5 platforms.</li>\r\n<li>Access to Go Markets WebTrader.</li>\r\n<li>Mobile trading with dedicated apps.</li>\r\n<li>Comprehensive analytical tools for technical trading.</li>\r\n</ul>\r\n<p>Both brokers provide excellent trading platforms, but Flexy Markets&rsquo; focus on modern web-based trading with a streamlined experience gives it an edge for beginners and professionals alike.</p>\r\n<h3>Trading Instruments: Flexy Markets vs. Go Markets</h3>\r\n<p><strong>Flexy Markets</strong></p>\r\n<ul data-spread=\"false\">\r\n<li>Forex pairs (major, minor, and exotic)</li>\r\n<li>Stocks and indices</li>\r\n<li>Commodities (gold, silver, oil)</li>\r\n<li>Cryptocurrencies</li>\r\n</ul>\r\n<p><strong>Go Markets</strong></p>\r\n<ul data-spread=\"false\">\r\n<li>Forex trading pairs</li>\r\n<li>Indices and commodities</li>\r\n<li>Shares and cryptocurrencies</li>\r\n<li>ETFs and bonds</li>\r\n</ul>\r\n<p>While both brokers offer diverse trading instruments, Flexy Markets provides more competitive trading conditions for crypto traders and those looking for a broader range of assets.</p>\r\n<h3>Spreads and Commissions: Flexy Markets vs. Go Markets</h3>\r\n<p><strong>Flexy Markets</strong></p>\r\n<ul data-spread=\"false\">\r\n<li>Ultra-low spreads starting from 0.0 pips.</li>\r\n<li>No commission on standard accounts.</li>\r\n<li>ECN accounts available for tight spreads.</li>\r\n<li>Transparent pricing with no hidden fees.</li>\r\n</ul>\r\n<p><strong>Go Markets</strong></p>\r\n<ul data-spread=\"false\">\r\n<li>Spreads start from 0.0 pips on the Go Plus+ account.</li>\r\n<li>Commission-based pricing for professional traders.</li>\r\n<li>Standard accounts with higher spreads but no commissions.</li>\r\n<li>Competitive swap rates for overnight positions.</li>\r\n</ul>\r\n<p>Both brokers offer low spreads, but Flexy Markets stands out by providing commission-free trading on standard accounts while maintaining tight spreads.</p>', 'Firky Marvel', 'img_67bf07d01a1327.44658032.png');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seo_meta`
--

CREATE TABLE `seo_meta` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `seo_keywords` text DEFAULT NULL,
  `seo_slug` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `meta_robots` enum('index, follow','noindex, follow','index, nofollow','noindex, nofollow') DEFAULT 'index, follow',
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seo_meta`
--

INSERT INTO `seo_meta` (`id`, `post_id`, `seo_title`, `seo_description`, `seo_keywords`, `seo_slug`, `canonical_url`, `meta_robots`, `og_title`, `og_description`, `og_image`, `created_at`, `updated_at`) VALUES
(3, 20, 'This is Blog', 'This is Blog', 'This is Blog', 'This is Blog', 'This is Blog', '', 'This is Blog', 'This is Blog', NULL, '2025-02-26 10:27:29', '2025-02-26 10:27:29'),
(4, 46, 'This is test Blog', 'Flexy Markets', 'Flexy Markets', 'Flexy Markets', 'Flexy Markets', '', 'This is test Blog', 'Flexy Markets', NULL, '2025-02-26 12:23:44', '2025-02-26 12:23:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','editor','viewer') NOT NULL DEFAULT 'viewer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `seo_meta`
--
ALTER TABLE `seo_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seo_meta`
--
ALTER TABLE `seo_meta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seo_meta`
--
ALTER TABLE `seo_meta`
  ADD CONSTRAINT `seo_meta_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
