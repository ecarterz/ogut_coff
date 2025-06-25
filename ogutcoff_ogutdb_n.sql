-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 25, 2025 at 04:36 PM
-- Server version: 10.6.21-MariaDB-cll-lve
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ogutcoff_ogutdb_n`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(2, 'owner', 'owner'),
(7, 'admin', '21232f297a57a5a743894a0e4a801fc3');

-- --------------------------------------------------------

--
-- Table structure for table `antrian`
--

CREATE TABLE `antrian` (
  `id` int(11) NOT NULL,
  `nomor_meja` int(11) NOT NULL,
  `nomor_antrian` int(11) NOT NULL,
  `waktu_pesan` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meja_cafe`
--

CREATE TABLE `meja_cafe` (
  `id_meja` int(11) NOT NULL,
  `nomor_meja` varchar(50) NOT NULL,
  `kapasitas` int(3) NOT NULL,
  `status` enum('Tersedia','Terisi','Perlu Dibersihkan') DEFAULT 'Tersedia',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `meja_cafe`
--

INSERT INTO `meja_cafe` (`id_meja`, `nomor_meja`, `kapasitas`, `status`, `created_at`) VALUES
(1, 'Meja 1', 4, 'Terisi', '2025-06-16 08:10:22'),
(2, 'Meja 2', 4, 'Terisi', '2025-06-16 08:10:22'),
(3, 'Meja 3', 4, 'Tersedia', '2025-06-16 08:10:22'),
(4, 'Meja 4', 4, 'Tersedia', '2025-06-16 08:10:22'),
(5, 'Meja 5', 4, 'Tersedia', '2025-06-16 08:10:22'),
(6, 'Meja 6', 4, 'Tersedia', '2025-06-16 08:10:22'),
(7, 'Meja 7', 4, 'Tersedia', '2025-06-16 08:10:22'),
(8, 'Meja 8', 4, 'Tersedia', '2025-06-16 08:10:22'),
(9, 'Meja 9', 4, 'Terisi', '2025-06-16 08:10:22'),
(10, 'Meja 10', 4, 'Tersedia', '2025-06-16 08:10:22'),
(11, 'Meja 11', 4, 'Tersedia', '2025-06-16 08:10:22'),
(12, 'Meja 12', 4, 'Tersedia', '2025-06-16 08:10:22'),
(13, 'Meja 13', 4, 'Terisi', '2025-06-16 08:10:22'),
(14, 'Meja 14', 4, 'Terisi', '2025-06-16 08:10:22'),
(15, 'Meja 15', 4, 'Tersedia', '2025-06-16 08:10:22'),
(16, 'Meja 16', 4, 'Tersedia', '2025-06-16 08:10:22'),
(17, 'Meja 17', 4, 'Tersedia', '2025-06-16 08:10:22'),
(18, 'Meja 18', 4, 'Tersedia', '2025-06-16 08:10:22');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL,
  `kode_menu` varchar(12) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `gambar` varchar(100) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `status` enum('tersedia','tidak tersedia') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `kode_menu`, `nama`, `harga`, `gambar`, `kategori`, `status`) VALUES
