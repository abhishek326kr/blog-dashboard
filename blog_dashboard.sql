-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2025 at 02:20 PM
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
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(5, 'Firky', 'admin@example.com', '$2y$10$msfFEW8v..zB/PvKlJTDjO8rlIFf0c3525TXR.Ux6wiZqyo8CHAEW', '2025-02-19 12:05:07');

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
(20, 'Danny Ramirez Flies High as the Falcon in \'Captain America: Brave New World\'', 'published', 1, '2025-02-21 10:18:16', '<p>The actor suits up as&nbsp;<a href=\"https://www.marvel.com/characters/joaquin-torres\" aria-label=\"Learn more about Joaquin Torres\">Joaquin Torres</a>, aka the Falcon, in Marvel Studios&rsquo; upcoming film&nbsp;<em><a href=\"https://www.marvel.com/movies/captain-america-brave-new-world\" aria-label=\"Learn more about Captain America: Brave New World\">Captain America: Brave New World</a>.&nbsp;</em>Ramirez, 32, is no stranger to the Marvel Cinematic Universe, having made his debut in the 2021 Disney+ series&nbsp;<em><a href=\"https://www.marvel.com/tv-shows/the-falcon-and-the-winter-soldier/1\" aria-label=\"Learn more about The Falcon and The Winter Soldier\">The Falcon and The Winter Soldier</a></em>. But with&nbsp;<em>Brave New World,&nbsp;</em>he&rsquo;s reaching new heights &mdash;&nbsp;literally. With&nbsp;<strong>Anthony Mackie&rsquo;s&nbsp;</strong><a href=\"https://www.marvel.com/characters/sam-wilson\" aria-label=\"Learn more about Sam Wilson\">Sam Wilson</a>&nbsp;taking up Captain America&rsquo;s shield, Joaquin follows in his footsteps as the new incarnation of the Falcon.</p>\r\n<p>Speaking to Marvel.com ahead of the film&rsquo;s premiere, Ramirez said he&rsquo;s been overwhelmed by the positive fan reaction to Joaquin so far.</p>\r\n<p>&ldquo;The part that&rsquo;s been really powerful has been the response I&rsquo;ve seen from kids that reminded me of myself,&rdquo; Ramirez tells Marvel.com. &ldquo;When I was their age, I didn&rsquo;t have anyone in the space to look up to in the same way. I was kind of saddened by that. For me, it was athletes and musicians mainly because that&rsquo;s who I could see myself as. But it&rsquo;s been the most amazing feeling seeing people get uplifted and their hope in seeing themselves on screen.&rdquo;</p>\r\n<p>He notes that even though the movie has yet to hit theaters, he&rsquo;s already seen scores of fans in flawless Falcon cosplay &mdash; wings and all. &ldquo;That&rsquo;s been beautiful to experience,&rdquo; he says with a smile.</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>', 'Firky', 'img_67b852e8847382.47792018.jpg'),
(21, 'Meet the Leader, the Hulk\'s Calculating Adversary', 'published', 0, '2025-02-21 10:19:19', '<p>The&nbsp;<a href=\"https://www.marvel.com/characters/hulk-bruce-banner\" aria-label=\"Learn more about Hulk\">Hulk</a>&nbsp;was born in an explosion of gamma radiation that ripped through Bruce Banner and transformed the scientist into a rampaging giant. However,&nbsp;<a href=\"https://www.marvel.com/characters/leader-samuel-sterns\" aria-label=\"Learn more about Samuel Sterns\">Samuel Sterns</a>&nbsp;experienced a different transformation when exposed to gamma radiation.<br><br>Instead of altering his body, the gamma radiation mutated the janitor\'s brain, granting him superhuman intelligence and a massive head. With his new abilities, Sterns became the Leader, one of the Hulk\'s most enduring and calculating foes.<br><br>Through his endless plans, the Leader destroyed towns and outsmarted death numerous times. While the public might call the Hulk a monster, the Leader and his lethal cruelty have shown the Marvel Universe what a true monster looks like.<br><br>Let&rsquo;s look at the Leader\'s exploits and his journey to earning a place as one of the Hulk\'s deadliest enemies. From their first encounters to battles that took them to the edge of existence, the Leader proved there are some threats that the Hulk can\'t smash his way through, despite his immense power.&nbsp;&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p><img src=\"../uploads/img_67b8531a8689d8.06697001.jpeg\" alt=\"\" width=\"480\" height=\"320\"></p>', 'Firky', ''),
(24, 'The Unstoppable Journey of Firky Kumar', 'published', 0, '2025-02-21 13:08:22', '<p data-pm-slice=\"1 1 []\"><em>Chapter 1: The Birth of a Legend</em></p>\r\n<p>On a crisp November 1st, the world unknowingly welcomed a force of nature&mdash;Firky Kumar. Born in the heart of Patna, he was destined for a life that refused to be ordinary. Even as a child, his curiosity was unmatched. While other kids played cricket in the streets, Firky was busy dismantling radios just to see how they worked. This was the first sign that technology would become his playground.</p>\r\n<p><em>Chapter 2: The Code Awakening</em></p>\r\n<p>Growing up, Firky&rsquo;s love for computers flourished. He spent hours experimenting with HTML, CSS, and eventually, PHP. By the time he reached college, he was already a mini-tycoon, earning side bucks fixing websites for local businesses. This knack for web development led him to YoForex, where he carved a niche as a Senior Web Developer, tackling server issues and making websites dance to his commands.</p>\r\n<p><em>Chapter 3: The Adventures Beyond Code</em></p>\r\n<p>Firky&rsquo;s life wasn&rsquo;t just about coding. His wanderlust took him across India, from the rustic beauty of Purulia to the enchanting landscapes of Ghatshila. Each trip was an adventure, filled with unexpected twists&mdash;like getting lost in a jungle, almost missing a train, or discovering a hidden waterfall. These moments fueled his soul, making him realize that life was meant to be lived beyond the screen.</p>\r\n<p><em>Chapter 5: Marvelse and Memes</em></p>\r\n<p>A die-hard Marvel fan, Firky wasn&rsquo;t content just watching the movies&mdash;he wanted to be part of the fandom. Thus, &lsquo;Marvelse&rsquo; was born, an Instagram page dedicated to Marvel memes and news. Running a meme empire was no easy feat, but he embraced the challenge, making his mark in the digital world.</p>\r\n<p><em>Chapter 6: The Loyal Companion&mdash;Pari</em></p>\r\n<p>Enter Pari, Firky&rsquo;s lazy yet lovable dog. If coding was his first love, Pari was his forever companion. She knew when he needed company, when he needed space, and when he needed an excuse to take a break from his screen. In many ways, she was his anchor in a chaotic world.</p>\r\n<p><em>Chapter 7: The Future Beckons</em></p>\r\n<p>With JavaScript mastery on his mind, DevOps on his checklist, and a ukulele waiting to be played, Firky&rsquo;s journey is far from over. His startup, Zironyx, stands as a testament to his ambitions. Whether it&rsquo;s building an empire, automating life, or embarking on another wild adventure, one thing is certain&mdash;Firky Kumar is unstoppable.</p>\r\n<p><em>To be continued...</em></p>', 'Firky Marvel', '');

-- --------------------------------------------------------

--
-- Table structure for table `blog_views`
--

CREATE TABLE `blog_views` (
  `id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_views`
--

INSERT INTO `blog_views` (`id`, `blog_id`, `ip_address`, `viewed_at`) VALUES
(1, 20, '::1', '2025-02-24 09:23:34');

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
(3, 24, '', '', '', '', '', 'index, follow', '', '', NULL, '2025-02-21 13:08:22', '2025-02-21 13:08:22');

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
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_views`
--
ALTER TABLE `blog_views`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blog_id` (`blog_id`,`ip_address`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `blog_views`
--
ALTER TABLE `blog_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
