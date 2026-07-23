<?php
/** Pengaturan Sistem — Admin */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');
$pageTitle = 'Pengaturan Sistem';

// Load current settings
$stmt = db()->query('SELECT * FROM pengaturan_sistem LIMIT 1');
$setting = $stmt->fetch();
if (!$setting) {
    // Auto-insert default if empty
    db()->exec("INSERT INTO pengaturan_sistem (nama_bank, no_rekening, nama_pemilik_rekening, persen_refund, batas_hari_pembatalan) VALUES ('Bank Jambi', '0000000000', 'Kincay Mania Hotel & Resort', 50, 2)");
    $stmt = db()->query('SELECT * FROM pengaturan_sistem LIMIT 1');
    $setting = $stmt->fetch();
}

$errors = [];

// ── Proses POST ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_pengaturan'])) {
    validate_csrf();

    $namaBank    = trim($_POST['nama_bank'] ?? '');
    $noRek       = trim($_POST['no_rekening'] ?? '');
    $namaPemilik = trim($_POST['nama_pemilik'] ?? '');
    $persenRefund = (int)($_POST['persen_refund'] ?? 50);
    $batasHari   = (int)($_POST['batas_hari'] ?? 2);
    $kebijakan   = trim($_POST['kebijakan'] ?? '');

    if (empty($namaBank)) $errors[] = 'Nama bank wajib diisi.';
    if (empty($noRek)) $errors[] = 'No. rekening wajib diisi.';
    if ($persenRefund < 0 || $persenRefund > 100) $errors[] = 'Persen refund harus 0-100.';
    if ($batasHari < 0) $errors[] = 'Batas hari harus >= 0.';

    if (empty($errors)) {
        $stmt = db()->prepare(
            'UPDATE pengaturan_sistem SET nama_bank = ?, no_rekening = ?, nama_pemilik_rekening = ?, persen_refund = ?, batas_hari_pembatalan = ?, kebijakan_pembatalan = ? WHERE id = ?'
        );
        $stmt->execute([$namaBank, $noRek, $namaPemilik, $persenRefund, $batasHari, $kebijakan ?: null, $setting['id']]);
        set_flash('success', 'Pengaturan berhasil disimpan!');
        header('Location: ' . BASE_URL . '/admin/pengaturan.php');
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-2xl mx-auto px-4">
            <?php $flash = get_flash(); if ($flash): ?>
            <div class="mb-6 p-4 bg-<?= $flash['type'] === 'success' ? 'success-light' : 'danger-light' ?> rounded-xl text-sm text-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>"><?= e($flash['message']) ?></div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
            <div class="mb-6 p-4 bg-danger-light rounded-xl text-sm text-danger"><ul class="list-disc list-inside space-y-1"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
            <?php endif; ?>

            <h1 class="font-sans text-3xl text-dark font-bold mb-8">Pengaturan Sistem</h1>

            <form method="POST" action="<?= BASE_URL ?>/admin/pengaturan.php" class="space-y-6">
                <?= csrf_field() ?>

                <!-- Bank Info -->
                <div class="bg-white rounded-2xl p-6 shadow-sm space-y-5">
                    <h2 class="font-semibold text-dark flex items-center gap-2"><svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg> Informasi Rekening</h2>
                    <div><label class="block text-sm font-medium text-dark mb-1">Nama Bank</label><input type="text" name="nama_bank" value="<?= e($setting['nama_bank']) ?>" required class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                    <div><label class="block text-sm font-medium text-dark mb-1">No. Rekening</label><input type="text" name="no_rekening" value="<?= e($setting['no_rekening']) ?>" required class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                    <div><label class="block text-sm font-medium text-dark mb-1">Nama Pemilik Rekening</label><input type="text" name="nama_pemilik" value="<?= e($setting['nama_pemilik_rekening']) ?>" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                </div>

                <!-- Kebijakan Refund -->
                <div class="bg-white rounded-2xl p-6 shadow-sm space-y-5">
                    <h2 class="font-semibold text-dark flex items-center gap-2"><svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg> Kebijakan Pembatalan & Refund</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-dark mb-1">Persen Refund (%)</label><input type="number" name="persen_refund" value="<?= $setting['persen_refund'] ?>" required min="0" max="100" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Batas Hari Pembatalan (H-)</label><input type="number" name="batas_hari" value="<?= $setting['batas_hari_pembatalan'] ?>" required min="0" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                    </div>
                    <div><label class="block text-sm font-medium text-dark mb-1">Kebijakan Pembatalan (teks lengkap)</label><textarea name="kebijakan" rows="4" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors resize-none"><?= e($setting['kebijakan_pembatalan'] ?? '') ?></textarea></div>
                </div>

                <button type="submit" name="simpan_pengaturan" class="w-full py-3.5 bg-primary hover:bg-primary-light text-white font-semibold rounded-xl transition-all hover:shadow-lg flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Simpan Pengaturan
                </button>
            </form>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
