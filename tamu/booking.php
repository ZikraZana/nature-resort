<?php
/**
 * Booking Kamar (Step 1) — Kincay Mania Hotel & Resort
 * Pilih kamar, tanggal, jumlah tamu
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');
$pageTitle = 'Booking Kamar';

// Pre-filled data from query params
$kamarId = (int)($_GET['kamar_id'] ?? 1);
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';

// Dummy kamar
$kamarList = [
    1 => ['id' => 1, 'nama' => 'Kabin Pinus A1', 'tipe' => 'Kabin', 'kapasitas' => 2, 'harga' => 450000, 'foto' => 'https://placehold.co/400x300/2D5016/FDF6E3?text=Kabin+Pinus+A1'],
    2 => ['id' => 2, 'nama' => 'Kabin Pinus A2', 'tipe' => 'Kabin', 'kapasitas' => 2, 'harga' => 450000, 'foto' => 'https://placehold.co/400x300/4A7C29/FDF6E3?text=Kabin+Pinus+A2'],
    3 => ['id' => 3, 'nama' => 'Kamar Deluxe B1', 'tipe' => 'Deluxe', 'kapasitas' => 4, 'harga' => 750000, 'foto' => 'https://placehold.co/400x300/8B6914/FDF6E3?text=Deluxe+B1'],
    5 => ['id' => 5, 'nama' => 'Suite Kerinci C1', 'tipe' => 'Suite', 'kapasitas' => 6, 'harga' => 1200000, 'foto' => 'https://placehold.co/400x300/6B4E2E/FDF6E3?text=Suite+Kerinci+C1'],
    6 => ['id' => 6, 'nama' => 'Standard Room D1', 'tipe' => 'Standard', 'kapasitas' => 2, 'harga' => 300000, 'foto' => 'https://placehold.co/400x300/1A2E0A/FDF6E3?text=Standard+D1'],
];

$selectedKamar = $kamarList[$kamarId] ?? null;

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_tamu.php';
?>

    <!-- Progress Steps -->
    <div class="pt-20 bg-white border-b border-cream-dark">
        <div class="max-w-3xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-semibold text-sm">1</div>
                    <span class="font-medium text-dark text-sm hidden sm:block">Pilih Kamar</span>
                </div>
                <div class="flex-1 h-0.5 mx-4 bg-cream-darker"></div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-cream-dark text-earth flex items-center justify-center font-semibold text-sm">2</div>
                    <span class="font-medium text-earth text-sm hidden sm:block">Paket Wisata</span>
                </div>
                <div class="flex-1 h-0.5 mx-4 bg-cream-darker"></div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-cream-dark text-earth flex items-center justify-center font-semibold text-sm">3</div>
                    <span class="font-medium text-earth text-sm hidden sm:block">Ringkasan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Form -->
    <section class="py-12 bg-cream">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Pilih Kamar & Tanggal</h1>
            <p class="text-earth mb-8">Langkah 1 dari 3 — Pilih kamar yang Anda inginkan dan tentukan tanggal menginap.</p>

            <form method="GET" action="<?= BASE_URL ?>/tamu/booking_paket.php" class="space-y-6">
                <!-- Pilih Kamar -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-semibold text-dark mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Kamar yang Dipilih
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($kamarList as $k): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="kamar_id" value="<?= $k['id'] ?>" class="peer hidden" <?= $k['id'] === $kamarId ? 'checked' : '' ?>>
                            <div class="border-2 border-cream-darker rounded-xl overflow-hidden transition-all peer-checked:border-primary peer-checked:shadow-md hover:border-earth/40">
                                <img src="<?= e($k['foto']) ?>" alt="<?= e($k['nama']) ?>" class="w-full h-32 object-cover">
                                <div class="p-3">
                                    <p class="font-medium text-dark text-sm"><?= e($k['nama']) ?></p>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-xs text-earth"><?= e($k['tipe']) ?> · <?= $k['kapasitas'] ?> tamu</span>
                                        <span class="text-sm font-bold text-primary"><?= format_rupiah($k['harga']) ?></span>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Tanggal & Detail -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-semibold text-dark mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Detail Menginap
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1">Check-in</label>
                            <input type="date" name="checkin" value="<?= e($checkin) ?>" required min="<?= date('Y-m-d') ?>"
                                   class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1">Check-out</label>
                            <input type="date" name="checkout" value="<?= e($checkout) ?>" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                   class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1">Jumlah Tamu</label>
                            <select name="jumlah_tamu" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?> tamu</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-dark mb-1">Catatan Khusus (opsional)</label>
                        <textarea name="catatan" rows="3" placeholder="Contoh: minta extra bed, lantai atas, dll."
                                  class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors resize-none"></textarea>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-between items-center">
                    <a href="<?= BASE_URL ?>/tamu/kamar.php" class="text-earth hover:text-dark flex items-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali
                    </a>
                    <button type="submit" class="px-8 py-3.5 bg-primary hover:bg-primary-light text-white font-semibold rounded-xl transition-all hover:shadow-lg flex items-center gap-2">
                        Lanjut: Paket Wisata
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
