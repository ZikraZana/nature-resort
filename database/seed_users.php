<?php
/**
 * Seed Akun Demo — Kincay Mania Hotel & Resort
 *
 * Cara pakai (via terminal/CMD, setelah XAMPP MySQL jalan & schema.sql sudah diimport):
 *   cd database
 *   php seed_users.php
 *
 * Script ini membuat hash password ASLI lewat password_hash() PHP kamu sendiri
 * (bukan hash tebakan), lalu insert ke tabel users. Aman dijalankan berkali-kali —
 * baris yang emailnya sudah ada otomatis dilewati.
 */

require_once __DIR__ . '/../config/database.php';

$akunDemo = [
    ['nama' => 'Admin Utama',   'email' => 'admin@kincaymania.com',       'no_hp' => '081200000001', 'password' => 'admin123',        'role' => 'admin'],
    ['nama' => 'Siti',          'email' => 'siti@kincaymania.com',        'no_hp' => '081200000002', 'password' => 'resepsionis123',  'role' => 'resepsionis'],
    ['nama' => 'Budi Tamu',     'email' => 'tamu@kincaymania.com',        'no_hp' => '081200000003', 'password' => 'tamu12345',        'role' => 'tamu'],
];

$pdo = db();

$cekStmt    = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$insertStmt = $pdo->prepare(
    'INSERT INTO users (nama, email, no_hp, password_hash, role, created_at)
     VALUES (?, ?, ?, ?, ?, NOW())'
);

foreach ($akunDemo as $akun) {
    $cekStmt->execute([$akun['email']]);

    if ($cekStmt->fetch()) {
        echo "SKIP  — {$akun['email']} sudah ada.\n";
        continue;
    }

    $hash = password_hash($akun['password'], PASSWORD_DEFAULT);
    $insertStmt->execute([
        $akun['nama'],
        $akun['email'],
        $akun['no_hp'],
        $hash,
        $akun['role'],
    ]);

    echo "OK    — {$akun['role']}: {$akun['email']} / {$akun['password']}\n";
}

echo "\nSelesai. Silakan login pakai kredensial di atas.\n";
