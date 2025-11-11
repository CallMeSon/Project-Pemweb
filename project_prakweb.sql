-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2025 at 02:51 PM
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
-- Database: `project_prakweb`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `nama_kategori`) VALUES
(2, 'Biji Kopi'),
(1, 'Kopi Minuman');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status_pesanan` varchar(50) DEFAULT 'Pending',
  `tanggal_order` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(5) NOT NULL,
  `harga_saat_beli` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `nama_produk`, `harga`, `deskripsi`, `gambar_url`) VALUES
(1, 1, 'Americano', 20000.00, 'Dibuat dari shot espresso kaya kami, dilarutkan dengan air panas untuk menciptakan rasa yang berani (bold) namun lembut. Sempurna untuk penikmat kopi hitam klasik.', 'gambar/Americano.png'),
(2, 1, 'Cafe Latte', 25000.00, 'Perpaduan sempurna antara shot espresso yang kaya dengan susu steam (steamed milk) yang lembut dan lapisan tipis microfoam. Pilihan klasik untuk kopi yang creamy dan menenangkan.', 'gambar/Latte.png'),
(3, 1, 'Espresso', 18000.00, 'Ekstraksi kopi murni dengan cita rasa paling pekat dan intens. Fondasi dari semua minuman kopi kami, disajikan dalam shot kecil yang penuh kekuatan.', 'gambar/Espresso.png'),
(4, 1, 'Cappuccino', 25000.00, 'Keseimbangan sempurna antara espresso, susu steam, dan lapisan busa susu (foam) yang tebal dan mewah. Memberikan sensasi kaya rasa di setiap tegukan.', 'gambar/Cappuccino.png'),
(5, 1, 'Kopi Susu Gula Aren', 22000.00, 'Minuman favorit modern. Kombinasi espresso, susu segar, dan manisnya gula aren asli yang khas. Creamy, legit, dan pas untuk membangkitkan semangat.', 'gambar/Aren.png'),
(6, 2, 'Biji Kopi Arabika', 120000.00, 'Biji kopi paling populer. Dikenal dengan keasaman (acidity) yang cerah, body yang ringan, dan profil rasa yang kompleks, seringkali dengan nuansa buah atau bunga.', 'gambar/Arabika.png'),
(7, 2, 'Biji Kopi Robusta', 85000.00, 'Pilihan bagi pencari kafein tinggi dan rasa yang kuat. Robusta memiliki body yang lebih tebal, rasa pahit yang khas, dan seringkali diiringi nuansa kacang atau cokelat.', 'gambar/Robusta.png'),
(8, 2, 'Biji Kopi Liberika', 95000.00, 'Spesies kopi langka dengan profil rasa unik. Seringkali memiliki aroma berasap (smoky) atau buah nangka (jackfruit) dengan body yang penuh dan rasa yang berani.', 'gambar/Liberika.png'),
(9, 2, 'Biji Kopi Excelsa', 90000.00, 'Sering diklasifikasikan sebagai varietas Liberika, Excelsa menawarkan profil yang lebih kompleks. Dikenal karena rasanya yang tajam (tart) dan fruity, memberikan dimensi rasa yang berbeda.', 'gambar/Excelsa.png'),
(10, 2, 'Biji Kopi Peaberry', 130000.00, 'Mutasi alami langka di mana hanya satu biji (bukan dua) yang tumbuh di dalam buah kopi. Bentuknya bulat, menghasilkan pemanggangan lebih merata serta rasa yang lebih cerah dan manis.', 'gambar/Peaberry.png');

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL COMMENT 'Rating dari 1 sampai 5',
  `komentar` text DEFAULT NULL,
  `tanggal_review` timestamp NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `alamat`, `telepon`) VALUES
(1, 'Admin', '$2y$10$MVXkhy8uerMq13LcuYKCcOCiZ8OHat3nIkNUc1JVzmtDgA3OtbHmm', 'Admin', 'Admin Utama', '123445');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_product_unique` (`user_id`,`product_id`),
  ADD KEY `product_id_idx` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `fk_review_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
