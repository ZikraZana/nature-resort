<?php
/** Form Kamar (Tambah/Edit) — Admin */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');
$id = (int)($_GET['id'] ?? 0);
$isEdit = $id > 0;
$pageTitle = $isEdit ? 'Edit Kamar' : 'Tambah Kamar';
$errors = [];

// Load data for edit
$kamar = ['nama' => '', 'tipe' => '', 'kapasitas' => 2, 'harga_per_malam' => 0, 'deskripsi' => '', 'fasilitas' => '', 'status_default' => 'tersedia'];
if ($isEdit) {
    $stmt = db()->prepare('SELECT * FROM kamar WHERE id = ?');
    $stmt->execute([$id]);
    $kamar = $stmt->fetch();
    if (!$kamar) {
        set_flash('danger', 'Kamar tidak ditemukan.');
        header('Location: ' . BASE_URL . '/admin/kelola_kamar.php');
        exit;
    }
}

$tipeList = ['Standard', 'Kabin', 'Deluxe', 'Suite'];

// ── Proses POST — simpan ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_kamar'])) {
    validate_csrf();

    $nama      = trim($_POST['nama'] ?? '');
    $tipe      = trim($_POST['tipe'] ?? '');
    $kapasitas = (int)($_POST['kapasitas'] ?? 2);
    $harga     = (float)($_POST['harga'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $fasilitas = trim($_POST['fasilitas'] ?? '');
    $status    = $_POST['status'] ?? 'tersedia';
    // nonaktif hanya bisa di-set oleh sistem (soft-delete), bukan manual
    if (!in_array($status, ['tersedia', 'maintenance'])) $status = 'tersedia';

    if (empty($nama)) $errors[] = 'Nama kamar wajib diisi.';
    if (empty($tipe)) $errors[] = 'Tipe wajib dipilih.';
    if ($harga <= 0) $errors[] = 'Harga harus lebih dari 0.';

    // Handle foto upload
    $fotoFilename = $isEdit ? ($kamar['foto'] ?? null) : null;
    if (!empty($_FILES['foto']['name'])) {
        $file = $_FILES['foto'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            if ($file['size'] > MAX_FILE_SIZE) { $errors[] = 'Ukuran foto maksimal 2MB.'; }
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ALLOWED_IMAGE_EXT)) { $errors[] = 'Format foto harus JPG/PNG.'; }

            if (empty($errors)) {
                $fotoFilename = 'kamar_' . time() . '.' . $ext;
                if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
                move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $fotoFilename);
            }
        }
    }

    if (empty($errors)) {
        if ($isEdit) {
            $kamarId = (int)$_POST['kamar_id'];
            $stmt = db()->prepare('UPDATE kamar SET nama = ?, tipe = ?, kapasitas = ?, harga_per_malam = ?, deskripsi = ?, fasilitas = ?, foto = ?, status_default = ? WHERE id = ?');
            $stmt->execute([$nama, $tipe, $kapasitas, $harga, $deskripsi ?: null, $fasilitas ?: null, $fotoFilename, $status, $kamarId]);
            set_flash('success', 'Kamar berhasil diperbarui!');
        } else {
            $stmt = db()->prepare('INSERT INTO kamar (nama, tipe, kapasitas, harga_per_malam, deskripsi, fasilitas, foto, status_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$nama, $tipe, $kapasitas, $harga, $deskripsi ?: null, $fasilitas ?: null, $fotoFilename, $status]);
            set_flash('success', 'Kamar berhasil ditambahkan!');
        }
        header('Location: ' . BASE_URL . '/admin/kelola_kamar.php');
        exit;
    }

    // Keep form values on error
    $kamar = ['nama' => $nama, 'tipe' => $tipe, 'kapasitas' => $kapasitas, 'harga_per_malam' => $harga, 'deskripsi' => $deskripsi, 'fasilitas' => $fasilitas, 'status_default' => $status];
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-2xl mx-auto px-4">
            <a href="<?= BASE_URL ?>/admin/kelola_kamar.php" class="inline-flex items-center gap-2 text-earth hover:text-primary text-sm mb-6 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali</a>
            <h1 class="font-sans text-3xl text-dark font-bold mb-8"><?= $pageTitle ?></h1>

            <?php if (!empty($errors)): ?>
            <div class="mb-6 p-4 bg-danger-light rounded-xl text-sm text-danger">
                <ul class="list-disc list-inside space-y-1"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>/admin/kamar_form.php<?= $isEdit ? '?id=' . $id : '' ?>" class="space-y-6">
                <?= csrf_field() ?>
                <?php if ($isEdit): ?><input type="hidden" name="kamar_id" value="<?= $id ?>"><?php endif; ?>

                <div class="bg-white rounded-2xl p-6 shadow-sm space-y-5">
                    <div><label class="block text-sm font-medium text-dark mb-1">Nama Kamar</label><input type="text" name="nama" value="<?= e($kamar['nama']) ?>" required placeholder="Contoh: Kabin Pinus A3" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-dark mb-1">Tipe</label>
                            <select name="tipe" required class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                                <option value="">Pilih tipe</option>
                                <?php foreach ($tipeList as $t): ?><option value="<?= $t ?>" <?= $kamar['tipe'] === $t ? 'selected' : '' ?>><?= $t ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Kapasitas</label><input type="number" name="kapasitas" value="<?= $kamar['kapasitas'] ?>" required min="1" max="10" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-dark mb-1">Harga/Malam (Rp)</label><input type="number" name="harga" value="<?= $kamar['harga_per_malam'] ?>" required min="0" step="1000" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Status</label>
                            <select name="status" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                                <option value="tersedia" <?= $kamar['status_default'] === 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                                <option value="maintenance" <?= $kamar['status_default'] === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                            </select>
                        </div>
                    </div>
                    <div><label class="block text-sm font-medium text-dark mb-1">Deskripsi</label><textarea name="deskripsi" rows="4" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors resize-none" placeholder="Deskripsi kamar..."><?= e($kamar['deskripsi']) ?></textarea></div>
                    <div><label class="block text-sm font-medium text-dark mb-1">Fasilitas (pisahkan dengan koma)</label><textarea name="fasilitas" rows="2" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors resize-none" placeholder="Wi-Fi, AC, TV, ..."><?= e($kamar['fasilitas'] ?? '') ?></textarea></div>
                    <div><label class="block text-sm font-medium text-dark mb-1">Foto Kamar</label>
                        <?php if ($isEdit && !empty($kamar['foto'])): ?>
                        <div class="mb-3 flex items-center gap-3">
                            <img src="<?= str_starts_with($kamar['foto'], 'http') ? e($kamar['foto']) : BASE_URL . '/uploads/' . e($kamar['foto']) ?>"
                                 alt="Foto saat ini" class="w-24 h-16 object-cover rounded-lg border border-cream-darker">
                            <span class="text-xs text-earth">Foto saat ini</span>
                        </div>
                        <?php endif; ?>
                        <div class="border-2 border-dashed border-cream-darker rounded-xl p-6 text-center hover:border-primary/50 transition-colors">
                            <input type="file" name="foto" accept=".jpg,.jpeg,.png" class="w-full text-sm text-earth file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                            <p class="text-xs text-earth/60 mt-2">JPG/PNG, maks 2MB <?= $isEdit ? '· Kosongkan jika tidak ingin ganti' : '' ?></p>
                        </div>
                    </div>
                </div>

                <button type="submit" name="simpan_kamar" class="w-full py-3.5 bg-primary hover:bg-primary-light text-white font-semibold rounded-xl transition-all hover:shadow-lg flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <?= $isEdit ? 'Update Kamar' : 'Simpan Kamar' ?>
                </button>
            </form>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
