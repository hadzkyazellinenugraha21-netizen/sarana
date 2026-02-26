-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2026 at 08:22 AM
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
-- Database: `sarana`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `nama_item` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `nama_item`) VALUES
(1, 'Rak sepatu'),
(2, 'Pintu Kamar'),
(3, ' Jendela'),
(4, 'Lantai Kramik'),
(5, 'Ranjang'),
(6, 'Lemari'),
(7, 'Atap Bocor'),
(8, 'Pintu Wc'),
(9, 'Mampet '),
(10, 'lampu');

-- --------------------------------------------------------

--
-- Table structure for table `kamar`
--

CREATE TABLE `kamar` (
  `id` int(11) NOT NULL,
  `no_kamar` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keluhan`
--

CREATE TABLE `keluhan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `no_kamar` varchar(20) DEFAULT NULL,
  `kamar_id` int(11) DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `tanggal_keluhan` date NOT NULL,
  `detail_keluhan` text DEFAULT NULL,
  `foto_keluhan` varchar(255) DEFAULT NULL,
  `status` enum('pending','selesai') DEFAULT 'pending',
  `tanggal_selesai` date DEFAULT NULL,
  `foto_selesai` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keluhan`
--

INSERT INTO `keluhan` (`id`, `user_id`, `no_kamar`, `kamar_id`, `item_id`, `tanggal_keluhan`, `detail_keluhan`, `foto_keluhan`, `status`, `tanggal_selesai`, `foto_selesai`, `created_at`) VALUES
(4, 3, '3097', NULL, 3, '2026-02-13', 'ss', NULL, 'pending', NULL, NULL, '2026-02-10 07:43:36'),
(5, 3, '3098', NULL, 3, '2026-02-05', 's', NULL, 'pending', NULL, NULL, '2026-02-10 07:43:43'),
(6, 3, '3099', NULL, 3, '2026-02-10', 'q', 'assets/uploads/1770709488_3.jpeg', 'selesai', '2026-02-26', 'assets/uploads/selesai_6_1772079128.jpeg', '2026-02-10 07:44:48'),
(7, 3, '1111', NULL, 10, '2026-02-10', '1', 'assets/uploads/1770711313_3.jpeg', 'selesai', '2026-02-26', 'assets/uploads/selesai_7_1772078955.png', '2026-02-10 08:15:13'),
(8, 5, '1001', NULL, 2, '2026-02-26', 'engsel', 'assets/uploads/1772079841_5.png', 'pending', NULL, NULL, '2026-02-26 04:24:01'),
(9, 5, '1122', NULL, 6, '2026-02-26', 'engsel', 'assets/uploads/1772080463_5.jpeg', 'selesai', '2026-02-26', 'assets/uploads/selesai_9_1772080678.png', '2026-02-26 04:34:23'),
(10, 5, '1122', NULL, 3, '2026-02-25', 'engsel', 'assets/uploads/1772081224_5.jpeg', 'selesai', '2026-02-26', 'assets/uploads/selesai_10_1772081244.png', '2026-02-26 04:47:04'),
(11, 5, '3098', NULL, 9, '2026-02-26', 'wc', 'assets/uploads/1772086430_5.png', 'selesai', '2026-02-26', 'assets/uploads/selesai_11_1772086454.png', '2026-02-26 06:13:50');

-- --------------------------------------------------------

--
-- Table structure for table `log_akses`
--

CREATE TABLE `log_akses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `aksi` varchar(50) DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('wali','admin','direktur') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Bapak Adi', 'adi@mail.com', '7360409d967a24b667afc33a8384ec9e', 'wali', '2026-02-10 04:58:25'),
(2, 'Ibu Sari', 'sari@mail.com', 'e9ee75b57bb1303190c8869621cad05b', 'wali', '2026-02-10 04:58:25'),
(3, 'Bapak Budi', 'budi@mail.com', '9c5fa085ce256c7c598f6710584ab25d', 'wali', '2026-02-10 04:58:25'),
(4, 'Admin', 'admin@example.com', '0192023a7bbd73250516f069df18b500', 'admin', '2026-02-10 06:00:17'),
(5, 'Wali 1', 'wali1@example.com', 'bf8cd26e6c6732b8df17a31b54800ed8', 'wali', '2026-02-10 06:00:17'),
(6, 'Wali 2', 'wali2@example.com', 'bf8cd26e6c6732b8df17a31b54800ed8', 'wali', '2026-02-10 06:00:17'),
(7, 'Direktur', 'direktur@example.com', '4fbfd324f5ffcdff5dbf6f019b02eca8', 'direktur', '2026-02-10 06:00:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kamar`
--
ALTER TABLE `kamar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_kamar` (`no_kamar`);

--
-- Indexes for table `keluhan`
--
ALTER TABLE `keluhan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `kamar_id` (`kamar_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `log_akses`
--
ALTER TABLE `log_akses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `kamar`
--
ALTER TABLE `kamar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `keluhan`
--
ALTER TABLE `keluhan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `log_akses`
--
ALTER TABLE `log_akses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `keluhan`
--
ALTER TABLE `keluhan`
  ADD CONSTRAINT `keluhan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `keluhan_ibfk_2` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`id`),
  ADD CONSTRAINT `keluhan_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `log_akses`
--
ALTER TABLE `log_akses`
  ADD CONSTRAINT `log_akses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
