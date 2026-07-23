<?php
/**
 * Detail Booking — Kincay Mania Hotel & Resort
 * Query detail booking + paket wisata + pembayaran dari database.
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');

$bookingId = (int)($_GET['id'] ?? 0);

// Query booking milik user
$stmt = db()->prepare(
    'SELECT b.*, k.nama AS kamar_nama, k.tipe AS kamar_tipe, k.kapasitas AS kamar_kapasitas,
            k.harga_per_malam, k.foto AS kamar_foto
     FROM booking b
     JOIN kamar k ON k.id = b.kamar_id
     WHERE b.id = ? AND b.user_id = ?'
);
$stmt->execute([$bookingId, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    set_flash('danger', 'Booking tidak ditemukan.');
    header('Location: ' . BASE_URL . '/tamu/riwayat.php');
    exit;
}

$pageTitle = 'Detail Booking #BK-' . str_pad($booking['id'], 4, '0', STR_PAD_LEFT);
$jumlahMalam = (int)((strtotime($booking['tanggal_checkout']) - strtotime($booking['tanggal_checkin'])) / 86400);

// Query paket wisata
$stmtPaket = db()->prepare(
    'SELECT bpw.*, jw.tanggal, pw.nama AS paket_nama, pw.harga AS paket_harga
     FROM booking_paket_wisata bpw
     JOIN jadwal_wisata jw ON jw.id = bpw.jadwal_wisata_id
     JOIN paket_wisata pw ON pw.id = jw.paket_wisata_id
     WHERE bpw.booking_id = ?'
);
$stmtPaket->execute([$bookingId]);
$paketList = $stmtPaket->fetchAll();

// Query riwayat pembayaran
$stmtBayar = db()->prepare(
    'SELECT * FROM pembayaran WHERE booking_id = ? ORDER BY created_at DESC'
);
$stmtBayar->execute([$bookingId]);
$pembayaranList = $stmtBayar->fetchAll();

// Query refund jika ada
$stmtRefund = db()->prepare('SELECT * FROM refund WHERE booking_id = ? LIMIT 1');
$stmtRefund->execute([$bookingId]);
$refund = $stmtRefund->fetch();

$statusLabels = [
    'menunggu_pembayaran' => ['label' => 'Menunggu Pembayaran', 'color' => 'bg-warning-light text-warning'],
    'menunggu_verifikasi' => ['label' => 'Menunggu Verifikasi', 'color' => 'bg-info-light text-info'],
    'dikonfirmasi' => ['label' => 'Dikonfirmasi', 'color' => 'bg-success-light text-success'],
    'checkin' => ['label' => 'Check-in', 'color' => 'bg-primary/10 text-primary'],
    'selesai' => ['label' => 'Selesai', 'color' => 'bg-success-light text-success'],
    'dibatalkan' => ['label' => 'Dibatalkan', 'color' => 'bg-gray-100 text-gray-500'],
    'ditolak' => ['label' => 'Ditolak', 'color' => 'bg-danger-light text-danger'],
    'menunggu_refund' => ['label' => 'Menunggu Refund', 'color' => 'bg-warning-light text-warning'],
    'refund_selesai' => ['label' => 'Refund Selesai', 'color' => 'bg-success-light text-success'],
];

$bayarStatusLabels = [
    'menunggu' => ['label' => 'Menunggu', 'color' => 'bg-warning-light text-warning'],
    'diterima' => ['label' => 'Terverifikasi', 'color' => 'bg-success-light text-success'],
    'ditolak' => ['label' => 'Ditolak', 'color' => 'bg-danger-light text-danger'],
];

$s = $statusLabels[$booking['status']] ?? ['label' => ucfirst($booking['status']), 'color' => 'bg-gray-100 text-gray-500'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_tamu.php';
?>

    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="<?= BASE_URL ?>/tamu/riwayat.php" class="inline-flex items-center gap-2 text-earth hover:text-primary text-sm mb-6 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Riwayat
            </a>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="font-sans text-3xl text-dark font-bold">#BK-<?= str_pad($booking['id'], 4, '0', STR_PAD_LEFT) ?></h1>
                    <p class="text-earth mt-1">Dibuat: <?= date('d M Y · H:i', strtotime($booking['created_at'])) ?></p>
                </div>
                <span class="px-4 py-1.5 <?= $s['color'] ?> text-sm font-semibold rounded-full self-start"><?= $s['label'] ?></span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Detail Kamar -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm">
                        <div class="h-48 overflow-hidden">
                            <img src="<?= e($booking['kamar_foto'] ?: 'https://placehold.co/800x400/2D5016/FDF6E3?text=' . urlencode($booking['kamar_nama'])) ?>" alt="<?= e($booking['kamar_nama']) ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <h2 class="font-semibold text-dark text-xl mb-4"><?= e($booking['kamar_nama']) ?></h2>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="p-3 bg-cream rounded-xl"><span class="text-earth block text-xs">Tipe</span><span class="font-medium text-dark"><?= e($booking['kamar_tipe']) ?></span></div>
                                <div class="p-3 bg-cream rounded-xl"><span class="text-earth block text-xs">Kapasitas</span><span class="font-medium text-dark"><?= $booking['kamar_kapasitas'] ?> tamu</span></div>
                                <div class="p-3 bg-cream rounded-xl"><span class="text-earth block text-xs">Check-in</span><span class="font-medium text-dark"><?= date('d M Y', strtotime($booking['tanggal_checkin'])) ?></span></div>
                                <div class="p-3 bg-cream rounded-xl"><span class="text-earth block text-xs">Check-out</span><span class="font-medium text-dark"><?= date('d M Y', strtotime($booking['tanggal_checkout'])) ?></span></div>
                                <div class="p-3 bg-cream rounded-xl"><span class="text-earth block text-xs">Durasi</span><span class="font-medium text-dark"><?= $jumlahMalam ?> malam</span></div>
                                <div class="p-3 bg-cream rounded-xl"><span class="text-earth block text-xs">Jumlah Tamu</span><span class="font-medium text-dark"><?= $booking['jumlah_tamu'] ?> orang</span></div>
                            </div>
                            <?php if ($booking['catatan']): ?>
                            <div class="mt-4 p-3 bg-cream rounded-xl">
                                <span class="text-earth text-xs block">Catatan</span>
                                <span class="text-dark text-sm"><?= e($booking['catatan']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Paket Wisata -->
                    <?php if (!empty($paketList)): ?>
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="font-semibold text-dark mb-4">Paket Wisata</h3>
                        <div class="space-y-3">
                            <?php foreach ($paketList as $pw): ?>
                            <div class="flex justify-between items-center py-3 border-b border-cream text-sm">
                                <div>
                                    <p class="font-medium text-dark"><?= e($pw['paket_nama']) ?></p>
                                    <p class="text-earth text-xs"><?= date('d M Y', strtotime($pw['tanggal'])) ?> · <?= $pw['jumlah_peserta'] ?> peserta</p>
                                </div>
                                <span class="font-medium text-dark"><?= format_rupiah($pw['subtotal']) ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Riwayat Pembayaran -->
                    <?php if (!empty($pembayaranList)): ?>
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="font-semibold text-dark mb-4">Riwayat Pembayaran</h3>
                        <div class="space-y-3">
                            <?php foreach ($pembayaranList as $py):
                                $bs = $bayarStatusLabels[$py['status']] ?? ['label' => ucfirst($py['status']), 'color' => 'bg-gray-100 text-gray-500'];
                            ?>
                            <div class="flex items-center justify-between p-4 bg-cream rounded-xl text-sm">
                                <div class="flex items-center gap-3">
                                    <a href="<?= BASE_URL ?>/uploads/<?= e($py['bukti_transfer']) ?>" target="_blank" class="w-14 h-14 rounded-lg bg-white border border-cream-darker overflow-hidden flex items-center justify-center">
                                        <?php if (in_array(strtolower(pathinfo($py['bukti_transfer'], PATHINFO_EXTENSION)), ['jpg','jpeg','png'])): ?>
                                        <img src="<?= BASE_URL ?>/uploads/<?= e($py['bukti_transfer']) ?>" alt="Bukti" class="w-full h-full object-cover">
                                        <?php else: ?>
                                        <svg class="w-6 h-6 text-earth" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        <?php endif; ?>
                                    </a>
                                    <div>
                                        <p class="font-medium text-dark"><?= format_rupiah($py['nominal']) ?></p>
                                        <p class="text-earth text-xs"><?= date('d M Y · H:i', strtotime($py['created_at'])) ?></p>
                                        <?php if ($py['alasan_penolakan']): ?>
                                        <p class="text-danger text-xs mt-1">Alasan: <?= e($py['alasan_penolakan']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span class="px-3 py-1 <?= $bs['color'] ?> text-xs font-medium rounded-full"><?= $bs['label'] ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Refund Info -->
                    <?php if ($refund): ?>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-warning">
                        <h3 class="font-semibold text-dark mb-4">Informasi Refund</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-earth">Nominal Refund</span><span class="font-bold text-warning"><?= format_rupiah($refund['nominal_refund']) ?></span></div>
                            <div class="flex justify-between"><span class="text-earth">Status</span><span class="font-medium text-dark"><?= ucfirst($refund['status']) ?></span></div>
                            <?php if ($refund['tanggal_refund']): ?>
                            <div class="flex justify-between"><span class="text-earth">Tanggal Refund</span><span class="text-dark"><?= date('d M Y', strtotime($refund['tanggal_refund'])) ?></span></div>
                            <?php endif; ?>
                            <?php if ($refund['bukti_refund']): ?>
                            <a href="<?= BASE_URL ?>/uploads/<?= e($refund['bukti_refund']) ?>" target="_blank" class="inline-flex items-center gap-1 text-primary hover:underline text-sm mt-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Lihat Bukti Refund
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar: Ringkasan Harga -->
                <div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-28">
                        <h3 class="font-semibold text-dark mb-4">Ringkasan Harga</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between"><span class="text-earth">Kamar (<?= $jumlahMalam ?> malam)</span><span class="text-dark"><?= format_rupiah($booking['harga_per_malam'] * $jumlahMalam) ?></span></div>
                            <?php
                            $totalPaket = 0;
                            foreach ($paketList as $pw) {
                                $totalPaket += $pw['subtotal'];
                            }
                            if ($totalPaket > 0):
                            ?>
                            <div class="flex justify-between"><span class="text-earth">Paket Wisata</span><span class="text-dark"><?= format_rupiah($totalPaket) ?></span></div>
                            <?php endif; ?>
                            <div class="border-t border-cream-dark pt-3">
                                <div class="flex justify-between"><span class="font-semibold text-dark text-base">Total</span><span class="font-bold text-primary text-xl"><?= format_rupiah($booking['total_harga']) ?></span></div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 space-y-3">
                            <?php if ($booking['status'] === 'menunggu_pembayaran'): ?>
                            <a href="<?= BASE_URL ?>/tamu/upload_bukti.php?booking_id=<?= $booking['id'] ?>" class="block w-full py-3 bg-primary hover:bg-primary-light text-white text-center font-semibold rounded-xl transition-all">Upload Bukti</a>
                            <?php endif; ?>
                            <?php if ($booking['status'] === 'ditolak'): ?>
                            <a href="<?= BASE_URL ?>/tamu/upload_bukti.php?booking_id=<?= $booking['id'] ?>" class="block w-full py-3 bg-info hover:bg-info/80 text-white text-center font-semibold rounded-xl transition-all">Upload Ulang</a>
                            <?php endif; ?>
                            <?php if (in_array($booking['status'], ['menunggu_pembayaran', 'menunggu_verifikasi', 'dikonfirmasi'])): ?>
                            <a href="<?= BASE_URL ?>/tamu/batal_booking.php?id=<?= $booking['id'] ?>" class="block w-full py-3 border border-danger/30 text-danger text-center font-semibold rounded-xl hover:bg-danger-light transition-all">Batalkan Booking</a>
                            <?php endif; ?>
                            <?php if (in_array($booking['status'], ['dikonfirmasi', 'checkin', 'selesai'])): ?>
                            <a href="<?= BASE_URL ?>/tamu/invoice.php?id=<?= $booking['id'] ?>" class="block w-full py-3 border border-earth/30 text-dark text-center font-semibold rounded-xl hover:bg-cream transition-all">Cetak Invoice</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
