-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: sql103.infinityfree.com
-- Üretim Zamanı: 26 Ara 2025, 14:15:18
-- Sunucu sürümü: 11.4.7-MariaDB
-- PHP Sürümü: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `if0_40106441_bitirme`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `id` int(11) NOT NULL,
  `ad_soyad` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `rol` enum('musteri','yonetici') NOT NULL DEFAULT 'musteri'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`id`, `ad_soyad`, `email`, `sifre`, `rol`) VALUES
(3, 'Admin Ad?', 'admin@mail.com', 'hashlenmis_sifre', 'yonetici'),
(1, 'admin', 'admin@halisaha.com', '$2y$10$MPozjxiBAMoVP6/4llUhsOMrQSw9X.I5DP2yGaeRt0.fjSkAjSoym', 'yonetici'),
(4, 'yasin k?l?c', 'klcyasin07@gmail.com', '$2y$10$TW1/Krn9d7X8ZcnwjtLesusn5jgsXwjgUov36CLSTpCQPB4ICSMdK', 'musteri'),
(6, 'fatih aydın tat', 'fatih@gmail.com', '$2y$10$xK5pP7fvMDhHn8kSHFqwuO1qacnIuBVQQlo82vKRBn7PO8b6AG5Qe', 'yonetici');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `rezervasyonlar`
--

CREATE TABLE `rezervasyonlar` (
  `id` int(11) NOT NULL,
  `saha_id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `tarih` date NOT NULL,
  `baslangic_saati` time NOT NULL,
  `bitis_saati` time NOT NULL,
  `durum` enum('onay bekliyor','onayland?','iptal') NOT NULL DEFAULT 'onay bekliyor',
  `kayit_tarihi` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `rezervasyonlar`
--

INSERT INTO `rezervasyonlar` (`id`, `saha_id`, `kullanici_id`, `tarih`, `baslangic_saati`, `bitis_saati`, `durum`, `kayit_tarihi`) VALUES
(1, 1, 4, '2025-12-24', '15:00:00', '16:00:00', '', '2025-12-24 19:26:58');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sahalar`
--

CREATE TABLE `sahalar` (
  `id` int(11) NOT NULL,
  `saha_adi` varchar(100) NOT NULL,
  `saatlik_fiyat` decimal(10,2) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `ozellikler` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `sahalar`
--

INSERT INTO `sahalar` (`id`, `saha_adi`, `saatlik_fiyat`, `aciklama`, `ozellikler`) VALUES
(1, 'tosmur', '150.00', NULL, NULL),
(2, 'Olive Garden Saha', '200.00', NULL, NULL),
(3, 'Oba', '170.00', NULL, NULL);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Tablo için indeksler `rezervasyonlar`
--
ALTER TABLE `rezervasyonlar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unik_rezervasyon` (`saha_id`,`tarih`,`baslangic_saati`),
  ADD KEY `kullanici_id` (`kullanici_id`);

--
-- Tablo için indeksler `sahalar`
--
ALTER TABLE `sahalar`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `rezervasyonlar`
--
ALTER TABLE `rezervasyonlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `sahalar`
--
ALTER TABLE `sahalar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
