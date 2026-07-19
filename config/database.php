<?php
/**
 * Koneksi Database — Kincay Mania Hotel & Resort
 * Menggunakan PDO dengan prepared statement (wajib, tidak boleh string concatenation SQL).
 * Sesuaikan DB_HOST, DB_NAME, DB_USER, DB_PASS dengan environment Anda.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'nature_resort_kerinci');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Singleton PDO — panggil db() di mana saja untuk mendapatkan koneksi.
 *
 * @return PDO
 * @throws RuntimeException Jika koneksi gagal.
 */
function db(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST
             . ';dbname=' . DB_NAME
             . ';charset=' . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Jangan tampilkan detail error di production — log saja
            error_log('[DB Error] ' . $e->getMessage());
            // Tampilkan halaman error generik
            http_response_code(503);
            die(<<<HTML
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Koneksi Database Gagal</title>
<style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#FDF6E3;margin:0;}
.box{text-align:center;padding:2rem;background:white;border-radius:1rem;box-shadow:0 4px 20px rgba(0,0,0,.1);max-width:400px;}
h1{color:#B91C1C;margin-bottom:.5rem;}p{color:#6B4E2E;}</style>
</head>
<body><div class="box">
<h1>⚠ Layanan Tidak Tersedia</h1>
<p>Tidak dapat terhubung ke database. Pastikan XAMPP (MySQL) sudah berjalan dan konfigurasi di <code>config/database.php</code> sudah benar.</p>
</div></body></html>
HTML);
        }
    }

    return $pdo;
}
