<?php
/** Verifikasi Pembayaran — Resepsionis */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Verifikasi Pembayaran';
$daftarVerifikasi = [
    ['id' => 1, 'booking_id' => 2, 'nama' => 'Budi Tamu', 'kamar' => 'Kamar Deluxe B1', 'total' => 1500000, 'tanggal_upload' => '2026-07-15 14:30:00', 'nominal' => 1500000],
    ['id' => 2, 'booking_id' => 8, 'nama' => 'Andi Wisata', 'kamar' => 'Kabin Pinus A1', 'total' => 900000, 'tanggal_upload' => '2026-07-16 09:15:00', 'nominal' => 900000],
    ['id' => 3, 'booking_id' => 9, 'nama' => 'Sari Lestari', 'kamar' => 'Suite Kerinci C1', 'total' => 3600000, 'tanggal_upload' => '2026-07-16 11:45:00', 'nominal' => 3600000],
];
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_resepsionis.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Verifikasi Pembayaran</h1>
            <p class="text-earth mb-8"><?= count($daftarVerifikasi) ?> booking menunggu verifikasi.</p>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead><tr class="bg-cream border-b border-cream-dark">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Booking</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Tamu</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Kamar</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-earth uppercase tracking-wider">Nominal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Upload</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-earth uppercase tracking-wider">Aksi</th>
                        </tr></thead>
                        <tbody class="divide-y divide-cream">
                            <?php foreach ($daftarVerifikasi as $v): ?>
                            <tr class="hover:bg-cream/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-mono text-dark">#BK-<?= str_pad($v['booking_id'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td class="px-6 py-4 text-sm font-medium text-dark"><?= e($v['nama']) ?></td>
                                <td class="px-6 py-4 text-sm text-earth"><?= e($v['kamar']) ?></td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-primary"><?= format_rupiah($v['nominal']) ?></td>
                                <td class="px-6 py-4 text-sm text-earth"><?= date('d M H:i', strtotime($v['tanggal_upload'])) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <a href="<?= BASE_URL ?>/resepsionis/verifikasi_detail.php?id=<?= $v['id'] ?>" class="px-4 py-2 bg-primary hover:bg-primary-light text-white text-xs font-medium rounded-full transition-colors">Review</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
