-- ============================================================
-- Schema Update: Kincay Mania Hotel & Resort
-- Kolom tambahan yang dibutuhkan oleh form frontend yang sudah ada
-- Jalankan SETELAH schema.sql utama sudah di-import
-- ============================================================

USE nature_resort_kerinci;

-- 1. Tambah kolom fasilitas di tabel kamar (dipakai di admin/kamar_form.php)
ALTER TABLE kamar ADD COLUMN fasilitas TEXT NULL AFTER deskripsi;

-- 2. Tambah kolom tambahan di paket_wisata (dipakai di admin/paket_form.php)
ALTER TABLE paket_wisata ADD COLUMN durasi VARCHAR(50) NULL AFTER harga;
ALTER TABLE paket_wisata ADD COLUMN jam_mulai TIME NULL AFTER durasi;
ALTER TABLE paket_wisata ADD COLUMN titik_kumpul VARCHAR(150) NULL AFTER jam_mulai;
ALTER TABLE paket_wisata ADD COLUMN termasuk TEXT NULL AFTER titik_kumpul;
ALTER TABLE paket_wisata ADD COLUMN status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif' AFTER foto;

-- 3. Tambah kolom guide di jadwal_wisata (dipakai di admin/kelola_jadwal.php)
ALTER TABLE jadwal_wisata ADD COLUMN guide VARCHAR(150) NULL AFTER kuota_maksimal;

-- 4. Tambah kolom status dan alamat di users
ALTER TABLE users ADD COLUMN status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif' AFTER role;
ALTER TABLE users ADD COLUMN alamat VARCHAR(255) NULL AFTER no_hp;
