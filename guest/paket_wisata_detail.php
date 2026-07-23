<?php
/**
 * Detail Paket Wisata — Kincay Mania Hotel & Resort
 */
require_once __DIR__ . '/../config/config.php';

// Query paket dari database
$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM paket_wisata WHERE id = ?');
$stmt->execute([$id]);
$paket = $stmt->fetch();

if (!$paket) {
    header('Location: ' . BASE_URL . '/guest/paket_wisata.php');
    exit;
}

// Default foto
if (empty($paket['foto'])) {
    $paket['foto'] = 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=900&h=600&fit=crop';
} elseif (!str_starts_with($paket['foto'], 'http')) {
    $paket['foto'] = BASE_URL . '/uploads/' . $paket['foto'];
}

// Query jadwal mendatang
$stmtJadwal = db()->prepare(
    "SELECT jw.id, jw.tanggal, jw.kuota_maksimal AS kuota_maks,
            COALESCE(SUM(bpw.jumlah_peserta), 0) AS terpakai
     FROM jadwal_wisata jw
     LEFT JOIN booking_paket_wisata bpw ON bpw.jadwal_wisata_id = jw.id
     LEFT JOIN booking b ON b.id = bpw.booking_id AND b.status NOT IN ('dibatalkan','ditolak')
     WHERE jw.paket_wisata_id = ? AND jw.tanggal >= CURDATE()
     GROUP BY jw.id
     ORDER BY jw.tanggal"
);
$stmtJadwal->execute([$id]);
$paket['jadwal'] = $stmtJadwal->fetchAll();

$pageTitle = $paket['nama'];

$kategoriLabels = ['trekking' => 'Trekking', 'perahu' => 'River / Perahu', 'kuliner' => 'Kuliner'];
$kategoriColors = ['trekking' => 'bg-success text-white', 'perahu' => 'bg-info text-white', 'kuliner' => 'bg-secondary text-white'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_guest.php';
?>

    <!-- Breadcrumb -->
    <div class="pt-24 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center gap-2 text-sm text-earth">
                <a href="<?= BASE_URL ?>/guest/" class="hover:text-primary transition-colors">Beranda</a>
                <svg class="w-4 h-4 text-earth/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="<?= BASE_URL ?>/guest/paket_wisata.php" class="hover:text-primary transition-colors">Paket Wisata</a>
                <svg class="w-4 h-4 text-earth/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-dark font-medium"><?= e($paket['nama']) ?></span>
            </nav>
        </div>
    </div>

    <!-- Detail Content -->
    <section class="pb-16 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <!-- Left: Image & Info -->
                <div class="lg:col-span-3 space-y-6">
                    <div class="rounded-2xl overflow-hidden shadow-lg">
                        <img src="<?= e($paket['foto']) ?>" alt="<?= e($paket['nama']) ?>" class="w-full h-80 sm:h-96 object-cover">
                    </div>

                    <div class="bg-white rounded-2xl p-8 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="px-3 py-1 <?= $kategoriColors[$paket['kategori']] ?> text-xs font-medium rounded-full">
                                <?= e($kategoriLabels[$paket['kategori']]) ?>
                            </span>
                        </div>
                        <h1 class="font-serif text-3xl text-dark font-bold mb-4"><?= e($paket['nama']) ?></h1>
                        <div class="text-earth leading-relaxed whitespace-pre-line"><?= e($paket['deskripsi']) ?></div>
                    </div>
                </div>

                <!-- Right: Jadwal & Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Price Card -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm">
                        <div class="flex items-baseline gap-2 mb-6">
                            <span class="text-3xl font-bold text-primary"><?= format_rupiah($paket['harga']) ?></span>
                            <span class="text-earth">/orang</span>
                        </div>
                        <p class="text-sm text-earth mb-4">Paket ini dapat ditambahkan sebagai add-on saat booking kamar.</p>
                        <a href="<?= BASE_URL ?>/guest/kamar.php"
                           class="block w-full py-3 bg-accent hover:bg-accent-light text-dark font-semibold rounded-xl text-center transition-all hover:shadow-lg">
                            Booking Kamar + Paket Ini
                        </a>
                    </div>

                    <!-- Jadwal & Kuota -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm">
                        <h3 class="font-serif text-xl text-dark font-semibold mb-4">Jadwal Tersedia</h3>
                        <div class="space-y-3">
                            <?php foreach ($paket['jadwal'] as $j):
                                $sisa = $j['kuota_maks'] - $j['terpakai'];
                                $persen = ($j['terpakai'] / $j['kuota_maks']) * 100;
                            ?>
                            <div class="p-4 rounded-xl <?= $sisa > 0 ? 'bg-cream' : 'bg-danger-light' ?>">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-dark">
                                        <?= date('d M Y', strtotime($j['tanggal'])) ?>
                                    </span>
                                    <span class="text-sm font-medium <?= $sisa > 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= $sisa > 0 ? "Sisa $sisa kuota" : 'Penuh' ?>
                                    </span>
                                </div>
                                <div class="w-full bg-cream-darker rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all <?= $sisa > 0 ? 'bg-success' : 'bg-danger' ?>" style="width: <?= $persen ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