(2, 'MN02', 'Kerang Asam manis', 50000, 'kerang-asam-manis.png', 'Makanan', 'tersedia'),
(3, 'MN03', 'Gurame Saus Tauco', 25000, 'gurame-saus-tauco.png', 'Makanan', 'tersedia'),
(4, 'MN04', 'Gurame Asam Manis', 30000, 'gurame-asam-manis.png', 'Makanan', 'tersedia'),
(5, 'MN05', 'Dendeng Balado', 35000, 'dendeng-balado.png', 'Makanan', 'tersedia'),
(6, 'MN06', 'Bebek Goreng Kelapa', 35000, 'bebek-goreng-kelapa.png', 'Makanan', 'tersedia'),
(7, 'MN07', 'Balado Kerang Pedas', 45000, 'balado-kerang-pedas.png', 'Makanan', 'tersedia'),
(8, 'MN08', 'Ayam Bakar Madu', 25000, 'ayam-bakar-madu.png', 'Makanan', 'tersedia'),
(9, 'MN09', 'Nasi Goreng Sosis', 15000, 'nasi-goreng-sosis.png', 'Makanan', 'tersedia'),
(10, 'MN10', 'Udang Tepung Gendut', 20000, 'udang-tepung.png', 'Fast Food', 'tersedia'),
(11, 'MN11', 'Macaroni Asam Pedas', 25000, 'macaroni-asam-pedas.png', 'Fast Food', 'tersedia'),
(12, 'MN12', 'Spaghetti Saus Ikan', 25000, 'spaghetti-saus-ikan.png', 'Fast Food', 'tersedia'),
(13, 'MN13', 'Ayam Goreng Tepung', 10000, 'ayam-goreng-tepung.png', 'Fast Food', 'tersedia'),
(14, 'MN14', 'Chicken Wings', 30000, 'chicken-wings.png', 'Fast Food', 'tersedia'),
(15, 'MN15', 'Roti Jalo Kuah Kari', 35000, 'roti-jalo.png', 'Fast Food', 'tersedia'),
(16, 'MN16', 'Burger Egg Cheese', 16000, 'egg-cheese-burger.png', 'Fast Food', 'tersedia'),
(17, 'MN17', 'Roll Sushi Tuna', 30000, 'roll-sushi-tuna.png', 'Fast Food', 'tersedia'),
(18, 'MN18', 'Mie Setan', 20000, 'mie-setan.png', 'Fast Food', 'tersedia'),
(19, 'MN19', 'Molen Kacang Hijau', 5000, 'molen-kacang-hijau.png', 'Snack', 'tersedia'),
(20, 'MN20', 'Kue Cubit', 10000, 'kue-cubit.png', 'Snack', 'tersedia'),
(21, 'MN21', 'Otak2 Udang Keju', 15000, 'otak-udang-keju.png', 'Snack', 'tersedia'),
(22, 'MN22', 'Donat Kentang', 15000, 'donat-kentang.png', 'Snack', 'tersedia'),
(23, 'MN23', 'Siomay Bandung', 30000, 'siomay-bandung.png', 'Snack', 'tersedia'),
(24, 'MN24', 'Rolade Tahu', 20000, 'rolade-tahu.png', 'Snack', 'tersedia'),
(25, 'MN25', 'Onion Ring', 10000, 'onion-ring.png', 'Snack', 'tersedia'),
(26, 'MN26', 'Puding Lumut', 10000, 'puding-lumut.png', 'Dessert', 'tersedia'),
(27, 'MN27', 'Oreo Cheese Cake', 25000, 'oreo-cheese-cake.png', 'Dessert', 'tersedia'),
(28, 'MN28', 'Strawberry Cheese Cake', 25000, 'strawberry-cheese-cake.png', 'Dessert', 'tersedia'),
(29, 'MN29', 'Cake Ubi Ungu', 20000, 'cake-ubi-ungu.png', 'Dessert', 'tersedia'),
(30, 'MN30', 'Black Forest', 25000, 'black-forest.png', 'Dessert', 'tersedia'),
(31, 'MN31', 'Wafer Coklat Puding', 20000, 'wafer-coklat-puding.png', 'Dessert', 'tersedia'),
(32, 'MN32', 'Es Krim Kacang Merah', 28000, 'es-krim-kacang-merah.png', 'Dessert', 'tersedia'),
(40, 'MN40', 'Matcha Coffee', 20000, 'matchacoffee.jpg', 'Minuman', 'tersedia'),
(43, 'MN43', 'Matcha', 18000, 'matcha.jpg', 'Minuman', 'tersedia'),
(44, 'MN44', 'Lecy Tea', 18000, 'lecytea.jpg', 'Minuman', 'tersedia'),
(45, 'MN45', 'Kopi Susu', 20000, 'kopisusu.jpg', 'Minuman', 'tersedia'),
(46, 'MN46', 'Klepon', 20000, 'klepon.jpg', 'Minuman', 'tersedia'),
(47, 'MN47', 'Hazelnut Latte', 20000, 'hazelnutlatte.jpg', 'Minuman', 'tersedia'),
(48, 'MN48', 'Cookies and Cream', 20000, 'cookiescream.jpg', 'Minuman', 'tersedia'),
(49, 'MN49', 'Caramel Cream Latte', 20000, 'caramelcream.jpg', 'Minuman', 'tersedia'),
(50, 'MN50', 'Brown Sugar', 20000, 'brownsugar.jpg', 'Minuman', 'tidak tersedia'),
(61, 'MN053', 'Red Velvet ', 18, '68502eb9be104.jpeg', 'Minuman', 'tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `kode_pesanan` varchar(255) NOT NULL,
  `kode_menu` varchar(225) NOT NULL,
  `qty` int(11) NOT NULL,
  `nomor_meja` int(11) DEFAULT NULL,
  `nomor_antrian` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `kode_pesanan`, `kode_menu`, `qty`, `nomor_meja`, `nomor_antrian`) VALUES
