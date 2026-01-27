-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 06, 2025 at 04:53 PM
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
-- Database: `dbdppl`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_reservasi`
--

CREATE TABLE `detail_reservasi` (
  `id_reservasi` varchar(10) NOT NULL,
  `id_layanan` varchar(10) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `harga` decimal(10,0) DEFAULT NULL,
  `subtotal` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_reservasi`
--

INSERT INTO `detail_reservasi` (`id_reservasi`, `id_layanan`, `jumlah`, `harga`, `subtotal`) VALUES
('RES6470', '1', 2, 52000, 104000),
('RES6470', '2', 1, 250000, 250000),
('RES4340', '2', 1, 250000, 250000),
('RES0301', '2', 1, 250000, 250000),
('RES5398', '4', 2, 350000, 700000),
('RES5398', '5', 1, 150000, 150000);

-- --------------------------------------------------------

--
-- Table structure for table `layanan`
--

CREATE TABLE `layanan` (
  `id_layanan` varchar(10) NOT NULL,
  `jenis_layanan` varchar(20) DEFAULT NULL,
  `harga` varchar(20) DEFAULT NULL,
  `deskripsi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `layanan`
--

INSERT INTO `layanan` (`id_layanan`, `jenis_layanan`, `harga`, `deskripsi`) VALUES
('1', 'Ganti Oli', '52000', 'Ganti oli mesin enduro, castrol, shell, mobil dengan produk original resmi'),
('2', 'Servis Mesin', '250000', 'Servis mesin untuk mobil matic '),
('3', 'Servis AC ', '250000', 'Servis AC depan mobil, langsung seperti baru, garansi freon bocor'),
('4', 'Power Steering', '350000', 'servis power steering kaki kaki mobil, dijamin ngacir'),
('5', 'Setrum Accu', '150000', 'Jasa Charge aki mobil 2200w garansi 7 hari aki full charge');

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

CREATE TABLE `reservasi` (
  `id_reservasi` varchar(10) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nomor_telepon` varchar(15) NOT NULL,
  `tanggal_servis` date NOT NULL,
  `waktu_servis` time NOT NULL,
  `catatan` text DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `status_pembayaran` varchar(25) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `no_mesin` varchar(30) NOT NULL,
  `nopolisi` varchar(10) NOT NULL,
  `merk` varchar(20) NOT NULL,
  `total_harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservasi`
--

INSERT INTO `reservasi` (`id_reservasi`, `id_user`, `nama_lengkap`, `nomor_telepon`, `tanggal_servis`, `waktu_servis`, `catatan`, `status`, `status_pembayaran`, `created_at`, `no_mesin`, `nopolisi`, `merk`, `total_harga`) VALUES
('RES0301', 10, 'Culpa officia ullam', '08718317381', '2025-07-06', '09:30:00', 'AH MASA SIH', 'Pending', 'Belum Lunas', '2025-07-06 11:34:11', '77YTRDEW23', 'B 1383 AV', 'ALFA', 250000),
('RES0783', 3, 'afriza', '0828173813', '2025-07-06', '12:15:00', 'Consequatur consequ', 'Antri', 'Lunas', '2025-07-06 08:43:20', 'JMB321', 'G 5510 BHF', 'TOYOTA', 56500),
('RES3107', 3, 'feyza', '7189210812', '2025-07-06', '15:34:00', 'Dolor dolor eos ill', 'Proses', 'Lunas', '2025-07-06 07:34:36', 'JMD2218631', 'G 5510 BHF', 'HONDA', 113000),
('RES4340', 9, 'Dolorem cupidatat ap', '0853523511311', '2025-07-06', '13:14:00', 'TEST AJAH', 'Pending', 'Belum Lunas', '2025-07-06 11:26:23', 'JMD131323213', 'G 5510 BHF', 'YAMAHA', 250000),
('RES5398', 10, 'FEYZA LINA', '0827831783', '2025-07-06', '14:04:00', 'Cum sunt magnam dol', 'Pending', 'Belum Lunas', '2025-07-06 14:03:53', 'JMD321332144', 'G 5510 BHF', 'HONDA', 850000),
('RES6470', 7, 'icha cantik', '09876543234', '2025-07-17', '13:19:00', 'APAYA', 'Antri', 'Belum Lunas', '2025-07-06 10:19:30', 'NH878998', 'G 5510 BHF', 'AVANZA', 354000);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(15) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `role`) VALUES
(1, 'feyza@gmail.com', 'FeyzaCantik', 'customer'),
(2, 'rafi@carscity.com', '$2y$10$9agP6IKwKS2QjDQ/hpUB1.uht.D8ai7QVjflNeOHb9pzoDOFPj2LS', 'admin'),
(3, 'feyzarevalina@gmail.com', 'feyzacantik', 'user'),
(4, 'fey@gmail.com', 'ichacantik', 'user'),
(5, 'icha@gmail.com', '$2y$10$bUktMNhZww.5IT1BGIEum.N', 'user'),
(6, 'dyla@gmail.com', '$2y$10$Sxo51aDx3W4hh5DUaDZW5uT', 'user'),
(7, 'ichacantik@gmail.com', '$2y$10$cyiPCXVGCQqZuVluOUlXSun', 'user'),
(8, 'rafi@gmai.com', '$2y$10$xpD7mk/CdLtDDJXIY.TTOet', 'user'),
(9, 'rafi@gmail.com', '$2y$10$xc53jDMtYOrpaFf/Dp52k.9', 'user'),
(10, 'afriza@gmail.com', '$2y$10$U16ebl9wjODH6qooyvxRquqglWYFjRgpjWnPUb6zfA8s.WzZvEgae', 'user'),
(11, 'lina@gmail.com', '$2y$10$9agP6IKwKS2QjDQ/hpUB1.uht.D8ai7QVjflNeOHb9pzoDOFPj2LS', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_reservasi`
--
ALTER TABLE `detail_reservasi`
  ADD KEY `id_layanan` (`id_layanan`),
  ADD KEY `detail_reservasi_ibfk_2` (`id_reservasi`);

--
-- Indexes for table `layanan`
--
ALTER TABLE `layanan`
  ADD PRIMARY KEY (`id_layanan`);

--
-- Indexes for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id_reservasi`),
  ADD KEY `nopolisi` (`nopolisi`),
  ADD KEY `reservasi_ibfk_1` (`id_user`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_reservasi`
--
ALTER TABLE `detail_reservasi`
  ADD CONSTRAINT `detail_reservasi_ibfk_2` FOREIGN KEY (`id_reservasi`) REFERENCES `reservasi` (`id_reservasi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_layanan` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
