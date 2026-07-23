<?php
/**
 * Upload Bukti Transfer — Kincay Mania Hotel & Resort
 * Upload file bukti transfer sungguhan, INSERT ke tabel pembayaran.
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');
$pageTitle = 'Upload Bukti Transfer';

$bookingId = (int)($_GET['booking_id'] ?? 0);

// Query booking milik user yang login
$stmt = db()->prepare(
    'SELECT b.id, b.total_harga, b.status, k.nama AS kamar_nama
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

// Hanya bisa upload jika status menunggu_pembayaran atau ditolak (upload ulang)
if (!in_array($booking['status'], ['menunggu_pembayaran', 'ditolak'])) {
    set_flash('warning', 'Bukti transfer tidak bisa diupload untuk booking dengan status ini.');
    header('Location: ' . BASE_URL . '/tamu/riwayat.php');
    exit;
}

// Query info bank
$stmtBank = db()->query('SELECT nama_bank, no_rekening, nama_pemilik_rekening FROM pengaturan_sistem LIMIT 1');
$bank = $stmtBank->fetch();
if (!$bank) {
    $bank = ['nama_bank' => '-', 'no_rekening' => '-', 'nama_pemilik_rekening' => '-'];
}

$errors = [];

// ── Proses POST — upload bukti ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_bukti'])) {
    validate_csrf();

    $nominal = (float)($_POST['nominal'] ?? 0);

    // Validasi nominal
    if ($nominal <= 0) {
        $errors[] = 'Nominal transfer wajib diisi.';
    }

    // Validasi file
    if (empty($_FILES['bukti_transfer']['name'])) {
        $errors[] = 'File bukti transfer wajib diupload.';
    } else {
        $file = $_FILES['bukti_transfer'];

        // Cek error upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Gagal mengupload file. Silakan coba lagi.';
        }

        // Cek ukuran (maks 2MB)
        if ($file['size'] > MAX_FILE_SIZE) {
            $errors[] = 'Ukuran file maksimal 2MB.';
        }

        // Cek tipe file
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExt = array_merge(ALLOWED_IMAGE_EXT, ALLOWED_DOC_EXT);
        if (!in_array($ext, $allowedExt)) {
            $errors[] = 'Format file harus JPG, JPEG, PNG, atau PDF.';
        }
    }

    if (empty($errors)) {
        // Simpan file dengan nama unik
        $filename = 'bukti_' . $bookingId . '_' . time() . '.' . $ext;
        $uploadPath = UPLOAD_DIR . $filename;

        // Pastikan folder uploads ada
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $pdo = db();
            try {
                $pdo->beginTransaction();

                // INSERT pembayaran
                $stmt = $pdo->prepare(
                    'INSERT INTO pembayaran (booking_id, bukti_transfer, nominal, status)
                     VALUES (?, ?, ?, ?)'
                );
                $stmt->execute([$bookingId, $filename, $nominal, 'menunggu']);

                // UPDATE status booking
                $stmt = $pdo->prepare('UPDATE booking SET status = ? WHERE id = ?');
                $stmt->execute(['menunggu_verifikasi', $bookingId]);

                $pdo->commit();

                set_flash('success', 'Bukti transfer berhasil diupload! Tim kami akan memverifikasi pembayaran Anda.');
                header('Location: ' . BASE_URL . '/tamu/riwayat.php');
                exit;

            } catch (\Exception $e) {
                $pdo->rollBack();
                // Hapus file yang sudah diupload
                @unlink($uploadPath);
                error_log('[Upload Error] ' . $e->getMessage());
                $errors[] = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
            }
        } else {
            $errors[] = 'Gagal menyimpan file. Silakan coba lagi.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_tamu.php';
?>

    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Message -->
            <?php $flash = get_flash(); if ($flash): ?>
            <div class="mb-6 p-4 bg-<?= $flash['type'] === 'success' ? 'success-light' : 'danger-light' ?> rounded-xl text-sm text-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
                <?= e($flash['message']) ?>
            </div>
            <?php endif; ?>

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

            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary/10 flex items-center justify-center">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </div>
                <h1 class="font-sans text-3xl text-dark font-bold mb-2">Upload Bukti Transfer</h1>
                <p class="text-earth">Booking #BK-<?= str_pad($bookingId, 4, '0', STR_PAD_LEFT) ?> — <?= e($booking['kamar_nama']) ?></p>
            </div>

            <!-- Bank Info -->
            <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
                <h3 class="font-semibold text-dark mb-3">Informasi Transfer</h3>
                <div class="p-4 bg-cream rounded-xl border border-cream-darker">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-earth">Bank:</span><span class="font-medium text-dark"><?= e($bank['nama_bank']) ?></span></div>
                        <div class="flex justify-between"><span class="text-earth">No. Rekening:</span><span class="font-mono font-bold text-primary text-lg"><?= e($bank['no_rekening']) ?></span></div>
                        <div class="flex justify-between"><span class="text-earth">Atas Nama:</span><span class="font-medium text-dark"><?= e($bank['nama_pemilik_rekening']) ?></span></div>
                        <div class="flex justify-between border-t border-cream-darker pt-2 mt-2"><span class="text-earth">Total Transfer:</span><span class="font-bold text-primary text-xl"><?= format_rupiah($booking['total_harga']) ?></span></div>
                    </div>
                </div>
            </div>

            <!-- Upload Form -->
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <form method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>/tamu/upload_bukti.php?booking_id=<?= $bookingId ?>" class="space-y-5">
                    <?= csrf_field() ?>

                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Bukti Transfer</label>
                        <div class="border-2 border-dashed border-cream-darker rounded-xl p-8 text-center hover:border-primary/50 transition-colors">
                            <svg class="w-12 h-12 mx-auto text-earth/30 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-sm text-earth mb-2">Pilih file atau drag & drop di sini</p>
                            <p class="text-xs text-earth/60">Format: JPG, JPEG, PNG, PDF · Maks. 2MB</p>
                            <input type="file" name="bukti_transfer" accept=".jpg,.jpeg,.png,.pdf" required
                                   class="mt-4 w-full text-sm text-earth file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Nominal Transfer</label>
                        <input type="number" name="nominal" value="<?= $booking['total_harga'] ?>" required
                               class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                    </div>

                    <button type="submit" name="upload_bukti"
                            class="w-full py-3.5 bg-primary hover:bg-primary-light text-white font-semibold rounded-xl transition-all hover:shadow-lg flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Upload Bukti Transfer
                    </button>
                </form>
            </div>

            <div class="text-center mt-6">
                <a href="<?= BASE_URL ?>/tamu/riwayat.php" class="text-earth hover:text-primary text-sm transition-colors">← Kembali ke Riwayat Booking</a>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
