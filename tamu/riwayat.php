<?php
/**
 * Riwayat Booking — Kincay Mania Hotel & Resort
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');
$pageTitle = 'Riwayat Booking';

// Dummy booking history
$bookingList = [
    ['id' => 1, 'kamar' => 'Kabin Pinus A1', 'checkin' => '2026-07-20', 'checkout' => '2026-07-23', 'total' => 2350000, 'status' => 'menunggu_pembayaran', 'created' => '2026-07-16 10:30:00'],
    ['id' => 2, 'kamar' => 'Kamar Deluxe B1', 'checkin' => '2026-07-25', 'checkout' => '2026-07-27', 'total' => 1500000, 'status' => 'menunggu_verifikasi', 'created' => '2026-07-15 14:00:00'],
    ['id' => 3, 'kamar' => 'Suite Kerinci C1', 'checkin' => '2026-08-01', 'checkout' => '2026-08-04', 'total' => 4600000, 'status' => 'dikonfirmasi', 'created' => '2026-07-10 09:00:00'],
    ['id' => 4, 'kamar' => 'Standard Room D1', 'checkin' => '2026-06-15', 'checkout' => '2026-06-17', 'total' => 600000, 'status' => 'selesai', 'created' => '2026-06-10 11:00:00'],
    ['id' => 5, 'kamar' => 'Kabin Pinus A2', 'checkin' => '2026-06-20', 'checkout' => '2026-06-22', 'total' => 900000, 'status' => 'dibatalkan', 'created' => '2026-06-18 16:00:00'],
    ['id' => 6, 'kamar' => 'Kamar Deluxe B1', 'checkin' => '2026-07-05', 'checkout' => '2026-07-07', 'total' => 1500000, 'status' => 'menunggu_refund', 'created' => '2026-07-01 08:00:00', 'refund_nominal' => 750000],
    ['id' => 7, 'kamar' => 'Kabin Pinus A1', 'checkin' => '2026-05-10', 'checkout' => '2026-05-12', 'total' => 900000, 'status' => 'refund_selesai', 'created' => '2026-05-05 12:00:00', 'refund_nominal' => 450000],
    ['id' => 8, 'kamar' => 'Standard Room D1', 'checkin' => '2026-06-01', 'checkout' => '2026-06-02', 'total' => 300000, 'status' => 'ditolak', 'created' => '2026-05-28 10:00:00'],
];

$statusLabels = [
    'menunggu_pembayaran' => ['label' => 'Menunggu Pembayaran', 'color' => 'bg-warning-light text-warning', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    'menunggu_verifikasi' => ['label' => 'Menunggu Verifikasi', 'color' => 'bg-info-light text-info', 'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
    'dikonfirmasi' => ['label' => 'Dikonfirmasi', 'color' => 'bg-success-light text-success', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    'checkin' => ['label' => 'Check-in', 'color' => 'bg-primary/10 text-primary', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
    'selesai' => ['label' => 'Selesai', 'color' => 'bg-success-light text-success', 'icon' => 'M5 13l4 4L19 7'],
    'dibatalkan' => ['label' => 'Dibatalkan', 'color' => 'bg-gray-100 text-gray-500', 'icon' => 'M6 18L18 6M6 6l12 12'],
    'ditolak' => ['label' => 'Ditolak', 'color' => 'bg-danger-light text-danger', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
    'menunggu_refund' => ['label' => 'Menunggu Refund', 'color' => 'bg-warning-light text-warning', 'icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6'],
    'refund_selesai' => ['label' => 'Refund Selesai', 'color' => 'bg-success-light text-success', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_tamu.php';
?>

    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="font-sans text-3xl text-dark font-bold">Riwayat Booking</h1>
                    <p class="text-earth mt-1">Kelola semua reservasi Anda di sini.</p>
                </div>
                <a href="<?= BASE_URL ?>/tamu/kamar.php" class="px-5 py-2.5 bg-accent hover:bg-accent-light text-dark font-semibold rounded-full transition-all hover:shadow-lg text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Booking Baru
                </a>
            </div>

            <div class="space-y-4">
                <?php foreach ($bookingList as $b):
                    $s = $statusLabels[$b['status']];
                ?>
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-sm font-mono text-earth">#BK-<?= str_pad($b['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                    <span class="px-3 py-1 <?= $s['color'] ?> text-xs font-medium rounded-full"><?= $s['label'] ?></span>
                                </div>
                                <h3 class="font-sans text-lg font-semibold text-dark"><?= e($b['kamar']) ?></h3>
                                <div class="flex flex-wrap gap-4 mt-2 text-sm text-earth">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <?= date('d M', strtotime($b['checkin'])) ?> — <?= date('d M Y', strtotime($b['checkout'])) ?>
                                    </span>
                                    <span>Dibuat: <?= date('d M Y H:i', strtotime($b['created'])) ?></span>
                                </div>
                                <?php if (!empty($b['refund_nominal'])): ?>
                                <p class="text-sm mt-2 text-warning font-medium">Refund: <?= format_rupiah($b['refund_nominal']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-2xl font-bold text-primary"><?= format_rupiah($b['total']) ?></p>
                                <div class="flex gap-2 mt-3 justify-end">
                                    <?php if ($b['status'] === 'menunggu_pembayaran'): ?>
                                    <a href="<?= BASE_URL ?>/tamu/upload_bukti.php?booking_id=<?= $b['id'] ?>" class="px-4 py-2 bg-primary hover:bg-primary-light text-white text-xs font-medium rounded-full transition-colors">Upload Bukti</a>
                                    <a href="<?= BASE_URL ?>/tamu/batal_booking.php?id=<?= $b['id'] ?>" class="px-4 py-2 border border-danger/30 text-danger text-xs font-medium rounded-full hover:bg-danger-light transition-colors" data-confirm="Yakin ingin membatalkan booking ini?">Batalkan</a>
                                    <?php endif; ?>
                                    <?php if ($b['status'] === 'menunggu_verifikasi'): ?>
                                    <a href="<?= BASE_URL ?>/tamu/batal_booking.php?id=<?= $b['id'] ?>" class="px-4 py-2 border border-danger/30 text-danger text-xs font-medium rounded-full hover:bg-danger-light transition-colors" data-confirm="Yakin ingin membatalkan booking ini?">Batalkan</a>
                                    <?php endif; ?>
                                    <?php if ($b['status'] === 'dikonfirmasi'): ?>
                                    <a href="<?= BASE_URL ?>/tamu/batal_booking.php?id=<?= $b['id'] ?>" class="px-4 py-2 border border-warning/30 text-warning text-xs font-medium rounded-full hover:bg-warning-light transition-colors" data-confirm="Pembatalan akan dikenakan biaya 50%. Lanjutkan?">Batalkan (Refund 50%)</a>
                                    <?php endif; ?>
                                    <?php if ($b['status'] === 'ditolak'): ?>
                                    <a href="<?= BASE_URL ?>/tamu/upload_bukti.php?booking_id=<?= $b['id'] ?>" class="px-4 py-2 bg-info hover:bg-info/80 text-white text-xs font-medium rounded-full transition-colors">Upload Ulang</a>
                                    <?php endif; ?>
                                    <a href="<?= BASE_URL ?>/tamu/booking_detail.php?id=<?= $b['id'] ?>" class="px-4 py-2 bg-cream hover:bg-cream-dark text-dark text-xs font-medium rounded-full transition-colors">Detail</a>
                                    <?php if (in_array($b['status'], ['dikonfirmasi', 'checkin', 'selesai'])): ?>
                                    <a href="<?= BASE_URL ?>/tamu/invoice.php?id=<?= $b['id'] ?>" class="px-4 py-2 bg-cream hover:bg-cream-dark text-dark text-xs font-medium rounded-full transition-colors">Invoice</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
