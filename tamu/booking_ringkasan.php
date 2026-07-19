<?php
/**
 * Ringkasan Booking (Step 3) — Kincay Mania Hotel & Resort
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');
$pageTitle = 'Ringkasan Booking';

// Dummy ringkasan booking
$booking = [
    'kamar' => ['nama' => 'Kabin Pinus A1', 'tipe' => 'Kabin', 'harga' => 450000],
    'checkin' => '2026-07-20',
    'checkout' => '2026-07-23',
    'jumlah_malam' => 3,
    'jumlah_tamu' => 2,
    'catatan' => 'Minta extra pillow',
    'harga_kamar_total' => 1350000,
    'paket_wisata' => [
        ['nama' => 'Trekking Gunung Kerinci', 'tanggal' => '2026-07-20', 'peserta' => 2, 'harga_satuan' => 350000, 'subtotal' => 700000],
        ['nama' => 'Wisata Kuliner Lokal', 'tanggal' => '2026-07-22', 'peserta' => 2, 'harga_satuan' => 150000, 'subtotal' => 300000],
    ],
    'total_paket' => 1000000,
    'grand_total' => 2350000,
];

$bank = ['nama' => 'Bank Mandiri', 'no_rek' => '1234-5678-9012', 'pemilik' => 'PT Kincay Mania Resort'];

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
                <div class="flex-1 h-0.5 mx-4 bg-success"></div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-success text-white flex items-center justify-center font-semibold text-sm">✓</div>
                    <span class="font-medium text-success text-sm hidden sm:block">Paket Wisata</span>
                </div>
                <div class="flex-1 h-0.5 mx-4 bg-primary"></div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-semibold text-sm">3</div>
                    <span class="font-medium text-dark text-sm hidden sm:block">Ringkasan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <section class="py-12 bg-cream">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Ringkasan Booking</h1>
            <p class="text-earth mb-8">Langkah 3 dari 3 — Periksa kembali detail booking Anda sebelum konfirmasi.</p>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Detail -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Kamar -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h2 class="font-semibold text-dark mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Detail Kamar
                        </h2>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Kamar</span><span class="font-medium text-dark"><?= e($booking['kamar']['nama']) ?> (<?= e($booking['kamar']['tipe']) ?>)</span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Check-in</span><span class="font-medium text-dark"><?= date('d M Y', strtotime($booking['checkin'])) ?></span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Check-out</span><span class="font-medium text-dark"><?= date('d M Y', strtotime($booking['checkout'])) ?></span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Durasi</span><span class="font-medium text-dark"><?= $booking['jumlah_malam'] ?> malam</span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Jumlah Tamu</span><span class="font-medium text-dark"><?= $booking['jumlah_tamu'] ?> orang</span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Catatan</span><span class="font-medium text-dark"><?= e($booking['catatan'] ?: '-') ?></span></div>
                            <div class="flex justify-between py-2"><span class="text-earth">Harga Kamar</span><span class="font-bold text-dark"><?= format_rupiah($booking['kamar']['harga']) ?> × <?= $booking['jumlah_malam'] ?> malam = <?= format_rupiah($booking['harga_kamar_total']) ?></span></div>
                        </div>
                    </div>

                    <!-- Paket Wisata -->
                    <?php if (!empty($booking['paket_wisata'])): ?>
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h2 class="font-semibold text-dark mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Paket Wisata Add-on
                        </h2>
                        <div class="space-y-3">
                            <?php foreach ($booking['paket_wisata'] as $pw): ?>
                            <div class="flex justify-between items-center py-3 border-b border-cream text-sm">
                                <div>
                                    <p class="font-medium text-dark"><?= e($pw['nama']) ?></p>
                                    <p class="text-earth text-xs"><?= date('d M Y', strtotime($pw['tanggal'])) ?> · <?= $pw['peserta'] ?> peserta</p>
                                </div>
                                <span class="font-medium text-dark"><?= format_rupiah($pw['harga_satuan']) ?> × <?= $pw['peserta'] ?> = <?= format_rupiah($pw['subtotal']) ?></span>
                            </div>
                            <?php endforeach; ?>
                            <div class="flex justify-between py-2">
                                <span class="text-earth text-sm">Subtotal Paket Wisata</span>
                                <span class="font-bold text-dark"><?= format_rupiah($booking['total_paket']) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar: Total & Confirm -->
                <div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-28">
                        <h3 class="font-semibold text-dark mb-4">Rincian Pembayaran</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between"><span class="text-earth">Kamar (<?= $booking['jumlah_malam'] ?> malam)</span><span class="text-dark"><?= format_rupiah($booking['harga_kamar_total']) ?></span></div>
                            <?php if ($booking['total_paket'] > 0): ?>
                            <div class="flex justify-between"><span class="text-earth">Paket Wisata</span><span class="text-dark"><?= format_rupiah($booking['total_paket']) ?></span></div>
                            <?php endif; ?>
                            <div class="border-t border-cream-dark pt-3 mt-3">
                                <div class="flex justify-between">
                                    <span class="font-semibold text-dark text-lg">Total</span>
                                    <span class="font-bold text-primary text-2xl"><?= format_rupiah($booking['grand_total']) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Info -->
                        <div class="mt-6 p-4 bg-cream rounded-xl border border-cream-darker">
                            <p class="text-xs font-medium text-earth uppercase tracking-wider mb-2">Transfer ke:</p>
                            <p class="font-bold text-dark"><?= e($bank['nama']) ?></p>
                            <p class="text-lg font-mono font-bold text-primary mt-1"><?= e($bank['no_rek']) ?></p>
                            <p class="text-sm text-earth">a.n. <?= e($bank['pemilik']) ?></p>
                        </div>

                        <!-- Confirm Button -->
                        <form method="POST" action="<?= BASE_URL ?>/tamu/booking_ringkasan.php" class="mt-6">
                            <?= csrf_field() ?>
                            <button type="submit" name="konfirmasi_booking"
                                    class="w-full py-3.5 bg-accent hover:bg-accent-light text-dark font-semibold rounded-xl transition-all hover:shadow-lg hover:shadow-accent/25 flex items-center justify-center gap-2 text-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Konfirmasi Booking
                            </button>
                            <p class="text-xs text-earth text-center mt-3">Setelah konfirmasi, lakukan transfer dan upload bukti pembayaran.</p>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Back -->
            <div class="mt-8">
                <a href="<?= BASE_URL ?>/tamu/booking_paket.php" class="text-earth hover:text-dark flex items-center gap-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Paket Wisata
                </a>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
