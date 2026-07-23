<?php
/** Laporan — Admin */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');
$pageTitle = 'Laporan';

$bulan = (int)($_GET['bulan'] ?? date('m'));
$tahun = (int)($_GET['tahun'] ?? date('Y'));

// ── Total pendapatan bulan ini ──────────────────────────────────────────────
$stmt = db()->prepare(
    "SELECT COALESCE(SUM(total_harga), 0) FROM booking WHERE status IN ('dikonfirmasi','checkin','selesai') AND MONTH(created_at) = ? AND YEAR(created_at) = ?"
);
$stmt->execute([$bulan, $tahun]);
$totalPendapatan = $stmt->fetchColumn();

// ── Total booking per status ────────────────────────────────────────────────
$stmt = db()->prepare(
    "SELECT status, COUNT(*) as jumlah FROM booking WHERE MONTH(created_at) = ? AND YEAR(created_at) = ? GROUP BY status"
);
$stmt->execute([$bulan, $tahun]);
$statusCounts = [];
foreach ($stmt->fetchAll() as $s) { $statusCounts[$s['status']] = $s['jumlah']; }
$totalBooking = array_sum($statusCounts);

// ── Total refund ────────────────────────────────────────────────────────────
$stmt = db()->prepare(
    "SELECT COALESCE(SUM(nominal_refund), 0) FROM refund WHERE status = 'selesai' AND MONTH(tanggal_refund) = ? AND YEAR(tanggal_refund) = ?"
);
$stmt->execute([$bulan, $tahun]);
$totalRefund = $stmt->fetchColumn();

// ── Booking harian untuk chart ──────────────────────────────────────────────
$stmt = db()->prepare(
    "SELECT DAY(created_at) AS hari, COUNT(*) AS jumlah FROM booking WHERE MONTH(created_at) = ? AND YEAR(created_at) = ? GROUP BY DAY(created_at) ORDER BY hari"
);
$stmt->execute([$bulan, $tahun]);
$chartData = $stmt->fetchAll();
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$chartLabels = [];
$chartValues = [];
$dataMap = [];
foreach ($chartData as $cd) { $dataMap[$cd['hari']] = $cd['jumlah']; }
for ($d = 1; $d <= $daysInMonth; $d++) {
    $chartLabels[] = $d;
    $chartValues[] = $dataMap[$d] ?? 0;
}

// ── Top kamar ───────────────────────────────────────────────────────────────
$stmt = db()->prepare(
    "SELECT k.nama, COUNT(b.id) AS total_booking, SUM(b.total_harga) AS total_revenue
     FROM booking b JOIN kamar k ON k.id = b.kamar_id
     WHERE b.status NOT IN ('dibatalkan','ditolak') AND MONTH(b.created_at) = ? AND YEAR(b.created_at) = ?
     GROUP BY k.id ORDER BY total_booking DESC LIMIT 5"
);
$stmt->execute([$bulan, $tahun]);
$topKamar = $stmt->fetchAll();

// ── Top paket wisata ────────────────────────────────────────────────────────
$stmt = db()->prepare(
    "SELECT pw.nama, SUM(bpw.jumlah_peserta) AS total_peserta, SUM(bpw.subtotal) AS total_revenue
     FROM booking_paket_wisata bpw
     JOIN jadwal_wisata jw ON jw.id = bpw.jadwal_wisata_id
     JOIN paket_wisata pw ON pw.id = jw.paket_wisata_id
     JOIN booking b ON b.id = bpw.booking_id
     WHERE b.status NOT IN ('dibatalkan','ditolak') AND MONTH(b.created_at) = ? AND YEAR(b.created_at) = ?
     GROUP BY pw.id ORDER BY total_peserta DESC LIMIT 5"
);
$stmt->execute([$bulan, $tahun]);
$topPaket = $stmt->fetchAll();

// ── Daftar booking terbaru ──────────────────────────────────────────────────
$stmt = db()->prepare(
    "SELECT b.id, b.total_harga, b.status, b.created_at, k.nama AS kamar_nama, COALESCE(u.nama, b.nama_tamu) AS tamu_nama
     FROM booking b JOIN kamar k ON k.id = b.kamar_id LEFT JOIN users u ON u.id = b.user_id
     WHERE MONTH(b.created_at) = ? AND YEAR(b.created_at) = ?
     ORDER BY b.created_at DESC LIMIT 10"
);
$stmt->execute([$bulan, $tahun]);
$recentBookings = $stmt->fetchAll();

