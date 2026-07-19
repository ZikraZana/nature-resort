<?php
/**
 * Booking Paket Wisata (Step 2) — Kincay Mania Hotel & Resort
 * Add-on paket wisata opsional ke booking kamar
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');
$pageTitle = 'Tambah Paket Wisata';

// Dummy available paket wisata
$paketList = [
    ['id' => 1, 'nama' => 'Trekking Gunung Kerinci', 'kategori' => 'trekking', 'harga' => 350000, 'foto' => 'https://placehold.co/400x250/15803D/FFFFFF?text=Trekking+Gunung+Kerinci', 'deskripsi' => 'Durasi 2 hari 1 malam, termasuk camping dan pemandu.', 'jadwal' => [['id' => 1, 'tanggal' => '2026-07-20', 'sisa' => 8], ['id' => 2, 'tanggal' => '2026-07-25', 'sisa' => 3]]],
    ['id' => 2, 'nama' => 'Susur Sungai Batang Merangin', 'kategori' => 'perahu', 'harga' => 200000, 'foto' => 'https://placehold.co/400x250/1D4ED8/FFFFFF?text=Susur+Sungai+Merangin', 'deskripsi' => 'Durasi 3 jam, perahu tradisional dengan pemandu.', 'jadwal' => [['id' => 3, 'tanggal' => '2026-07-18', 'sisa' => 12], ['id' => 4, 'tanggal' => '2026-07-22', 'sisa' => 5]]],
    ['id' => 3, 'nama' => 'River Tubing Sungai Kerinci', 'kategori' => 'perahu', 'harga' => 250000, 'foto' => 'https://placehold.co/400x250/1D4ED8/FFFFFF?text=River+Tubing+Kerinci', 'deskripsi' => 'Durasi 2-3 jam, termasuk alat safety dan pemandu.', 'jadwal' => [['id' => 5, 'tanggal' => '2026-07-19', 'sisa' => 5], ['id' => 6, 'tanggal' => '2026-07-26', 'sisa' => 0]]],
    ['id' => 4, 'nama' => 'Wisata Kuliner Lokal Kerinci', 'kategori' => 'kuliner', 'harga' => 150000, 'foto' => 'https://placehold.co/400x250/8B6914/FFFFFF?text=Wisata+Kuliner+Kerinci', 'deskripsi' => 'Durasi 4 jam, tur kuliner ke warung tradisional.', 'jadwal' => [['id' => 7, 'tanggal' => '2026-07-17', 'sisa' => 15], ['id' => 8, 'tanggal' => '2026-07-24', 'sisa' => 8]]],
];

$kategoriColors = ['trekking' => 'bg-success text-white', 'perahu' => 'bg-info text-white', 'kuliner' => 'bg-secondary text-white'];
$kategoriLabels = ['trekking' => 'Trekking', 'perahu' => 'River / Perahu', 'kuliner' => 'Kuliner'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_tamu.php';
?>

    <!-- Progress Steps -->
    <div class="pt-20 bg-white border-b border-cream-dark">
        <div class="max-w-3xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-success text-white flex items-center justify-center font-semibold text-sm">✓</div>
                    <span class="font-medium text-success text-sm hidden sm:block">Pilih Kamar</span>
                </div>
                <div class="flex-1 h-0.5 mx-4 bg-primary"></div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-semibold text-sm">2</div>
                    <span class="font-medium text-dark text-sm hidden sm:block">Paket Wisata</span>
                </div>
                <div class="flex-1 h-0.5 mx-4 bg-cream-darker"></div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-cream-dark text-earth flex items-center justify-center font-semibold text-sm">3</div>
                    <span class="font-medium text-earth text-sm hidden sm:block">Ringkasan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Paket Wisata Selection -->
    <section class="py-12 bg-cream">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Tambah Paket Wisata</h1>
            <p class="text-earth mb-8">Langkah 2 dari 3 — Pilih paket wisata sebagai add-on (opsional). Anda bisa melewati langkah ini.</p>

            <form method="POST" action="<?= BASE_URL ?>/tamu/booking_ringkasan.php">
                <?= csrf_field() ?>

                <div class="space-y-6">
                    <?php foreach ($paketList as $paket): ?>
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm">
                        <div class="flex flex-col sm:flex-row">
                            <div class="w-full sm:w-48 h-40 sm:h-auto shrink-0">
                                <img src="<?= e($paket['foto']) ?>" alt="<?= e($paket['nama']) ?>" class="w-full h-full object-cover">
                            </div>
                            <div class="p-6 flex-1">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <span class="px-2.5 py-1 <?= $kategoriColors[$paket['kategori']] ?> text-xs font-medium rounded-full"><?= e($kategoriLabels[$paket['kategori']]) ?></span>
                                        <h3 class="font-sans text-lg font-semibold text-dark mt-2"><?= e($paket['nama']) ?></h3>
                                        <p class="text-sm text-earth mt-1"><?= e($paket['deskripsi']) ?></p>
                                    </div>
                                    <span class="text-xl font-bold text-primary whitespace-nowrap ml-4"><?= format_rupiah($paket['harga']) ?><span class="text-sm text-earth font-normal">/org</span></span>
                                </div>

                                <!-- Jadwal Selection -->
                                <div class="mt-4 p-4 bg-cream rounded-xl">
                                    <p class="text-xs font-medium text-earth uppercase tracking-wider mb-3">Pilih Jadwal & Jumlah Peserta</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <?php foreach ($paket['jadwal'] as $j): ?>
                                        <div class="flex items-center gap-3 p-3 bg-white rounded-lg <?= $j['sisa'] === 0 ? 'opacity-50' : '' ?>">
                                            <input type="checkbox" name="paket[<?= $paket['id'] ?>][jadwal_<?= $j['id'] ?>]" value="<?= $j['id'] ?>"
                                                   <?= $j['sisa'] === 0 ? 'disabled' : '' ?>
                                                   class="w-4 h-4 text-primary rounded border-cream-darker focus:ring-primary/20">
                                            <div class="flex-1">
                                                <span class="text-sm font-medium text-dark"><?= date('d M Y', strtotime($j['tanggal'])) ?></span>
                                                <span class="text-xs text-earth block">Sisa <?= $j['sisa'] ?> kuota</span>
                                            </div>
                                            <select name="paket[<?= $paket['id'] ?>][peserta_<?= $j['id'] ?>]" <?= $j['sisa'] === 0 ? 'disabled' : '' ?>
                                                    class="w-20 px-2 py-1 text-sm bg-cream border border-cream-darker rounded-lg">
                                                <?php for ($i = 1; $i <= min($j['sisa'], 10); $i++): ?>
                                                <option value="<?= $i ?>"><?= $i ?></option>
                                                <?php endfor; ?>
                                                <?php if ($j['sisa'] === 0): ?>
                                                <option value="0">Penuh</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Navigation -->
                <div class="flex justify-between items-center mt-8">
                    <a href="<?= BASE_URL ?>/tamu/booking.php" class="text-earth hover:text-dark flex items-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali
                    </a>
                    <div class="flex gap-3">
                        <a href="<?= BASE_URL ?>/tamu/booking_ringkasan.php" class="px-6 py-3 border border-earth/30 text-earth hover:text-dark hover:border-earth/50 rounded-xl transition-colors">
                            Lewati
                        </a>
                        <button type="submit" class="px-8 py-3.5 bg-primary hover:bg-primary-light text-white font-semibold rounded-xl transition-all hover:shadow-lg flex items-center gap-2">
                            Lanjut: Ringkasan
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
