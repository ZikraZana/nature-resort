<?php
/** Form Paket Wisata (Tambah/Edit) — Admin */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');
$id = (int)($_GET['id'] ?? 0);
$isEdit = $id > 0;
$pageTitle = $isEdit ? 'Edit Paket Wisata' : 'Tambah Paket Wisata';
$errors = [];

// Load data for edit
$paket = ['nama' => '', 'kategori' => '', 'harga' => 0, 'durasi' => '', 'deskripsi' => '', 'termasuk' => '', 'titik_kumpul' => 'Lobby Resort', 'jam_mulai' => '08:00', 'status' => 'aktif'];
if ($isEdit) {
    $stmt = db()->prepare('SELECT * FROM paket_wisata WHERE id = ?');
    $stmt->execute([$id]);
    $paket = $stmt->fetch();
    if (!$paket) {
        set_flash('danger', 'Paket tidak ditemukan.');
        header('Location: ' . BASE_URL . '/admin/kelola_paket.php');
        exit;
    }
}

$kategoriList = ['trekking' => 'Trekking', 'perahu' => 'Perahu / River Tubing', 'kuliner' => 'Kuliner Lokal'];

// ── Proses POST ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_paket'])) {
    validate_csrf();

    $nama       = trim($_POST['nama'] ?? '');
    $kategori   = trim($_POST['kategori'] ?? '');
    $harga      = (float)($_POST['harga'] ?? 0);
    $durasi     = trim($_POST['durasi'] ?? '');
    $deskripsi  = trim($_POST['deskripsi'] ?? '');
    $termasuk   = trim($_POST['termasuk'] ?? '');
    $titikKumpul = trim($_POST['titik_kumpul'] ?? '');
    $jamMulai   = trim($_POST['jam_mulai'] ?? '');
    $status     = $_POST['status'] ?? 'aktif';

    if (empty($nama)) $errors[] = 'Nama paket wajib diisi.';
    if (empty($kategori) || !array_key_exists($kategori, $kategoriList)) $errors[] = 'Kategori tidak valid.';
    if ($harga <= 0) $errors[] = 'Harga harus lebih dari 0.';

    // Handle foto upload
    $fotoFilename = $isEdit ? ($paket['foto'] ?? null) : null;
    if (!empty($_FILES['foto']['name'])) {
        $file = $_FILES['foto'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            if ($file['size'] > MAX_FILE_SIZE) $errors[] = 'Ukuran foto maksimal 2MB.';
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ALLOWED_IMAGE_EXT)) $errors[] = 'Format foto harus JPG/PNG.';
            if (empty($errors)) {
                $fotoFilename = 'paket_' . time() . '.' . $ext;
                if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
                move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $fotoFilename);
            }
        }
    }

    if (empty($errors)) {
        if ($isEdit) {
            $paketId = (int)$_POST['paket_id'];
            $stmt = db()->prepare('UPDATE paket_wisata SET nama = ?, kategori = ?, harga = ?, durasi = ?, jam_mulai = ?, titik_kumpul = ?, termasuk = ?, deskripsi = ?, foto = ?, status = ? WHERE id = ?');
            $stmt->execute([$nama, $kategori, $harga, $durasi ?: null, $jamMulai ?: null, $titikKumpul ?: null, $termasuk ?: null, $deskripsi ?: null, $fotoFilename, $status, $paketId]);
            set_flash('success', 'Paket wisata berhasil diperbarui!');
        } else {
            $stmt = db()->prepare('INSERT INTO paket_wisata (nama, kategori, harga, durasi, jam_mulai, titik_kumpul, termasuk, deskripsi, foto, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$nama, $kategori, $harga, $durasi ?: null, $jamMulai ?: null, $titikKumpul ?: null, $termasuk ?: null, $deskripsi ?: null, $fotoFilename, $status]);
            set_flash('success', 'Paket wisata berhasil ditambahkan!');
        }
        header('Location: ' . BASE_URL . '/admin/kelola_paket.php');
        exit;
    }
    $paket = ['nama' => $nama, 'kategori' => $kategori, 'harga' => $harga, 'durasi' => $durasi, 'deskripsi' => $deskripsi, 'termasuk' => $termasuk, 'titik_kumpul' => $titikKumpul, 'jam_mulai' => $jamMulai, 'status' => $status];
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-2xl mx-auto px-4">
            <a href="<?= BASE_URL ?>/admin/kelola_paket.php" class="inline-flex items-center gap-2 text-earth hover:text-primary text-sm mb-6 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali</a>
            <h1 class="font-sans text-3xl text-dark font-bold mb-8"><?= $pageTitle ?></h1>

            <?php if (!empty($errors)): ?>
            <div class="mb-6 p-4 bg-danger-light rounded-xl text-sm text-danger"><ul class="list-disc list-inside space-y-1"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>/admin/paket_form.php<?= $isEdit ? '?id=' . $id : '' ?>" class="space-y-6">
                <?= csrf_field() ?>
                <?php if ($isEdit): ?><input type="hidden" name="paket_id" value="<?= $id ?>"><?php endif; ?>

                <div class="bg-white rounded-2xl p-6 shadow-sm space-y-5">
                    <div><label class="block text-sm font-medium text-dark mb-1">Nama Paket</label><input type="text" name="nama" value="<?= e($paket['nama']) ?>" required placeholder="Contoh: Trekking Gunung Kerinci" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-dark mb-1">Kategori</label>
                            <select name="kategori" required class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                                <option value="">Pilih kategori</option>
                                <?php foreach ($kategoriList as $kv => $kl): ?><option value="<?= $kv ?>" <?= ($paket['kategori'] ?? '') === $kv ? 'selected' : '' ?>><?= $kl ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Harga (Rp/orang)</label><input type="number" name="harga" value="<?= $paket['harga'] ?>" required min="0" step="1000" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div><label class="block text-sm font-medium text-dark mb-1">Durasi</label><input type="text" name="durasi" value="<?= e($paket['durasi'] ?? '') ?>" placeholder="3 jam" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Jam Mulai</label><input type="time" name="jam_mulai" value="<?= e($paket['jam_mulai'] ?? '08:00') ?>" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Status</label>
                            <select name="status" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                                <option value="aktif" <?= ($paket['status'] ?? 'aktif') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= ($paket['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div><label class="block text-sm font-medium text-dark mb-1">Titik Kumpul</label><input type="text" name="titik_kumpul" value="<?= e($paket['titik_kumpul'] ?? '') ?>" placeholder="Lobby Resort" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                    <div><label class="block text-sm font-medium text-dark mb-1">Deskripsi</label><textarea name="deskripsi" rows="4" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors resize-none" placeholder="Deskripsi paket..."><?= e($paket['deskripsi'] ?? '') ?></textarea></div>
                    <div><label class="block text-sm font-medium text-dark mb-1">Termasuk (pisahkan dengan koma)</label><textarea name="termasuk" rows="2" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors resize-none" placeholder="Guide, Makan siang, P3K, ..."><?= e($paket['termasuk'] ?? '') ?></textarea></div>
                    <div><label class="block text-sm font-medium text-dark mb-1">Foto Paket</label>
                        <?php if ($isEdit && !empty($paket['foto'])): ?>
                        <div class="mb-3 flex items-center gap-3">
                            <img src="<?= str_starts_with($paket['foto'], 'http') ? e($paket['foto']) : BASE_URL . '/uploads/' . e($paket['foto']) ?>"
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

                <button type="submit" name="simpan_paket" class="w-full py-3.5 bg-primary hover:bg-primary-light text-white font-semibold rounded-xl transition-all hover:shadow-lg flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <?= $isEdit ? 'Update Paket' : 'Simpan Paket' ?>
                </button>
            </form>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
