<?php
/**
 * Logout — Kincay Mania Hotel & Resort
 * Hancurkan session lalu redirect ke landing page.
 */
require_once __DIR__ . '/../config/config.php';

// Hapus semua data session
session_unset();
session_destroy();

// Redirect ke landing page guest
header('Location: ' . BASE_URL . '/guest/');
exit;
