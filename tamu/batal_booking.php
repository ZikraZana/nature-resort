<?php
/**
 * Batal Booking — Kincay Mania Hotel & Resort
 * Business logic: menunggu_verifikasi → batal langsung tanpa refund
 *                 dikonfirmasi → cek batas waktu H-2, refund 50%
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');
$pageTitle = 'Pembatalan Booking';

$id = (int)($_GET['id'] ?? 0);

// Query booking milik user
$stmt = db()->prepare(
    'SELECT b.id, b.total_harga, b.status, b.tanggal_checkin, k.nama AS kamar_nama
     FROM booking b
     JOIN kamar k ON k.id = b.kamar_id
     WHERE b.id = ? AND b.user_id = ?'
);
$stmt->execute([$id, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    set_flash('danger', 'Booking tidak ditemukan.');
    header('Location: ' . BASE_URL . '/tamu/riwayat.php');
    exit;
}

// Hanya bisa batal jika status tertentu
if (!in_array($booking['status'], ['menunggu_pembayaran', 'menunggu_verifikasi', 'dikonfirmasi'])) {
    set_flash('warning', 'Booking dengan status ini tidak bisa dibatalkan.');
    header('Location: ' . BASE_URL . '/tamu/riwayat.php');
    exit;
}

// Ambil setting batas hari dan persen refund
$stmtSetting = db()->query('SELECT batas_hari_pembatalan, persen_refund FROM pengaturan_sistem LIMIT 1');
$setting = $stmtSetting->fetch();
$batasHari = $setting ? (int)$setting['batas_hari_pembatalan'] : 2;
$persenRefund = $setting ? (int)$setting['persen_refund'] : 50;

$selisihHari = (strtotime($booking['tanggal_checkin']) - time()) / 86400;
$bisaBatal = ($booking['status'] !== 'dikonfirmasi') || ($selisihHari >= $batasHari);
$adaRefund = ($booking['status'] === 'dikonfirmasi') && $bisaBatal;
$refundNominal = $adaRefund ? ($booking['total_harga'] * $persenRefund / 100) : 0;

// ── Proses POST — konfirmasi pembatalan ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi_batal'])) {
    validate_csrf();

    $bookingId = (int)($_POST['booking_id'] ?? 0);
    if ($bookingId !== $id) {
        set_flash('danger', 'Request tidak valid.');
        header('Location: ' . BASE_URL . '/tamu/riwayat.php');
        exit;
    }

    // Re-check booking status
    $stmtRecheck = db()->prepare('SELECT status, tanggal_checkin, total_harga FROM booking WHERE id = ? AND user_id = ?');
    $stmtRecheck->execute([$bookingId, $_SESSION['user_id']]);
    $recheck = $stmtRecheck->fetch();

    if (!$recheck || !in_array($recheck['status'], ['menunggu_pembayaran', 'menunggu_verifikasi', 'dikonfirmasi'])) {
        set_flash('danger', 'Booking tidak bisa dibatalkan.');
        header('Location: ' . BASE_URL . '/tamu/riwayat.php');
        exit;
    }

    $pdo = db();
    try {
        $pdo->beginTransaction();

        if ($recheck['status'] === 'dikonfirmasi') {
            // Cek batas waktu
            $selisih = (strtotime($recheck['tanggal_checkin']) - time()) / 86400;
            if ($selisih < $batasHari) {
                $pdo->rollBack();
                set_flash('danger', 'Pembatalan tidak bisa dilakukan karena sudah melewati batas waktu H-' . $batasHari . '.');
                header('Location: ' . BASE_URL . '/tamu/riwayat.php');
                exit;
            }

            // Hitung refund
            $nominalRefund = $recheck['total_harga'] * $persenRefund / 100;

            // INSERT refund
            $stmtRefund = $pdo->prepare(
                'INSERT INTO refund (booking_id, nominal_refund, status) VALUES (?, ?, ?)'
            );
            $stmtRefund->execute([$bookingId, $nominalRefund, 'menunggu']);

            // UPDATE booking status
            $stmtUpdate = $pdo->prepare('UPDATE booking SET status = ? WHERE id = ?');
            $stmtUpdate->execute(['menunggu_refund', $bookingId]);

            $pdo->commit();
            set_flash('success', 'Booking berhasil dibatalkan. Refund ' . format_rupiah($nominalRefund) . ' akan diproses oleh tim kami.');
        } else {
            // Batal langsung tanpa refund
            $stmtUpdate = $pdo->prepare('UPDATE booking SET status = ? WHERE id = ?');
            $stmtUpdate->execute(['dibatalkan', $bookingId]);

            $pdo->commit();
            set_flash('success', 'Booking berhasil dibatalkan.');
        }

        header('Location: ' . BASE_URL . '/tamu/riwayat.php');
        exit;

    } catch (\Exception $e) {
        $pdo->rollBack();
        error_log('[Batal Error] ' . $e->getMessage());
        set_flash('danger', 'Terjadi kesalahan. Silakan coba lagi.');
        header('Location: ' . BASE_URL . '/tamu/riwayat.php');
        exit;
    }
}

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
                <p class="text-earth mb-6">Booking #BK-<?= str_pad($id, 4, '0', STR_PAD_LEFT) ?> — <?= e($booking['kamar_nama']) ?></p>

                <?php if ($booking['status'] === 'dikonfirmasi' && $bisaBatal): ?>
                    <div class="p-4 bg-warning-light rounded-xl text-left mb-6">
                        <p class="font-medium text-warning mb-1">Refund <?= $persenRefund ?>% akan diberikan</p>
                        <p class="text-sm text-earth">Booking sudah dikonfirmasi. Anda akan menerima refund sebesar <strong class="text-dark"><?= format_rupiah($refundNominal) ?></strong> dari total <?= format_rupiah($booking['total_harga']) ?>.</p>
                        <p class="text-sm text-earth mt-2">Refund akan diproses oleh tim kami via transfer bank.</p>
                    </div>
                <?php elseif ($booking['status'] === 'dikonfirmasi' && !$bisaBatal): ?>
                    <div class="p-4 bg-danger-light rounded-xl text-left mb-6">
                        <p class="font-medium text-danger mb-1">Tidak dapat dibatalkan</p>
                        <p class="text-sm text-earth">Pembatalan hanya bisa dilakukan maksimal H-<?= $batasHari ?> sebelum check-in. Tanggal check-in Anda: <?= date('d M Y', strtotime($booking['tanggal_checkin'])) ?>.</p>
                    </div>
                <?php else: ?>
                    <div class="p-4 bg-cream rounded-xl text-left mb-6">
                        <p class="font-medium text-dark mb-1">Pembatalan tanpa refund</p>
                        <p class="text-sm text-earth">Booking belum diverifikasi. Pembatalan langsung tanpa biaya dan tanpa refund.</p>
                    </div>
                <?php endif; ?>

                <?php if ($bisaBatal): ?>
                <form method="POST" action="<?= BASE_URL ?>/tamu/batal_booking.php?id=<?= $id ?>">
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
