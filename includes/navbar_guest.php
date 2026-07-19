    <?php require_once __DIR__ . '/auth_check.php'; // untuk dashboard_url() jika user sudah login 
    ?>
    <!-- Navbar Guest -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 <?= ($navbarTransparent ?? false) ? 'bg-transparent' : 'bg-dark/95 backdrop-blur-md shadow-lg' ?>">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="<?= BASE_URL ?>/guest/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-full bg-accent/20 flex items-center justify-center group-hover:bg-accent/30 transition-colors">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <div>
                        <span class="font-serif text-xl text-white font-semibold">Kincay Mania</span>
                        <span class="hidden sm:block text-xs text-accent/80 -mt-1">Nature Resort Kerinci</span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center gap-1">
                    <a href="<?= BASE_URL ?>/guest/" class="px-4 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/guest/index') ?>">Beranda</a>
                    <a href="<?= BASE_URL ?>/guest/kamar.php" class="px-4 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/guest/kamar') ?>">Kamar & Kabin</a>
                    <a href="<?= BASE_URL ?>/guest/paket_wisata.php" class="px-4 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/guest/paket_wisata') ?>">Paket Wisata</a>
                    <a href="<?= BASE_URL ?>/guest/cek_ketersediaan.php" class="px-4 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/guest/cek_ketersediaan') ?>">Cek Ketersediaan</a>
                    <a href="<?= BASE_URL ?>/guest/informasi.php" class="px-4 py-2 text-sm text-cream/80 hover:text-accent rounded-lg transition-colors <?= is_active('/guest/informasi') ?>">Informasi</a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden lg:flex items-center gap-3">
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <a href="<?= dashboard_url($_SESSION['role']) ?>" class="px-5 py-2.5 text-sm text-cream/90 hover:text-white border border-cream/20 hover:border-cream/40 rounded-full transition-all">
                            Dashboard Saya
                        </a>
                        <a href="<?= BASE_URL ?>/auth/logout.php" class="px-5 py-2.5 text-sm bg-accent hover:bg-accent-light text-dark font-semibold rounded-full transition-all hover:shadow-lg hover:shadow-accent/25">
                            Keluar
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/auth/login.php" class="px-5 py-2.5 text-sm text-cream/90 hover:text-white border border-cream/20 hover:border-cream/40 rounded-full transition-all">
                            Masuk
                        </a>
                        <a href="<?= BASE_URL ?>/auth/register.php" class="px-5 py-2.5 text-sm bg-accent hover:bg-accent-light text-dark font-semibold rounded-full transition-all hover:shadow-lg hover:shadow-accent/25">
                            Daftar
                        </a>
                    <?php endif; ?>
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
                <a href="<?= BASE_URL ?>/guest/" class="block px-4 py-3 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg transition-colors">Beranda</a>
                <a href="<?= BASE_URL ?>/guest/kamar.php" class="block px-4 py-3 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg transition-colors">Kamar & Kabin</a>
                <a href="<?= BASE_URL ?>/guest/paket_wisata.php" class="block px-4 py-3 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg transition-colors">Paket Wisata</a>
                <a href="<?= BASE_URL ?>/guest/cek_ketersediaan.php" class="block px-4 py-3 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg transition-colors">Cek Ketersediaan</a>
                <a href="<?= BASE_URL ?>/guest/informasi.php" class="block px-4 py-3 text-cream/80 hover:text-accent hover:bg-white/5 rounded-lg transition-colors">Informasi</a>
                <div class="border-t border-white/10 pt-4 mt-4 space-y-2">
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <a href="<?= dashboard_url($_SESSION['role']) ?>" class="block px-4 py-3 text-center text-cream/90 border border-cream/20 rounded-lg hover:border-cream/40 transition-colors">Halo, <?= e($_SESSION['nama']) ?></a>
                        <a href="<?= BASE_URL ?>/auth/logout.php" class="block px-4 py-3 text-center bg-accent text-dark font-semibold rounded-lg hover:bg-accent-light transition-colors">Keluar</a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/auth/login.php" class="block px-4 py-3 text-center text-cream/90 border border-cream/20 rounded-lg hover:border-cream/40 transition-colors">Masuk</a>
                        <a href="<?= BASE_URL ?>/auth/register.php" class="block px-4 py-3 text-center bg-accent text-dark font-semibold rounded-lg hover:bg-accent-light transition-colors">Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>