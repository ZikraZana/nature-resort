<?php
/** Check-out — Resepsionis */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Proses Check-out';

// ── Proses POST — check-out ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proses_checkout'])) {
    validate_csrf();
    $bookingId = (int)($_POST['booking_id'] ?? 0);

    $stmt = db()->prepare('SELECT id, status FROM booking WHERE id = ? AND status = ?');
    $stmt->execute([$bookingId, 'checkin']);
    $booking = $stmt->fetch();

    if ($booking) {
        $stmt = db()->prepare('UPDATE booking SET status = ? WHERE id = ?');
        $stmt->execute(['selesai', $bookingId]);
        set_flash('success', 'Check-out berhasil untuk booking #BK-' . str_pad($bookingId, 4, '0', STR_PAD_LEFT) . '!');
    } else {
        set_flash('danger', 'Booking tidak ditemukan atau status tidak valid.');
    }
    header('Location: ' . BASE_URL . '/resepsionis/checkout.php');
    exit;
}

// Query booking berstatus checkin
$stmt = db()->prepare(
    'SELECT b.id, b.tanggal_checkin, b.tanggal_checkout, b.jumlah_tamu, b.total_harga,
            k.nama AS kamar_nama,
            COALESCE(u.nama, b.nama_tamu) AS tamu_nama
     FROM booking b
     JOIN kamar k ON k.id = b.kamar_id
     LEFT JOIN users u ON u.id = b.user_id
     WHERE b.status = ?
     ORDER BY b.tanggal_checkout ASC'
);
$stmt->execute(['checkin']);
$daftarCheckout = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_resepsionis.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php $flash = get_flash(); if ($flash): ?>
            <div class="mb-6 p-4 bg-<?= $flash['type'] === 'success' ? 'success-light' : 'danger-light' ?> rounded-xl text-sm text-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
                <?= e($flash['message']) ?>
            </div>
            <?php endif; ?>

            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Proses Check-out</h1>
            <p class="text-earth mb-8">Booking berstatus check-in yang siap untuk di-checkout.</p>
            <?php if (empty($daftarCheckout)): ?>
            <div class="bg-white rounded-2xl p-12 shadow-sm text-center"><p class="text-earth">Tidak ada tamu yang perlu check-out saat ini.</p></div>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($daftarCheckout as $c): ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1"><span class="text-sm font-mono text-earth">#BK-<?= str_pad($c['id'], 4, '0', STR_PAD_LEFT) ?></span><span class="px-2 py-0.5 bg-primary/10 text-primary text-xs rounded-full">Check-in</span></div>
                        <h3 class="font-semibold text-dark text-lg"><?= e($c['tamu_nama']) ?></h3>
                        <p class="text-sm text-earth"><?= e($c['kamar_nama']) ?> · <?= date('d M', strtotime($c['tanggal_checkin'])) ?> — <?= date('d M Y', strtotime($c['tanggal_checkout'])) ?></p>
                    </div>
                    <div class="flex gap-2">
                        <a href="<?= BASE_URL ?>/resepsionis/invoice.php?id=<?= $c['id'] ?>" class="px-4 py-2 bg-cream hover:bg-cream-dark text-dark text-xs font-medium rounded-full transition-colors">Cetak Invoice</a>
                        <form method="POST" action="<?= BASE_URL ?>/resepsionis/checkout.php">
                            <?= csrf_field() ?>
                            <input type="hidden" name="booking_id" value="<?= $c['id'] ?>">
                            <button type="submit" name="proses_checkout" class="px-6 py-2 bg-earth hover:bg-earth-light text-white font-semibold rounded-full transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Check-out
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
