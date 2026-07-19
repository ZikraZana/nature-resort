<?php
/**
 * Daftar Kamar/Kabin — Kincay Mania Hotel & Resort
 */
require_once __DIR__ . '/../config/config.php';

$pageTitle = 'Kamar & Kabin';
$pageDescription = 'Jelajahi pilihan kamar dan kabin eksklusif di Kincay Mania Hotel & Resort — dari kabin pinus hingga suite premium.';

// Dummy data semua kamar
$kamarList = [
    ['id' => 1, 'nama' => 'Kabin Pinus A1', 'tipe' => 'Kabin', 'kapasitas' => 2, 'harga' => 450000, 'foto' => 'https://images.unsplash.com/photo-1618767689160-da3fb810aad7?w=600&h=400&fit=crop', 'deskripsi' => 'Kabin kayu eksklusif di tengah hutan pinus dengan pemandangan gunung.', 'status' => 'tersedia'],
    ['id' => 2, 'nama' => 'Kabin Pinus A2', 'tipe' => 'Kabin', 'kapasitas' => 2, 'harga' => 450000, 'foto' => 'https://images.unsplash.com/photo-1510798831971-661eb04b3739?w=600&h=400&fit=crop', 'deskripsi' => 'Kabin kayu nyaman dengan teras privat menghadap kebun teh.', 'status' => 'tersedia'],
    ['id' => 3, 'nama' => 'Kamar Deluxe B1', 'tipe' => 'Deluxe', 'kapasitas' => 4, 'harga' => 750000, 'foto' => 'https://images.unsplash.com/photo-1590490360182-c33d955f4c4e?w=600&h=400&fit=crop', 'deskripsi' => 'Kamar luas dengan pemandangan Gunung Kerinci, AC, dan TV kabel.', 'status' => 'tersedia'],
    ['id' => 4, 'nama' => 'Kamar Deluxe B2', 'tipe' => 'Deluxe', 'kapasitas' => 4, 'harga' => 750000, 'foto' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=600&h=400&fit=crop', 'deskripsi' => 'Kamar deluxe dengan balkon dan area duduk nyaman.', 'status' => 'maintenance'],
    ['id' => 5, 'nama' => 'Suite Kerinci C1', 'tipe' => 'Suite', 'kapasitas' => 6, 'harga' => 1200000, 'foto' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600&h=400&fit=crop', 'deskripsi' => 'Suite premium dengan ruang tamu terpisah, jacuzzi, dan balkon panorama.', 'status' => 'tersedia'],
    ['id' => 6, 'nama' => 'Standard Room D1', 'tipe' => 'Standard', 'kapasitas' => 2, 'harga' => 300000, 'foto' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600&h=400&fit=crop', 'deskripsi' => 'Kamar standar bersih dan nyaman, cocok untuk backpacker.', 'status' => 'tersedia'],
];

// Filter dummy
$filterTipe = $_GET['tipe'] ?? '';
$filterCheckin = $_GET['checkin'] ?? '';
$filterCheckout = $_GET['checkout'] ?? '';

if ($filterTipe) {
    $kamarList = array_filter($kamarList, fn($k) => $k['tipe'] === $filterTipe);
}

$tipeList = ['Kabin', 'Standard', 'Deluxe', 'Suite'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_guest.php';
?>

    <!-- Page Header -->
    <section class="pt-28 pb-12 bg-dark relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <img src="https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=1920&h=400&fit=crop" alt="" class="w-full h-full object-cover">
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-accent font-medium tracking-wider uppercase text-sm mb-3">Akomodasi</p>
            <h1 class="font-serif text-4xl sm:text-5xl text-white font-bold mb-4">Kamar & Kabin</h1>
            <p class="text-cream/60 max-w-2xl mx-auto">Temukan penginapan yang sempurna — dari kabin kayu di tengah hutan pinus hingga suite premium dengan pemandangan Gunung Kerinci.</p>
        </div>
    </section>

    <!-- Filter Bar -->
    <section class="bg-white border-b border-cream-dark sticky top-20 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-earth mb-1">Check-in</label>
                    <input type="date" name="checkin" value="<?= e($filterCheckin) ?>"
                           class="w-full px-4 py-2.5 bg-cream border border-cream-darker rounded-lg text-sm text-dark focus:border-primary transition-colors">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-earth mb-1">Check-out</label>
                    <input type="date" name="checkout" value="<?= e($filterCheckout) ?>"
                           class="w-full px-4 py-2.5 bg-cream border border-cream-darker rounded-lg text-sm text-dark focus:border-primary transition-colors">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-earth mb-1">Tipe Kamar</label>
                    <select name="tipe" class="w-full px-4 py-2.5 bg-cream border border-cream-darker rounded-lg text-sm text-dark focus:border-primary transition-colors">
                        <option value="">Semua Tipe</option>
                        <?php foreach ($tipeList as $tipe): ?>
                        <option value="<?= e($tipe) ?>" <?= $filterTipe === $tipe ? 'selected' : '' ?>><?= e($tipe) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-primary-light text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari
                </button>
            </form>
        </div>
    </section>

    <!-- Room Grid -->
    <section class="py-12 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-sm text-earth mb-6"><?= count($kamarList) ?> kamar ditemukan</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($kamarList as $kamar): ?>
                <div class="card-hover bg-white rounded-2xl overflow-hidden shadow-sm">
                    <div class="img-zoom relative h-56">
                        <img src="<?= e($kamar['foto']) ?>" alt="<?= e($kamar['nama']) ?>" class="w-full h-full object-cover" loading="lazy">
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-primary/90 text-white text-xs font-medium rounded-full backdrop-blur-sm">
                                <?= e($kamar['tipe']) ?>
                            </span>
                        </div>
                        <div class="absolute top-4 right-4 flex gap-2">
                            <span class="px-3 py-1 bg-white/90 text-dark text-xs font-medium rounded-full backdrop-blur-sm flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <?= $kamar['kapasitas'] ?>
                            </span>
                        </div>
                        <?php if ($kamar['status'] === 'maintenance'): ?>
                        <div class="absolute inset-0 bg-dark/60 flex items-center justify-center">
                            <span class="px-4 py-2 bg-warning text-white text-sm font-medium rounded-full">Maintenance</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <h3 class="font-serif text-xl font-semibold text-dark mb-2"><?= e($kamar['nama']) ?></h3>
                        <p class="text-earth text-sm mb-4 line-clamp-2"><?= e($kamar['deskripsi']) ?></p>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-primary"><?= format_rupiah($kamar['harga']) ?></span>
                                <span class="text-earth text-sm">/malam</span>
                            </div>
                            <a href="<?= BASE_URL ?>/guest/kamar_detail.php?id=<?= $kamar['id'] ?>"
                               class="px-5 py-2.5 bg-primary hover:bg-primary-light text-white text-sm font-medium rounded-full transition-colors">
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
