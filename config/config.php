<?php
/**
 * Konfigurasi Umum — Kincay Mania Hotel & Resort
 */

// Muat koneksi database (PDO)
require_once __DIR__ . '/database.php';

// Deteksi base URL secara dinamis
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE_URL', $protocol . '://' . $host . '/nature-resort');

// Info situs
define('SITE_NAME', 'Kincay Mania Hotel & Resort');
define('SITE_TAGLINE', 'Nature Resort di Jantung Kerinci');

// Upload config (untuk nanti)
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_IMAGE_EXT', ['jpg', 'jpeg', 'png']);
define('ALLOWED_DOC_EXT', ['pdf']);

// Start session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Helper: escape output untuk XSS prevention
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Helper: format harga ke Rupiah
 */
function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Helper: generate CSRF token
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Helper: render hidden CSRF input
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Helper: cek apakah halaman aktif (untuk navbar)
 */
function is_active($path) {
    $current = $_SERVER['REQUEST_URI'] ?? '';
    return strpos($current, $path) !== false ? 'text-accent font-semibold' : '';
}

/**
 * Helper: flash message (untuk nanti dengan backend)
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Helper: validasi CSRF token dari POST request.
 * Terminasi dengan 403 jika token tidak valid.
 */
function validate_csrf(): void {
    $submitted = $_POST['csrf_token'] ?? '';
    $expected  = $_SESSION['csrf_token'] ?? '';

    if (empty($submitted) || empty($expected) || !hash_equals($expected, $submitted)) {
        http_response_code(403);
        die('Invalid CSRF token. Silakan kembali dan coba lagi.');
    }

    // Regenerate token setelah validasi untuk mencegah replay
    unset($_SESSION['csrf_token']);
}
