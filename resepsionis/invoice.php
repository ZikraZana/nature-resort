<?php
/**
 * Invoice Resepsionis — Kincay Mania Hotel & Resort
 * Print-friendly invoice accessible by resepsionis (not restricted to booking owner).
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');

$bookingId = (int)($_GET['id'] ?? 0);

// Query booking (any booking, not restricted to user_id)
$stmt = db()->prepare(
    "SELECT b.*, k.nama AS kamar_nama, k.tipe AS kamar_tipe, k.harga_per_malam,
            COALESCE(u.nama, b.nama_tamu) AS tamu_nama,
            COALESCE(u.email, '') AS tamu_email,
            COALESCE(u.no_hp, b.kontak_tamu) AS tamu_telepon
     FROM booking b
     JOIN kamar k ON k.id = b.kamar_id
     LEFT JOIN users u ON u.id = b.user_id
     WHERE b.id = ?"
);
$stmt->execute([$bookingId]);
$booking = $stmt->fetch();

if (!$booking) {
    set_flash('danger', 'Booking tidak ditemukan.');
    header('Location: ' . BASE_URL . '/resepsionis/checkout.php');
    exit;
}

$jumlahMalam = (int)((strtotime($booking['tanggal_checkout']) - strtotime($booking['tanggal_checkin'])) / 86400);
$hargaKamar = $booking['harga_per_malam'] * $jumlahMalam;

// Query paket wisata
$stmtPaket = db()->prepare(
    'SELECT bpw.jumlah_peserta, bpw.subtotal, pw.nama AS paket_nama, pw.harga AS paket_harga, jw.tanggal
     FROM booking_paket_wisata bpw
     JOIN jadwal_wisata jw ON jw.id = bpw.jadwal_wisata_id
     JOIN paket_wisata pw ON pw.id = jw.paket_wisata_id
     WHERE bpw.booking_id = ?'
);
$stmtPaket->execute([$bookingId]);
$paketList = $stmtPaket->fetchAll();

$pageTitle = 'Invoice #INV-' . str_pad($booking['id'], 4, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> — Kincay Mania Hotel & Resort</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; color: #2D3319; background: #fff; font-size: 14px; }
        .invoice-container { max-width: 800px; margin: 0 auto; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #2D5016; }
        .brand h1 { font-size: 24px; color: #2D5016; margin-bottom: 4px; }
        .brand p { font-size: 12px; color: #6B705C; }
        .invoice-meta { text-align: right; }
        .invoice-meta h2 { font-size: 28px; color: #2D5016; margin-bottom: 8px; }
        .invoice-meta p { font-size: 12px; color: #6B705C; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .info-box h3 { font-size: 12px; color: #6B705C; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .info-box p { margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        thead th { background: #F4F0E5; color: #6B705C; text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
        thead th:last-child, tbody td:last-child { text-align: right; }
        tbody td { padding: 12px 16px; border-bottom: 1px solid #EDE8D5; }
        tfoot td { padding: 12px 16px; font-weight: 600; }
        tfoot tr:last-child td { font-size: 18px; color: #2D5016; border-top: 2px solid #2D5016; }
        .status-badge { display: inline-block; padding: 4px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-success { background: #D1E7DD; color: #0F5132; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #EDE8D5; text-align: center; font-size: 12px; color: #6B705C; }
        .print-btn { display: inline-block; margin: 20px auto; padding: 12px 32px; background: #2D5016; color: #fff; border: none; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .print-btn:hover { background: #3D6B22; }
        .back-btn { display: inline-block; margin-left: 12px; padding: 12px 32px; background: #F4F0E5; color: #2D3319; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; }
        @media print { .no-print { display: none !important; } .invoice-container { padding: 0; } body { print-color-adjust: exact; -webkit-print-color-adjust: exact; } }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; padding: 20px; background: #F4F0E5;">
        <button onclick="window.print()" class="print-btn">🖨️ Cetak Invoice</button>
        <a href="<?= BASE_URL ?>/resepsionis/checkout.php" class="back-btn">← Kembali</a>
    </div>
    <div class="invoice-container">
        <div class="header">
            <div class="brand"><h1>🏔️ Kincay Mania</h1><p>Hotel & Resort — Nature Resort Kerinci</p><p style="margin-top: 8px">Kerinci, Jambi, Sumatera</p></div>
            <div class="invoice-meta"><h2>INVOICE</h2><p><strong>#INV-<?= str_pad($booking['id'], 4, '0', STR_PAD_LEFT) ?></strong></p><p>Tanggal: <?= date('d M Y', strtotime($booking['created_at'])) ?></p><p style="margin-top: 8px"><span class="status-badge status-success"><?= ucfirst(str_replace('_', ' ', $booking['status'])) ?></span></p></div>
        </div>
        <div class="info-grid">
            <div class="info-box"><h3>Ditagihkan Kepada</h3><p><strong><?= e($booking['tamu_nama']) ?></strong></p><p><?= e($booking['tamu_email'] ?: '-') ?></p><p><?= e($booking['tamu_telepon'] ?: '-') ?></p></div>
            <div class="info-box" style="text-align: right;"><h3>Detail Menginap</h3><p>Check-in: <strong><?= date('d M Y', strtotime($booking['tanggal_checkin'])) ?></strong></p><p>Check-out: <strong><?= date('d M Y', strtotime($booking['tanggal_checkout'])) ?></strong></p><p>Durasi: <strong><?= $jumlahMalam ?> malam</strong></p><p>Tamu: <strong><?= $booking['jumlah_tamu'] ?> orang</strong></p></div>
        </div>
        <table>
            <thead><tr><th>Deskripsi</th><th>Qty</th><th>Harga Satuan</th><th>Subtotal</th></tr></thead>
            <tbody>
                <tr><td><strong><?= e($booking['kamar_nama']) ?></strong> (<?= e($booking['kamar_tipe']) ?>)</td><td><?= $jumlahMalam ?> malam</td><td><?= format_rupiah($booking['harga_per_malam']) ?></td><td><?= format_rupiah($hargaKamar) ?></td></tr>
                <?php $totalPaket = 0; foreach ($paketList as $pw): $totalPaket += $pw['subtotal']; ?>
                <tr><td><?= e($pw['paket_nama']) ?> <span style="color:#6B705C; font-size:12px">(<?= date('d M Y', strtotime($pw['tanggal'])) ?>)</span></td><td><?= $pw['jumlah_peserta'] ?> peserta</td><td><?= format_rupiah($pw['paket_harga']) ?></td><td><?= format_rupiah($pw['subtotal']) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><td colspan="3">Subtotal Kamar</td><td><?= format_rupiah($hargaKamar) ?></td></tr>
                <?php if ($totalPaket > 0): ?><tr><td colspan="3">Subtotal Paket Wisata</td><td><?= format_rupiah($totalPaket) ?></td></tr><?php endif; ?>
                <tr><td colspan="3"><strong>TOTAL</strong></td><td><strong><?= format_rupiah($booking['total_harga']) ?></strong></td></tr>
            </tfoot>
        </table>
        <div class="footer"><p>Terima kasih telah menginap di Kincay Mania Hotel & Resort!</p><p style="margin-top: 4px">Dokumen ini merupakan bukti booking resmi yang sah.</p></div>
    </div>
</body>
</html>
