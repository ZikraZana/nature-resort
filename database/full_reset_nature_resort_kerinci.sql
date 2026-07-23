-- ============================================================
-- FULL RESET — Kincay Mania Hotel & Resort (Nature Resort Kerinci)
-- File ini sudah menggabungkan:
--   - schema.sql (struktur awal)
--   - schema_update.sql (kolom tambahan: fasilitas, durasi, dll)
--   - migration_kamar_nonaktif.sql (status kamar 'nonaktif')
--   - seed_users.php (3 akun demo, hash sudah final)
--   - seed_data_kamar_paket_wisata.sql (data kamar & paket wisata)
--
-- CARA PAKAI:
-- 1. Di phpMyAdmin, drop dulu database lama (kalau ada), atau langsung
--    Import file ini (sudah ada DROP DATABASE IF EXISTS di bawah)
-- 2. Tab Import -> pilih file ini -> Go
-- 3. Selesai, tidak perlu import file lain apa pun lagi
-- ============================================================

DROP DATABASE IF EXISTS nature_resort_kerinci;
CREATE DATABASE nature_resort_kerinci
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nature_resort_kerinci;

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- USERS
-- ------------------------------------------------------------
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  no_hp VARCHAR(20),
  alamat VARCHAR(255) NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('tamu','resepsionis','admin') NOT NULL DEFAULT 'tamu',
  status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
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
  fasilitas TEXT NULL,
  foto VARCHAR(255),
  status_default ENUM('tersedia','maintenance','nonaktif') NOT NULL DEFAULT 'tersedia'
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
  durasi VARCHAR(50) NULL,
  jam_mulai TIME NULL,
  titik_kumpul VARCHAR(150) NULL,
  termasuk TEXT NULL,
  foto VARCHAR(255),
  status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif'
);

