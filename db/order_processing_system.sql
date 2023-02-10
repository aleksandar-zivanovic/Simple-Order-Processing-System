-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 10, 2023 at 02:41 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `order_processing_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `aname` varchar(255) NOT NULL,
  `atype` varchar(255) NOT NULL,
  `acode` varchar(5) NOT NULL,
  `aprice` float NOT NULL,
  `aunit` varchar(10) NOT NULL,
  `acomment` text DEFAULT NULL,
  `astatus` varchar(8) NOT NULL DEFAULT 'active',
  `acreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `aupdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `aname`, `atype`, `acode`, `aprice`, `aunit`, `acomment`, `astatus`, `acreated`, `aupdated`) VALUES
(1, 'Cheeseburger', 'burgers', 'b0001', 300, 'kom.', '200g junetine, topljeni sir, kupus, pavlaka, burger sos', 'active', '2022-11-24 18:45:44', '2022-11-27 12:56:41'),
(2, 'Pecenica - przeno jaje', 'sandwiches', 's0001', 150, 'kom.', 'svinjska pecenica, pavlaka, jaja na oko, kecap', 'active', '2022-11-24 22:13:48', '2022-11-29 21:40:30'),
(3, 'Burger la Vista', 'burgers', 'b0002', 440, 'kom.', '200g junetine, topljeni sir. slanina, paradajz, iceber, crni luk, kiseli krastavci', 'active', '2022-11-27 12:43:54', '2022-11-29 09:44:21'),
(4, 'Chickeburger la Vista', 'burgers', 'b0003', 380, 'kom.', 'pohovano belo, iceberg, paradajz, burger sos)', 'active', '2022-11-27 13:01:15', '2022-11-29 10:24:56'),
(5, 'Chickeburger', 'burgers', 'b0004', 330, 'kom.', 'pileci file 200g, topljeni sir, paradajz', 'active', '2022-11-27 13:04:46', '2022-11-27 13:06:12'),
(6, 'Monster burger', 'burgers', 'b0005', 550, 'kom.', 'pohovano belo 200g, 200g juntine, topljeni sir, burger sos, icberg', 'active', '2022-11-27 13:11:12', '2022-11-27 13:11:12'),
(7, 'Domaci sendvic', 'sandwiches', 's0002', 180.5, 'kom.', 'pecenica, kajgana, kajmak, kackavalj', 'inactive', '2022-11-27 14:07:41', '2022-11-29 13:42:23'),
(8, 'Zorina palacinka', 'salty-pancakes', 'p0001', 220, 'kom.', 'sunka, kackavalj, pavlaka, jaje, zdenka', 'inactive', '2022-11-27 14:22:15', '2022-11-29 14:16:05'),
(9, 'Srpska palacinka', 'salty-pancakes', 'p0002', 220, 'kom.', 'kulen, ajvar, pavlaka, jaje, svarci', 'active', '2022-11-27 14:24:39', '2022-11-29 09:46:09'),
(10, 'Cezar palacinka', 'salty-pancakes', 'p0003', 250, 'kom.', 'dimljena piletina, kackavalj, cezar dresing, slanina', 'active', '2022-11-27 14:35:44', '2022-11-29 10:18:00'),
(11, 'Srpski sendvic', 'sandwiches', 's0003', 180.75, 'kom.', 'kajmak, slanina', 'active', '2022-11-27 14:38:28', '2022-11-27 14:46:14'),
(12, 'Lepinja sa dimljenom kolenicom i kajmakom', 'sandwiches', 's0004', 280, 'kom.', 'dimljena kolenica, kajmak', 'active', '2022-11-27 14:41:42', '2022-11-27 14:41:42'),
(13, 'Lepinja sa obeskoscenim rebrima i kajmakom', 'sandwiches', 's0005', 280, 'kom.', 'obeskoscena rebra, kajmak', 'active', '2022-11-27 14:45:44', '2023-01-10 22:26:51'),
(14, 'Palacinke la Vista', 'sweet-pancakes', 'p0004', 250, '2 kom.', 'nutela, plazma, sladoled, maline', 'active', '2022-11-27 15:03:00', '2022-11-27 15:03:00'),
(15, 'Palacinke Bananica', 'sweet-pancakes', 'p0005', 250, '2 kom.', 'nutela, krem bananica, banana, sladoled', 'active', '2022-11-27 15:04:50', '2022-11-27 15:04:50'),
(16, 'Palacinke Opsesija', 'sweet-pancakes', 'p0006', 300.33, '2 kom.', 'topla cokolada, nutela, orasasti plodovi miks, prelivene belom i crnom cokoladom', 'active', '2022-11-27 15:08:35', '2022-11-27 15:08:35'),
(17, 'Palacinke Classic', 'sweet-pancakes', 'p0007', 200, '2 kom.', 'nutela, plazma', 'active', '2022-11-27 15:11:49', '2023-01-20 20:22:25'),
(18, 'Palacinke Retro', 'sweet-pancakes', 'p0008', 200, '2 kom.', 'orah, secer, med', 'active', '2022-11-27 23:59:44', '2022-12-22 22:17:58'),
(19, 'Palacinke Kinder bueno', 'sweet-pancakes', 'p0009', 400, '2 kom.', 'nutela, kinder cokolada, kinder bueno, sladoled', 'active', '2022-11-29 09:25:24', '2023-01-10 22:26:36');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `cname` varchar(255) NOT NULL,
  `cstatus` varchar(8) NOT NULL DEFAULT 'active',
  `ctype` varchar(10) NOT NULL DEFAULT 'person',
  `ccomment` text DEFAULT NULL,
  `cupdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ccreated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `cname`, `cstatus`, `ctype`, `ccomment`, `cupdated`, `ccreated`) VALUES
