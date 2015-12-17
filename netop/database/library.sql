-- phpMyAdmin SQL Dump
-- version 4.5.0.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 17, 2015 at 07:17 PM
-- Server version: 10.0.17-MariaDB
-- PHP Version: 5.5.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category_id` int(3) NOT NULL,
  `price` float(10,2) NOT NULL,
  `author` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `name`, `image`, `category_id`, `price`, `author`) VALUES
(12, 'Humans of New York: Stories', 'assets/images/books/Humans_of_New_York__Stories_banner.jpg', 1, 16.49, 'Brandon Stanton'),
(13, 'Andy Goldsworthy: Ephemeral Works: 2004-2014', 'assets/images/books/Andy_Goldsworthy__Ephemeral_Works__2004-2014_banner.jpg', 1, 56.93, 'Andy Goldsworthy'),
(14, 'Dust & Grooves: Adventures in Record Collecting', 'assets/images/books/Dust___Grooves__Adventures_in_Record_Collecting_banner.jpg', 1, 30.00, 'Eilon Paz'),
(15, 'Covert to Overt: The Under/Overground Art of Shepard Fairey', 'assets/images/books/Covert_to_Overt__The_Under_Overground_Art_of_Shepard_Fairey_banner.jpg', 1, 33.06, 'Shepard Fairey'),
(16, 'See Me', 'assets/images/books/See_Me_banner.jpg', 19, 14.56, 'Nicholas Sparks'),
(17, 'H Is for Hawk', 'assets/images/books/H_Is_for_Hawk_banner.jpg', 15, 16.60, 'Helen Macdonald');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(3) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Arts & Photography'),
(2, 'Business & Investing'),
(3, 'Comics & Graphic Novels'),
(4, 'History'),
(5, 'Literature & Fiction'),
(6, ' Science Fiction & Fantasy'),
(14, 'Fashion'),
(15, 'Sports & Outdoors'),
(16, 'Children''s Books'),
(17, 'Crafts, Home & Garden'),
(19, 'Romance');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(3) NOT NULL,
  `name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `last_name`, `email`, `password`) VALUES
(3, 'Manta', 'Alex', 'admin@admin.ro', '$2y$10$lJ9qBeq1ZdLn4eI3qMcjluy0FuyBGzSVilG9NNqG9DYaIVRE9A2B6');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
