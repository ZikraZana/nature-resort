<?php
/** Kelola Paket Wisata — Admin CRUD */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');
$pageTitle = 'Kelola Paket Wisata';

$paketList = [
    ['id' => 1, 'nama' => 'Trekking Gunung Kerinci', 'kategori' => 'trekking', 'harga' => 350000, 'kuota' => 10, 'durasi' => '8 jam', 'status' => 'aktif'],
    ['id' => 2, 'nama' => 'River Tubing Sungai Batang Merangin', 'kategori' => 'perahu', 'harga' => 250000, 'kuota' => 15, 'durasi' => '4 jam', 'status' => 'aktif'],
    ['id' => 3, 'nama' => 'Wisata Kuliner Lokal Kerinci', 'kategori' => 'kuliner', 'harga' => 150000, 'kuota' => 20, 'durasi' => '3 jam', 'status' => 'aktif'],
    ['id' => 4, 'nama' => 'Susur Danau Kerinci', 'kategori' => 'perahu', 'harga' => 200000, 'kuota' => 12, 'durasi' => '5 jam', 'status' => 'nonaktif'],
];

$kategoriColors = [
    'trekking' => 'bg-success/10 text-success',
    'perahu'   => 'bg-info/10 text-info',
    'kuliner'  => 'bg-secondary/10 text-secondary',
];
$kategoriIcons = [
    'trekking' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>',
    'perahu'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>',
    'kuliner'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2"/>',
];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div>
                    <h1 class="font-sans text-3xl text-dark font-bold">Kelola Paket Wisata</h1>
                    <p class="text-earth mt-1"><?= count($paketList) ?> paket terdaftar</p>
                </div>
                <a href="<?= BASE_URL ?>/admin/paket_form.php" class="px-5 py-2.5 bg-primary hover:bg-primary-light text-white font-semibold rounded-full transition-all hover:shadow-lg text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Paket
                </a>
            </div>

            <!-- Stats mini -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-success/10 flex items-center justify-center"><svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg></div>
                    <div><p class="text-2xl font-bold text-dark"><?= count(array_filter($paketList, fn($p) => $p['status'] === 'aktif')) ?></p><p class="text-xs text-earth">Paket Aktif</p></div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-info/10 flex items-center justify-center"><svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                    <div><p class="text-2xl font-bold text-dark"><?= array_sum(array_column($paketList, 'kuota')) ?></p><p class="text-xs text-earth">Total Kuota</p></div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center"><svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg></div>
                    <div><p class="text-2xl font-bold text-dark">3</p><p class="text-xs text-earth">Kategori</p></div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead><tr class="bg-cream border-b border-cream-dark">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Nama Paket</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-earth uppercase tracking-wider">Durasi</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-earth uppercase tracking-wider">Kuota</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-earth uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-earth uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-earth uppercase tracking-wider">Aksi</th>
                        </tr></thead>
                        <tbody class="divide-y divide-cream">
                            <?php foreach ($paketList as $p): ?>
                            <tr class="hover:bg-cream/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-dark"><?= e($p['nama']) ?></td>
                                <td class="px-6 py-4 text-sm"><span class="px-2.5 py-1 <?= $kategoriColors[$p['kategori']] ?? 'bg-earth/10 text-earth' ?> text-xs font-medium rounded-full"><?= ucfirst(e($p['kategori'])) ?></span></td>
                                <td class="px-6 py-4 text-sm text-center text-earth"><?= e($p['durasi']) ?></td>
                                <td class="px-6 py-4 text-sm text-center text-earth"><?= $p['kuota'] ?> orang</td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-primary"><?= format_rupiah($p['harga']) ?></td>
                                <td class="px-6 py-4 text-center"><span class="px-3 py-1 <?= $p['status'] === 'aktif' ? 'bg-success-light text-success' : 'bg-gray-100 text-gray-500' ?> text-xs font-medium rounded-full"><?= ucfirst($p['status']) ?></span></td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="<?= BASE_URL ?>/admin/paket_form.php?id=<?= $p['id'] ?>" class="p-2 text-info hover:bg-info/10 rounded-lg transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                        <form method="POST" action="<?= BASE_URL ?>/admin/kelola_paket.php" class="inline"><?= csrf_field() ?><input type="hidden" name="paket_id" value="<?= $p['id'] ?>"><button type="submit" name="hapus_paket" class="p-2 text-danger hover:bg-danger/10 rounded-lg transition-colors" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