(1, 'Mika Mikic', 'active', 'person', 'kupuje ponedeljkom i petkom u 11:30h!', '2022-12-21 05:34:43', '2022-11-23 12:04:58'),
(2, 'Petar Petrovic', 'active', 'person', '', '2022-11-30 09:59:35', '2022-11-29 15:09:58'),
(3, 'Dunav DOO', 'active', 'company', 'Placaju svakog 1. i 15. u mesecu preko racuna', '2022-11-30 09:59:24', '2022-11-29 15:10:55'),
(4, 'Dom Zdravlja Pozarevac', 'active', 'company', '', '2022-11-30 10:22:19', '2022-11-29 15:12:11'),
(5, 'EPS', 'active', 'company', '10% popusta na hranu i 5% poupusta na pice', '2022-11-30 09:59:56', '2022-11-29 15:13:06'),
(6, 'Djoka Djokic', 'active', 'person', '', '2023-02-09 12:01:10', '2022-11-30 09:23:05'),
(7, 'IDEA DOO', 'active', 'company', '10% popusta', '2023-02-09 14:08:31', '2022-11-30 09:24:17'),
(8, 'Pera Peric', 'active', 'person', 'Kralja Petra 2/3', '2022-12-20 15:13:38', '2022-12-20 14:36:47'),
(9, 'Globatel AD', 'active', 'company', 'Kneza Lazara 123', '2023-01-14 12:37:30', '2022-12-20 14:58:01'),
(10, 'Telekom Srbija AD', 'active', 'company', 'Robna kuca', '2022-12-21 04:55:20', '2022-12-21 04:25:07'),
(11, 'Zika Zikic', 'active', 'person', 'Zarka Zrenjanina 23', '2023-01-20 20:23:30', '2022-12-20 15:09:35'),
(12, 'Sima Simic', 'active', 'person', 'Bulevar Kralja Aleksandar 23/4\r\n0123456789', '2022-12-20 15:13:41', '2022-12-20 15:10:37'),
(13, 'Laza Lazic', 'active', 'person', 'Branicevska 1234', '2022-12-20 15:24:15', '2022-12-20 15:24:15'),
(14, 'Bosko Boskic', 'active', 'person', 'Novi Sad', '2023-01-20 20:50:53', '2022-12-20 15:33:05'),
(15, 'Dragan Dodjos', 'active', 'person', 'Ovaj ne narucuje', '2023-01-20 20:43:17', '2022-12-20 15:35:31'),
(16, 'Marko Kraljevic', 'active', 'person', 'Ima aktivnu karticu. Kartica vazi samo za njega, ne moze pola da je Sarcu daje.', '2023-01-21 16:19:18', '2023-01-21 16:19:18'),
(17, 'Baja Patak', 'active', 'person', 'Njemu kartica ne treba, posto je preduzece njegovo!', '2023-01-21 16:22:00', '2023-01-21 16:22:00'),
(18, 'Patkovgrad Inc.', 'active', 'company', 'Preduzece u vlasnistvu Baje Patka', '2023-01-21 16:23:59', '2023-01-21 16:23:59'),
(19, 'Paja Patak', 'active', 'person', 'U minusu 150$', '2023-01-21 16:29:05', '2023-01-21 16:29:05'),
(20, 'Gaja', 'active', 'person', '', '2023-01-21 16:29:29', '2023-01-21 16:29:29'),
(21, 'Vlaja', 'active', 'person', '', '2023-01-21 16:39:54', '2023-01-21 16:39:54'),
(23, 'Raja', 'active', 'person', '', '2023-01-21 16:41:12', '2023-01-21 16:41:12'),
(24, 'Bambi DOO', 'active', 'company', '', '2023-02-06 17:48:41', '2023-01-21 16:43:11'),
(25, 'Maxi DOO', 'active', 'company', '', '2023-01-21 18:18:27', '2023-01-21 16:44:01'),
(26, 'Banca Intesa', 'active', 'company', '', '2023-02-06 17:47:23', '2023-01-21 16:44:24'),
(27, 'OTP banka', 'active', 'company', 'Duguju za nabavku', '2023-02-09 14:09:14', '2023-01-21 16:45:03'),
(28, 'Pera Detlic', 'active', 'person', 'Zivi na drvetu!', '2023-02-09 18:24:27', '2023-01-21 16:45:56');

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_card`
--

CREATE TABLE `loyalty_card` (
  `id` int(11) NOT NULL,
  `lccid` int(10) NOT NULL,
  `lcstatus` varchar(8) NOT NULL DEFAULT 'active',
  `lccomment` text DEFAULT NULL,
  `lccreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `lcupdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `loyalty_card`
--

INSERT INTO `loyalty_card` (`id`, `lccid`, `lcstatus`, `lccomment`, `lccreated`, `lcupdated`) VALUES
(1, 1, 'active', 'Dobio kao prvi kupac', '2023-01-14 12:35:13', '2023-01-16 10:17:00'),
(2, 3, 'active', 'po ugovoru', '2023-01-14 12:36:17', '2023-01-14 12:36:17'),
(3, 5, 'inactive', NULL, '2023-01-14 12:36:17', '2023-01-16 10:31:14'),
(4, 6, 'removed', '', '2023-01-14 12:36:59', '2023-01-20 20:22:54'),
(5, 7, 'active', '', '2023-01-14 12:36:59', '2023-02-09 14:08:31'),
(6, 10, 'active', 'po ugovoru', '2023-01-14 12:38:12', '2023-01-16 20:23:17'),
(7, 11, 'active', '', '2023-01-14 12:38:52', '2023-01-19 22:42:38'),
(8, 14, 'removed', '', '2023-01-20 19:41:30', '2023-01-21 16:17:14'),
(9, 16, 'active', '', '2023-01-21 16:19:18', '2023-01-21 16:19:18'),
(10, 18, 'active', '', '2023-01-21 16:23:59', '2023-01-21 16:38:06'),
(11, 19, 'active', 'Bez limita', '2023-01-21 16:38:56', '2023-01-21 16:38:56'),
(12, 25, 'active', '', '2023-01-21 16:44:01', '2023-01-21 16:44:01'),
(13, 26, 'active', '', '2023-01-21 16:44:24', '2023-02-06 17:47:23'),
(14, 27, 'inactive', '', '2023-01-21 16:45:03', '2023-02-09 14:09:14'),
(18, 24, 'active', NULL, '2023-02-06 17:50:30', '2023-02-06 17:50:30'),
(19, 4, 'inactive', NULL, '2023-02-06 17:50:30', '2023-02-06 17:50:30'),
(20, 28, 'inactive', '', '2023-02-09 18:24:27', '2023-02-09 18:24:27');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) NOT NULL,
  `ocid` int(10) NOT NULL,
  `ocomment` text DEFAULT NULL,
  `ostatus` varchar(8) NOT NULL DEFAULT 'active',
  `odate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `ocid`, `ocomment`, `ostatus`, `odate`) VALUES
