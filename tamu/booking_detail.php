<?php
/**
 * Detail Booking — Kincay Mania Hotel & Resort
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');
$pageTitle = 'Detail Booking';
$id = (int)($_GET['id'] ?? 1);

$booking = [
    'id' => $id, 'kamar' => 'Kabin Pinus A1', 'tipe' => 'Kabin', 'checkin' => '2026-07-20', 'checkout' => '2026-07-23',
    'jumlah_malam' => 3, 'jumlah_tamu' => 2, 'catatan' => 'Minta extra pillow', 'harga_kamar' => 450000,
    'total_kamar' => 1350000, 'total' => 2350000, 'status' => 'dikonfirmasi', 'created' => '2026-07-16 10:30:00',
    'paket_wisata' => [
        ['nama' => 'Trekking Gunung Kerinci', 'tanggal' => '2026-07-20', 'peserta' => 2, 'subtotal' => 700000],
        ['nama' => 'Wisata Kuliner Lokal', 'tanggal' => '2026-07-22', 'peserta' => 2, 'subtotal' => 300000],
    ],
    'pembayaran' => ['nominal' => 2350000, 'status' => 'diterima', 'tanggal' => '2026-07-16 11:00:00'],
];

$statusLabels = [
    'menunggu_pembayaran' => ['label' => 'Menunggu Pembayaran', 'color' => 'bg-warning-light text-warning'],
    'menunggu_verifikasi' => ['label' => 'Menunggu Verifikasi', 'color' => 'bg-info-light text-info'],
    'dikonfirmasi' => ['label' => 'Dikonfirmasi', 'color' => 'bg-success-light text-success'],
    'checkin' => ['label' => 'Check-in', 'color' => 'bg-primary/10 text-primary'],
    'selesai' => ['label' => 'Selesai', 'color' => 'bg-success-light text-success'],
    'dibatalkan' => ['label' => 'Dibatalkan', 'color' => 'bg-gray-100 text-gray-500'],
    'ditolak' => ['label' => 'Ditolak', 'color' => 'bg-danger-light text-danger'],
    'menunggu_refund' => ['label' => 'Menunggu Refund', 'color' => 'bg-warning-light text-warning'],
    'refund_selesai' => ['label' => 'Refund Selesai', 'color' => 'bg-success-light text-success'],
];
$s = $statusLabels[$booking['status']];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_tamu.php';
?>

    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back -->
            <a href="<?= BASE_URL ?>/tamu/riwayat.php" class="inline-flex items-center gap-2 text-earth hover:text-primary text-sm mb-6 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Riwayat
            </a>

            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-8">
                <div>
                    <h1 class="font-sans text-3xl text-dark font-bold">Booking #BK-<?= str_pad($id, 4, '0', STR_PAD_LEFT) ?></h1>
                    <p class="text-earth mt-1">Dibuat pada <?= date('d M Y, H:i', strtotime($booking['created'])) ?></p>
                </div>
                <span class="px-4 py-2 <?= $s['color'] ?> text-sm font-medium rounded-full"><?= $s['label'] ?></span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Kamar Detail -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h2 class="font-semibold text-dark mb-4">Detail Kamar</h2>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Kamar</span><span class="font-medium"><?= e($booking['kamar']) ?> (<?= e($booking['tipe']) ?>)</span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Check-in</span><span class="font-medium"><?= date('d M Y', strtotime($booking['checkin'])) ?></span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Check-out</span><span class="font-medium"><?= date('d M Y', strtotime($booking['checkout'])) ?></span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Durasi</span><span class="font-medium"><?= $booking['jumlah_malam'] ?> malam</span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Jumlah Tamu</span><span class="font-medium"><?= $booking['jumlah_tamu'] ?> orang</span></div>
                            <div class="flex justify-between py-2"><span class="text-earth">Catatan</span><span class="font-medium"><?= e($booking['catatan'] ?: '-') ?></span></div>
                        </div>
                    </div>

                    <!-- Paket Wisata -->
                    <?php if (!empty($booking['paket_wisata'])): ?>
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h2 class="font-semibold text-dark mb-4">Paket Wisata</h2>
                        <?php foreach ($booking['paket_wisata'] as $pw): ?>
                        <div class="flex justify-between items-center py-3 border-b border-cream text-sm last:border-0">
                            <div>
                                <p class="font-medium text-dark"><?= e($pw['nama']) ?></p>
                                <p class="text-earth text-xs"><?= date('d M Y', strtotime($pw['tanggal'])) ?> · <?= $pw['peserta'] ?> peserta</p>
                            </div>
                            <span class="font-medium"><?= format_rupiah($pw['subtotal']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Pembayaran -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h2 class="font-semibold text-dark mb-4">Riwayat Pembayaran</h2>
                        <div class="flex justify-between items-center py-3 text-sm">
                            <div>
                                <p class="font-medium text-dark">Transfer Bank — <?= format_rupiah($booking['pembayaran']['nominal']) ?></p>
                                <p class="text-earth text-xs"><?= date('d M Y, H:i', strtotime($booking['pembayaran']['tanggal'])) ?></p>
                            </div>
                            <span class="px-3 py-1 bg-success-light text-success text-xs font-medium rounded-full">Diterima</span>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-28">
                        <h3 class="font-semibold text-dark mb-4">Ringkasan Harga</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between"><span class="text-earth">Kamar (<?= $booking['jumlah_malam'] ?> mlm)</span><span><?= format_rupiah($booking['total_kamar']) ?></span></div>
                            <div class="flex justify-between"><span class="text-earth">Paket Wisata</span><span><?= format_rupiah(1000000) ?></span></div>
                            <div class="border-t border-cream-dark pt-3">
                                <div class="flex justify-between"><span class="font-semibold text-dark">Total</span><span class="text-xl font-bold text-primary"><?= format_rupiah($booking['total']) ?></span></div>
                            </div>
                        </div>

                        <div class="mt-6 space-y-2">
                            <a href="<?= BASE_URL ?>/tamu/invoice.php?id=<?= $id ?>" class="w-full py-2.5 bg-primary hover:bg-primary-light text-white text-sm font-medium rounded-xl text-center flex items-center justify-center gap-2 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Cetak Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
