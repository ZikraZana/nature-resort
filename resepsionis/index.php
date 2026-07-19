<?php
/** Dashboard Resepsionis — Kincay Mania Hotel & Resort */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Dashboard Resepsionis';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_resepsionis.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8"><h1 class="font-sans text-3xl text-dark font-bold">Dashboard Resepsionis</h1><p class="text-earth mt-1">Selamat datang, Siti! Berikut ringkasan operasional hari ini.</p></div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="stat-gradient-gold rounded-2xl p-6 text-white"><div class="flex items-center justify-between mb-4"><span class="text-sm font-medium text-white/80">Menunggu Verifikasi</span><div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div></div><p class="text-4xl font-bold">3</p><a href="<?= BASE_URL ?>/resepsionis/verifikasi.php" class="text-xs text-white/70 hover:text-white mt-2 inline-flex items-center gap-1">Lihat semua →</a></div>
                <div class="stat-gradient-green rounded-2xl p-6 text-white"><div class="flex items-center justify-between mb-4"><span class="text-sm font-medium text-white/80">Check-in Hari Ini</span><div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg></div></div><p class="text-4xl font-bold">2</p><a href="<?= BASE_URL ?>/resepsionis/checkin.php" class="text-xs text-white/70 hover:text-white mt-2 inline-flex items-center gap-1">Lihat semua →</a></div>
                <div class="stat-gradient-earth rounded-2xl p-6 text-white"><div class="flex items-center justify-between mb-4"><span class="text-sm font-medium text-white/80">Check-out Hari Ini</span><div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></div></div><p class="text-4xl font-bold">1</p><a href="<?= BASE_URL ?>/resepsionis/checkout.php" class="text-xs text-white/70 hover:text-white mt-2 inline-flex items-center gap-1">Lihat semua →</a></div>
                <div class="stat-gradient-dark rounded-2xl p-6 text-white"><div class="flex items-center justify-between mb-4"><span class="text-sm font-medium text-white/80">Menunggu Refund</span><div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg></div></div><p class="text-4xl font-bold">1</p><a href="<?= BASE_URL ?>/resepsionis/refund.php" class="text-xs text-white/70 hover:text-white mt-2 inline-flex items-center gap-1">Lihat semua →</a></div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="<?= BASE_URL ?>/resepsionis/walkin.php" class="bg-white rounded-2xl p-6 shadow-sm flex items-center gap-4 group">
                    <div class="w-14 h-14 rounded-xl bg-accent/10 flex items-center justify-center group-hover:bg-accent/20 transition-colors"><svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg></div>
                    <div><p class="font-semibold text-dark">Booking Walk-in</p><p class="text-sm text-earth">Input booking manual</p></div>
                </a>
                <a href="<?= BASE_URL ?>/resepsionis/kamar_status.php" class="bg-white rounded-2xl p-6 shadow-sm flex items-center gap-4 group">
                    <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors"><svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                    <div><p class="font-semibold text-dark">Status Kamar</p><p class="text-sm text-earth">Monitor ketersediaan</p></div>
                </a>
                <a href="<?= BASE_URL ?>/resepsionis/jadwal_wisata.php" class="bg-white rounded-2xl p-6 shadow-sm flex items-center gap-4 group">
                    <div class="w-14 h-14 rounded-xl bg-secondary/10 flex items-center justify-center group-hover:bg-secondary/20 transition-colors"><svg class="w-7 h-7 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                    <div><p class="font-semibold text-dark">Jadwal Wisata</p><p class="text-sm text-earth">Peserta hari ini</p></div>
                </a>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
