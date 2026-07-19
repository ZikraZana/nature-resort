    <!-- Navbar Resepsionis -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-dark/95 backdrop-blur-md shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="<?= BASE_URL ?>/resepsionis/" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-accent/20 flex items-center justify-center"><svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg></div>
                    <div><span class="font-serif text-lg text-white font-semibold">Kincay Mania</span><span class="block text-xs text-accent/70 -mt-1">Resepsionis</span></div>
                </a>
                <div class="hidden lg:flex items-center gap-1">
                    <a href="<?= BASE_URL ?>/resepsionis/" class="px-3 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/resepsionis/index') ?>">Dashboard</a>
                    <a href="<?= BASE_URL ?>/resepsionis/verifikasi.php" class="px-3 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/resepsionis/verifikasi') ?>">Verifikasi</a>
                    <a href="<?= BASE_URL ?>/resepsionis/kamar_status.php" class="px-3 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/resepsionis/kamar_status') ?>">Status Kamar</a>
                    <a href="<?= BASE_URL ?>/resepsionis/checkin.php" class="px-3 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/resepsionis/checkin') ?>">Check-in</a>
                    <a href="<?= BASE_URL ?>/resepsionis/checkout.php" class="px-3 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/resepsionis/checkout') ?>">Check-out</a>
                    <a href="<?= BASE_URL ?>/resepsionis/refund.php" class="px-3 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/resepsionis/refund') ?>">Refund</a>
                    <a href="<?= BASE_URL ?>/resepsionis/walkin.php" class="px-3 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/resepsionis/walkin') ?>">Walk-in</a>
                    <a href="<?= BASE_URL ?>/resepsionis/jadwal_wisata.php" class="px-3 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/resepsionis/jadwal') ?>">Jadwal</a>
                </div>
                <div class="hidden lg:flex items-center gap-3">
                    <div class="flex items-center gap-2"><div class="w-8 h-8 rounded-full bg-info/20 flex items-center justify-center"><span class="text-sm font-bold text-info">S</span></div><span class="text-sm text-white">Siti</span></div>
                    <a href="<?= BASE_URL ?>/auth/logout.php" class="p-2 text-cream/60 hover:text-danger transition-colors" title="Logout"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></a>
                </div>
                <button id="mobile-menu-btn" class="lg:hidden text-cream/80 hover:text-white p-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path id="menu-icon-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/><path id="menu-icon-close" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
        </div>
        <div id="mobile-menu" class="hidden lg:hidden bg-dark/98 backdrop-blur-md border-t border-white/10">
            <div class="px-4 py-4 space-y-1">
                <a href="<?= BASE_URL ?>/resepsionis/" class="block px-4 py-2.5 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg text-sm">Dashboard</a>
                <a href="<?= BASE_URL ?>/resepsionis/verifikasi.php" class="block px-4 py-2.5 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg text-sm">Verifikasi Pembayaran</a>
                <a href="<?= BASE_URL ?>/resepsionis/kamar_status.php" class="block px-4 py-2.5 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg text-sm">Status Kamar</a>
                <a href="<?= BASE_URL ?>/resepsionis/checkin.php" class="block px-4 py-2.5 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg text-sm">Check-in</a>
                <a href="<?= BASE_URL ?>/resepsionis/checkout.php" class="block px-4 py-2.5 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg text-sm">Check-out</a>
                <a href="<?= BASE_URL ?>/resepsionis/refund.php" class="block px-4 py-2.5 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg text-sm">Refund</a>
                <a href="<?= BASE_URL ?>/resepsionis/walkin.php" class="block px-4 py-2.5 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg text-sm">Booking Walk-in</a>
                <a href="<?= BASE_URL ?>/resepsionis/jadwal_wisata.php" class="block px-4 py-2.5 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg text-sm">Jadwal Wisata</a>
                <div class="border-t border-white/10 pt-3 mt-3"><a href="<?= BASE_URL ?>/auth/logout.php" class="block px-4 py-2.5 text-danger hover:bg-danger/10 rounded-lg text-sm">Keluar</a></div>
            </div>
        </div>
    </nav>
