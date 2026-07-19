<?php
/** Laporan — Admin */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');
$pageTitle = 'Laporan';

// Active tab
$tab = $_GET['tab'] ?? 'pendapatan';

// Dummy: Laporan Pendapatan
$pendapatanBulanan = [
    ['bulan' => 'Januari 2026',  'booking' => 5,  'kamar' => 2800000,  'paket' => 400000,   'total' => 3200000],
    ['bulan' => 'Februari 2026', 'booking' => 7,  'kamar' => 3500000,  'paket' => 600000,   'total' => 4100000],
    ['bulan' => 'Maret 2026',    'booking' => 9,  'kamar' => 4700000,  'paket' => 900000,   'total' => 5600000],
    ['bulan' => 'April 2026',    'booking' => 8,  'kamar' => 4000000,  'paket' => 800000,   'total' => 4800000],
    ['bulan' => 'Mei 2026',      'booking' => 10, 'kamar' => 5200000,  'paket' => 1000000,  'total' => 6200000],
    ['bulan' => 'Juni 2026',     'booking' => 11, 'kamar' => 6300000,  'paket' => 1200000,  'total' => 7500000],
    ['bulan' => 'Juli 2026',     'booking' => 12, 'kamar' => 7250000,  'paket' => 1500000,  'total' => 8750000],
];
$totalPendapatan = array_sum(array_column($pendapatanBulanan, 'total'));
$totalBooking = array_sum(array_column($pendapatanBulanan, 'booking'));

// Dummy: Laporan Okupansi
$okupansiKamar = [
    ['nama' => 'Kabin Pinus A1',    'tipe' => 'Kabin',    'total_malam' => 45, 'terisi_malam' => 32, 'persen' => 71],
    ['nama' => 'Kabin Pinus A2',    'tipe' => 'Kabin',    'total_malam' => 45, 'terisi_malam' => 28, 'persen' => 62],
    ['nama' => 'Kamar Deluxe B1',   'tipe' => 'Deluxe',   'total_malam' => 45, 'terisi_malam' => 38, 'persen' => 84],
    ['nama' => 'Kamar Deluxe B2',   'tipe' => 'Deluxe',   'total_malam' => 45, 'terisi_malam' => 15, 'persen' => 33],
    ['nama' => 'Suite Kerinci C1',  'tipe' => 'Suite',    'total_malam' => 45, 'terisi_malam' => 40, 'persen' => 89],
    ['nama' => 'Standard Room D1',  'tipe' => 'Standard', 'total_malam' => 45, 'terisi_malam' => 22, 'persen' => 49],
];
$avgOkupansi = round(array_sum(array_column($okupansiKamar, 'persen')) / count($okupansiKamar));

// Dummy: Laporan Paket Wisata
$performaPaket = [
    ['nama' => 'Trekking Gunung Kerinci',            'kategori' => 'trekking', 'total_peserta' => 48, 'total_pendapatan' => 16800000, 'rata_rating' => 4.8],
    ['nama' => 'River Tubing Sungai Batang Merangin', 'kategori' => 'perahu',   'total_peserta' => 65, 'total_pendapatan' => 16250000, 'rata_rating' => 4.6],
    ['nama' => 'Wisata Kuliner Lokal Kerinci',        'kategori' => 'kuliner',  'total_peserta' => 38, 'total_pendapatan' => 5700000,  'rata_rating' => 4.9],
    ['nama' => 'Susur Danau Kerinci',                 'kategori' => 'perahu',   'total_peserta' => 22, 'total_pendapatan' => 4400000,  'rata_rating' => 4.5],
];

