<?php
/** Batal Booking — Kincay Mania Hotel & Resort */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');
$pageTitle = 'Pembatalan Booking';
$id = (int)($_GET['id'] ?? 1);
// Dummy: status dikonfirmasi, refund 50%
$booking = ['id' => $id, 'kamar' => 'Suite Kerinci C1', 'checkin' => '2026-08-01', 'total' => 4600000, 'status' => 'dikonfirmasi'];
$refundNominal = $booking['total'] * 0.5;
$batasHari = 2;
$selisihHari = (strtotime($booking['checkin']) - time()) / 86400;
$bisaBatal = $selisihHari >= $batasHari;

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_tamu.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-lg mx-auto px-4">
            <a href="<?= BASE_URL ?>/tamu/riwayat.php" class="inline-flex items-center gap-2 text-earth hover:text-primary text-sm mb-6 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali
            </a>

            <div class="bg-white rounded-2xl p-8 shadow-sm text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-danger-light flex items-center justify-center">
                    <svg class="w-8 h-8 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h1 class="font-sans text-2xl text-dark font-bold mb-2">Batalkan Booking?</h1>
                <p class="text-earth mb-6">Booking #BK-<?= str_pad($id, 4, '0', STR_PAD_LEFT) ?> — <?= e($booking['kamar']) ?></p>

                <?php if ($booking['status'] === 'dikonfirmasi' && $bisaBatal): ?>
                    <div class="p-4 bg-warning-light rounded-xl text-left mb-6">
                        <p class="font-medium text-warning mb-1">Refund 50% akan diberikan</p>
                        <p class="text-sm text-earth">Booking sudah dikonfirmasi. Anda akan menerima refund sebesar <strong class="text-dark"><?= format_rupiah($refundNominal) ?></strong> dari total <?= format_rupiah($booking['total']) ?>.</p>
                        <p class="text-sm text-earth mt-2">Refund akan diproses oleh tim kami via transfer bank.</p>
                    </div>
                <?php elseif ($booking['status'] === 'dikonfirmasi' && !$bisaBatal): ?>
                    <div class="p-4 bg-danger-light rounded-xl text-left mb-6">
                        <p class="font-medium text-danger mb-1">Tidak dapat dibatalkan</p>
                        <p class="text-sm text-earth">Pembatalan hanya bisa dilakukan maksimal H-<?= $batasHari ?> sebelum check-in. Tanggal check-in Anda: <?= date('d M Y', strtotime($booking['checkin'])) ?>.</p>
                    </div>
                <?php else: ?>
                    <div class="p-4 bg-cream rounded-xl text-left mb-6">
                        <p class="font-medium text-dark mb-1">Pembatalan tanpa refund</p>
                        <p class="text-sm text-earth">Booking belum diverifikasi. Pembatalan langsung tanpa biaya dan tanpa refund.</p>
                    </div>
                <?php endif; ?>

                <?php if ($booking['status'] !== 'dikonfirmasi' || $bisaBatal): ?>
                <form method="POST" action="<?= BASE_URL ?>/tamu/batal_booking.php">
                    <?= csrf_field() ?>
                    <input type="hidden" name="booking_id" value="<?= $id ?>">
                    <div class="flex gap-3">
                        <a href="<?= BASE_URL ?>/tamu/riwayat.php" class="flex-1 py-3 border border-cream-darker text-earth hover:text-dark font-medium rounded-xl text-center transition-colors">Tidak, Kembali</a>
                        <button type="submit" name="konfirmasi_batal" class="flex-1 py-3 bg-danger hover:bg-danger/90 text-white font-semibold rounded-xl transition-colors">Ya, Batalkan</button>
                    </div>
                </form>
                <?php else: ?>
                <a href="<?= BASE_URL ?>/tamu/riwayat.php" class="block w-full py-3 bg-primary hover:bg-primary-light text-white font-semibold rounded-xl text-center transition-colors">Kembali ke Riwayat</a>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