(1, 3, 'Uz svaki artikl dodati po 2 salvete\r\npromenjen customerId od 3 na 1', 'canceled', '2023-01-19 22:11:10'),
(2, 2, 'pozvati korisnika pre nego sto dostavljac krene', 'done', '2023-01-05 17:23:15'),
(3, 7, 'kontakt: 1234567', 'active', '2023-01-05 13:25:40'),
(4, 1, 'ostavljen novac na stolu u dvoristu. dostavljac neka udje u dvoriste, uzme novac i ostavi hranu na stolu ispod vaze', 'active', '2023-01-07 17:01:53'),
(5, 6, 'pokusaj zamene ID-ja customer-a', 'canceled', '2023-01-19 21:34:24'),
(6, 14, '', 'active', '2023-01-10 22:23:00'),
(7, 7, 'ostaviti na kasi br. 2\r\nnovac je ispod novine', 'active', '2023-01-08 17:23:07');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) NOT NULL,
  `order_id` int(10) NOT NULL,
  `article_id` int(10) NOT NULL,
  `article_quantity` int(11) NOT NULL,
  `item_comment` text DEFAULT NULL,
  `oiupdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `article_id`, `article_quantity`, `item_comment`, `oiupdated`) VALUES
(1, 1, 1, 2, 'test + 2 (x2)', '2023-01-04 15:22:57'),
(2, 1, 2, 1, '', '2022-12-22 13:25:54'),
(3, 1, 8, 8, '1x sa senfom\r\n2x sa kecapom', '2022-12-02 12:32:43'),
(5, 2, 7, 1, 'ketcap i senf', '2022-12-18 10:12:39'),
(6, 2, 5, 4, '1x iceber salata (dadatno)\r\n2x suva slanina', '2022-12-18 10:12:39'),
(7, 3, 7, 6, '3x kecap \r\n2x senf\r\n1x kecap i senf', '2022-12-18 22:51:18'),
(8, 3, 1, 2, '', '2022-12-18 22:51:18'),
(9, 4, 7, 1, 'dodati i krastavcice', '2022-12-18 20:14:37'),
(10, 4, 9, 4, '', '2022-12-18 20:14:37'),
(11, 5, 13, 2, 'dodati kisele krastavcice', '2022-12-20 12:00:02'),
(12, 5, 7, 4, '', '2022-12-06 13:33:56'),
(13, 5, 11, 2, 'obrisao sam komentare', '2023-01-04 14:46:43'),
(14, 3, 14, 1, '', '2022-12-18 22:50:38'),
(15, 3, 15, 2, '', '2022-12-18 22:50:38'),
(16, 3, 16, 6, 'Palacinke Opsesija (p0006)', '2023-01-04 10:09:57'),
(17, 3, 17, 4, 'Palacinke Classic (p0007)', '2022-12-18 22:50:38'),
(18, 3, 18, 2, 'test + 2 (x2)', '2023-01-04 15:22:57'),
(19, 4, 1, 2, 'test + 2 (x2)', '2023-01-04 15:22:57'),
(88, 6, 1, 10, 'bez kupusa (x2) | test 6 (x6)', '2023-01-10 22:23:00'),
(90, 6, 6, 9, 'test3 (x3) | test 6 (x6)', '2023-01-04 16:42:31'),
(111, 6, 11, 6, 'test 6', '2023-01-04 16:42:31'),
(112, 6, 13, 6, 'test 6', '2023-01-04 16:42:31'),
(113, 7, 3, 1, '', '2023-01-07 16:17:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `aname` (`aname`),
  ADD UNIQUE KEY `acode` (`acode`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cname` (`cname`);

--
-- Indexes for table `loyalty_card`
--
ALTER TABLE `loyalty_card`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lccid` (`lccid`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders.ocid_customers.id` (`ocid`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_items.order_id_orders.id` (`order_id`),
  ADD KEY `fk_order_items_article_id_articles_id` (`article_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `loyalty_card`
--
ALTER TABLE `loyalty_card`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loyalty_card`
--
ALTER TABLE `loyalty_card`
  ADD CONSTRAINT `fk_lloyalty_card.lccid_customers.id` FOREIGN KEY (`lccid`) REFERENCES `customers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders.ocid_customers.id` FOREIGN KEY (`ocid`) REFERENCES `customers` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items.order_id_orders.id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_article_id_articles_id` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
