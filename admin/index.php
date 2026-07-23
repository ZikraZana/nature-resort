<?php
/** Dashboard Admin — Kincay Mania Hotel & Resort */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');
$pageTitle = 'Dashboard Admin';

$today = date('Y-m-d');
$thisMonth = date('m');
$thisYear  = date('Y');

// Stats
$stmtPendapatan = db()->prepare("SELECT COALESCE(SUM(total_harga), 0) FROM booking WHERE status IN ('dikonfirmasi','checkin','selesai') AND MONTH(created_at) = ? AND YEAR(created_at) = ?");
$stmtPendapatan->execute([$thisMonth, $thisYear]);
$pendapatanBulan = $stmtPendapatan->fetchColumn();

$stmtBooking = db()->prepare("SELECT COUNT(*) FROM booking WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?");
$stmtBooking->execute([$thisMonth, $thisYear]);
$totalBookingBulan = $stmtBooking->fetchColumn();

$stmtKamar = db()->query("SELECT COUNT(*) FROM kamar");
$totalKamar = $stmtKamar->fetchColumn();

$stmtUsers = db()->query("SELECT COUNT(*) FROM users WHERE role = 'tamu'");
$totalTamu = $stmtUsers->fetchColumn();

// Recent bookings
$stmtRecent = db()->prepare(
    "SELECT b.id, b.total_harga, b.status, b.created_at, k.nama AS kamar_nama, COALESCE(u.nama, b.nama_tamu) AS tamu_nama
     FROM booking b JOIN kamar k ON k.id = b.kamar_id LEFT JOIN users u ON u.id = b.user_id
     ORDER BY b.created_at DESC LIMIT 5"
);
$stmtRecent->execute();
$recentBookings = $stmtRecent->fetchAll();

// Pending actions
$stmtPending = db()->prepare("SELECT COUNT(*) FROM pembayaran p JOIN booking b ON b.id = p.booking_id WHERE p.status = 'menunggu' AND b.status = 'menunggu_verifikasi'");
$stmtPending->execute();
$pendingVerif = $stmtPending->fetchColumn();

$stmtPendingRef = db()->prepare("SELECT COUNT(*) FROM refund WHERE status = 'menunggu'");
$stmtPendingRef->execute();
$pendingRefund = $stmtPendingRef->fetchColumn();

$bulanNama = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8"><h1 class="font-sans text-3xl text-dark font-bold">Dashboard Admin</h1><p class="text-earth mt-1">Selamat datang, <?= e($_SESSION['nama'] ?? 'Admin') ?>! Ringkasan <?= $bulanNama[(int)$thisMonth] ?> <?= $thisYear ?>.</p></div>

            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="stat-gradient-gold rounded-2xl p-6 text-white"><div class="flex items-center justify-between mb-4"><span class="text-sm text-white/80">Pendapatan Bulan Ini</span><div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div></div><p class="text-3xl font-bold"><?= format_rupiah($pendapatanBulan) ?></p><a href="<?= BASE_URL ?>/admin/laporan.php" class="text-xs text-white/70 hover:text-white mt-2 inline-flex items-center gap-1">Detail →</a></div>
                <div class="stat-gradient-green rounded-2xl p-6 text-white"><div class="flex items-center justify-between mb-4"><span class="text-sm text-white/80">Booking Bulan Ini</span><div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div></div><p class="text-3xl font-bold"><?= $totalBookingBulan ?></p></div>
                <div class="stat-gradient-earth rounded-2xl p-6 text-white"><div class="flex items-center justify-between mb-4"><span class="text-sm text-white/80">Total Kamar</span><div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div></div><p class="text-3xl font-bold"><?= $totalKamar ?></p><a href="<?= BASE_URL ?>/admin/kelola_kamar.php" class="text-xs text-white/70 hover:text-white mt-2 inline-flex items-center gap-1">Kelola →</a></div>
                <div class="stat-gradient-dark rounded-2xl p-6 text-white"><div class="flex items-center justify-between mb-4"><span class="text-sm text-white/80">Total Tamu Terdaftar</span><div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div></div><p class="text-3xl font-bold"><?= $totalTamu ?></p><a href="<?= BASE_URL ?>/admin/kelola_user.php" class="text-xs text-white/70 hover:text-white mt-2 inline-flex items-center gap-1">Kelola →</a></div>
            </div>

            <!-- Alerts -->
            <?php if ($pendingVerif > 0 || $pendingRefund > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                <?php if ($pendingVerif > 0): ?>
                <div class="bg-warning-light rounded-2xl p-5 flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-warning/20 flex items-center justify-center"><svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.07 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg></div><div><p class="font-semibold text-dark"><?= $pendingVerif ?> Pembayaran Menunggu</p><p class="text-xs text-earth">Perlu diverifikasi oleh resepsionis</p></div></div>
                <?php endif; ?>
                <?php if ($pendingRefund > 0): ?>
                <div class="bg-danger-light rounded-2xl p-5 flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-danger/20 flex items-center justify-center"><svg class="w-6 h-6 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg></div><div><p class="font-semibold text-dark"><?= $pendingRefund ?> Refund Menunggu</p><p class="text-xs text-earth">Perlu diproses oleh resepsionis</p></div></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Quick Actions -->
                <div class="space-y-4">
                    <h2 class="font-semibold text-dark text-lg">Menu Cepat</h2>
                    <a href="<?= BASE_URL ?>/admin/kelola_kamar.php" class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4 group hover:shadow-md transition-shadow block"><div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors"><svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg></div><div><p class="font-medium text-dark">Kelola Kamar</p><p class="text-xs text-earth"><?= $totalKamar ?> kamar</p></div></a>
                    <a href="<?= BASE_URL ?>/admin/kelola_paket.php" class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4 group hover:shadow-md transition-shadow block"><div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center group-hover:bg-secondary/20 transition-colors"><svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg></div><div><p class="font-medium text-dark">Kelola Paket</p></div></a>
                    <a href="<?= BASE_URL ?>/admin/pengaturan.php" class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4 group hover:shadow-md transition-shadow block"><div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center group-hover:bg-accent/20 transition-colors"><svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div><div><p class="font-medium text-dark">Pengaturan</p></div></a>
                </div>

                <!-- Recent Bookings -->
                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between mb-4"><h2 class="font-semibold text-dark text-lg">Booking Terbaru</h2><a href="<?= BASE_URL ?>/admin/laporan.php" class="text-sm text-primary hover:underline">Lihat semua →</a></div>
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead><tr class="bg-cream/50">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-earth uppercase">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-earth uppercase">Tamu</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-earth uppercase">Kamar</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-earth uppercase">Total</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-earth uppercase">Status</th>
                                </tr></thead>
                                <tbody class="divide-y divide-cream">
                                    <?php foreach ($recentBookings as $rb): ?>
                                    <tr class="hover:bg-cream/30 transition-colors">
                                        <td class="px-4 py-3 text-sm font-mono text-dark">#BK-<?= str_pad($rb['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                        <td class="px-4 py-3 text-sm text-dark"><?= e($rb['tamu_nama']) ?></td>
                                        <td class="px-4 py-3 text-sm text-earth"><?= e($rb['kamar_nama']) ?></td>
                                        <td class="px-4 py-3 text-sm text-right font-bold text-primary"><?= format_rupiah($rb['total_harga']) ?></td>
                                        <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 bg-primary/10 text-primary text-xs rounded-full"><?= ucfirst(str_replace('_', ' ', $rb['status'])) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
