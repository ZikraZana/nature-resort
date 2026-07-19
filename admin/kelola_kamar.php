<?php
/** Kelola Kamar — Admin CRUD */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');
$pageTitle = 'Kelola Kamar';

$kamarList = [
    ['id' => 1, 'nama' => 'Kabin Pinus A1', 'tipe' => 'Kabin', 'kapasitas' => 2, 'harga' => 450000, 'status' => 'aktif'],
    ['id' => 2, 'nama' => 'Kabin Pinus A2', 'tipe' => 'Kabin', 'kapasitas' => 2, 'harga' => 450000, 'status' => 'aktif'],
    ['id' => 3, 'nama' => 'Kamar Deluxe B1', 'tipe' => 'Deluxe', 'kapasitas' => 4, 'harga' => 750000, 'status' => 'aktif'],
    ['id' => 4, 'nama' => 'Kamar Deluxe B2', 'tipe' => 'Deluxe', 'kapasitas' => 4, 'harga' => 750000, 'status' => 'maintenance'],
    ['id' => 5, 'nama' => 'Suite Kerinci C1', 'tipe' => 'Suite', 'kapasitas' => 6, 'harga' => 1200000, 'status' => 'aktif'],
    ['id' => 6, 'nama' => 'Standard Room D1', 'tipe' => 'Standard', 'kapasitas' => 2, 'harga' => 300000, 'status' => 'aktif'],
];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div><h1 class="font-sans text-3xl text-dark font-bold">Kelola Kamar</h1><p class="text-earth mt-1"><?= count($kamarList) ?> kamar terdaftar</p></div>
                <a href="<?= BASE_URL ?>/admin/kamar_form.php" class="px-5 py-2.5 bg-primary hover:bg-primary-light text-white font-semibold rounded-full transition-all hover:shadow-lg text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Kamar
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead><tr class="bg-cream border-b border-cream-dark">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-earth uppercase tracking-wider">Kapasitas</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-earth uppercase tracking-wider">Harga/Malam</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-earth uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-earth uppercase tracking-wider">Aksi</th>
                        </tr></thead>
                        <tbody class="divide-y divide-cream">
                            <?php foreach ($kamarList as $k): ?>
                            <tr class="hover:bg-cream/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-dark"><?= e($k['nama']) ?></td>
                                <td class="px-6 py-4 text-sm"><span class="px-2.5 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full"><?= e($k['tipe']) ?></span></td>
                                <td class="px-6 py-4 text-sm text-center text-earth"><?= $k['kapasitas'] ?> tamu</td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-primary"><?= format_rupiah($k['harga']) ?></td>
                                <td class="px-6 py-4 text-center"><span class="px-3 py-1 <?= $k['status'] === 'aktif' ? 'bg-success-light text-success' : 'bg-warning-light text-warning' ?> text-xs font-medium rounded-full"><?= ucfirst($k['status']) ?></span></td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="<?= BASE_URL ?>/admin/kamar_form.php?id=<?= $k['id'] ?>" class="p-2 text-info hover:bg-info/10 rounded-lg transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                        <form method="POST" action="<?= BASE_URL ?>/admin/kelola_kamar.php" class="inline"><input type="hidden" name="kamar_id" value="<?= $k['id'] ?>"><?= csrf_field() ?><button type="submit" name="hapus_kamar" class="p-2 text-danger hover:bg-danger/10 rounded-lg transition-colors" title="Hapus" data-confirm="Yakin hapus kamar <?= e($k['nama']) ?>?"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
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
