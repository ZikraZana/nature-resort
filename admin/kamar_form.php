<?php
/** Form Kamar (Tambah/Edit) — Admin */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');
$id = (int)($_GET['id'] ?? 0);
$isEdit = $id > 0;
$pageTitle = $isEdit ? 'Edit Kamar' : 'Tambah Kamar';

$kamar = $isEdit ? ['id' => $id, 'nama' => 'Kabin Pinus A1', 'tipe' => 'Kabin', 'kapasitas' => 2, 'harga' => 450000, 'deskripsi' => 'Kabin kayu eksklusif di tengah hutan pinus.', 'fasilitas' => 'Tempat Tidur Queen-Size, Kamar Mandi Dalam, Air Panas, Teras Privat, Wi-Fi Gratis', 'status' => 'aktif'] : ['nama' => '', 'tipe' => '', 'kapasitas' => 2, 'harga' => 0, 'deskripsi' => '', 'fasilitas' => '', 'status' => 'aktif'];
$tipeList = ['Standard', 'Kabin', 'Deluxe', 'Suite'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-2xl mx-auto px-4">
            <a href="<?= BASE_URL ?>/admin/kelola_kamar.php" class="inline-flex items-center gap-2 text-earth hover:text-primary text-sm mb-6 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali</a>
            <h1 class="font-sans text-3xl text-dark font-bold mb-8"><?= $pageTitle ?></h1>

            <form method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>/admin/kamar_form.php" class="space-y-6">
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
                        <div><label class="block text-sm font-medium text-dark mb-1">Harga/Malam (Rp)</label><input type="number" name="harga" value="<?= $kamar['harga'] ?>" required min="0" step="1000" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Status</label>
                            <select name="status" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                                <option value="aktif" <?= $kamar['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="maintenance" <?= $kamar['status'] === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                            </select>
                        </div>
                    </div>
                    <div><label class="block text-sm font-medium text-dark mb-1">Deskripsi</label><textarea name="deskripsi" rows="4" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors resize-none" placeholder="Deskripsi kamar..."><?= e($kamar['deskripsi']) ?></textarea></div>
                    <div><label class="block text-sm font-medium text-dark mb-1">Fasilitas (pisahkan dengan koma)</label><textarea name="fasilitas" rows="2" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors resize-none" placeholder="Wi-Fi, AC, TV, ..."><?= e($kamar['fasilitas']) ?></textarea></div>
                    <div><label class="block text-sm font-medium text-dark mb-1">Foto Kamar</label>
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
