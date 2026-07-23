<?php
/**
 * Proses Refund — Upload Bukti — Resepsionis
 * Upload bukti transfer refund & update status.
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Proses Refund';

$id = (int)($_GET['id'] ?? 0);

// Query refund detail
$stmt = db()->prepare(
    "SELECT r.*, b.id AS booking_id, b.total_harga,
            k.nama AS kamar_nama,
            COALESCE(u.nama, b.nama_tamu) AS tamu_nama,
            COALESCE(u.no_hp, b.kontak_tamu) AS tamu_telepon,
            COALESCE(u.email, '') AS tamu_email
     FROM refund r
     JOIN booking b ON b.id = r.booking_id
     JOIN kamar k ON k.id = b.kamar_id
     LEFT JOIN users u ON u.id = b.user_id
     WHERE r.id = ? AND r.status = 'menunggu'"
);
$stmt->execute([$id]);
$refund = $stmt->fetch();

if (!$refund) {
    set_flash('danger', 'Data refund tidak ditemukan atau sudah diproses.');
    header('Location: ' . BASE_URL . '/resepsionis/refund.php');
    exit;
}

$errors = [];

// ── Proses POST — upload bukti refund ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proses_refund'])) {
    validate_csrf();

    $refundId = (int)($_POST['refund_id'] ?? 0);
    if ($refundId !== $id) {
        set_flash('danger', 'Request tidak valid.');
        header('Location: ' . BASE_URL . '/resepsionis/refund.php');
        exit;
    }

    // Validasi file
    if (empty($_FILES['bukti_refund']['name'])) {
        $errors[] = 'File bukti refund wajib diupload.';
    } else {
        $file = $_FILES['bukti_refund'];
        if ($file['error'] !== UPLOAD_ERR_OK) $errors[] = 'Gagal mengupload file.';
        if ($file['size'] > MAX_FILE_SIZE) $errors[] = 'Ukuran file maksimal 2MB.';

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExt = array_merge(ALLOWED_IMAGE_EXT, ALLOWED_DOC_EXT);
        if (!in_array($ext, $allowedExt)) $errors[] = 'Format file harus JPG, JPEG, PNG, atau PDF.';
    }

    if (empty($errors)) {
        $filename = 'refund_' . $refund['booking_id'] . '_' . time() . '.' . $ext;
        $uploadPath = UPLOAD_DIR . $filename;

        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $pdo = db();
            try {
                $pdo->beginTransaction();

                // UPDATE refund
                $stmtUpdate = $pdo->prepare(
                    'UPDATE refund SET bukti_refund = ?, diproses_oleh = ?, status = ?, tanggal_refund = CURDATE() WHERE id = ?'
                );
                $stmtUpdate->execute([$filename, $_SESSION['user_id'], 'selesai', $id]);

                // UPDATE booking status
                $stmtBooking = $pdo->prepare('UPDATE booking SET status = ? WHERE id = ?');
                $stmtBooking->execute(['refund_selesai', $refund['booking_id']]);

                $pdo->commit();
                set_flash('success', 'Refund berhasil diproses! Bukti transfer telah disimpan.');
                header('Location: ' . BASE_URL . '/resepsionis/refund.php');
                exit;

            } catch (\Exception $e) {
                $pdo->rollBack();
                @unlink($uploadPath);
                error_log('[Refund Error] ' . $e->getMessage());
                $errors[] = 'Terjadi kesalahan saat memproses refund.';
            }
        } else {
            $errors[] = 'Gagal menyimpan file. Silakan coba lagi.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_resepsionis.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-xl mx-auto px-4">
            <a href="<?= BASE_URL ?>/resepsionis/refund.php" class="inline-flex items-center gap-2 text-earth hover:text-primary text-sm mb-6 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali</a>

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

            <div class="bg-white rounded-2xl p-8 shadow-sm">
                <h1 class="font-sans text-2xl text-dark font-bold mb-6">Proses Refund</h1>
                <div class="p-4 bg-cream rounded-xl mb-6 text-sm space-y-2">
                    <div class="flex justify-between"><span class="text-earth">Booking:</span><span class="font-medium">#BK-<?= str_pad($refund['booking_id'], 4, '0', STR_PAD_LEFT) ?></span></div>
                    <div class="flex justify-between"><span class="text-earth">Tamu:</span><span class="font-medium"><?= e($refund['tamu_nama']) ?></span></div>
                    <div class="flex justify-between"><span class="text-earth">Kontak:</span><span class="font-medium"><?= e($refund['tamu_telepon'] ?: $refund['tamu_email']) ?></span></div>
                    <div class="flex justify-between"><span class="text-earth">Kamar:</span><span class="font-medium"><?= e($refund['kamar_nama']) ?></span></div>
                    <div class="flex justify-between"><span class="text-earth">Total Booking:</span><span class="font-medium"><?= format_rupiah($refund['total_harga']) ?></span></div>
                    <div class="flex justify-between border-t border-cream-darker pt-2"><span class="text-earth font-medium">Nominal Refund:</span><span class="font-bold text-primary text-lg"><?= format_rupiah($refund['nominal_refund']) ?></span></div>
                </div>
                <form method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>/resepsionis/refund_proses.php?id=<?= $id ?>" class="space-y-5">
                    <?= csrf_field() ?>
                    <input type="hidden" name="refund_id" value="<?= $id ?>">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Upload Bukti Transfer Refund</label>
                        <div class="border-2 border-dashed border-cream-darker rounded-xl p-6 text-center hover:border-primary/50 transition-colors">
                            <p class="text-sm text-earth mb-2">Screenshot bukti transfer balik ke tamu</p>
                            <input type="file" name="bukti_refund" accept=".jpg,.jpeg,.png,.pdf" required class="mt-2 w-full text-sm text-earth file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                        </div>
                    </div>
                    <button type="submit" name="proses_refund" class="w-full py-3.5 bg-success hover:bg-success/90 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Konfirmasi Refund Selesai
                    </button>
                </form>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
