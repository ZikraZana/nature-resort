-- ============================================================
-- Migration: Tambah status 'nonaktif' pada ENUM kamar.status_default
-- Digunakan sebagai soft-delete kamar oleh sistem
-- (berbeda dari 'maintenance' yang untuk kamar sedang diperbaiki)
-- ============================================================

ALTER TABLE kamar 
  MODIFY COLUMN status_default ENUM('tersedia','maintenance','nonaktif') 
  NOT NULL DEFAULT 'tersedia';
