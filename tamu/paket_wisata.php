<?php
/**
 * Daftar Paket Wisata — Kincay Mania Hotel & Resort
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');

$pageTitle = 'Paket Wisata Alam';
$pageDescription = 'Jelajahi paket wisata alam Kerinci — trekking Gunung Kerinci, river tubing, susur perahu, dan wisata kuliner lokal.';

$paketList = [
    ['id' => 1, 'nama' => 'Trekking Gunung Kerinci', 'kategori' => 'trekking', 'harga' => 350000, 'foto' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=600&h=400&fit=crop', 'deskripsi' => 'Jelajahi puncak tertinggi Sumatera dengan pemandu profesional. Durasi 2 hari 1 malam termasuk camping.', 'sisa_kuota' => 8],
    ['id' => 2, 'nama' => 'Susur Sungai Batang Merangin', 'kategori' => 'perahu', 'harga' => 200000, 'foto' => 'https://images.unsplash.com/photo-1529011060498-3553d7bee26a?w=600&h=400&fit=crop', 'deskripsi' => 'Nikmati keindahan tepian sungai Batang Merangin dengan perahu tradisional. Durasi 3 jam.', 'sisa_kuota' => 12],
    ['id' => 3, 'nama' => 'River Tubing Sungai Kerinci', 'kategori' => 'perahu', 'harga' => 250000, 'foto' => 'https://images.unsplash.com/photo-1530866495561-507c83580c5d?w=600&h=400&fit=crop', 'deskripsi' => 'Arungi jeram sungai Kerinci yang memacu adrenalin. Cocok untuk pencinta petualangan!', 'sisa_kuota' => 5],
    ['id' => 4, 'nama' => 'Wisata Kuliner Lokal Kerinci', 'kategori' => 'kuliner', 'harga' => 150000, 'foto' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&h=400&fit=crop', 'deskripsi' => 'Cicipi masakan khas Kerinci — gulai ikan semah, dendeng batokok, dan kopi Kayu Aro.', 'sisa_kuota' => 15],
];

$filterKategori = $_GET['kategori'] ?? '';
if ($filterKategori) {
    $paketList = array_filter($paketList, fn($p) => $p['kategori'] === $filterKategori);
}

$kategoriLabels = ['trekking' => 'Trekking', 'perahu' => 'River / Perahu', 'kuliner' => 'Kuliner'];
$kategoriColors = ['trekking' => 'bg-success text-white', 'perahu' => 'bg-info text-white', 'kuliner' => 'bg-secondary text-white'];
$kategoriIcons = [
    'trekking' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>',
    'perahu' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/></svg>',
    'kuliner' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_tamu.php';
?>

    <!-- Page Header -->
    <section class="pt-28 pb-12 bg-dark relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <img src="https://images.unsplash.com/photo-1682687220742-aba13b6e50ba?w=1920&h=400&fit=crop" alt="" class="w-full h-full object-cover">
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-accent font-medium tracking-wider uppercase text-sm mb-3">Petualangan</p>
            <h1 class="font-serif text-4xl sm:text-5xl text-white font-bold mb-4">Paket Wisata Alam</h1>
            <p class="text-cream/60 max-w-2xl mx-auto">Tambahkan pengalaman tak terlupakan ke penginapan Anda — trekking, river tubing, atau wisata kuliner lokal Kerinci.</p>
        </div>
    </section>

    <!-- Filter Tabs -->
    <section class="bg-white border-b border-cream-dark sticky top-20 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex gap-2 py-4 overflow-x-auto">
                <a href="<?= BASE_URL ?>/tamu/paket_wisata.php"
                   class="px-5 py-2.5 rounded-full text-sm font-medium transition-colors whitespace-nowrap <?= !$filterKategori ? 'bg-primary text-white' : 'bg-cream text-earth hover:bg-cream-dark' ?>">
                    Semua
                </a>
                <?php foreach ($kategoriLabels as $key => $label): ?>
                <a href="<?= BASE_URL ?>/tamu/paket_wisata.php?kategori=<?= $key ?>"
                   class="px-5 py-2.5 rounded-full text-sm font-medium transition-colors whitespace-nowrap flex items-center gap-2 <?= $filterKategori === $key ? 'bg-primary text-white' : 'bg-cream text-earth hover:bg-cream-dark' ?>">
                    <?= $kategoriIcons[$key] ?>
                    <?= $label ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Paket Grid -->
    <section class="py-12 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php foreach ($paketList as $paket): ?>
                <div class="card-hover bg-white rounded-2xl overflow-hidden shadow-sm flex flex-col sm:flex-row">
                    <div class="img-zoom w-full sm:w-72 h-56 sm:h-auto shrink-0">
                        <img src="<?= e($paket['foto']) ?>" alt="<?= e($paket['nama']) ?>" class="w-full h-full object-cover" loading="lazy">
                    </div>
                    <div class="p-6 flex flex-col justify-between flex-1">
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-3 py-1 <?= $kategoriColors[$paket['kategori']] ?> text-xs font-medium rounded-full">
                                    <?= e($kategoriLabels[$paket['kategori']]) ?>
                                </span>
                                <span class="px-3 py-1 bg-cream text-earth text-xs font-medium rounded-full">
                                    Sisa <?= $paket['sisa_kuota'] ?> kuota
                                </span>
                            </div>
                            <h3 class="font-serif text-xl font-semibold text-dark mb-2"><?= e($paket['nama']) ?></h3>
                            <p class="text-earth text-sm mb-4"><?= e($paket['deskripsi']) ?></p>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-primary"><?= format_rupiah($paket['harga']) ?></span>
                                <span class="text-earth text-sm">/orang</span>
                            </div>
                            <a href="<?= BASE_URL ?>/tamu/paket_wisata_detail.php?id=<?= $paket['id'] ?>"
                               class="px-5 py-2.5 bg-secondary hover:bg-secondary-light text-white text-sm font-medium rounded-full transition-colors">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
