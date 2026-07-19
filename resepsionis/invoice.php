<?php
/** Invoice Resepsionis — same as tamu invoice but accessed by staff */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
// Redirect to tamu invoice with same params
$id = (int)($_GET['id'] ?? 1);
header('Location: ' . BASE_URL . '/tamu/invoice.php?id=' . $id);
exit;
