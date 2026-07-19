<?php
/** Dashboard Admin — Kincay Mania Hotel & Resort */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');
$pageTitle = 'Dashboard Admin';

// Dummy summary stats
$stats = [
    'total_booking' => 47, 'booking_bulan_ini' => 12,
    'total_pendapatan' => 35400000, 'pendapatan_bulan_ini' => 8750000,
    'total_tamu' => 35, 'kamar_terisi' => 2, 'kamar_total' => 6,
    'paket_terjual' => 18,
];

$recentBooking = [
    ['id' => 12, 'nama' => 'Budi Tamu', 'kamar' => 'Suite Kerinci C1', 'total' => 4600000, 'status' => 'dikonfirmasi', 'tanggal' => '2026-07-16'],
    ['id' => 11, 'nama' => 'Dewi Anggraini', 'kamar' => 'Kabin Pinus A1', 'total' => 900000, 'status' => 'menunggu_verifikasi', 'tanggal' => '2026-07-15'],
    ['id' => 10, 'nama' => 'Andi Wisata', 'kamar' => 'Deluxe B1', 'total' => 2250000, 'status' => 'selesai', 'tanggal' => '2026-07-14'],
    ['id' => 9, 'nama' => 'Sari Lestari', 'kamar' => 'Standard D1', 'total' => 600000, 'status' => 'menunggu_pembayaran', 'tanggal' => '2026-07-13'],
    ['id' => 8, 'nama' => 'Rudi Hartono', 'kamar' => 'Kabin Pinus A2', 'total' => 1350000, 'status' => 'dibatalkan', 'tanggal' => '2026-07-12'],
];

$statusColors = [
    'menunggu_pembayaran' => 'bg-warning-light text-warning', 'menunggu_verifikasi' => 'bg-info-light text-info',
    'dikonfirmasi' => 'bg-success-light text-success', 'selesai' => 'bg-success-light text-success',
    'dibatalkan' => 'bg-gray-100 text-gray-500',
];
$statusLabels = [
    'menunggu_pembayaran' => 'Menunggu Bayar', 'menunggu_verifikasi' => 'Verifikasi',
    'dikonfirmasi' => 'Dikonfirmasi', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan',
];

// Dummy monthly revenue data for chart
$pendapatanBulanan = [3200000, 4100000, 5600000, 4800000, 6200000, 7500000, 8750000];
$bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="font-sans text-3xl text-dark font-bold">Dashboard Admin</h1>
                <p class="text-earth mt-1">Ringkasan bisnis Kincay Mania Hotel & Resort — <?= date('d M Y') ?></p>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-primary">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-earth">Total Booking</span>
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center"><svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
                    </div>
                    <p class="text-3xl font-bold text-dark"><?= $stats['total_booking'] ?></p>
                    <p class="text-xs text-earth mt-1"><?= $stats['booking_bulan_ini'] ?> bulan ini</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-success">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-earth">Total Pendapatan</span>
                        <div class="w-10 h-10 rounded-xl bg-success/10 flex items-center justify-center"><svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    </div>
                    <p class="text-3xl font-bold text-dark"><?= format_rupiah($stats['total_pendapatan']) ?></p>
                    <p class="text-xs text-earth mt-1"><?= format_rupiah($stats['pendapatan_bulan_ini']) ?> bulan ini</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-accent">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-earth">Hunian Kamar</span>
                        <div class="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center"><svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                    </div>
                    <p class="text-3xl font-bold text-dark"><?= $stats['kamar_terisi'] ?>/<?= $stats['kamar_total'] ?></p>
                    <p class="text-xs text-earth mt-1"><?= round(($stats['kamar_terisi'] / $stats['kamar_total']) * 100) ?>% terisi</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-secondary">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-earth">Paket Terjual</span>
                        <div class="w-10 h-10 rounded-xl bg-secondary/10 flex items-center justify-center"><svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    </div>
                    <p class="text-3xl font-bold text-dark"><?= $stats['paket_terjual'] ?></p>
                    <p class="text-xs text-earth mt-1">Total peserta paket wisata</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Revenue Chart (CSS-only bar chart) -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-semibold text-dark mb-6">Pendapatan Bulanan (2026)</h2>
                    <div class="flex items-end gap-3 h-48">
                        <?php
                        $maxVal = max($pendapatanBulanan);
                        foreach ($pendapatanBulanan as $i => $val):
                            $pct = ($val / $maxVal) * 100;
                            $isLast = $i === count($pendapatanBulanan) - 1;
                        ?>
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <span class="text-xs text-earth font-medium"><?= number_format($val / 1000000, 1) ?>jt</span>
                            <div class="w-full rounded-t-lg transition-all <?= $isLast ? 'bg-primary' : 'bg-primary/30' ?>" style="height: <?= $pct ?>%"></div>
                            <span class="text-xs text-earth"><?= $bulanLabels[$i] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-semibold text-dark mb-4">Pintasan</h2>
                    <div class="space-y-3">
                        <a href="<?= BASE_URL ?>/admin/kelola_kamar.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-cream transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors"><svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg></div>
                            <div><p class="text-sm font-medium text-dark">Kelola Kamar</p><p class="text-xs text-earth"><?= $stats['kamar_total'] ?> kamar</p></div>
                        </a>
                        <a href="<?= BASE_URL ?>/admin/kelola_paket.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-cream transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center group-hover:bg-secondary/20 transition-colors"><svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064"/></svg></div>
                            <div><p class="text-sm font-medium text-dark">Kelola Paket Wisata</p><p class="text-xs text-earth">4 paket aktif</p></div>
                        </a>
                        <a href="<?= BASE_URL ?>/admin/kelola_user.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-cream transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-info/10 flex items-center justify-center group-hover:bg-info/20 transition-colors"><svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>
                            <div><p class="text-sm font-medium text-dark">Kelola User</p><p class="text-xs text-earth"><?= $stats['total_tamu'] ?> tamu terdaftar</p></div>
                        </a>
                        <a href="<?= BASE_URL ?>/admin/laporan.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-cream transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center group-hover:bg-accent/20 transition-colors"><svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
                            <div><p class="text-sm font-medium text-dark">Laporan</p><p class="text-xs text-earth">Pendapatan & okupansi</p></div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings Table -->
            <div class="mt-8 bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-cream">
                    <h2 class="font-semibold text-dark">Booking Terbaru</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead><tr class="bg-cream/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase tracking-wider">Tamu</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase tracking-wider">Kamar</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-earth uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-earth uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase tracking-wider">Tanggal</th>
                        </tr></thead>
                        <tbody class="divide-y divide-cream">
                            <?php foreach ($recentBooking as $b): ?>
                            <tr class="hover:bg-cream/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-mono text-dark">#BK-<?= str_pad($b['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td class="px-6 py-4 text-sm font-medium text-dark"><?= e($b['nama']) ?></td>
                                <td class="px-6 py-4 text-sm text-earth"><?= e($b['kamar']) ?></td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-primary"><?= format_rupiah($b['total']) ?></td>
                                <td class="px-6 py-4 text-center"><span class="px-3 py-1 <?= $statusColors[$b['status']] ?> text-xs font-medium rounded-full"><?= $statusLabels[$b['status']] ?></span></td>
                                <td class="px-6 py-4 text-sm text-earth"><?= date('d M Y', strtotime($b['tanggal'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
