-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2025 at 04:11 PM
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
-- Database: `barker`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `pid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `doc` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `pid`, `uid`, `comment`, `doc`) VALUES
(12, 11, 5, 'GAGA KA!!!!!!!!!!', '2025-03-06 12:24:37'),
(13, 11, 6, 'HAHAHAHAHAHAHHA', '2025-03-06 12:25:19'),
(14, 9, 6, 'epal', '2025-03-06 12:25:36'),
(16, 31, 2, 'repost comment test', '2025-03-06 16:42:06'),
(18, 37, 8, 'hello', '2025-03-07 14:30:58'),
(19, 37, 8, 'under 10 ka ba?', '2025-03-07 14:31:08'),
(20, 37, 8, 'tera soc', '2025-03-07 14:31:13'),
(21, 42, 3, 'wtf', '2025-03-07 14:32:44'),
(22, 31, 8, 'anong test? testtest?', '2025-03-07 14:47:14'),
(23, 4, 8, 'anong test? testtest?', '2025-03-07 15:03:27');

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `follower_id` int(11) NOT NULL,
  `followed_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `followers`
--

INSERT INTO `followers` (`follower_id`, `followed_id`) VALUES
(2, 3),
(2, 4),
(2, 8),
(3, 4),
(8, 2),
(8, 3),
(8, 4);

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `pid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `pid`, `uid`) VALUES
(5, 10, 3),
(6, 9, 3),
(7, 8, 3),
(8, 5, 3),
(9, 4, 3),
(10, 10, 5),
(11, 11, 6),
(17, 32, 2),
(20, 41, 8),
(21, 37, 8),
(22, 8, 8),
(28, 40, 8),
(32, 14, 8),
(33, 13, 8),
(34, 12, 8),
(35, 5, 8),
(36, 4, 8);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `from_user_id`, `post_id`, `message`, `is_read`, `created_at`) VALUES
(10, 2, 'follow', 8, NULL, 'started following you.', 0, '2025-03-07 15:02:52'),
(11, 2, 'like', 8, 40, 'liked your post.', 0, '2025-03-07 15:02:54'),
(12, 2, 'like', 8, 40, 'liked your post.', 0, '2025-03-07 15:02:55'),
(13, 2, 'like', 8, 40, 'liked your post.', 0, '2025-03-07 15:02:56'),
(14, 2, 'like', 8, 40, 'liked your post.', 0, '2025-03-07 15:02:57'),
(15, 2, 'like', 8, 32, 'liked your post.', 0, '2025-03-07 15:02:59'),
(16, 2, 'like', 8, 32, 'liked your post.', 0, '2025-03-07 15:03:00'),
(17, 2, 'like', 8, 31, 'liked your post.', 0, '2025-03-07 15:03:02'),
(18, 2, 'like', 8, 31, 'liked your post.', 0, '2025-03-07 15:03:03'),
(19, 2, 'like', 8, 30, 'liked your post.', 0, '2025-03-07 15:03:04'),
(20, 2, 'like', 8, 30, 'liked your post.', 0, '2025-03-07 15:03:05'),
(21, 2, 'like', 8, 14, 'liked your post.', 0, '2025-03-07 15:03:08'),
(22, 2, 'like', 8, 13, 'liked your post.', 0, '2025-03-07 15:03:10'),
(23, 2, 'like', 8, 12, 'liked your post.', 0, '2025-03-07 15:03:11'),
(24, 2, 'like', 8, 5, 'liked your post.', 0, '2025-03-07 15:03:12'),
(25, 2, 'like', 8, 4, 'liked your post.', 0, '2025-03-07 15:03:14'),
(26, 2, 'comment', 8, 4, 'commented on your post.', 0, '2025-03-07 15:03:27'),
(27, 2, 'repost', 8, 4, 'reposted your post.', 0, '2025-03-07 15:07:05');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `media` varchar(255) DEFAULT NULL,
  `dop` timestamp NOT NULL DEFAULT current_timestamp(),
  `repost` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `uid`, `content`, `media`, `dop`, `repost`) VALUES
(4, 2, 'Test Post!', NULL, '2025-03-06 12:17:49', NULL),
(5, 2, 'Test Post, with Image!', '67c9928e09fcf.png', '2025-03-06 12:18:22', NULL),
(6, 3, 'Wtf', '67c992cba6cd4.png', '2025-03-06 12:19:23', NULL),
(7, 3, 'Eyce', '67c992e15fea7.png', '2025-03-06 12:19:45', NULL),
(8, 4, 'Hello!', NULL, '2025-03-06 12:20:43', NULL),
(9, 5, 'Estetik', '67c9935a01492.jpg', '2025-03-06 12:21:46', NULL),
(10, 6, 'qt', '67c993b2a5864.jpg', '2025-03-06 12:23:14', NULL),
(11, 3, 'gahahahahahah', '67c993d4e7d4d.png', '2025-03-06 12:23:48', NULL),
(12, 2, 'This is an editable post!\r\n\r\nEdit this -> HATDOG', NULL, '2025-03-06 12:26:37', NULL),
(13, 2, 'This is an editable post with an image!\r\n\r\nEdit this text->\r\n\r\nAnd remove the image!', '67c994ad05667.png', '2025-03-06 12:27:25', NULL),
(14, 2, 'This is an editable post with an image!\r\n\r\nChange this text -> Dildo\r\n\r\nChange the image!', '67c994d77b8e9.png', '2025-03-06 12:28:07', NULL),
(30, 2, '', '', '2025-03-06 16:34:01', 12),
(31, 2, '', '', '2025-03-06 16:36:13', 11),
(32, 2, '', '', '2025-03-06 16:51:16', 8),
(33, 3, '', '', '2025-03-06 17:09:20', 14),
(37, 4, 'Hi', NULL, '2025-03-07 13:48:57', NULL),
(40, 2, '', '', '2025-03-07 14:01:13', 37),
(41, 8, 'under 18 lang pinapatulan ko', NULL, '2025-03-07 14:30:13', NULL),
(42, 8, 'under 10 lang pala', NULL, '2025-03-07 14:30:34', NULL),
(43, 8, 'WANT SO BAD!!!!!!!!!!', '67cb03e1401c2.jpg', '2025-03-07 14:34:09', NULL),
(44, 8, '', '', '2025-03-07 14:47:26', 4),
(45, 8, '', '', '2025-03-07 15:03:28', 4),
(46, 2, '', '', '2025-03-07 15:04:25', 43),
(47, 2, '', '', '2025-03-07 15:04:35', 43),
(48, 8, '', '', '2025-03-07 15:07:05', 4);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `dp` varchar(255) DEFAULT NULL,
  `bp` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `doc` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `dp`, `bp`, `bio`, `doc`) VALUES
(2, 'testuser', 'testuser@gmail.com', '$2y$10$Ta9CP2Fit7tvS9BLNbiJU.3ryl1xjxQh8S9vU1qXhRM8TI3h7otpO', 'dp_67c9dfb32b75d.jpg', NULL, '', '2025-03-06 12:14:32'),
(3, 'Fhrus', 'gabbydave822@gmail.com', '$2y$10$iL5vetrNj2rsqgJakUL.Tui5GXU9v8/yMe3GLLpM9hZIwTaRszwnO', 'dp_67c9e5d8b5df3.png', 'bp_67c9e5d8b72ac.png', 'bilat', '2025-03-06 12:15:07'),
(4, 'Kyle', 'kesteves@gmail.com', '$2y$10$kqb6zIWR6Wh0HLITh6k1yuItZra68MfUSGXkmWGJFVKd0nG/56pYG', NULL, NULL, NULL, '2025-03-06 12:16:33'),
(5, 'Min', 'aya@gmail.com', '$2y$10$.9W6/jR04pD7.4RA6KIAYuk40RAJvwsjHYIknTdCIrWlN4xegnNvC', NULL, NULL, NULL, '2025-03-06 12:17:02'),
(6, 'Chi', 'imlilchi@gmail.com', '$2y$10$FrAVuExJxfSU2bOaszpMFOyQtSoE5zvf0hyiMP1./vF/Lce81Dh.a', NULL, NULL, NULL, '2025-03-06 12:17:33'),
(7, 'Mewthh', 'klizarondo@gmail.com', '$2y$10$3ylHJcTihq94LsrTOyzHj.QqvQy.FzHcolEY8k/qdFLQM7cZAz2qu', NULL, NULL, NULL, '2025-03-07 14:03:08'),
(8, 'Zer', 'childhandler@gmail.com', '$2y$10$2FtW/Jr0ewTi7pVMvUXr/.WttFpqS8VmetsKV8Pi7xQL4y2377Qi2', 'dp_67cb02ca527f7.jpg', 'bp_67cb02ca52e28.jpg', 'i love lolis', '2025-03-07 14:26:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `pid` (`pid`);

--
-- Indexes for table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`follower_id`,`followed_id`),
  ADD KEY `followed_id` (`followed_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `pid` (`pid`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_from_user` (`from_user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`pid`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `followers`
--
ALTER TABLE `followers`
  ADD CONSTRAINT `followers_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `followers_ibfk_2` FOREIGN KEY (`followed_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`pid`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_from_user` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
