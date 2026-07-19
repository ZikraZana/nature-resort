<?php
/**
 * Detail Paket Wisata — Kincay Mania Hotel & Resort
 */
require_once __DIR__ . '/../config/config.php';

$paketData = [
    1 => ['id' => 1, 'nama' => 'Trekking Gunung Kerinci', 'kategori' => 'trekking', 'harga' => 350000, 'foto' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=900&h=600&fit=crop', 'deskripsi' => "Jelajahi puncak tertinggi Sumatera (3.805 mdpl) dengan pemandu profesional bersertifikat. Perjalanan ini menawarkan pengalaman trekking tak terlupakan melewati hutan tropis, padang edelweiss, dan puncak yang menawarkan pemandangan 360° spektakuler.\n\nDurasi: 2 hari 1 malam\nTerbit: termasuk tenda, sleeping bag, makanan, pemandu, dan porter.\nTingkat kesulitan: Menengah-Tinggi",
        'jadwal' => [
            ['tanggal' => '2026-07-20', 'kuota_maks' => 10, 'terpakai' => 2],
            ['tanggal' => '2026-07-25', 'kuota_maks' => 10, 'terpakai' => 7],
            ['tanggal' => '2026-08-01', 'kuota_maks' => 12, 'terpakai' => 0],
            ['tanggal' => '2026-08-10', 'kuota_maks' => 10, 'terpakai' => 10],
        ]],
    2 => ['id' => 2, 'nama' => 'Susur Sungai Batang Merangin', 'kategori' => 'perahu', 'harga' => 200000, 'foto' => 'https://images.unsplash.com/photo-1529011060498-3553d7bee26a?w=900&h=600&fit=crop', 'deskripsi' => "Nikmati keindahan tepian sungai Batang Merangin dengan perahu tradisional. Suasana tenang dengan pemandangan tebing batu dan hutan tropis.\n\nDurasi: 3 jam (pagi/sore)\nTermasuk: perahu, jaket pelampung, pemandu, snack.",
        'jadwal' => [
            ['tanggal' => '2026-07-18', 'kuota_maks' => 15, 'terpakai' => 3],
            ['tanggal' => '2026-07-22', 'kuota_maks' => 15, 'terpakai' => 10],
        ]],
    3 => ['id' => 3, 'nama' => 'River Tubing Sungai Kerinci', 'kategori' => 'perahu', 'harga' => 250000, 'foto' => 'https://images.unsplash.com/photo-1530866495561-507c83580c5d?w=900&h=600&fit=crop', 'deskripsi' => "Arungi jeram sungai Kerinci yang memacu adrenalin! Cocok untuk pencinta petualangan.\n\nDurasi: 2-3 jam\nTermasuk: tube, helm, pelampung, pemandu bersertifikat.\nTingkat kesulitan: Menengah",
        'jadwal' => [
            ['tanggal' => '2026-07-19', 'kuota_maks' => 8, 'terpakai' => 3],
            ['tanggal' => '2026-07-26', 'kuota_maks' => 8, 'terpakai' => 8],
        ]],
    4 => ['id' => 4, 'nama' => 'Wisata Kuliner Lokal Kerinci', 'kategori' => 'kuliner', 'harga' => 150000, 'foto' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=900&h=600&fit=crop', 'deskripsi' => "Tur kuliner ke warung dan dapur tradisional Kerinci. Cicipi gulai ikan semah, dendeng batokok, kopi arabika Kayu Aro, dan jajanan pasar khas.\n\nDurasi: 4 jam (termasuk makan)\nTermasuk: transportasi, pemandu, semua makanan.",
        'jadwal' => [
            ['tanggal' => '2026-07-17', 'kuota_maks' => 20, 'terpakai' => 5],
            ['tanggal' => '2026-07-24', 'kuota_maks' => 20, 'terpakai' => 12],
        ]],
];

$id = (int)($_GET['id'] ?? 1);
$paket = $paketData[$id] ?? $paketData[1];
$pageTitle = $paket['nama'];

$kategoriLabels = ['trekking' => 'Trekking', 'perahu' => 'River / Perahu', 'kuliner' => 'Kuliner'];
$kategoriColors = ['trekking' => 'bg-success text-white', 'perahu' => 'bg-info text-white', 'kuliner' => 'bg-secondary text-white'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_guest.php';
?>

    <!-- Breadcrumb -->
    <div class="pt-24 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center gap-2 text-sm text-earth">
                <a href="<?= BASE_URL ?>/guest/" class="hover:text-primary transition-colors">Beranda</a>
                <svg class="w-4 h-4 text-earth/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="<?= BASE_URL ?>/guest/paket_wisata.php" class="hover:text-primary transition-colors">Paket Wisata</a>
                <svg class="w-4 h-4 text-earth/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-dark font-medium"><?= e($paket['nama']) ?></span>
            </nav>
        </div>
    </div>

    <!-- Detail Content -->
    <section class="pb-16 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <!-- Left: Image & Info -->
                <div class="lg:col-span-3 space-y-6">
                    <div class="rounded-2xl overflow-hidden shadow-lg">
                        <img src="<?= e($paket['foto']) ?>" alt="<?= e($paket['nama']) ?>" class="w-full h-80 sm:h-96 object-cover">
                    </div>

                    <div class="bg-white rounded-2xl p-8 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="px-3 py-1 <?= $kategoriColors[$paket['kategori']] ?> text-xs font-medium rounded-full">
                                <?= e($kategoriLabels[$paket['kategori']]) ?>
                            </span>
                        </div>
                        <h1 class="font-serif text-3xl text-dark font-bold mb-4"><?= e($paket['nama']) ?></h1>
                        <div class="text-earth leading-relaxed whitespace-pre-line"><?= e($paket['deskripsi']) ?></div>
                    </div>
                </div>

                <!-- Right: Jadwal & Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Price Card -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm">
                        <div class="flex items-baseline gap-2 mb-6">
                            <span class="text-3xl font-bold text-primary"><?= format_rupiah($paket['harga']) ?></span>
                            <span class="text-earth">/orang</span>
                        </div>
                        <p class="text-sm text-earth mb-4">Paket ini dapat ditambahkan sebagai add-on saat booking kamar.</p>
                        <a href="<?= BASE_URL ?>/guest/kamar.php"
                           class="block w-full py-3 bg-accent hover:bg-accent-light text-dark font-semibold rounded-xl text-center transition-all hover:shadow-lg">
                            Booking Kamar + Paket Ini
                        </a>
                    </div>

                    <!-- Jadwal & Kuota -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm">
                        <h3 class="font-serif text-xl text-dark font-semibold mb-4">Jadwal Tersedia</h3>
                        <div class="space-y-3">
                            <?php foreach ($paket['jadwal'] as $j):
                                $sisa = $j['kuota_maks'] - $j['terpakai'];
                                $persen = ($j['terpakai'] / $j['kuota_maks']) * 100;
                            ?>
                            <div class="p-4 rounded-xl <?= $sisa > 0 ? 'bg-cream' : 'bg-danger-light' ?>">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-dark">
                                        <?= date('d M Y', strtotime($j['tanggal'])) ?>
                                    </span>
                                    <span class="text-sm font-medium <?= $sisa > 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= $sisa > 0 ? "Sisa $sisa kuota" : 'Penuh' ?>
                                    </span>
                                </div>
                                <div class="w-full bg-cream-darker rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all <?= $sisa > 0 ? 'bg-success' : 'bg-danger' ?>" style="width: <?= $persen ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
