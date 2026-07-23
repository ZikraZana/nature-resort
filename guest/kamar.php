<?php
/**
 * Daftar Kamar/Kabin — Kincay Mania Hotel & Resort
 */
require_once __DIR__ . '/../config/config.php';

$pageTitle = 'Kamar & Kabin';
$pageDescription = 'Jelajahi pilihan kamar dan kabin eksklusif di Kincay Mania Hotel & Resort — dari kabin pinus hingga suite premium.';

// Query kamar dari database
$filterTipe = $_GET['tipe'] ?? '';
$filterCheckin = $_GET['checkin'] ?? '';
$filterCheckout = $_GET['checkout'] ?? '';

$sql = "SELECT id, nama, tipe, kapasitas, harga_per_malam AS harga, foto, deskripsi, status_default AS status FROM kamar WHERE status_default != 'nonaktif'";
$params = [];

if ($filterTipe) {
    $sql .= ' AND tipe = ?';
    $params[] = $filterTipe;
}

// Jika filter tanggal, exclude kamar yang sudah di-book
if ($filterCheckin && $filterCheckout) {
    $sql .= ' AND id NOT IN (SELECT kamar_id FROM booking WHERE status NOT IN (?,?) AND tanggal_checkin < ? AND tanggal_checkout > ?)';
    $params[] = 'dibatalkan';
    $params[] = 'ditolak';
    $params[] = $filterCheckout;
    $params[] = $filterCheckin;
}

$sql .= ' ORDER BY tipe, nama';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$kamarList = $stmt->fetchAll();

// Default foto jika kosong
foreach ($kamarList as &$k) {
    if (empty($k['foto'])) {
        $k['foto'] = 'https://images.unsplash.com/photo-1618767689160-da3fb810aad7?w=600&h=400&fit=crop';
    } elseif (!str_starts_with($k['foto'], 'http')) {
        $k['foto'] = BASE_URL . '/uploads/' . $k['foto'];
    }
}
unset($k);

$tipeList = ['Kabin', 'Standard', 'Deluxe', 'Suite'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_guest.php';
?>

    <!-- Page Header -->
    <section class="pt-28 pb-12 bg-dark relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <img src="https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=1920&h=400&fit=crop" alt="" class="w-full h-full object-cover">
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-accent font-medium tracking-wider uppercase text-sm mb-3">Akomodasi</p>
            <h1 class="font-serif text-4xl sm:text-5xl text-white font-bold mb-4">Kamar & Kabin</h1>
            <p class="text-cream/60 max-w-2xl mx-auto">Temukan penginapan yang sempurna — dari kabin kayu di tengah hutan pinus hingga suite premium dengan pemandangan Gunung Kerinci.</p>
        </div>
    </section>

    <!-- Filter Bar -->
    <section class="bg-white border-b border-cream-dark sticky top-20 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-earth mb-1">Check-in</label>
                    <input type="date" name="checkin" value="<?= e($filterCheckin) ?>"
                           class="w-full px-4 py-2.5 bg-cream border border-cream-darker rounded-lg text-sm text-dark focus:border-primary transition-colors">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-earth mb-1">Check-out</label>
                    <input type="date" name="checkout" value="<?= e($filterCheckout) ?>"
                           class="w-full px-4 py-2.5 bg-cream border border-cream-darker rounded-lg text-sm text-dark focus:border-primary transition-colors">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-earth mb-1">Tipe Kamar</label>
                    <select name="tipe" class="w-full px-4 py-2.5 bg-cream border border-cream-darker rounded-lg text-sm text-dark focus:border-primary transition-colors">
                        <option value="">Semua Tipe</option>
                        <?php foreach ($tipeList as $tipe): ?>
                        <option value="<?= e($tipe) ?>" <?= $filterTipe === $tipe ? 'selected' : '' ?>><?= e($tipe) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-primary-light text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari
                </button>
            </form>
        </div>
    </section>

    <!-- Room Grid -->
    <section class="py-12 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-sm text-earth mb-6"><?= count($kamarList) ?> kamar ditemukan</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($kamarList as $kamar): ?>
                <div class="card-hover bg-white rounded-2xl overflow-hidden shadow-sm">
                    <div class="img-zoom relative h-56">
                        <img src="<?= e($kamar['foto']) ?>" alt="<?= e($kamar['nama']) ?>" class="w-full h-full object-cover" loading="lazy">
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-primary/90 text-white text-xs font-medium rounded-full backdrop-blur-sm">
                                <?= e($kamar['tipe']) ?>
                            </span>
                        </div>
                        <div class="absolute top-4 right-4 flex gap-2">
                            <span class="px-3 py-1 bg-white/90 text-dark text-xs font-medium rounded-full backdrop-blur-sm flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <?= $kamar['kapasitas'] ?>
                            </span>
                        </div>
                        <?php if ($kamar['status'] === 'maintenance'): ?>
                        <div class="absolute inset-0 bg-dark/60 flex items-center justify-center">
                            <span class="px-4 py-2 bg-warning text-white text-sm font-medium rounded-full">Maintenance</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <h3 class="font-serif text-xl font-semibold text-dark mb-2"><?= e($kamar['nama']) ?></h3>
                        <p class="text-earth text-sm mb-4 line-clamp-2"><?= e($kamar['deskripsi']) ?></p>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-primary"><?= format_rupiah($kamar['harga']) ?></span>
                                <span class="text-earth text-sm">/malam</span>
                            </div>
                            <a href="<?= BASE_URL ?>/guest/kamar_detail.php?id=<?= $kamar['id'] ?>"
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

<?php include __DIR__ . '/../includes/footer.php'; ?>
