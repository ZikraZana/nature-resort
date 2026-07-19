<?php
/**
 * Landing Page — Kincay Mania Hotel & Resort
 * Halaman publik utama bertema Nature Resort Kerinci
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');

$pageTitle = 'Beranda';
$pageDescription = 'Kincay Mania Hotel & Resort — Nature Resort di jantung Kerinci. Nikmati penginapan kabin alam, paket wisata trekking & river tubing, serta kuliner lokal autentik.';
$navbarTransparent = true;

// Dummy data kamar
$kamarHighlight = [
    ['id' => 1, 'nama' => 'Kabin Pinus A1', 'tipe' => 'Kabin', 'kapasitas' => 2, 'harga' => 450000, 'foto' => 'https://images.unsplash.com/photo-1618767689160-da3fb810aad7?w=600&h=400&fit=crop', 'deskripsi' => 'Kabin kayu eksklusif di tengah hutan pinus'],
    ['id' => 3, 'nama' => 'Kamar Deluxe B1', 'tipe' => 'Deluxe', 'kapasitas' => 4, 'harga' => 750000, 'foto' => 'https://images.unsplash.com/photo-1590490360182-c33d955f4c4e?w=600&h=400&fit=crop', 'deskripsi' => 'Kamar luas dengan pemandangan gunung Kerinci'],
    ['id' => 5, 'nama' => 'Suite Kerinci C1', 'tipe' => 'Suite', 'kapasitas' => 6, 'harga' => 1200000, 'foto' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600&h=400&fit=crop', 'deskripsi' => 'Suite premium dengan balkon privat dan jacuzzi'],
];

// Dummy data paket wisata
$paketHighlight = [
    ['id' => 1, 'nama' => 'Trekking Gunung Kerinci', 'kategori' => 'trekking', 'harga' => 350000, 'foto' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=600&h=400&fit=crop', 'deskripsi' => 'Jelajahi puncak tertinggi Sumatera dengan pemandu berpengalaman'],
    ['id' => 2, 'nama' => 'River Tubing Sungai Kerinci', 'kategori' => 'perahu', 'harga' => 250000, 'foto' => 'https://images.unsplash.com/photo-1530866495561-507c83580c5d?w=600&h=400&fit=crop', 'deskripsi' => 'Arungi jeram sungai Kerinci yang memacu adrenalin'],
    ['id' => 4, 'nama' => 'Wisata Kuliner Lokal', 'kategori' => 'kuliner', 'harga' => 150000, 'foto' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&h=400&fit=crop', 'deskripsi' => 'Cicipi masakan khas Kerinci langsung dari dapur tradisional'],
];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_tamu.php';
?>

    <!-- ═══════════════════════════════════════════
         HERO SECTION
         ═══════════════════════════════════════════ -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="<?= BASE_URL ?>/assets/img/hero-rawa-bento.jpg"
                 alt="Rawa Bento"
                 class="w-full h-full object-cover">
            <div class="hero-overlay absolute inset-0"></div>
        </div>

        <!-- Floating particles decoration -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-accent/30 rounded-full animate-float" style="animation-delay: 0s;"></div>
            <div class="absolute top-1/3 right-1/3 w-3 h-3 bg-accent/20 rounded-full animate-float" style="animation-delay: 1s;"></div>
            <div class="absolute bottom-1/3 left-1/3 w-2 h-2 bg-accent/25 rounded-full animate-float" style="animation-delay: 2s;"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto animate-fade-in">
            <p class="text-accent font-medium tracking-[0.3em] uppercase text-sm mb-6">
                ✦ Nature Resort di Jantung Kerinci ✦
            </p>
            <h1 class="font-serif text-5xl sm:text-6xl lg:text-7xl text-white font-bold leading-tight mb-6">
                Rasakan Keajaiban<br>
                <span class="text-gradient">Alam Kerinci</span>
            </h1>
            <p class="text-cream/70 text-lg sm:text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
                Penginapan eksklusif di tengah hutan tropis, paket wisata trekking & river tubing, serta kuliner lokal autentik yang tak terlupakan.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= BASE_URL ?>/tamu/kamar.php"
                   class="px-8 py-4 bg-accent hover:bg-accent-light text-dark font-semibold rounded-full transition-all hover:shadow-xl hover:shadow-accent/25 text-lg">
                    Jelajahi Kamar
                </a>
                <a href="<?= BASE_URL ?>/tamu/paket_wisata.php"
                   class="px-8 py-4 border-2 border-cream/30 hover:border-cream/60 text-white rounded-full transition-all hover:bg-white/10 text-lg">
                    Lihat Paket Wisata
                </a>
            </div>
        </div>

        <!-- Scroll indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-cream/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
         KEUNGGULAN / USP SECTION
         ═══════════════════════════════════════════ -->
    <section class="py-24 bg-white relative overflow-hidden">
        <!-- Decorative background -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-accent/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-center mb-16 reveal">
                <p class="text-accent font-medium tracking-wider uppercase text-sm mb-3">Mengapa Kincay Mania?</p>
                <h2 class="font-serif text-4xl sm:text-5xl text-dark font-bold mb-4">Tiga Pengalaman<br>Dalam Satu Destinasi</h2>
                <div class="w-24 h-1 bg-accent mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
                <!-- Card 1: Penginapan -->
                <div class="reveal group text-center p-8 rounded-2xl bg-cream hover:bg-cream-dark transition-all duration-300">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <h3 class="font-serif text-2xl text-dark font-semibold mb-3">Penginapan Alam</h3>
                    <p class="text-earth leading-relaxed">Kabin kayu eksklusif & kamar nyaman di tengah hutan pinus, dengan pemandangan Gunung Kerinci yang menakjubkan.</p>
                </div>

                <!-- Card 2: Wisata -->
                <div class="reveal group text-center p-8 rounded-2xl bg-cream hover:bg-cream-dark transition-all duration-300" style="transition-delay: 0.15s">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-serif text-2xl text-dark font-semibold mb-3">Wisata Petualangan</h3>
                    <p class="text-earth leading-relaxed">Trekking ke puncak Kerinci, river tubing memacu adrenalin, dan susur perahu di sungai yang tenang.</p>
                </div>

                <!-- Card 3: Kuliner -->
                <div class="reveal group text-center p-8 rounded-2xl bg-cream hover:bg-cream-dark transition-all duration-300" style="transition-delay: 0.3s">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="font-serif text-2xl text-dark font-semibold mb-3">Kuliner Lokal</h3>
                    <p class="text-earth leading-relaxed">Cita rasa autentik masakan Kerinci — dari gulai ikan semah hingga kopi arabika Kayu Aro langsung dari petani.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
         KAMAR & KABIN HIGHLIGHT
         ═══════════════════════════════════════════ -->
    <section class="py-24 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-12 reveal">
                <div>
                    <p class="text-accent font-medium tracking-wider uppercase text-sm mb-3">Akomodasi</p>
                    <h2 class="font-serif text-4xl sm:text-5xl text-dark font-bold">Kamar & Kabin Kami</h2>
                </div>
                <a href="<?= BASE_URL ?>/tamu/kamar.php" class="mt-4 sm:mt-0 text-primary hover:text-primary-light font-medium flex items-center gap-2 transition-colors">
                    Lihat Semua
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($kamarHighlight as $i => $kamar): ?>
                <div class="reveal card-hover bg-white rounded-2xl overflow-hidden shadow-sm" style="transition-delay: <?= $i * 0.1 ?>s">
                    <div class="img-zoom relative h-56">
                        <img src="<?= e($kamar['foto']) ?>" alt="<?= e($kamar['nama']) ?>" class="w-full h-full object-cover" loading="lazy">
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-primary/90 text-white text-xs font-medium rounded-full backdrop-blur-sm">
                                <?= e($kamar['tipe']) ?>
                            </span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 bg-white/90 text-dark text-xs font-medium rounded-full backdrop-blur-sm flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <?= $kamar['kapasitas'] ?> tamu
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-serif text-xl font-semibold text-dark mb-2"><?= e($kamar['nama']) ?></h3>
                        <p class="text-earth text-sm mb-4"><?= e($kamar['deskripsi']) ?></p>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-primary"><?= format_rupiah($kamar['harga']) ?></span>
                                <span class="text-earth text-sm">/malam</span>
                            </div>
                            <a href="<?= BASE_URL ?>/tamu/kamar_detail.php?id=<?= $kamar['id'] ?>"
                               class="px-5 py-2.5 bg-primary hover:bg-primary-light text-white text-sm font-medium rounded-full transition-colors">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
         PAKET WISATA HIGHLIGHT
         ═══════════════════════════════════════════ -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-12 reveal">
                <div>
                    <p class="text-accent font-medium tracking-wider uppercase text-sm mb-3">Petualangan</p>
                    <h2 class="font-serif text-4xl sm:text-5xl text-dark font-bold">Paket Wisata Alam</h2>
                </div>
                <a href="<?= BASE_URL ?>/tamu/paket_wisata.php" class="mt-4 sm:mt-0 text-primary hover:text-primary-light font-medium flex items-center gap-2 transition-colors">
                    Lihat Semua
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php
                $kategoriIcons = [
                    'trekking' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>',
                    'perahu' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>',
                    'kuliner' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                ];
                $kategoriLabels = ['trekking' => 'Trekking', 'perahu' => 'River / Perahu', 'kuliner' => 'Kuliner'];
                $kategoriColors = ['trekking' => 'bg-success text-white', 'perahu' => 'bg-info text-white', 'kuliner' => 'bg-secondary text-white'];
                ?>
                <?php foreach ($paketHighlight as $i => $paket): ?>
                <div class="reveal card-hover bg-cream rounded-2xl overflow-hidden shadow-sm" style="transition-delay: <?= $i * 0.1 ?>s">
                    <div class="img-zoom relative h-56">
                        <img src="<?= e($paket['foto']) ?>" alt="<?= e($paket['nama']) ?>" class="w-full h-full object-cover" loading="lazy">
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 <?= $kategoriColors[$paket['kategori']] ?> text-xs font-medium rounded-full backdrop-blur-sm">
                                <?= e($kategoriLabels[$paket['kategori']]) ?>
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-serif text-xl font-semibold text-dark mb-2"><?= e($paket['nama']) ?></h3>
                        <p class="text-earth text-sm mb-4"><?= e($paket['deskripsi']) ?></p>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-primary"><?= format_rupiah($paket['harga']) ?></span>
                                <span class="text-earth text-sm">/orang</span>
                            </div>
                            <a href="<?= BASE_URL ?>/tamu/paket_wisata_detail.php?id=<?= $paket['id'] ?>"
                               class="px-5 py-2.5 bg-secondary hover:bg-secondary-light text-white text-sm font-medium rounded-full transition-colors">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
         CTA SECTION
         ═══════════════════════════════════════════ -->
    <section class="relative py-32 overflow-hidden">
        <div class="absolute inset-0">
            <img src="<?= BASE_URL ?>/assets/img/rawa-bento-2.png"
                 alt="Rawa Bento"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-dark/70"></div>
        </div>
        <div class="relative z-10 max-w-3xl mx-auto text-center px-4 reveal">
            <p class="text-accent font-medium tracking-wider uppercase text-sm mb-4">Siap Bertualang?</p>
            <h2 class="font-serif text-4xl sm:text-5xl text-white font-bold mb-6">Pesan Sekarang &<br>Ciptakan Kenangan Tak Terlupakan</h2>
            <p class="text-cream/70 text-lg mb-10 max-w-xl mx-auto">Penginapan nyaman, wisata seru, dan kuliner autentik — semuanya menanti Anda di Kincay Mania.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= BASE_URL ?>/tamu/cek_ketersediaan.php"
                   class="px-8 py-4 bg-accent hover:bg-accent-light text-dark font-semibold rounded-full transition-all hover:shadow-xl hover:shadow-accent/25 text-lg">
                    Cek Ketersediaan
                </a>
                <a href="<?= BASE_URL ?>/tamu/informasi.php"
                   class="px-8 py-4 border-2 border-cream/30 hover:border-cream/60 text-white rounded-full transition-all hover:bg-white/10 text-lg">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
         STATISTIK SECTION
         ═══════════════════════════════════════════ -->
    <section class="py-20 bg-primary">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
                <div class="reveal">
                    <div class="text-4xl sm:text-5xl font-bold text-accent mb-2">6+</div>
                    <div class="text-cream/70 text-sm">Kamar & Kabin</div>
                </div>
                <div class="reveal" style="transition-delay: 0.1s">
                    <div class="text-4xl sm:text-5xl font-bold text-accent mb-2">4</div>
                    <div class="text-cream/70 text-sm">Paket Wisata</div>
                </div>
                <div class="reveal" style="transition-delay: 0.2s">
                    <div class="text-4xl sm:text-5xl font-bold text-accent mb-2">500+</div>
                    <div class="text-cream/70 text-sm">Tamu Puas</div>
                </div>
                <div class="reveal" style="transition-delay: 0.3s">
                    <div class="text-4xl sm:text-5xl font-bold text-accent mb-2">4.8</div>
                    <div class="text-cream/70 text-sm">Rating Tamu</div>
                </div>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
