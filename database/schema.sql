-- ============================================================
-- Database Schema: Kincay Mania Hotel & Resort (Nature Resort Kerinci)
-- Diturunkan dari ERD hasil perancangan sistem
-- Engine: MySQL / MariaDB (XAMPP)
-- ============================================================

CREATE DATABASE IF NOT EXISTS nature_resort_kerinci
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nature_resort_kerinci;

-- ------------------------------------------------------------
-- USERS
-- ------------------------------------------------------------
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  no_hp VARCHAR(20),
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('tamu','resepsionis','admin') NOT NULL DEFAULT 'tamu',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- KAMAR
-- ------------------------------------------------------------
CREATE TABLE kamar (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(150) NOT NULL,
  tipe VARCHAR(100),
  kapasitas INT NOT NULL,
  harga_per_malam DECIMAL(12,2) NOT NULL,
  deskripsi TEXT,
  foto VARCHAR(255),
  status_default ENUM('tersedia','maintenance') NOT NULL DEFAULT 'tersedia'
);

-- ------------------------------------------------------------
-- PAKET WISATA
-- ------------------------------------------------------------
CREATE TABLE paket_wisata (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(150) NOT NULL,
  kategori ENUM('trekking','perahu','kuliner') NOT NULL,
  deskripsi TEXT,
  harga DECIMAL(12,2) NOT NULL,
  foto VARCHAR(255)
);

-- ------------------------------------------------------------
-- JADWAL WISATA (kuota per tanggal)
-- ------------------------------------------------------------
CREATE TABLE jadwal_wisata (
  id INT AUTO_INCREMENT PRIMARY KEY,
  paket_wisata_id INT NOT NULL,
  tanggal DATE NOT NULL,
  kuota_maksimal INT NOT NULL,
  FOREIGN KEY (paket_wisata_id) REFERENCES paket_wisata(id)
    ON UPDATE CASCADE ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- BOOKING
-- user_id NULL      => booking walk-in (dibuat_oleh WAJIB terisi)
-- dibuat_oleh NULL  => booking online oleh tamu (user_id WAJIB terisi)
-- ------------------------------------------------------------
CREATE TABLE booking (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  kamar_id INT NOT NULL,
  dibuat_oleh INT NULL,
  nama_tamu VARCHAR(150) NULL,
  kontak_tamu VARCHAR(50) NULL,
  tanggal_checkin DATE NOT NULL,
  tanggal_checkout DATE NOT NULL,
  jumlah_tamu INT NOT NULL DEFAULT 1,
  catatan TEXT,
  total_harga DECIMAL(12,2) NOT NULL,
  status ENUM(
    'menunggu_pembayaran',
    'menunggu_verifikasi',
    'dikonfirmasi',
    'ditolak',
    'checkin',
    'selesai',
    'menunggu_refund',
    'refund_selesai',
    'dibatalkan'
  ) NOT NULL DEFAULT 'menunggu_pembayaran',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (kamar_id) REFERENCES kamar(id),
  FOREIGN KEY (dibuat_oleh) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_booking_tanggal (tanggal_checkin, tanggal_checkout),
  INDEX idx_booking_status (status)
);

-- ------------------------------------------------------------
-- BOOKING <-> PAKET WISATA (many-to-many via jadwal_wisata)
-- ------------------------------------------------------------
CREATE TABLE booking_paket_wisata (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  jadwal_wisata_id INT NOT NULL,
  jumlah_peserta INT NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (booking_id) REFERENCES booking(id) ON DELETE CASCADE,
  FOREIGN KEY (jadwal_wisata_id) REFERENCES jadwal_wisata(id)
);

-- ------------------------------------------------------------
-- PEMBAYARAN (one-to-many terhadap booking: mendukung reject -> upload ulang)
-- ------------------------------------------------------------
CREATE TABLE pembayaran (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  bukti_transfer VARCHAR(255) NOT NULL,
  nominal DECIMAL(12,2) NOT NULL,
  status ENUM('menunggu','diterima','ditolak') NOT NULL DEFAULT 'menunggu',
  alasan_penolakan VARCHAR(255) NULL,
  diverifikasi_oleh INT NULL,
  tanggal_verifikasi DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES booking(id) ON DELETE CASCADE,
  FOREIGN KEY (diverifikasi_oleh) REFERENCES users(id) ON DELETE SET NULL
);

-- ------------------------------------------------------------
-- REFUND (0 atau 1 per booking, hanya jika dibatalkan dari status dikonfirmasi)
-- ------------------------------------------------------------
CREATE TABLE refund (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL UNIQUE,
  nominal_refund DECIMAL(12,2) NOT NULL,
  bukti_refund VARCHAR(255) NULL,
  diproses_oleh INT NULL,
  status ENUM('menunggu','selesai') NOT NULL DEFAULT 'menunggu',
  tanggal_refund DATETIME NULL,
  FOREIGN KEY (booking_id) REFERENCES booking(id) ON DELETE CASCADE,
  FOREIGN KEY (diproses_oleh) REFERENCES users(id) ON DELETE SET NULL
);

-- ------------------------------------------------------------
-- PENGATURAN SISTEM (singleton, idealnya hanya 1 baris)
-- ------------------------------------------------------------
CREATE TABLE pengaturan_sistem (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_bank VARCHAR(100),
  no_rekening VARCHAR(50),
  nama_pemilik_rekening VARCHAR(150),
  persen_refund INT NOT NULL DEFAULT 50,
  batas_hari_pembatalan INT NOT NULL DEFAULT 2,
  kebijakan_pembatalan TEXT
);

-- Seed data awal pengaturan sistem
INSERT INTO pengaturan_sistem
  (nama_bank, no_rekening, nama_pemilik_rekening, persen_refund, batas_hari_pembatalan, kebijakan_pembatalan)
VALUES
  ('Bank Jambi', '0000000000', 'Kincay Mania Hotel & Resort', 50, 2,
   'Pembatalan hanya dapat dilakukan maksimal H-2 sebelum tanggal check-in. Refund sebesar 50% dari total pembayaran hanya berlaku untuk booking yang sudah berstatus Dikonfirmasi.');

-- Seed akun admin default (password: admin123 -- WAJIB diganti setelah setup)
-- Hash di bawah ini contoh, generate ulang dengan password_hash() di PHP
-- INSERT INTO users (nama, email, no_hp, password_hash, role)
-- VALUES ('Admin Utama', 'admin@kincaymania.test', '081234567890', '<hasil_password_hash>', 'admin');
