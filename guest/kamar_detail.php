<?php
/**
 * Detail Kamar — Kincay Mania Hotel & Resort
 */
require_once __DIR__ . '/../config/config.php';

// Dummy data detail kamar
$kamarData = [
    1 => ['id' => 1, 'nama' => 'Kabin Pinus A1', 'tipe' => 'Kabin', 'kapasitas' => 2, 'harga' => 450000, 'foto' => 'https://images.unsplash.com/photo-1618767689160-da3fb810aad7?w=900&h=600&fit=crop', 'deskripsi' => "Kabin kayu eksklusif yang terletak di tengah hutan pinus Kerinci. Didesain dengan konsep rustic-modern, kabin ini menawarkan pengalaman menginap yang unik dengan pemandangan gunung yang memukau.\n\nDilengkapi dengan tempat tidur queen-size, kamar mandi dalam dengan air panas, teras privat, serta fasilitas dasar seperti handuk, perlengkapan mandi, dan Wi-Fi.", 'fasilitas' => ['Tempat Tidur Queen-Size', 'Kamar Mandi Dalam', 'Air Panas', 'Teras Privat', 'Wi-Fi Gratis', 'Handuk & Perlengkapan Mandi']],
    2 => ['id' => 2, 'nama' => 'Kabin Pinus A2', 'tipe' => 'Kabin', 'kapasitas' => 2, 'harga' => 450000, 'foto' => 'https://images.unsplash.com/photo-1510798831971-661eb04b3739?w=900&h=600&fit=crop', 'deskripsi' => "Kabin kayu nyaman dengan teras privat menghadap kebun teh Kayu Aro. Suasana tenang dan sejuk, sempurna untuk pasangan yang mencari ketenangan.\n\nDilengkapi fasilitas lengkap untuk kenyamanan Anda.", 'fasilitas' => ['Tempat Tidur Queen-Size', 'Kamar Mandi Dalam', 'Air Panas', 'Teras Privat', 'Wi-Fi Gratis', 'Pemandangan Kebun Teh']],
    3 => ['id' => 3, 'nama' => 'Kamar Deluxe B1', 'tipe' => 'Deluxe', 'kapasitas' => 4, 'harga' => 750000, 'foto' => 'https://images.unsplash.com/photo-1590490360182-c33d955f4c4e?w=900&h=600&fit=crop', 'deskripsi' => "Kamar deluxe yang luas dengan pemandangan Gunung Kerinci langsung dari jendela. Cocok untuk keluarga kecil atau rombongan.\n\nDilengkapi dengan 2 tempat tidur single atau 1 king-size, AC, TV kabel, minibar, dan balkon.", 'fasilitas' => ['2 Tempat Tidur / 1 King-Size', 'AC', 'TV Kabel', 'Minibar', 'Balkon', 'Kamar Mandi Dalam', 'Air Panas', 'Wi-Fi Gratis']],
    4 => ['id' => 4, 'nama' => 'Kamar Deluxe B2', 'tipe' => 'Deluxe', 'kapasitas' => 4, 'harga' => 750000, 'foto' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=900&h=600&fit=crop', 'deskripsi' => "Kamar deluxe dengan balkon dan area duduk nyaman. Pemandangan taman tropis resort.", 'fasilitas' => ['2 Tempat Tidur / 1 King-Size', 'AC', 'TV Kabel', 'Minibar', 'Balkon', 'Kamar Mandi Dalam', 'Air Panas', 'Wi-Fi Gratis']],
    5 => ['id' => 5, 'nama' => 'Suite Kerinci C1', 'tipe' => 'Suite', 'kapasitas' => 6, 'harga' => 1200000, 'foto' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=900&h=600&fit=crop', 'deskripsi' => "Suite premium terbaik kami. Ruang tamu terpisah, kamar tidur mewah, jacuzzi pribadi, dan balkon panorama 180° menghadap Gunung Kerinci.\n\nPengalaman menginap paling eksklusif di resort kami.", 'fasilitas' => ['Ruang Tamu Terpisah', '1 King-Size Bed', 'Jacuzzi Pribadi', 'Balkon Panorama', 'AC', 'Smart TV 55"', 'Minibar Premium', 'Kamar Mandi Mewah', 'Bathrobe & Sandal', 'Wi-Fi High-Speed']],
    6 => ['id' => 6, 'nama' => 'Standard Room D1', 'tipe' => 'Standard', 'kapasitas' => 2, 'harga' => 300000, 'foto' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=900&h=600&fit=crop', 'deskripsi' => "Kamar standar yang bersih dan nyaman. Pilihan tepat untuk backpacker atau traveler solo yang mengutamakan kenyamanan dengan budget terjangkau.", 'fasilitas' => ['Tempat Tidur Double', 'Kipas Angin', 'Kamar Mandi Dalam', 'Wi-Fi Gratis', 'Handuk']],
];

$id = (int)($_GET['id'] ?? 1);
$kamar = $kamarData[$id] ?? $kamarData[1];

$pageTitle = $kamar['nama'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_guest.php';
?>

    <!-- Breadcrumb -->
    <div class="pt-24 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center gap-2 text-sm text-earth">
                <a href="<?= BASE_URL ?>/guest/" class="hover:text-primary transition-colors">Beranda</a>
                <svg class="w-4 h-4 text-earth/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="<?= BASE_URL ?>/guest/kamar.php" class="hover:text-primary transition-colors">Kamar & Kabin</a>
                <svg class="w-4 h-4 text-earth/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-dark font-medium"><?= e($kamar['nama']) ?></span>
            </nav>
        </div>
    </div>

    <!-- Detail Content -->
    <section class="pb-16 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <!-- Left: Image & Info (3 cols) -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Main Image -->
                    <div class="rounded-2xl overflow-hidden shadow-lg">
                        <img src="<?= e($kamar['foto']) ?>" alt="<?= e($kamar['nama']) ?>" class="w-full h-80 sm:h-96 lg:h-[28rem] object-cover">
                    </div>

                    <!-- Description -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm">
                        <h2 class="font-serif text-2xl text-dark font-semibold mb-4">Deskripsi</h2>
                        <div class="text-earth leading-relaxed whitespace-pre-line"><?= e($kamar['deskripsi']) ?></div>
                    </div>

                    <!-- Fasilitas -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm">
                        <h2 class="font-serif text-2xl text-dark font-semibold mb-4">Fasilitas</h2>
                        <div class="grid grid-cols-2 gap-3">
                            <?php foreach ($kamar['fasilitas'] as $f): ?>
                            <div class="flex items-center gap-3 text-earth">
                                <svg class="w-5 h-5 text-success shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-sm"><?= e($f) ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Right: Booking Card (2 cols) -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl p-8 shadow-sm sticky top-28">
                        <!-- Header -->
                        <div class="mb-6">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full"><?= e($kamar['tipe']) ?></span>
                                <span class="flex items-center gap-1 text-sm text-earth">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Maks. <?= $kamar['kapasitas'] ?> tamu
                                </span>
                            </div>
                            <h1 class="font-serif text-2xl text-dark font-bold"><?= e($kamar['nama']) ?></h1>
                        </div>

                        <!-- Price -->
                        <div class="flex items-baseline gap-2 mb-6 pb-6 border-b border-cream-dark">
                            <span class="text-3xl font-bold text-primary"><?= format_rupiah($kamar['harga']) ?></span>
                            <span class="text-earth">/malam</span>
                        </div>

                        <!-- Quick Booking Form -->
                        <form method="GET" action="<?= BASE_URL ?>/tamu/booking.php" class="space-y-4">
                            <input type="hidden" name="kamar_id" value="<?= $kamar['id'] ?>">

                            <div>
                                <label class="block text-sm font-medium text-dark mb-1">Check-in</label>
                                <input type="date" name="checkin" required min="<?= date('Y-m-d') ?>"
                                       class="w-full px-4 py-2.5 bg-cream border border-cream-darker rounded-lg text-sm text-dark focus:border-primary transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1">Check-out</label>
                                <input type="date" name="checkout" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                       class="w-full px-4 py-2.5 bg-cream border border-cream-darker rounded-lg text-sm text-dark focus:border-primary transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1">Jumlah Tamu</label>
                                <select name="jumlah_tamu" class="w-full px-4 py-2.5 bg-cream border border-cream-darker rounded-lg text-sm text-dark focus:border-primary transition-colors">
                                    <?php for ($i = 1; $i <= $kamar['kapasitas']; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?> tamu</option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <button type="submit"
                                    class="w-full py-3.5 bg-accent hover:bg-accent-light text-dark font-semibold rounded-xl transition-all hover:shadow-lg hover:shadow-accent/25 flex items-center justify-center gap-2 text-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Booking Sekarang
                            </button>
                        </form>

                        <!-- Info -->
                        <div class="mt-6 pt-6 border-t border-cream-dark space-y-3 text-sm text-earth">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Konfirmasi instan setelah pembayaran
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Pembatalan gratis H-2 (refund 50%)
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Bisa tambah paket wisata
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
