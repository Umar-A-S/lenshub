# Penjelasan Struktur Tabel:
# users: untuk fitur Role Management (memisahkan hak akses Owner dan Admin).  
# clients: database penyewa untuk mempermudah verifikasi identitas di masa depan.  
# gears: manajemen inventaris barang.  
# rentals: Menyimpan data waktu mulai (started_at) jatuh tempo (end_at). Untuk menjalankan fitur Smart Tracking Timer.  
# rental_items: Tabel pendukung untuk satu transaksi berisi banyak alat.  
# penalties: Mencatat hasil Automated Penalty.  


# Define the SQL schema for LensHub
sql_content = """-- Database Schema for LensHub (Photography & Video Gear Rental System)
-- Target: MariaDB / MySQL (Compatible with Laravel 13)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- 1. Tabel Users (Manajemen Akun Owner & Admin)
CREATE TABLE `users` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('owner', 'admin') NOT NULL DEFAULT 'admin',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabel Clients (Database Riwayat Penyewa)
CREATE TABLE `clients` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nik` VARCHAR(16) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `whatsapp_number` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `identity_photo` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clients_nik_unique` (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabel Gears (Inventaris Alat Fotografi/Videografi)
CREATE TABLE `gears` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `stock_total` INT(11) NOT NULL DEFAULT 1,
  `stock_available` INT(11) NOT NULL DEFAULT 1,
  `price_per_hour` DECIMAL(12,2) NOT NULL,
  `price_per_day` DECIMAL(12,2) NOT NULL,
  `status` ENUM('available', 'maintenance') NOT NULL DEFAULT 'available',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabel Rentals (Transaksi Utama & Penjadwalan Waktu)
CREATE TABLE `rentals` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT(20) UNSIGNED NOT NULL,
  `client_id` BIGINT(20) UNSIGNED NOT NULL,
  `total_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('active', 'completed', 'late', 'canceled') NOT NULL DEFAULT 'active',
  `started_at` TIMESTAMP NOT NULL,
  `end_at` TIMESTAMP NOT NULL, -- Waktu Jatuh Tempo (untuk Smart Tracking Timer)
  `returned_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_rentals_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rentals_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabel Rental_Items (Pivot Transaksi Multi-Alat)
CREATE TABLE `rental_items` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rental_id` BIGINT(20) UNSIGNED NOT NULL,
  `gear_id` BIGINT(20) UNSIGNED NOT NULL,
  `quantity` INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_items_rental` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_items_gear` FOREIGN KEY (`gear_id`) REFERENCES `gears` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabel Penalties (Kalkulasi Denda Otomatis)
CREATE TABLE `penalties` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rental_id` BIGINT(20) UNSIGNED NOT NULL,
  `late_duration_minutes` INT(11) NOT NULL DEFAULT 0,
  `penalty_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `reason` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_penalties_rental` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
"""

# Save the SQL content to a file
file_name = "LensHub_Database_Schema.sql"
with open(file_name, "w") as f:
    f.write(sql_content)

print(f"File created: {file_name}")