-- ------------------------------------------------------------
-- JADWAL WISATA (kuota per tanggal)
-- ------------------------------------------------------------
CREATE TABLE jadwal_wisata (
  id INT AUTO_INCREMENT PRIMARY KEY,
  paket_wisata_id INT NOT NULL,
  tanggal DATE NOT NULL,
  kuota_maksimal INT NOT NULL,
  guide VARCHAR(150) NULL,
  FOREIGN KEY (paket_wisata_id) REFERENCES paket_wisata(id)
    ON UPDATE CASCADE ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- BOOKING
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
-- BOOKING <-> PAKET WISATA
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
-- PEMBAYARAN
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
-- REFUND
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
-- PENGATURAN SISTEM
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

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Pengaturan sistem (default)
INSERT INTO pengaturan_sistem (nama_bank, no_rekening, nama_pemilik_rekening, persen_refund, batas_hari_pembatalan, kebijakan_pembatalan) VALUES
('Bank Jambi', '0000000000', 'Kincay Mania Hotel & Resort', 50, 2,
 'Pembatalan hanya dapat dilakukan maksimal H-2 sebelum tanggal check-in. Refund sebesar 50% dari total pembayaran hanya berlaku untuk booking yang sudah berstatus Dikonfirmasi.');

-- Akun demo (SAMA PERSIS dengan yang sudah kamu pakai testing selama ini,
-- password tidak berubah — hash diambil langsung dari database kamu sebelumnya)
INSERT INTO users (nama, email, no_hp, alamat, password_hash, role, status, created_at) VALUES
('Admin Utama', 'admin@kincaymania.com', '081200000001', NULL, '$2y$10$aiHYGSVh0bb8p1gyAqk2.O8AU.QoU7Kj2u8Jozrq1Y.cFl7DML0M6', 'admin', 'aktif', NOW()),
('Siti', 'siti@kincaymania.com', '081200000002', NULL, '$2y$10$rGqinibCFnBZDeQrKVAhgOgzcOHEkMHLdQKp.ZndEtqz4VcH1RSJe', 'resepsionis', 'aktif', NOW()),
('Budi Tamu', 'tamu@kincaymania.com', '081200000003', NULL, '$2y$10$2uhTAKQ0IWD3cPwo68kl.u/Alvk8jO3z75kcszCAhAPTUkC8SOaTa', 'tamu', 'aktif', NOW());

-- ------------------------------------------------------------
-- KAMAR
-- ------------------------------------------------------------
INSERT INTO kamar (nama, tipe, kapasitas, harga_per_malam, deskripsi, fasilitas, foto, status_default) VALUES
('Kabin Rindu Alam', 'Kabin Standard', 2, 450000.00,
 'Kabin kayu sederhana dengan pemandangan langsung ke perbukitan Kerinci. Cocok untuk pasangan atau solo traveler yang ingin ketenangan.',
 'Kasur queen size
Kamar mandi dalam (air panas)
Balkon pribadi
Wifi
Sarapan untuk 2 orang',
 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800',
 'tersedia'),

('Kabin Embun Pagi', 'Kabin Standard', 2, 480000.00,
 'Kabin dengan desain terbuka menghadap kebun teh, udara sejuk khas dataran tinggi Kerinci sepanjang hari.',
 'Kasur queen size
Kamar mandi dalam (air panas)
Teras dengan kursi santai
Wifi
Sarapan untuk 2 orang',
 'https://images.unsplash.com/photo-1499793983690-e29da59ef1c2?w=800',
 'tersedia'),

('Villa Danau Kerinci', 'Villa Keluarga', 4, 950000.00,
 'Villa 2 kamar tidur dengan pemandangan Danau Kerinci, cocok untuk keluarga atau rombongan kecil.',
 '2 kamar tidur (1 queen + 2 single)
2 kamar mandi dalam
Ruang tamu
Dapur kecil
Wifi
Sarapan untuk 4 orang',
 'https://images.unsplash.com/photo-1449844908441-8829872d2607?w=800',
 'tersedia'),

('Villa Puncak Tujuh', 'Villa Keluarga', 6, 1350000.00,
 'Villa terbesar dengan 3 kamar tidur, cocok untuk keluarga besar, menghadap langsung ke Gunung Kerinci.',
 '3 kamar tidur
2 kamar mandi dalam
Ruang keluarga
Dapur lengkap
BBQ area pribadi
Wifi
Sarapan untuk 6 orang',
 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800',
 'tersedia'),

('Kabin Kabut Senja', 'Kabin Standard', 3, 550000.00,
 'Kabin dengan extra bed, terletak paling dekat dengan area trekking, favorit tamu yang ikut paket wisata alam.',
 'Kasur queen size + extra bed
Kamar mandi dalam (air panas)
Wifi
Sarapan untuk 3 orang
Loker penyimpanan alat trekking',
 'https://images.unsplash.com/photo-1587061949409-02df41d5e562?w=800',
 'tersedia'),

('Kabin Renovasi Utara', 'Kabin Standard', 2, 420000.00,
 'Kabin sedang dalam perbaikan atap, sementara tidak menerima booking.',
 'Kasur queen size
Kamar mandi dalam
Wifi',
 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=800',
 'maintenance');

-- ------------------------------------------------------------
-- PAKET WISATA
-- ------------------------------------------------------------
INSERT INTO paket_wisata (nama, kategori, deskripsi, harga, durasi, jam_mulai, titik_kumpul, termasuk, foto, status) VALUES
('Trekking Gunung Kerinci (Basecamp)', 'trekking',
 'Trekking ringan menuju pos 1-2 jalur pendakian Gunung Kerinci, cocok untuk pemula, menikmati hutan hujan tropis dan udara segar pegunungan.',
 175000.00, '4 jam', '06:00:00', 'Lobby Kincay Mania Resort',
 'Guide lokal
Snack & air mineral
Asuransi perjalanan
Dokumentasi foto',
 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=800',
 'aktif'),

('Susur Perahu Danau Kerinci', 'perahu',
 'Menyusuri Danau Kerinci dengan perahu tradisional, melihat aktivitas nelayan lokal dan pemandangan matahari terbit.',
 125000.00, '2 jam', '05:30:00', 'Dermaga Desa Sanggaran Agung',
 'Perahu & pemandu
Jaket pelampung
Air mineral
Dokumentasi foto',
 'https://images.unsplash.com/photo-1509233725247-49e657c54213?w=800',
 'aktif'),

('Wisata Kuliner Lokal Kerinci', 'kuliner',
 'Mencicipi kuliner khas Kerinci seperti gulai ikan semah, dendeng batokok, dan kopi robusta lokal, sambil belajar proses memasaknya bersama warga.',
 95000.00, '3 jam', '11:00:00', 'Lobby Kincay Mania Resort',
 'Pemandu lokal
Bahan & alat masak
Makan siang
Kopi khas Kerinci',
 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800',
 'aktif'),

('Trekking Air Terjun Telun Berasap', 'trekking',
 'Trekking menuju air terjun ikonik Telun Berasap, jalur menengah dengan pemandangan tebing dan kabut alami.',
 150000.00, '5 jam', '07:00:00', 'Lobby Kincay Mania Resort',
 'Guide lokal
Snack & air mineral
Asuransi perjalanan
Tiket masuk kawasan',
 'https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?w=800',
 'aktif'),

('Paket Kuliner Malam (Nonaktif Sementara)', 'kuliner',
 'Paket kuliner malam sedang tidak tersedia karena vendor lokal sedang cuti.',
 85000.00, '2 jam', '19:00:00', 'Lobby Kincay Mania Resort',
 'Pemandu lokal
Makan malam',
 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800',
 'nonaktif');

-- ------------------------------------------------------------
-- JADWAL WISATA (tanggal relatif ke hari import, otomatis selalu relevan)
-- ------------------------------------------------------------
INSERT INTO jadwal_wisata (paket_wisata_id, tanggal, kuota_maksimal, guide) VALUES
(1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), 5, 'Pak Rahmat'),
(1, DATE_ADD(CURDATE(), INTERVAL 3 DAY), 5, 'Pak Rahmat'),
(1, DATE_ADD(CURDATE(), INTERVAL 5 DAY), 8, 'Bang Yudi'),

(2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), 10, 'Pak Herman'),
(2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), 10, 'Pak Herman'),
(2, DATE_ADD(CURDATE(), INTERVAL 4 DAY), 10, 'Bang Doni'),

(3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), 15, 'Ibu Ratna'),
(3, DATE_ADD(CURDATE(), INTERVAL 3 DAY), 15, 'Ibu Ratna'),

(4, DATE_ADD(CURDATE(), INTERVAL 3 DAY), 1, 'Pak Joni'),
(4, DATE_ADD(CURDATE(), INTERVAL 6 DAY), 6, 'Pak Joni');

-- Catatan: paket_wisata id 5 ('Paket Kuliner Malam') sengaja tidak diberi
-- jadwal karena statusnya 'nonaktif'.

-- ============================================================
-- SELESAI. Database siap dipakai, tidak perlu import file lain.
-- ============================================================
