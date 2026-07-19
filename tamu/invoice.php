<?php
/** Invoice / Cetak — Kincay Mania Hotel & Resort */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');
$pageTitle = 'Invoice';
$id = (int)($_GET['id'] ?? 1);
$booking = ['id' => $id, 'kamar' => 'Kabin Pinus A1', 'tipe' => 'Kabin', 'checkin' => '2026-07-20', 'checkout' => '2026-07-23', 'jumlah_malam' => 3, 'jumlah_tamu' => 2, 'harga_kamar' => 450000, 'total_kamar' => 1350000, 'total' => 2350000, 'status' => 'dikonfirmasi', 'created' => '2026-07-16', 'nama_tamu' => 'Budi Tamu', 'email' => 'tamu@kincaymania.com', 'no_hp' => '081234567890',
    'paket' => [['nama' => 'Trekking Gunung Kerinci', 'tanggal' => '2026-07-20', 'peserta' => 2, 'subtotal' => 700000], ['nama' => 'Wisata Kuliner Lokal', 'tanggal' => '2026-07-22', 'peserta' => 2, 'subtotal' => 300000]]
];
include __DIR__ . '/../includes/header.php';
?>
    <!-- Print Button (no-print) -->
    <div class="no-print fixed top-0 left-0 right-0 z-50 bg-dark/95 backdrop-blur-md shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="<?= BASE_URL ?>/tamu/riwayat.php" class="text-cream/80 hover:text-white text-sm flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali</a>
            <button onclick="window.print()" class="px-5 py-2 bg-accent hover:bg-accent-light text-dark text-sm font-semibold rounded-full transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Invoice
            </button>
        </div>
    </div>

    <!-- Invoice Content -->
    <div class="max-w-3xl mx-auto px-8 py-20">
        <!-- Header -->
        <div class="flex justify-between items-start mb-10 border-b-2 border-primary pb-6">
            <div>
                <h1 class="font-sans text-3xl text-primary font-bold">Kincay Mania</h1>
                <p class="text-sm text-earth">Hotel & Resort — Nature Resort Kerinci</p>
                <p class="text-xs text-earth mt-2">Jl. Raya Kerinci No. 88, Kerinci, Jambi</p>
                <p class="text-xs text-earth">Tel: +62 812-3456-7890 | info@kincaymania.com</p>
            </div>
            <div class="text-right">
                <h2 class="text-2xl font-bold text-dark">INVOICE</h2>
                <p class="text-sm text-earth mt-1">#INV-<?= str_pad($id, 4, '0', STR_PAD_LEFT) ?></p>
                <p class="text-sm text-earth">Tanggal: <?= date('d M Y', strtotime($booking['created'])) ?></p>
            </div>
        </div>

        <!-- Guest Info -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <p class="text-xs text-earth uppercase tracking-wider mb-2">Ditagihkan Kepada:</p>
                <p class="font-semibold text-dark"><?= e($booking['nama_tamu']) ?></p>
                <p class="text-sm text-earth"><?= e($booking['email']) ?></p>
                <p class="text-sm text-earth"><?= e($booking['no_hp']) ?></p>
            </div>
            <div>
                <p class="text-xs text-earth uppercase tracking-wider mb-2">Detail Menginap:</p>
                <p class="text-sm text-dark">Check-in: <strong><?= date('d M Y', strtotime($booking['checkin'])) ?></strong></p>
                <p class="text-sm text-dark">Check-out: <strong><?= date('d M Y', strtotime($booking['checkout'])) ?></strong></p>
                <p class="text-sm text-dark">Durasi: <strong><?= $booking['jumlah_malam'] ?> malam</strong></p>
            </div>
        </div>

        <!-- Table -->
        <table class="w-full mb-8">
            <thead>
                <tr class="bg-primary/10">
                    <th class="text-left py-3 px-4 text-sm font-semibold text-dark">Deskripsi</th>
                    <th class="text-center py-3 px-4 text-sm font-semibold text-dark">Qty</th>
                    <th class="text-right py-3 px-4 text-sm font-semibold text-dark">Harga</th>
                    <th class="text-right py-3 px-4 text-sm font-semibold text-dark">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-cream-dark">
                    <td class="py-3 px-4 text-sm"><?= e($booking['kamar']) ?> (<?= e($booking['tipe']) ?>)</td>
                    <td class="py-3 px-4 text-sm text-center"><?= $booking['jumlah_malam'] ?> malam</td>
                    <td class="py-3 px-4 text-sm text-right"><?= format_rupiah($booking['harga_kamar']) ?></td>
                    <td class="py-3 px-4 text-sm text-right font-medium"><?= format_rupiah($booking['total_kamar']) ?></td>
                </tr>
                <?php foreach ($booking['paket'] as $pw): ?>
                <tr class="border-b border-cream-dark">
                    <td class="py-3 px-4 text-sm"><?= e($pw['nama']) ?> <span class="text-earth">(<?= date('d M', strtotime($pw['tanggal'])) ?>)</span></td>
                    <td class="py-3 px-4 text-sm text-center"><?= $pw['peserta'] ?> org</td>
                    <td class="py-3 px-4 text-sm text-right"><?= format_rupiah($pw['subtotal'] / $pw['peserta']) ?></td>
                    <td class="py-3 px-4 text-sm text-right font-medium"><?= format_rupiah($pw['subtotal']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="bg-cream">
                    <td colspan="3" class="py-4 px-4 text-right font-bold text-dark text-lg">TOTAL</td>
                    <td class="py-4 px-4 text-right font-bold text-primary text-xl"><?= format_rupiah($booking['total']) ?></td>
                </tr>
            </tfoot>
        </table>

        <!-- Footer -->
        <div class="border-t border-cream-dark pt-6 text-center text-sm text-earth">
            <p>Terima kasih telah memilih Kincay Mania Hotel & Resort.</p>
            <p class="mt-1">Selamat menikmati liburan Anda di alam Kerinci! 🌿</p>
        </div>
    </div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
