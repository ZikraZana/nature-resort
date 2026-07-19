<?php
/** Jadwal Wisata Harian — Resepsionis */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Jadwal Wisata Hari Ini';
$jadwalHariIni = [
    ['paket' => 'Trekking Gunung Kerinci', 'kategori' => 'trekking', 'peserta' => [
        ['nama' => 'Budi Tamu', 'kontak' => '081234567890', 'jumlah' => 2],
        ['nama' => 'Dewi Anggraini', 'kontak' => '082345678901', 'jumlah' => 3],
    ], 'total_peserta' => 5, 'kuota' => 10],
    ['paket' => 'Wisata Kuliner Lokal Kerinci', 'kategori' => 'kuliner', 'peserta' => [
        ['nama' => 'Andi Wisata', 'kontak' => '083456789012', 'jumlah' => 2],
    ], 'total_peserta' => 2, 'kuota' => 20],
];
$kategoriColors = ['trekking' => 'bg-success text-white', 'perahu' => 'bg-info text-white', 'kuliner' => 'bg-secondary text-white'];
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_resepsionis.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Jadwal Wisata Hari Ini</h1>
            <p class="text-earth mb-8"><?= date('l, d M Y') ?> — Daftar peserta paket wisata.</p>

            <?php if (empty($jadwalHariIni)): ?>
            <div class="bg-white rounded-2xl p-12 shadow-sm text-center"><p class="text-earth">Tidak ada jadwal wisata hari ini.</p></div>
            <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($jadwalHariIni as $j): ?>
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-cream">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 <?= $kategoriColors[$j['kategori']] ?? 'bg-earth text-white' ?> text-xs font-medium rounded-full"><?= ucfirst(e($j['kategori'])) ?></span>
                                <h2 class="font-sans text-xl font-semibold text-dark"><?= e($j['paket']) ?></h2>
                            </div>
                            <span class="text-sm text-earth"><?= $j['total_peserta'] ?>/<?= $j['kuota'] ?> peserta</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead><tr class="bg-cream/50"><th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase">Nama Tamu</th><th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase">Kontak</th><th class="px-6 py-3 text-center text-xs font-semibold text-earth uppercase">Jumlah</th></tr></thead>
                            <tbody class="divide-y divide-cream">
                                <?php foreach ($j['peserta'] as $p): ?>
                                <tr><td class="px-6 py-3 text-sm font-medium text-dark"><?= e($p['nama']) ?></td><td class="px-6 py-3 text-sm text-earth"><?= e($p['kontak']) ?></td><td class="px-6 py-3 text-sm text-center"><?= $p['jumlah'] ?> orang</td></tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