(80, '68440c2a43e4', 'MN52', 1, NULL, NULL),
(81, '68440c33337e', 'MN50', 1, NULL, NULL),
(82, '68440c33337e', 'MN49', 1, NULL, NULL),
(83, '68458402da06', 'MN52', 1, NULL, NULL),
(84, '68458402da06', 'MN50', 1, NULL, NULL),
(85, '684838e4f15b', 'MN52', 1, NULL, NULL),
(86, '68483900a81b', 'MN50', 1, NULL, NULL),
(87, '6848390aa8b3', 'MN49', 1, NULL, NULL),
(88, '6848392189dc', 'MN45', 2, NULL, NULL),
(89, '6848394b9eb8', 'MN46', 1, NULL, NULL),
(90, '684839572c6f', 'MN45', 1, NULL, NULL),
(91, '684aafc0b12c5', 'MN52', 6, NULL, NULL),
(92, '684aafd7d1a63', 'MN52', 6, NULL, NULL),
(93, '684aaff735457', 'MN52', 6, NULL, NULL),
(94, '684ab01a82950', 'MN52', 6, NULL, NULL),
(95, '684ab04198ad1', 'MN52', 6, NULL, NULL),
(96, '684ab0a71c8d4', 'MN52', 6, NULL, NULL),
(97, '684ab0f90d649', 'MN52', 6, NULL, NULL),
(98, '684ab1143c0c0', 'MN52', 5, NULL, NULL),
(99, '684ab13e0acb6', 'MN52', 5, NULL, NULL),
(100, '684ab3377031a', 'MN52', 5, NULL, NULL),
(101, '684abdc1d6906', 'MN44', 1, NULL, NULL),
(102, '684abdc1d6906', 'MN43', 1, NULL, NULL),
(103, '684bbcc93cdf5', 'MN02', 1, NULL, NULL),
(104, '684d459088cad', 'MN52', 1, NULL, NULL),
(105, '684d459088cad', 'MN50', 1, NULL, NULL),
(106, '684d467b18306', 'MN50', 1, NULL, NULL),
(107, '684d467b18306', 'MN34', 1, NULL, NULL),
(108, '684ed16595779', 'MN52', 1, NULL, NULL),
(109, '684ed16595779', 'MN50', 1, NULL, NULL),
(110, '684ed16595779', 'MN49', 1, NULL, NULL),
(111, '684ed2ebf0e0f', 'MN49', 1, NULL, NULL),
(112, '684ed2ebf0e0f', 'MN50', 1, NULL, NULL),
(113, '684edbc012ce0', 'MN16', 2, NULL, NULL),
(114, '684edbc012ce0', 'MN49', 1, NULL, NULL),
(115, '684edbc012ce0', 'MN46', 2, NULL, NULL),
(116, '684ef199cb924', 'MN52', 1, NULL, NULL),
(117, '684ef199cb924', 'MN50', 1, NULL, NULL),
(118, '684ef199cb924', 'MN49', 1, NULL, NULL),
(119, '684ef2b462199', 'MN43', 1, NULL, NULL),
(120, '684ef2fc01fae', 'MN49', 1, NULL, NULL),
(121, '684ef8496d5ba', 'MN52', 2, NULL, NULL),
(122, '684ef8f994050', 'MN49', 1, NULL, NULL),
(123, '684efe124d992', 'MN52', 1, NULL, NULL),
(124, '684f0182cd12d', 'MN52', 3, NULL, NULL),
(125, '684f0182cd12d', 'MN50', 3, NULL, NULL),
(126, '684f0182cd12d', 'MN49', 1, NULL, NULL),
(127, '684f0182cd12d', 'MN48', 1, NULL, NULL),
(128, '684f01a798a2f', 'MN49', 1, NULL, NULL),
(129, '684f02a322336', 'MN48', 2, NULL, NULL),
(130, '684f02a322336', 'MN52', 1, NULL, NULL),
(131, '684f04b86c9b8', 'MN50', 2, NULL, NULL),
(132, '684fc66aa6467', 'MN49', 1, 1, 2),
(133, '684fc66aa6467', 'MN48', 1, 1, 2),
(134, '684fcc2234b96', 'MN50', 1, 1, 0),
(135, '684fcc2234b96', 'MN48', 1, 1, 0),
(136, '684fcc2234b96', 'MN49', 3, 1, 0),
(137, '684fce1d3c0ac', 'MN40', 1, 1, 0),
(138, '684fce1d3c0ac', 'MN43', 1, 1, 0),
(139, 'OGT-685021f4ce3fd', 'MN39', 1, NULL, NULL),
(140, 'OGT-685021f4ce3fd', 'MN38', 1, NULL, NULL),
(141, 'OGT-6850242155064', 'MN49', 1, NULL, NULL),
(142, 'OGT-6850242155064', 'MN48', 1, NULL, NULL),
(143, 'OGT-68502b9407cf1', 'MN43', 1, NULL, NULL),
(144, 'OGT-68502c6fc52e4', 'MN48', 1, NULL, NULL),
(145, 'OGT-68502c8f92627', 'MN40', 1, NULL, NULL),
(146, 'OGT-68502cf413836', 'MN52', 1, NULL, NULL),
(147, 'OGT-68502cf413836', 'MN50', 1, NULL, NULL),
(148, 'OGT-68502cf413836', 'MN49', 1, NULL, NULL),
(149, 'OGT-68502ee893881', 'MN52', 1, NULL, NULL),
(150, 'OGT-68502ee893881', 'MN23', 1, NULL, NULL),
(151, 'OGT-685034a434324', 'MN50', 1, NULL, NULL),
(152, 'OGT-685034a434324', 'MN49', 1, NULL, NULL),
(153, 'OGT-685034a434324', 'MN48', 1, NULL, NULL),
(154, 'OGT-68504de852fd9', 'MN52', 1, NULL, NULL),
(155, 'OGT-68504de852fd9', 'MN50', 1, NULL, NULL),
(156, 'OGT-68593b90ef85a', 'MN52', 1, NULL, NULL),
(157, 'OGT-68593f68aa301', 'MN52', 1, NULL, NULL),
(158, 'OGT-68593f68aa301', 'MN50', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reset_password`
--

CREATE TABLE `reset_password` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiry` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `reset_password`
--

INSERT INTO `reset_password` (`id`, `email`, `token`, `expiry`) VALUES
(1, 'otakunoyaro@gmail.com', 'aa08fcb7dc712b10e70b4a2d8e4d19047af44833a01571ad3be9012e014d36b2da0e6306b80dd3780f0269ae75ef1573cb60', '2025-06-15 22:17:49'),
(2, 'otakunoyaro@gmail.com', 'ecbab341c43a5422f175ca869b447f5ed981314d60ea8c8347752fa6043e2f4e7d8e1a12bf4dccda0c6bb9fbc5ceb066dcec', '2025-06-15 22:36:58'),
(3, 'otakunoyaro@gmail.com', '501524701bd2b471685a44441409ac9efbca68205dc98a12823a4807b5cb2646df2179354910ea07175aeff597b4b5ea3bb1', '2025-06-15 23:50:16'),
(4, 'nuramaliakim@gmail.com', 'ccdc15c17996c430da55cf01add2be341c6ca4a87740e54bd5830d12500bddf2cc683686797d22805a30ab209f4389a0cbbe', '2025-06-16 22:57:50');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `kode_pesanan` varchar(255) NOT NULL,
  `nama_pelanggan` varchar(50) NOT NULL,
  `tanggal_transaksi` datetime NOT NULL DEFAULT current_timestamp(),
  `total_harga` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status_transaksi` enum('Pending','Selesai','Dibatalkan') DEFAULT 'Pending',
  `nomor_meja` varchar(50) DEFAULT NULL,
  `nomor_antrian` varchar(50) DEFAULT NULL,
  `jenis_pesanan` enum('Dine-in','Takeaway') DEFAULT 'Dine-in'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `kode_pesanan`, `nama_pelanggan`, `tanggal_transaksi`, `total_harga`, `status_transaksi`, `nomor_meja`, `nomor_antrian`, `jenis_pesanan`) VALUES
(37, '68440c2a43e4', 'putra', '2025-06-07 17:53:46', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(38, '68440c33337e', 'sari', '2025-06-07 17:53:55', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(39, '68458402da06', 'udin', '2025-06-08 20:37:22', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(40, '684838e4f15b', 'wahyu', '2025-06-10 21:53:40', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(41, '68483900a81b', 'mulyo', '2025-06-10 21:54:08', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(42, '6848390aa8b3', 'sari', '2025-06-10 21:54:18', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(43, '6848392189dc', 'condoriano', '2025-06-10 21:54:41', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(44, '6848394b9eb8', 'peter', '2025-06-10 21:55:23', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(45, '684839572c6f', 'jack', '2025-06-10 21:55:35', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(46, '684ab3377031a', 'tes simpan', '2025-06-12 18:00:07', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(47, '684abdc1d6906', 'kevin', '2025-06-12 18:45:05', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(48, '684bbcc93cdf5', 'Apin', '2025-06-13 12:53:13', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(49, '684d459088cad', 'mahmud', '2025-06-14 16:49:04', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(50, '684d467b18306', 'peni', '2025-06-14 16:52:59', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(51, '684ed16595779', 'wati', '2025-06-15 20:57:57', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(52, '684ed2ebf0e0f', 'sukarjo', '2025-06-15 21:04:27', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(53, '684edbc012ce0', 'ea', '2025-06-15 21:42:08', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(54, '684ef199cb924', 'Rahmi', '2025-06-15 23:15:21', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(55, '684ef2b462199', 'Pelu', '2025-06-15 23:20:04', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(56, '684ef2fc01fae', 'igit', '2025-06-15 23:21:16', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(57, '684ef8496d5ba', 'ea', '2025-06-15 23:43:53', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(58, '684ef8f994050', 'ea', '2025-06-15 23:46:49', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(59, '684efe124d992', 'waeqweqw', '2025-06-16 00:08:34', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(60, '684f0182cd12d', 'putri', '2025-06-16 00:23:14', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(61, '684f01a798a2f', 'putri', '2025-06-16 00:23:51', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(62, '684f02a322336', 'putri', '2025-06-16 00:28:03', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(63, '684f04b86c9b8', 'ea', '2025-06-16 00:36:56', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(64, '684fc66aa6467', 'aaaaaaaaaaaaaaaaaaa', '2025-06-16 14:23:22', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(65, '684fcc2234b96', 'luyo', '2025-06-16 14:47:46', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(66, '684fce1d3c0ac', 'luyoaja', '2025-06-16 14:56:13', 0.00, 'Pending', NULL, NULL, 'Dine-in'),
(67, 'OGT-685021f4ce3fd', 'ea', '2025-06-16 20:53:56', 13000.00, 'Pending', 'Meja 9', NULL, 'Dine-in'),
(68, 'OGT-6850242155064', 'putri', '2025-06-16 21:03:13', 40000.00, 'Pending', NULL, '20250616-001', 'Takeaway'),
(69, 'OGT-68502b9407cf1', 'waluyo jr', '2025-06-16 21:35:00', 18000.00, 'Pending', NULL, '20250616-002', 'Takeaway'),
(70, 'OGT-68502c6fc52e4', 'mekel', '2025-06-16 21:38:39', 20000.00, 'Pending', 'Meja 2', NULL, 'Dine-in'),
(71, 'OGT-68502c8f92627', 'peni', '2025-06-16 21:39:11', 20000.00, 'Pending', NULL, '20250616-003', 'Takeaway'),
(72, 'OGT-68502cf413836', 'rahmi', '2025-06-16 21:40:52', 60000.00, 'Pending', 'Meja 1', NULL, 'Dine-in'),
(73, 'OGT-68502ee893881', 'Bang red', '2025-06-16 21:49:12', 50000.00, 'Pending', NULL, '20250616-004', 'Takeaway'),
(74, 'OGT-685034a434324', 'yudi', '2025-06-16 22:13:40', 60000.00, 'Pending', 'Meja 13', NULL, 'Dine-in'),
(75, 'OGT-68504de852fd9', 'juna', '2025-06-17 00:01:28', 40000.00, 'Pending', NULL, '20250617-001', 'Takeaway'),
(76, 'OGT-68593b90ef85a', 'NUR AMALIA', '2025-06-23 18:33:36', 20000.00, 'Pending', 'Meja 14', NULL, 'Dine-in'),
(77, 'OGT-68593f68aa301', 'pelu', '2025-06-23 18:50:00', 40000.00, 'Pending', NULL, '20250623-001', 'Takeaway');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nohp` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `email`, `nohp`) VALUES
(1, 'rendi12', '69c796f5bbd1339f3ba3e18ce54fcc63', '', ''),
(3, 'user', 'c4ca4238a0b923820dcc509a6f75849b', '', ''),
(4, '1', 'c4ca4238a0b923820dcc509a6f75849b', '', ''),
(5, '2', 'c81e728d9d4c2f636f067f89cc14862c', '', ''),
(6, 'udin', 'c4ca4238a0b923820dcc509a6f75849b', '', ''),
(7, 'wahyu', 'c4ca4238a0b923820dcc509a6f75849b', '', ''),
(8, 'mulyono', 'c4ca4238a0b923820dcc509a6f75849b', '', ''),
(9, 'peter', 'c4ca4238a0b923820dcc509a6f75849b', '', ''),
(10, 'ea', 'c4ca4238a0b923820dcc509a6f75849b', '', ''),
(11, 'aaa', 'c4ca4238a0b923820dcc509a6f75849b', '', ''),
(12, 'tes', 'e10adc3949ba59abbe56e057f20f883e', '', ''),
(13, 'mahmud', 'c4ca4238a0b923820dcc509a6f75849b', '', ''),
(14, 'gibran', 'c4ca4238a0b923820dcc509a6f75849b', '', ''),
(15, 'mel', '25d55ad283aa400af464c76d713c07ad', '', ''),
(16, 'kevin', 'c4ca4238a0b923820dcc509a6f75849b', '', ''),
(17, 'Apin', '1fe3efa96f534c9ef8d9404f652819fb', '', ''),
(18, 'karjo', 'c4ca4238a0b923820dcc509a6f75849b', '', ''),
(19, 'hewan', 'c4ca4238a0b923820dcc509a6f75849b', 'otakunoyaro@gmail.com', '08222222222'),
(20, 'Rahmi', '827ccb0eea8a706c4c34a16891f84e7b', 'rahmiletta9@gmail.com', '087883240568'),
(21, 'Pelu17', '7b66b28ed1f611357ea4959d7fd5e44c', 'hairulpelu39@gmail.com', '081222322533'),
(22, 'bian', '8bb982d93aef0bf4ff0c0fc063be2552', 'ansyah1725@gmail.com', '085123233595'),
(23, 'waluyo', 'c4ca4238a0b923820dcc509a6f75849b', 'waluyo69@gmail.com', '085512345678'),
(24, 'ijo', 'c4ca4238a0b923820dcc509a6f75849b', 'ijo@gmail.com', '081166669999'),
(25, 'Igitt68 ', '9bc642931e83e72b064a61b966d8a0f0', 'nurantosigit@gmail.com', '081545676508'),
(26, 'nur amalia', '827ccb0eea8a706c4c34a16891f84e7b', 'nuramaliakim@gmail.com', '087819886324'),
(27, 'yudi', '827ccb0eea8a706c4c34a16891f84e7b', 'yuduiio@gmail.com', '087865433367'),
(28, 'pelu', '827ccb0eea8a706c4c34a16891f84e7b', 'pelu@gmail.com', '089900098776');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `antrian`
--
ALTER TABLE `antrian`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meja_cafe`
--
ALTER TABLE `meja_cafe`
  ADD PRIMARY KEY (`id_meja`),
  ADD UNIQUE KEY `nomor_meja` (`nomor_meja`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`);

--
-- Indexes for table `reset_password`
--
ALTER TABLE `reset_password`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `antrian`
--
ALTER TABLE `antrian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meja_cafe`
--
ALTER TABLE `meja_cafe`
  MODIFY `id_meja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `reset_password`
--
ALTER TABLE `reset_password`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
