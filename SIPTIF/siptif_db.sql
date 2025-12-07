-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Nov 2025 pada 14.48
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `siptif_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_kunjungan`
--

CREATE TABLE `data_kunjungan` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `masuk` time NOT NULL,
  `keluar` time DEFAULT NULL,
  `keperluan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_kunjungan`
--

INSERT INTO `data_kunjungan` (`id`, `nama`, `email`, `tanggal`, `masuk`, `keluar`, `keperluan`, `created_at`) VALUES
(1, 'Ahmad Rizki', 'ahmad@gmail.com', '2024-01-15', '09:30:00', '11:45:00', 'Meminjam Buku', '2025-11-29 13:39:21'),
(2, 'Siti Nurhaliza', 'siti@yahoo.com', '2024-01-15', '10:15:00', '12:30:00', 'Mengunjungi Perpustakaan', '2025-11-29 13:39:21'),
(3, 'Budi Santoso', 'budi@polibatam.ac.id', '2024-01-16', '08:45:00', '10:20:00', 'Meminjam Alat', '2025-11-29 13:39:21'),
(4, 'Maya Sari', 'maya@gmail.com', '2024-01-16', '13:20:00', '15:10:00', 'Meminjam Ruangan', '2025-11-29 13:39:21'),
(5, 'Rizky Pratama', 'rizky@student.polibatam.ac.id', '2024-01-17', '14:00:00', NULL, 'Meminjam Kunci Ruangan', '2025-11-29 13:39:21'),
(6, 'Akbar Zamroni', 'mas.akbarzamroni@gmail.com', '2025-11-29', '11:11:00', NULL, 'Meminjam Ruangan', '2025-11-29 13:45:09');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `data_kunjungan`
--
ALTER TABLE `data_kunjungan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `data_kunjungan`
--
ALTER TABLE `data_kunjungan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