$kategoriColors = [
    'trekking' => 'bg-success/10 text-success',
    'perahu'   => 'bg-info/10 text-info',
    'kuliner'  => 'bg-secondary/10 text-secondary',
];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div>
                    <h1 class="font-sans text-3xl text-dark font-bold">Laporan</h1>
                    <p class="text-earth mt-1">Analitik bisnis Kincay Mania Hotel & Resort</p>
                </div>
                <div class="flex items-center gap-2">
                    <select class="px-4 py-2 bg-white border border-cream-darker rounded-xl text-sm text-dark focus:border-primary transition-colors">
                        <option>2026</option>
                        <option>2025</option>
                    </select>
                    <button class="px-5 py-2 bg-primary/10 text-primary text-sm font-medium rounded-xl hover:bg-primary/20 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Unduh Laporan
                    </button>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white rounded-2xl p-1.5 shadow-sm mb-8 flex gap-1">
                <a href="?tab=pendapatan" class="flex-1 px-4 py-2.5 text-sm font-medium rounded-xl text-center transition-all <?= $tab === 'pendapatan' ? 'bg-primary text-white shadow-sm' : 'text-earth hover:text-dark hover:bg-cream' ?>">
                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pendapatan
                </a>
                <a href="?tab=okupansi" class="flex-1 px-4 py-2.5 text-sm font-medium rounded-xl text-center transition-all <?= $tab === 'okupansi' ? 'bg-primary text-white shadow-sm' : 'text-earth hover:text-dark hover:bg-cream' ?>">
                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Okupansi
                </a>
                <a href="?tab=paket" class="flex-1 px-4 py-2.5 text-sm font-medium rounded-xl text-center transition-all <?= $tab === 'paket' ? 'bg-primary text-white shadow-sm' : 'text-earth hover:text-dark hover:bg-cream' ?>">
                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Paket Wisata
                </a>
            </div>

            <?php if ($tab === 'pendapatan'): ?>
            <!-- ==================== TAB: PENDAPATAN ==================== -->
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-success">
                    <p class="text-sm text-earth mb-1">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-dark"><?= format_rupiah($totalPendapatan) ?></p>
                    <p class="text-xs text-success mt-1">↑ 16.7% dari bulan lalu</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-primary">
                    <p class="text-sm text-earth mb-1">Total Booking</p>
                    <p class="text-2xl font-bold text-dark"><?= $totalBooking ?></p>
                    <p class="text-xs text-primary mt-1">Rata-rata <?= round($totalBooking / count($pendapatanBulanan)) ?>/bulan</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-accent">
                    <p class="text-sm text-earth mb-1">Rata-rata per Booking</p>
                    <p class="text-2xl font-bold text-dark"><?= format_rupiah(round($totalPendapatan / $totalBooking)) ?></p>
                    <p class="text-xs text-earth mt-1">Nilai transaksi rata-rata</p>
                </div>
            </div>

            <!-- Chart -->
            <div class="bg-white rounded-2xl p-6 shadow-sm mb-8">
                <h2 class="font-semibold text-dark mb-6">Tren Pendapatan Bulanan (2026)</h2>
                <div class="flex items-end gap-3 h-52">
                    <?php
                    $maxVal = max(array_column($pendapatanBulanan, 'total'));
                    foreach ($pendapatanBulanan as $i => $p):
                        $pct = ($p['total'] / $maxVal) * 100;
                        $isLast = $i === count($pendapatanBulanan) - 1;
                        $bulanShort = substr($p['bulan'], 0, 3);
                    ?>
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <span class="text-xs text-earth font-medium"><?= number_format($p['total'] / 1000000, 1) ?>jt</span>
                        <div class="w-full rounded-t-lg transition-all hover:opacity-80 cursor-pointer <?= $isLast ? 'bg-primary' : 'bg-primary/30' ?>" style="height: <?= $pct ?>%" title="<?= $p['bulan'] ?>: <?= format_rupiah($p['total']) ?>"></div>
                        <span class="text-xs text-earth"><?= $bulanShort ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Table Detail -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-cream"><h2 class="font-semibold text-dark">Detail Pendapatan per Bulan</h2></div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead><tr class="bg-cream/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase tracking-wider">Bulan</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-earth uppercase tracking-wider">Booking</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-earth uppercase tracking-wider">Pendapatan Kamar</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-earth uppercase tracking-wider">Pendapatan Paket</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-earth uppercase tracking-wider">Total</th>
                        </tr></thead>
                        <tbody class="divide-y divide-cream">
                            <?php foreach ($pendapatanBulanan as $p): ?>
                            <tr class="hover:bg-cream/30 transition-colors">
                                <td class="px-6 py-3 text-sm font-medium text-dark"><?= e($p['bulan']) ?></td>
                                <td class="px-6 py-3 text-sm text-center text-earth"><?= $p['booking'] ?></td>
                                <td class="px-6 py-3 text-sm text-right text-earth"><?= format_rupiah($p['kamar']) ?></td>
                                <td class="px-6 py-3 text-sm text-right text-earth"><?= format_rupiah($p['paket']) ?></td>
                                <td class="px-6 py-3 text-sm text-right font-bold text-primary"><?= format_rupiah($p['total']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot><tr class="bg-cream/50 font-semibold">
                            <td class="px-6 py-3 text-sm text-dark">Total</td>
                            <td class="px-6 py-3 text-sm text-center text-dark"><?= $totalBooking ?></td>
                            <td class="px-6 py-3 text-sm text-right text-dark"><?= format_rupiah(array_sum(array_column($pendapatanBulanan, 'kamar'))) ?></td>
                            <td class="px-6 py-3 text-sm text-right text-dark"><?= format_rupiah(array_sum(array_column($pendapatanBulanan, 'paket'))) ?></td>
                            <td class="px-6 py-3 text-sm text-right font-bold text-primary"><?= format_rupiah($totalPendapatan) ?></td>
                        </tr></tfoot>
                    </table>
                </div>
            </div>

            <?php elseif ($tab === 'okupansi'): ?>
            <!-- ==================== TAB: OKUPANSI ==================== -->
            <!-- Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-accent">
                    <p class="text-sm text-earth mb-1">Rata-rata Okupansi</p>
                    <p class="text-2xl font-bold text-dark"><?= $avgOkupansi ?>%</p>
                    <p class="text-xs text-earth mt-1">Semua tipe kamar</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-success">
                    <p class="text-sm text-earth mb-1">Kamar Terlaris</p>
                    <p class="text-2xl font-bold text-dark">Suite Kerinci C1</p>
                    <p class="text-xs text-success mt-1">89% okupansi</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-warning">
                    <p class="text-sm text-earth mb-1">Perlu Perhatian</p>
                    <p class="text-2xl font-bold text-dark">Deluxe B2</p>
                    <p class="text-xs text-warning mt-1">33% okupansi</p>
                </div>
            </div>

            <!-- Visual Bars -->
            <div class="bg-white rounded-2xl p-6 shadow-sm mb-8">
                <h2 class="font-semibold text-dark mb-6">Okupansi per Kamar (Periode Berjalan)</h2>
                <div class="space-y-4">
                    <?php foreach ($okupansiKamar as $ok): ?>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-dark"><?= e($ok['nama']) ?></span>
                                <span class="px-2 py-0.5 bg-primary/10 text-primary text-xs rounded-full"><?= e($ok['tipe']) ?></span>
                            </div>
                            <span class="text-sm font-bold <?= $ok['persen'] >= 70 ? 'text-success' : ($ok['persen'] >= 50 ? 'text-warning' : 'text-danger') ?>"><?= $ok['persen'] ?>%</span>
                        </div>
                        <div class="w-full bg-cream rounded-full h-3">
                            <div class="h-3 rounded-full transition-all <?= $ok['persen'] >= 70 ? 'bg-success' : ($ok['persen'] >= 50 ? 'bg-warning' : 'bg-danger') ?>" style="width: <?= $ok['persen'] ?>%"></div>
                        </div>
                        <p class="text-xs text-earth mt-1"><?= $ok['terisi_malam'] ?> dari <?= $ok['total_malam'] ?> malam terisi</p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-cream"><h2 class="font-semibold text-dark">Detail Okupansi Kamar</h2></div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead><tr class="bg-cream/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase tracking-wider">Kamar</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-earth uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-earth uppercase tracking-wider">Total Malam</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-earth uppercase tracking-wider">Terisi</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-earth uppercase tracking-wider">Okupansi</th>
                        </tr></thead>
                        <tbody class="divide-y divide-cream">
                            <?php foreach ($okupansiKamar as $ok): ?>
                            <tr class="hover:bg-cream/30 transition-colors">
                                <td class="px-6 py-3 text-sm font-medium text-dark"><?= e($ok['nama']) ?></td>
                                <td class="px-6 py-3 text-sm text-center"><span class="px-2.5 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full"><?= e($ok['tipe']) ?></span></td>
                                <td class="px-6 py-3 text-sm text-center text-earth"><?= $ok['total_malam'] ?></td>
                                <td class="px-6 py-3 text-sm text-center text-earth"><?= $ok['terisi_malam'] ?></td>
                                <td class="px-6 py-3 text-center"><span class="px-3 py-1 <?= $ok['persen'] >= 70 ? 'bg-success-light text-success' : ($ok['persen'] >= 50 ? 'bg-warning-light text-warning' : 'bg-danger-light text-danger') ?> text-xs font-bold rounded-full"><?= $ok['persen'] ?>%</span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php elseif ($tab === 'paket'): ?>
            <!-- ==================== TAB: PAKET WISATA ==================== -->
            <!-- Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-info">
                    <p class="text-sm text-earth mb-1">Total Peserta</p>
                    <p class="text-2xl font-bold text-dark"><?= array_sum(array_column($performaPaket, 'total_peserta')) ?></p>
                    <p class="text-xs text-info mt-1">Semua paket wisata</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-success">
                    <p class="text-sm text-earth mb-1">Pendapatan Paket</p>
                    <p class="text-2xl font-bold text-dark"><?= format_rupiah(array_sum(array_column($performaPaket, 'total_pendapatan'))) ?></p>
                    <p class="text-xs text-success mt-1">↑ 25% dari bulan lalu</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border-l-4 border-accent">
                    <p class="text-sm text-earth mb-1">Paket Terlaris</p>
                    <p class="text-2xl font-bold text-dark">River Tubing</p>
                    <p class="text-xs text-accent mt-1">65 peserta</p>
                </div>
            </div>

            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <?php foreach ($performaPaket as $pp): ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 <?= $kategoriColors[$pp['kategori']] ?? 'bg-earth/10 text-earth' ?> text-xs font-medium rounded-full"><?= ucfirst(e($pp['kategori'])) ?></span>
                            <h3 class="font-semibold text-dark text-sm"><?= e($pp['nama']) ?></h3>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-3 bg-cream rounded-xl">
                            <p class="text-xl font-bold text-dark"><?= $pp['total_peserta'] ?></p>
                            <p class="text-xs text-earth">Peserta</p>
                        </div>
                        <div class="text-center p-3 bg-cream rounded-xl">
                            <p class="text-xl font-bold text-primary"><?= number_format($pp['total_pendapatan'] / 1000000, 1) ?>jt</p>
                            <p class="text-xs text-earth">Pendapatan</p>
                        </div>
                        <div class="text-center p-3 bg-cream rounded-xl">
                            <div class="flex items-center justify-center gap-1">
                                <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span class="text-xl font-bold text-dark"><?= $pp['rata_rating'] ?></span>
                            </div>
                            <p class="text-xs text-earth">Rating</p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-cream"><h2 class="font-semibold text-dark">Performa Detail Paket Wisata</h2></div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead><tr class="bg-cream/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase tracking-wider">Paket</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-earth uppercase tracking-wider">Peserta</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-earth uppercase tracking-wider">Pendapatan</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-earth uppercase tracking-wider">Rating</th>
                        </tr></thead>
                        <tbody class="divide-y divide-cream">
                            <?php foreach ($performaPaket as $pp): ?>
                            <tr class="hover:bg-cream/30 transition-colors">
                                <td class="px-6 py-3 text-sm font-medium text-dark"><?= e($pp['nama']) ?></td>
                                <td class="px-6 py-3 text-sm"><span class="px-2.5 py-1 <?= $kategoriColors[$pp['kategori']] ?? 'bg-earth/10 text-earth' ?> text-xs font-medium rounded-full"><?= ucfirst(e($pp['kategori'])) ?></span></td>
                                <td class="px-6 py-3 text-sm text-center font-bold text-dark"><?= $pp['total_peserta'] ?></td>
                                <td class="px-6 py-3 text-sm text-right font-bold text-primary"><?= format_rupiah($pp['total_pendapatan']) ?></td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <span class="text-sm font-bold text-dark"><?= $pp['rata_rating'] ?></span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot><tr class="bg-cream/50 font-semibold">
                            <td class="px-6 py-3 text-sm text-dark" colspan="2">Total</td>
                            <td class="px-6 py-3 text-sm text-center text-dark"><?= array_sum(array_column($performaPaket, 'total_peserta')) ?></td>
                            <td class="px-6 py-3 text-sm text-right font-bold text-primary"><?= format_rupiah(array_sum(array_column($performaPaket, 'total_pendapatan'))) ?></td>
                            <td class="px-6 py-3 text-sm text-center text-dark">—</td>
                        </tr></tfoot>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
