<?php
/** Form Paket Wisata (Tambah/Edit) — Admin */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');
$id = (int)($_GET['id'] ?? 0);
$isEdit = $id > 0;
$pageTitle = $isEdit ? 'Edit Paket Wisata' : 'Tambah Paket Wisata';

$paket = $isEdit
    ? ['id' => $id, 'nama' => 'Trekking Gunung Kerinci', 'kategori' => 'trekking', 'harga' => 350000, 'kuota' => 10, 'durasi' => '8 jam', 'deskripsi' => 'Pendakian gunung tertinggi di Sumatera. Menyusuri jalur hutan tropis, padang edelweiss, hingga puncak dengan pemandangan matahari terbit yang spektakuler.', 'termasuk' => 'Guide lokal berpengalaman, Perlengkapan trekking, Makan siang, Air mineral, P3K', 'titik_kumpul' => 'Lobby Resort', 'jam_mulai' => '05:00', 'status' => 'aktif']
    : ['nama' => '', 'kategori' => '', 'harga' => 0, 'kuota' => 10, 'durasi' => '', 'deskripsi' => '', 'termasuk' => '', 'titik_kumpul' => 'Lobby Resort', 'jam_mulai' => '08:00', 'status' => 'aktif'];

$kategoriList = ['trekking' => 'Trekking', 'perahu' => 'Perahu / River Tubing', 'kuliner' => 'Kuliner Lokal'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-2xl mx-auto px-4">
            <a href="<?= BASE_URL ?>/admin/kelola_paket.php" class="inline-flex items-center gap-2 text-earth hover:text-primary text-sm mb-6 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali</a>
            <h1 class="font-sans text-3xl text-dark font-bold mb-8"><?= $pageTitle ?></h1>

            <form method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>/admin/paket_form.php" class="space-y-6">
                <?= csrf_field() ?>
                <?php if ($isEdit): ?><input type="hidden" name="paket_id" value="<?= $id ?>"><?php endif; ?>

                <div class="bg-white rounded-2xl p-6 shadow-sm space-y-5">
                    <div><label class="block text-sm font-medium text-dark mb-1">Nama Paket</label><input type="text" name="nama" value="<?= e($paket['nama']) ?>" required placeholder="Contoh: Trekking Gunung Kerinci" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>

                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-dark mb-1">Kategori</label>
                            <select name="kategori" required class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                                <option value="">Pilih kategori</option>
                                <?php foreach ($kategoriList as $key => $label): ?><option value="<?= $key ?>" <?= $paket['kategori'] === $key ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Durasi</label><input type="text" name="durasi" value="<?= e($paket['durasi']) ?>" required placeholder="Contoh: 4 jam" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-dark mb-1">Harga per Orang (Rp)</label><input type="number" name="harga" value="<?= $paket['harga'] ?>" required min="0" step="1000" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Kuota Maksimal</label><input type="number" name="kuota" value="<?= $paket['kuota'] ?>" required min="1" max="100" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-dark mb-1">Jam Mulai</label><input type="time" name="jam_mulai" value="<?= e($paket['jam_mulai']) ?>" required class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Titik Kumpul</label><input type="text" name="titik_kumpul" value="<?= e($paket['titik_kumpul']) ?>" required class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                    </div>

                    <div><label class="block text-sm font-medium text-dark mb-1">Deskripsi</label><textarea name="deskripsi" rows="4" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors resize-none" placeholder="Deskripsi paket wisata..."><?= e($paket['deskripsi']) ?></textarea></div>

                    <div><label class="block text-sm font-medium text-dark mb-1">Yang Termasuk (pisahkan dengan koma)</label><textarea name="termasuk" rows="2" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors resize-none" placeholder="Guide, Makan siang, Air mineral, ..."><?= e($paket['termasuk']) ?></textarea></div>

                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-dark mb-1">Status</label>
                            <select name="status" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                                <option value="aktif" <?= $paket['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= $paket['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Foto Paket</label>
                            <div class="border-2 border-dashed border-cream-darker rounded-xl p-4 text-center hover:border-primary/50 transition-colors">
                                <input type="file" name="foto" accept=".jpg,.jpeg,.png" class="w-full text-sm text-earth file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                                <p class="text-xs text-earth/60 mt-1">JPG/PNG, maks 2MB</p>
                            </div>
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
