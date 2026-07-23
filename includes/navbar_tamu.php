    <!-- Navbar Tamu (sudah login) -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-dark/95 backdrop-blur-md shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="<?= BASE_URL ?>/tamu/" class="flex items-center gap-3 group">
                    <div class="w-8 h-8 rounded-full bg-accent/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <span class="font-serif text-lg text-white font-semibold">Kincay Mania</span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center gap-1">
                    <a href="<?= BASE_URL ?>/tamu/" class="px-4 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/tamu/index') ?>">Beranda</a>
                    <a href="<?= BASE_URL ?>/tamu/kamar.php" class="px-4 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/tamu/kamar') ?>">Kamar</a>
                    <a href="<?= BASE_URL ?>/tamu/paket_wisata.php" class="px-4 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/tamu/paket_wisata') ?>">Paket Wisata</a>
                    <a href="<?= BASE_URL ?>/tamu/cek_ketersediaan.php" class="px-4 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/tamu/cek_ketersediaan') ?>">Cek Ketersediaan</a>
                    <a href="<?= BASE_URL ?>/tamu/informasi.php" class="px-4 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/tamu/informasi') ?>">Informasi</a>
                    <a href="<?= BASE_URL ?>/tamu/riwayat.php" class="px-4 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/tamu/riwayat') ?>">Riwayat Booking</a>
                </div>

                <!-- User Menu -->
                <div class="hidden lg:flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-accent/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-white font-medium"><?= e($_SESSION['nama'] ?? 'Tamu') ?></p>
                            <p class="text-xs text-cream/50">Tamu</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="<?= BASE_URL ?>/tamu/profil.php" class="p-2 text-cream/60 hover:text-accent transition-colors" title="Profil">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </a>
                        <a href="<?= BASE_URL ?>/auth/logout.php" class="p-2 text-cream/60 hover:text-danger transition-colors" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="lg:hidden text-cream/80 hover:text-white p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path id="menu-icon-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path id="menu-icon-close" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden lg:hidden bg-dark/98 backdrop-blur-md border-t border-white/10">
            <div class="px-4 py-6 space-y-2">
                <div class="flex items-center gap-3 px-4 py-3 mb-2 border-b border-white/10 pb-4">
                    <div class="w-10 h-10 rounded-full bg-accent/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-medium"><?= e($_SESSION['nama'] ?? 'Tamu') ?></p>
                        <p class="text-xs text-cream/50"><?= e($_SESSION['email'] ?? '') ?></p>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>/tamu/" class="block px-4 py-3 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg transition-colors">Beranda</a>
                <a href="<?= BASE_URL ?>/tamu/kamar.php" class="block px-4 py-3 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg transition-colors">Kamar & Kabin</a>
                <a href="<?= BASE_URL ?>/tamu/paket_wisata.php" class="block px-4 py-3 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg transition-colors">Paket Wisata</a>
                <a href="<?= BASE_URL ?>/tamu/cek_ketersediaan.php" class="block px-4 py-3 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg transition-colors">Cek Ketersediaan</a>
                <a href="<?= BASE_URL ?>/tamu/informasi.php" class="block px-4 py-3 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg transition-colors">Informasi</a>
                <a href="<?= BASE_URL ?>/tamu/riwayat.php" class="block px-4 py-3 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg transition-colors">Riwayat Booking</a>
                <a href="<?= BASE_URL ?>/tamu/profil.php" class="block px-4 py-3 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg transition-colors">Profil Saya</a>
                <div class="border-t border-white/10 pt-4 mt-4">
                    <a href="<?= BASE_URL ?>/auth/logout.php" class="block px-4 py-3 text-danger hover:bg-danger/10 rounded-lg transition-colors">Keluar</a>
                </div>
            </div>
        </div>
    </nav>