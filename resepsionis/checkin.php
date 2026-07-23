<?php
/** Check-in — Resepsionis */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Proses Check-in';

// ── Proses POST — check-in ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proses_checkin'])) {
    validate_csrf();
    $bookingId = (int)($_POST['booking_id'] ?? 0);

    $stmt = db()->prepare('SELECT id, status FROM booking WHERE id = ? AND status = ?');
    $stmt->execute([$bookingId, 'dikonfirmasi']);
    $booking = $stmt->fetch();

    if ($booking) {
        $stmt = db()->prepare('UPDATE booking SET status = ? WHERE id = ?');
        $stmt->execute(['checkin', $bookingId]);
        set_flash('success', 'Check-in berhasil untuk booking #BK-' . str_pad($bookingId, 4, '0', STR_PAD_LEFT) . '!');
    } else {
        set_flash('danger', 'Booking tidak ditemukan atau status tidak valid.');
    }
    header('Location: ' . BASE_URL . '/resepsionis/checkin.php');
    exit;
}

// Query booking yang dikonfirmasi dengan tanggal checkin hari ini atau sudah lewat
$stmt = db()->prepare(
    'SELECT b.id, b.tanggal_checkin, b.tanggal_checkout, b.jumlah_tamu, b.total_harga,
            k.nama AS kamar_nama,
            COALESCE(u.nama, b.nama_tamu) AS tamu_nama
     FROM booking b
     JOIN kamar k ON k.id = b.kamar_id
     LEFT JOIN users u ON u.id = b.user_id
     WHERE b.status = ? AND b.tanggal_checkin <= ?
     ORDER BY b.tanggal_checkin ASC'
);
$stmt->execute(['dikonfirmasi', date('Y-m-d')]);
$daftarCheckin = $stmt->fetchAll();

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

            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Proses Check-in</h1>
            <p class="text-earth mb-8">Booking dikonfirmasi dengan tanggal check-in hari ini (<?= date('d M Y') ?>).</p>
            <?php if (empty($daftarCheckin)): ?>
            <div class="bg-white rounded-2xl p-12 shadow-sm text-center"><p class="text-earth">Tidak ada tamu yang check-in hari ini.</p></div>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($daftarCheckin as $c): ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1"><span class="text-sm font-mono text-earth">#BK-<?= str_pad($c['id'], 4, '0', STR_PAD_LEFT) ?></span><span class="px-2 py-0.5 bg-success-light text-success text-xs rounded-full">Dikonfirmasi</span></div>
                        <h3 class="font-semibold text-dark text-lg"><?= e($c['tamu_nama']) ?></h3>
                        <p class="text-sm text-earth"><?= e($c['kamar_nama']) ?> · <?= $c['jumlah_tamu'] ?> tamu · <?= date('d M', strtotime($c['tanggal_checkin'])) ?> — <?= date('d M Y', strtotime($c['tanggal_checkout'])) ?></p>
                    </div>
                    <form method="POST" action="<?= BASE_URL ?>/resepsionis/checkin.php">
                        <?= csrf_field() ?>
                        <input type="hidden" name="booking_id" value="<?= $c['id'] ?>">
                        <button type="submit" name="proses_checkin" class="px-6 py-2.5 bg-success hover:bg-success/90 text-white font-semibold rounded-full transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Check-in
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
