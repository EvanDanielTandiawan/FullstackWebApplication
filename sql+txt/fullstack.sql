-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 10, 2026 at 02:29 PM
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
-- Database: `fullstack`
--

-- --------------------------------------------------------

--
-- Table structure for table `akun`
--

CREATE TABLE `akun` (
  `username` varchar(20) NOT NULL,
  `password` varchar(100) DEFAULT NULL,
  `nrp_mahasiswa` char(9) DEFAULT NULL,
  `npk_dosen` char(6) DEFAULT NULL,
  `isadmin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `akun`
--

INSERT INTO `akun` (`username`, `password`, `nrp_mahasiswa`, `npk_dosen`, `isadmin`) VALUES
('admin', '$2y$10$qlrGq..6Wmv43zNGARoltutnEgwSXhM6R6Ur4TG2LpfqQJh8FV0Wu', NULL, NULL, 1),
('alex', '$2y$10$h8limE5xBlMAUyVSpdHC2.LTjec5Lj3baBEO5ctyUxBaDNxqvKB/S', NULL, '160401', 0),
('dave', '$2y$10$RXgkzxKLxeT/.p2msWGDCOwXbl8wUOUHabPviZPYxQvVm14H2zae2', '160423001', NULL, 0),
('davina', '$2y$10$ZoYkj4JUVXuf5h3m6vmO0uDt08a485R3.AjVxkG.1m.6.F10vcmWq', '160423002', NULL, 0),
('evan', '$2y$10$Cq457VwNcGnePvnIVwpoW.w6YWd.zGkNmy8ozJC9UXUkQW3w1uhQC', '160423003', NULL, 0),
('maria', '$2y$10$XwiBym0XH.nPnA1cCVKA7O4nPHqyUBEGkrlnt31DEbAvGzJWq/RmG', NULL, '160405', 0),
('mario', '$2y$10$sp1C7yQmqV5GPIqWMJaHM.SVSc6eKeoPrI/A6JE7CBfHHyPAT1yve', NULL, '160406', 0),
('mikayla', '$2y$10$iCrEzrNYL.14JOd9r/ZWEudvKem4yKUz9My/mM4gAX5pslm3Q6n/y', NULL, '160404', 0),
('pangestu', '$2y$10$IXp1o5QtZ05cauAF43Roz.H8Q4tNnXcccHNKC63Z4eMjPEq/OR9ci', NULL, '160403', 0),
('prajogo', '$2y$10$wWAHhfbjWd2xwv4N1STPpuODRT8Thnyi02gACDsYwNLNwxgBpIpym', NULL, '160402', 0),
('sukamto', '$2y$10$WtUWQSCFeidU55bHhBFCJO.0JtwL4m27ZGVFoUG/EiOFaquQhcpgC', '160424001', NULL, 0),
('suprapti', '$2y$10$WvHf6S5kJlb1W1g776JBXuHG7u1Ha285pqfgM4OGaBz917G02R5sC', '160423005', NULL, 0),
('tim', '$2y$10$UUXhqkUv9ajvG23HLb.KJuL3mGNm3j0W3PXm62xHKX0PmOAZPrhfm', '160423004', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `idchat` int(11) NOT NULL,
  `idthread` int(11) NOT NULL,
  `username_pembuat` varchar(20) NOT NULL,
  `isi` text DEFAULT NULL,
  `tanggal_pembuatan` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`idchat`, `idthread`, `username_pembuat`, `isi`, `tanggal_pembuatan`) VALUES
(1, 1, 'dave', 'test', '2026-01-07 17:11:39'),
(2, 1, 'dave', 'tes', '2026-01-10 14:09:22'),
(3, 1, 'dave', 'coba lagi', '2026-01-10 14:09:27'),
(4, 1, 'alex', 'tes', '2026-01-10 20:14:30');

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `npk` char(6) NOT NULL,
  `nama` varchar(45) DEFAULT NULL,
  `foto_extension` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`npk`, `nama`, `foto_extension`) VALUES
('160401', 'alex lapod', 'jpg'),
('160402', 'prajogo pangestu', 'jpeg'),
('160403', 'pangestu wijaya', 'jpg'),
('160404', 'mikayla leyla lalala', 'jpg'),
('160405', 'maria kelly', 'jpg'),
('160406', 'mario bros', 'jpg');

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `idevent` int(11) NOT NULL,
  `idgrup` int(11) NOT NULL,
  `judul` varchar(45) DEFAULT NULL,
  `judul-slug` varchar(45) DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `jenis` enum('Privat','Publik') DEFAULT NULL,
  `poster_extension` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`idevent`, `idgrup`, `judul`, `judul-slug`, `tanggal`, `keterangan`, `jenis`, `poster_extension`) VALUES
(1, 1, 'borobudur marathon 2025', 'borobudur-marathon', '2025-12-28 00:00:00', 'lari', 'Publik', NULL),
(2, 1, 'event 3', 'event-2', '2025-12-30 00:00:00', 'test 2', 'Privat', NULL),
(3, 3, 'event 5', 'event-5', '2026-01-30 00:00:00', 'test', 'Publik', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `grup`
--

CREATE TABLE `grup` (
  `idgrup` int(11) NOT NULL,
  `username_pembuat` varchar(20) NOT NULL,
  `nama` varchar(45) DEFAULT NULL,
  `deskripsi` varchar(45) DEFAULT NULL,
  `tanggal_pembentukan` datetime DEFAULT NULL,
  `jenis` enum('Privat','Publik') DEFAULT NULL,
  `kode_pendaftaran` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `grup`
--

INSERT INTO `grup` (`idgrup`, `username_pembuat`, `nama`, `deskripsi`, `tanggal_pembentukan`, `jenis`, `kode_pendaftaran`) VALUES
(1, 'alex', 'grup 1', 'test 1', '2025-12-02 23:32:55', 'Privat', 'GRP2512026105'),
(3, 'alex', 'test', 'test', '2026-01-07 11:39:14', 'Publik', 'GRP260107626D'),
(4, 'alex', 'grup FSP', 'testing', '2026-01-10 14:14:58', 'Privat', 'GRP2601100D86');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `nrp` char(9) NOT NULL,
  `nama` varchar(45) DEFAULT NULL,
  `gender` enum('Pria','Wanita') DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `angkatan` year(4) DEFAULT NULL,
  `foto_extention` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`nrp`, `nama`, `gender`, `tanggal_lahir`, `angkatan`, `foto_extention`) VALUES
('160423001', 'dave natanael c', 'Pria', '2025-09-29', '2023', 'jpeg'),
('160423002', 'davina suparto', 'Wanita', '2025-10-06', '2023', 'jpg'),
('160423003', 'evan daniel', 'Pria', '2025-10-02', '2023', 'jpg'),
('160423004', 'timotius', 'Pria', '2025-09-29', '2023', 'jpg'),
('160423005', 'suprapti kasino', 'Wanita', '2025-10-02', '2023', 'jpg'),
('160424001', 'sukamto panjangjiwo', 'Pria', '2025-10-02', '2024', 'jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `member_grup`
--

CREATE TABLE `member_grup` (
  `idgrup` int(11) NOT NULL,
  `username` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `member_grup`
--

INSERT INTO `member_grup` (`idgrup`, `username`) VALUES
(1, 'alex'),
(1, 'dave'),
(1, 'evan'),
(1, 'maria'),
(3, 'alex'),
(4, 'alex');

-- --------------------------------------------------------

--
-- Table structure for table `thread`
--

CREATE TABLE `thread` (
  `idthread` int(11) NOT NULL,
  `username_pembuat` varchar(20) NOT NULL,
  `idgrup` int(11) NOT NULL,
  `tanggal_pembuatan` datetime DEFAULT NULL,
  `status` enum('Open','Close') DEFAULT 'Open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `thread`
--

INSERT INTO `thread` (`idthread`, `username_pembuat`, `idgrup`, `tanggal_pembuatan`, `status`) VALUES
(1, 'dave', 1, '2026-01-07 17:11:30', 'Open'),
(2, 'alex', 4, '2026-01-10 20:15:05', 'Open');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `akun`
--
ALTER TABLE `akun`
  ADD PRIMARY KEY (`username`),
  ADD KEY `fk_akun_mahasiswa_idx` (`nrp_mahasiswa`),
  ADD KEY `fk_akun_dosen1_idx` (`npk_dosen`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`idchat`),
  ADD KEY `fk_chat_thread1_idx` (`idthread`),
  ADD KEY `fk_chat_akun1_idx` (`username_pembuat`);

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`npk`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`idevent`),
  ADD KEY `fk_event_grup1_idx` (`idgrup`);

--
-- Indexes for table `grup`
--
ALTER TABLE `grup`
  ADD PRIMARY KEY (`idgrup`),
  ADD KEY `fk_grup_akun1_idx` (`username_pembuat`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`nrp`);

--
-- Indexes for table `member_grup`
--
ALTER TABLE `member_grup`
  ADD PRIMARY KEY (`idgrup`,`username`),
  ADD KEY `fk_grup_has_akun_akun1_idx` (`username`),
  ADD KEY `fk_grup_has_akun_grup1_idx` (`idgrup`);

--
-- Indexes for table `thread`
--
ALTER TABLE `thread`
  ADD PRIMARY KEY (`idthread`),
  ADD KEY `fk_thread_akun1_idx` (`username_pembuat`),
  ADD KEY `fk_thread_grup1_idx` (`idgrup`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `idchat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `idevent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `grup`
--
ALTER TABLE `grup`
  MODIFY `idgrup` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `thread`
--
ALTER TABLE `thread`
  MODIFY `idthread` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `akun`
--
ALTER TABLE `akun`
  ADD CONSTRAINT `fk_akun_dosen1` FOREIGN KEY (`npk_dosen`) REFERENCES `dosen` (`npk`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_akun_mahasiswa` FOREIGN KEY (`nrp_mahasiswa`) REFERENCES `mahasiswa` (`nrp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `fk_chat_akun1` FOREIGN KEY (`username_pembuat`) REFERENCES `akun` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_thread1` FOREIGN KEY (`idthread`) REFERENCES `thread` (`idthread`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_event_grup1` FOREIGN KEY (`idgrup`) REFERENCES `grup` (`idgrup`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `grup`
--
ALTER TABLE `grup`
  ADD CONSTRAINT `fk_grup_akun1` FOREIGN KEY (`username_pembuat`) REFERENCES `akun` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `member_grup`
--
ALTER TABLE `member_grup`
  ADD CONSTRAINT `fk_grup_has_akun_akun1` FOREIGN KEY (`username`) REFERENCES `akun` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_grup_has_akun_grup1` FOREIGN KEY (`idgrup`) REFERENCES `grup` (`idgrup`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `thread`
--
ALTER TABLE `thread`
  ADD CONSTRAINT `fk_thread_akun1` FOREIGN KEY (`username_pembuat`) REFERENCES `akun` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_thread_grup1` FOREIGN KEY (`idgrup`) REFERENCES `grup` (`idgrup`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
