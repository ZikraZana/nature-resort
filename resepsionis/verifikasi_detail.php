<?php
/**
 * Detail Verifikasi Pembayaran — Resepsionis
 * Review bukti transfer, approve/reject pembayaran.
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Detail Verifikasi';

$id = (int)($_GET['id'] ?? 0);

// Query pembayaran detail
$stmt = db()->prepare(
    "SELECT p.*, b.id AS booking_id, b.total_harga, b.tanggal_checkin, b.tanggal_checkout,
            k.nama AS kamar_nama,
            COALESCE(u.nama, b.nama_tamu) AS tamu_nama,
            COALESCE(u.email, '') AS tamu_email,
            COALESCE(u.no_hp, b.kontak_tamu) AS tamu_telepon
     FROM pembayaran p
     JOIN booking b ON b.id = p.booking_id
     JOIN kamar k ON k.id = b.kamar_id
     LEFT JOIN users u ON u.id = b.user_id
     WHERE p.id = ? AND p.status = 'menunggu'"
);
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    set_flash('danger', 'Data pembayaran tidak ditemukan atau sudah diproses.');
    header('Location: ' . BASE_URL . '/resepsionis/verifikasi.php');
    exit;
}

$errors = [];

// ── Proses POST — approve/reject ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();

    $pembayaranId = (int)($_POST['pembayaran_id'] ?? 0);
    $aksi = $_POST['aksi'] ?? '';

    if ($pembayaranId !== $id) {
        set_flash('danger', 'Request tidak valid.');
        header('Location: ' . BASE_URL . '/resepsionis/verifikasi.php');
        exit;
    }

    if ($aksi === 'approve') {
        $pdo = db();
        try {
            $pdo->beginTransaction();

            // UPDATE pembayaran
            $stmtUpdate = $pdo->prepare(
                'UPDATE pembayaran SET status = ?, diverifikasi_oleh = ? WHERE id = ?'
            );
            $stmtUpdate->execute(['diterima', $_SESSION['user_id'], $id]);

            // UPDATE booking status
            $stmtBooking = $pdo->prepare('UPDATE booking SET status = ? WHERE id = ?');
            $stmtBooking->execute(['dikonfirmasi', $data['booking_id']]);

            $pdo->commit();
            set_flash('success', 'Pembayaran booking #BK-' . str_pad($data['booking_id'], 4, '0', STR_PAD_LEFT) . ' berhasil diverifikasi!');
            header('Location: ' . BASE_URL . '/resepsionis/verifikasi.php');
            exit;

        } catch (\Exception $e) {
            $pdo->rollBack();
            error_log('[Verifikasi Error] ' . $e->getMessage());
            $errors[] = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    } elseif ($aksi === 'reject') {
        $alasan = trim($_POST['alasan'] ?? '');
        if (empty($alasan)) {
            $errors[] = 'Alasan penolakan wajib diisi.';
        } else {
            $pdo = db();
            try {
                $pdo->beginTransaction();

                // UPDATE pembayaran
                $stmtUpdate = $pdo->prepare(
                    'UPDATE pembayaran SET status = ?, alasan_penolakan = ?, diverifikasi_oleh = ? WHERE id = ?'
                );
                $stmtUpdate->execute(['ditolak', $alasan, $_SESSION['user_id'], $id]);

                // UPDATE booking status
                $stmtBooking = $pdo->prepare('UPDATE booking SET status = ? WHERE id = ?');
                $stmtBooking->execute(['ditolak', $data['booking_id']]);

                $pdo->commit();
                set_flash('success', 'Pembayaran booking #BK-' . str_pad($data['booking_id'], 4, '0', STR_PAD_LEFT) . ' ditolak.');
                header('Location: ' . BASE_URL . '/resepsionis/verifikasi.php');
                exit;

            } catch (\Exception $e) {
                $pdo->rollBack();
                error_log('[Verifikasi Error] ' . $e->getMessage());
                $errors[] = 'Terjadi kesalahan. Silakan coba lagi.';
            }
        }
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_resepsionis.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="<?= BASE_URL ?>/resepsionis/verifikasi.php" class="inline-flex items-center gap-2 text-earth hover:text-primary text-sm mb-6 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali</a>

            <!-- Errors -->
            <?php if (!empty($errors)): ?>
            <div class="mb-6 p-4 bg-danger-light rounded-xl text-sm text-danger">
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <h1 class="font-sans text-3xl text-dark font-bold mb-8">Review Pembayaran #BK-<?= str_pad($data['booking_id'], 4, '0', STR_PAD_LEFT) ?></h1>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Bukti Transfer -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-semibold text-dark mb-4">Bukti Transfer</h2>
                    <div class="rounded-xl overflow-hidden border border-cream-dark">
                        <?php
                        $buktiExt = strtolower(pathinfo($data['bukti_transfer'], PATHINFO_EXTENSION));
                        if (in_array($buktiExt, ['jpg','jpeg','png'])):
                        ?>
                        <a href="<?= BASE_URL ?>/uploads/<?= e($data['bukti_transfer']) ?>" target="_blank">
                            <img src="<?= BASE_URL ?>/uploads/<?= e($data['bukti_transfer']) ?>" alt="Bukti transfer" class="w-full">
                        </a>
                        <?php else: ?>
                        <div class="p-8 text-center">
                            <svg class="w-16 h-16 mx-auto text-earth/30 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <a href="<?= BASE_URL ?>/uploads/<?= e($data['bukti_transfer']) ?>" target="_blank" class="text-primary hover:underline font-medium">Buka File PDF</a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="mt-4 p-4 bg-cream rounded-xl text-sm">
                        <div class="flex justify-between mb-1"><span class="text-earth">Nominal Transfer:</span><span class="font-bold text-primary text-lg"><?= format_rupiah($data['nominal']) ?></span></div>
                        <div class="flex justify-between"><span class="text-earth">Total Booking:</span><span class="font-bold text-dark"><?= format_rupiah($data['total_harga']) ?></span></div>
                        <?php if ($data['nominal'] >= $data['total_harga']): ?>
                        <p class="mt-2 text-success text-xs font-medium flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Nominal sesuai</p>
                        <?php else: ?>
                        <p class="mt-2 text-warning text-xs font-medium flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg> Nominal kurang dari total booking</p>
                        <?php endif; ?>
                        <p class="text-xs text-earth mt-2">Diupload: <?= date('d M Y H:i', strtotime($data['created_at'])) ?></p>
                    </div>
                </div>

                <!-- Detail Booking & Aksi -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h2 class="font-semibold text-dark mb-4">Detail Booking</h2>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Tamu</span><span class="font-medium"><?= e($data['tamu_nama']) ?></span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Email</span><span class="font-medium"><?= e($data['tamu_email'] ?: '-') ?></span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">No. HP</span><span class="font-medium"><?= e($data['tamu_telepon'] ?: '-') ?></span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Kamar</span><span class="font-medium"><?= e($data['kamar_nama']) ?></span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Check-in</span><span class="font-medium"><?= date('d M Y', strtotime($data['tanggal_checkin'])) ?></span></div>
                            <div class="flex justify-between py-2"><span class="text-earth">Check-out</span><span class="font-medium"><?= date('d M Y', strtotime($data['tanggal_checkout'])) ?></span></div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h2 class="font-semibold text-dark mb-4">Keputusan Verifikasi</h2>
                        <form method="POST" action="<?= BASE_URL ?>/resepsionis/verifikasi_detail.php?id=<?= $id ?>" class="space-y-4">
                            <?= csrf_field() ?>
                            <input type="hidden" name="pembayaran_id" value="<?= $id ?>">
                            <button type="submit" name="aksi" value="approve" class="w-full py-3 bg-success hover:bg-success/90 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Approve — Konfirmasi Pembayaran
                            </button>
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1">Alasan Penolakan (wajib jika tolak)</label>
                                <textarea name="alasan" rows="3" placeholder="Contoh: Nominal tidak sesuai, bukti tidak jelas, dll."
                                          class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-sm text-dark placeholder-earth/40 focus:border-primary transition-colors resize-none"></textarea>
                            </div>
                            <button type="submit" name="aksi" value="reject" class="w-full py-3 bg-danger hover:bg-danger/90 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Tolak Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
