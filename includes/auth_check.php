<?php
/**
 * Auth Check Helper — Kincay Mania Hotel & Resort
 *
 * Include file ini di baris PALING ATAS setiap halaman terproteksi,
 * SEBELUM output HTML apa pun, tepat setelah require config.php.
 *
 * Contoh penggunaan:
 *   require_once __DIR__ . '/../includes/auth_check.php';
 *   require_role('tamu');
 */

// Pastikan config sudah di-load (session sudah start di config.php)
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/config.php';
}

/**
 * Kembalikan URL dashboard sesuai role.
 *
 * @param string $role
 * @return string
 */
function dashboard_url(string $role): string {
    return match ($role) {
        'resepsionis' => BASE_URL . '/resepsionis/index.php',
        'admin'       => BASE_URL . '/admin/index.php',
        default       => BASE_URL . '/tamu/riwayat.php',  // tamu
    };
}

/**
 * Pastikan user sudah login. Jika belum, set flash dan redirect ke login.
 * Panggil fungsi ini di awal halaman — tidak perlu argumen.
 */
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        set_flash('warning', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '');
        header('Location: ' . BASE_URL . '/auth/login.php' . ($redirect ? '?redirect=' . $redirect : ''));
        exit;
    }
}

/**
 * Pastikan user sudah login DAN memiliki role yang sesuai.
 * - Jika belum login → redirect ke halaman login.
 * - Jika login tapi role salah → redirect ke dashboard role aslinya (403-style redirect).
 *
 * @param string|array $allowed_role Role yang diizinkan, bisa string atau array.
 */
function require_role(string|array $allowed_role): void {
    require_login();

    $allowed = (array) $allowed_role;
    $user_role = $_SESSION['role'] ?? '';

    if (!in_array($user_role, $allowed, true)) {
        // Redirect ke dashboard sesuai role asli user, bukan halaman yang dicoba diakses
        header('Location: ' . dashboard_url($user_role));
        exit;
    }
}

/**
 * Jika user sudah login, redirect ke dashboard mereka.
 * Gunakan di halaman login & register agar user yang sudah login
 * tidak melihat form auth lagi.
 */
function redirect_if_logged_in(): void {
    if (!empty($_SESSION['user_id']) && !empty($_SESSION['role'])) {
        header('Location: ' . dashboard_url($_SESSION['role']));
        exit;
    }
}