$bulanNama = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div><h1 class="font-sans text-3xl text-dark font-bold">Laporan</h1><p class="text-earth mt-1"><?= $bulanNama[$bulan] ?> <?= $tahun ?></p></div>
                <form class="flex items-center gap-2">
                    <select name="bulan" class="px-3 py-2 bg-white border border-cream-darker rounded-xl text-sm text-dark">
                        <?php for ($m = 1; $m <= 12; $m++): ?><option value="<?= $m ?>" <?= $m === $bulan ? 'selected' : '' ?>><?= $bulanNama[$m] ?></option><?php endfor; ?>
                    </select>
                    <input type="number" name="tahun" value="<?= $tahun ?>" min="2020" max="2030" class="px-3 py-2 bg-white border border-cream-darker rounded-xl text-sm text-dark w-24">
                    <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-light transition-colors">Tampilkan</button>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="stat-gradient-gold rounded-2xl p-6 text-white"><p class="text-sm text-white/80 mb-1">Total Pendapatan</p><p class="text-3xl font-bold"><?= format_rupiah($totalPendapatan) ?></p></div>
                <div class="stat-gradient-green rounded-2xl p-6 text-white"><p class="text-sm text-white/80 mb-1">Total Booking</p><p class="text-3xl font-bold"><?= $totalBooking ?></p></div>
                <div class="stat-gradient-earth rounded-2xl p-6 text-white"><p class="text-sm text-white/80 mb-1">Selesai</p><p class="text-3xl font-bold"><?= $statusCounts['selesai'] ?? 0 ?></p></div>
                <div class="stat-gradient-dark rounded-2xl p-6 text-white"><p class="text-sm text-white/80 mb-1">Total Refund</p><p class="text-3xl font-bold"><?= format_rupiah($totalRefund) ?></p></div>
            </div>

            <!-- Chart -->
            <div class="bg-white rounded-2xl p-6 shadow-sm mb-8">
                <h2 class="font-semibold text-dark mb-4">Booking Harian — <?= $bulanNama[$bulan] ?> <?= $tahun ?></h2>
                <div style="height: 200px; display: flex; align-items: end; gap: 2px;">
                    <?php $maxVal = max(1, max($chartValues)); foreach ($chartValues as $i => $v): $h = ($v / $maxVal) * 180; ?>
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: end;">
                        <?php if ($v > 0): ?><span style="font-size: 10px; color: #6B705C;"><?= $v ?></span><?php endif; ?>
                        <div style="width: 100%; max-width: 24px; height: <?= max(2, $h) ?>px; background: linear-gradient(to top, #2D5016, #3D6B22); border-radius: 4px 4px 0 0; transition: height 0.3s;"></div>
                        <?php if ($chartLabels[$i] % 5 === 0 || $chartLabels[$i] === 1): ?><span style="font-size: 10px; color: #6B705C; margin-top: 4px;"><?= $chartLabels[$i] ?></span><?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Top Kamar -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-semibold text-dark mb-4">Top 5 Kamar Terpopuler</h2>
                    <?php if (empty($topKamar)): ?><p class="text-sm text-earth">Belum ada data.</p>
                    <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($topKamar as $i => $tk): ?>
                        <div class="flex items-center justify-between p-3 bg-cream rounded-xl">
                            <div class="flex items-center gap-3"><span class="w-8 h-8 rounded-full bg-primary text-white text-sm font-bold flex items-center justify-center"><?= $i + 1 ?></span><div><p class="text-sm font-medium text-dark"><?= e($tk['nama']) ?></p><p class="text-xs text-earth"><?= $tk['total_booking'] ?> booking</p></div></div>
                            <span class="text-sm font-bold text-primary"><?= format_rupiah($tk['total_revenue']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Top Paket -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-semibold text-dark mb-4">Top 5 Paket Wisata</h2>
                    <?php if (empty($topPaket)): ?><p class="text-sm text-earth">Belum ada data.</p>
                    <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($topPaket as $i => $tp): ?>
                        <div class="flex items-center justify-between p-3 bg-cream rounded-xl">
                            <div class="flex items-center gap-3"><span class="w-8 h-8 rounded-full bg-secondary text-white text-sm font-bold flex items-center justify-center"><?= $i + 1 ?></span><div><p class="text-sm font-medium text-dark"><?= e($tp['nama']) ?></p><p class="text-xs text-earth"><?= $tp['total_peserta'] ?> peserta</p></div></div>
                            <span class="text-sm font-bold text-secondary"><?= format_rupiah($tp['total_revenue']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Booking Status Breakdown -->
            <div class="bg-white rounded-2xl p-6 shadow-sm mb-8">
                <h2 class="font-semibold text-dark mb-4">Breakdown Status Booking</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-4">
                    <?php
                    $allStatuses = ['menunggu_pembayaran' => 'Menunggu Bayar', 'menunggu_verifikasi' => 'Verifikasi', 'dikonfirmasi' => 'Dikonfirmasi', 'checkin' => 'Check-in', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak', 'dibatalkan' => 'Dibatalkan', 'menunggu_refund' => 'Refund', 'refund_selesai' => 'Refund OK'];
                    foreach ($allStatuses as $sk => $sl):
                    ?>
                    <div class="p-3 bg-cream rounded-xl text-center"><p class="text-2xl font-bold text-dark"><?= $statusCounts[$sk] ?? 0 ?></p><p class="text-xs text-earth"><?= $sl ?></p></div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-cream"><h2 class="font-semibold text-dark">10 Booking Terbaru</h2></div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead><tr class="bg-cream/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase">Tamu</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase">Kamar</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-earth uppercase">Total</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-earth uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase">Tanggal</th>
                        </tr></thead>
                        <tbody class="divide-y divide-cream">
                            <?php foreach ($recentBookings as $rb): ?>
                            <tr class="hover:bg-cream/30 transition-colors">
                                <td class="px-6 py-3 text-sm font-mono text-dark">#BK-<?= str_pad($rb['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td class="px-6 py-3 text-sm text-dark"><?= e($rb['tamu_nama']) ?></td>
                                <td class="px-6 py-3 text-sm text-earth"><?= e($rb['kamar_nama']) ?></td>
                                <td class="px-6 py-3 text-sm text-right font-bold text-primary"><?= format_rupiah($rb['total_harga']) ?></td>
                                <td class="px-6 py-3 text-center"><span class="px-2 py-0.5 bg-primary/10 text-primary text-xs rounded-full"><?= ucfirst(str_replace('_', ' ', $rb['status'])) ?></span></td>
                                <td class="px-6 py-3 text-sm text-earth"><?= date('d M Y', strtotime($rb['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
