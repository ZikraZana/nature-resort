<?php
/**
 * Cek Ketersediaan Kamar — Kincay Mania Hotel & Resort
 */
require_once __DIR__ . '/../config/config.php';

$pageTitle = 'Cek Ketersediaan';
$pageDescription = 'Cek ketersediaan kamar di Kincay Mania Hotel & Resort berdasarkan tanggal check-in dan check-out.';

$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$showResults = !empty($checkin) && !empty($checkout);

// Query ketersediaan kamar dari database
$kamarTersedia = [];
if ($showResults && $checkin < $checkout) {
    $stmt = db()->prepare(
        "SELECT id, nama, tipe, kapasitas, harga_per_malam AS harga, foto
         FROM kamar
         WHERE status_default = 'tersedia'
           AND id NOT IN (
               SELECT kamar_id FROM booking
               WHERE status NOT IN ('dibatalkan','ditolak')
                 AND tanggal_checkin < ? AND tanggal_checkout > ?
           )
         ORDER BY tipe, nama"
    );
    $stmt->execute([$checkout, $checkin]);
    $kamarTersedia = $stmt->fetchAll();
    foreach ($kamarTersedia as &$k) {
        if (empty($k['foto'])) $k['foto'] = 'https://images.unsplash.com/photo-1618767689160-da3fb810aad7?w=400&h=300&fit=crop';
        elseif (!str_starts_with($k['foto'], 'http')) $k['foto'] = BASE_URL . '/uploads/' . $k['foto'];
    }
    unset($k);
}

$jumlahMalam = 0;
if ($showResults && strtotime($checkout) > strtotime($checkin)) {
    $jumlahMalam = (strtotime($checkout) - strtotime($checkin)) / 86400;
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_guest.php';
?>

    <!-- Page Header -->
    <section class="pt-28 pb-12 bg-dark relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=1920&h=400&fit=crop" alt="" class="w-full h-full object-cover">
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-accent font-medium tracking-wider uppercase text-sm mb-3">Ketersediaan</p>
            <h1 class="font-serif text-4xl sm:text-5xl text-white font-bold mb-4">Cek Ketersediaan Kamar</h1>
            <p class="text-cream/60 max-w-2xl mx-auto">Masukkan tanggal menginap Anda untuk melihat kamar yang tersedia.</p>
        </div>
    </section>

    <!-- Search Form -->
    <section class="bg-white py-8 border-b border-cream-dark">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-dark mb-1">Tanggal Check-in</label>
                    <input type="date" name="checkin" value="<?= e($checkin) ?>" required min="<?= date('Y-m-d') ?>"
                           class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-dark mb-1">Tanggal Check-out</label>
                    <input type="date" name="checkout" value="<?= e($checkout) ?>" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                           class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                </div>
                <button type="submit" class="px-8 py-3 bg-accent hover:bg-accent-light text-dark font-semibold rounded-xl transition-all hover:shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari Kamar
                </button>
            </form>
        </div>
    </section>

    <!-- Results -->
    <section class="py-12 bg-cream min-h-[40vh]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if ($showResults): ?>
                <div class="mb-8 p-4 bg-success-light rounded-xl border border-success/20">
                    <p class="text-success font-medium flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <?= count($kamarTersedia) ?> kamar tersedia untuk tanggal <?= date('d M Y', strtotime($checkin)) ?> — <?= date('d M Y', strtotime($checkout)) ?>
                        (<?= $jumlahMalam ?> malam)
                    </p>
                </div>

                <div class="space-y-4">
                    <?php foreach ($kamarTersedia as $kamar): ?>
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm flex flex-col sm:flex-row card-hover">
                        <div class="img-zoom w-full sm:w-56 h-48 sm:h-auto shrink-0">
                            <img src="<?= e($kamar['foto']) ?>" alt="<?= e($kamar['nama']) ?>" class="w-full h-full object-cover" loading="lazy">
                        </div>
                        <div class="p-6 flex-1 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full"><?= e($kamar['tipe']) ?></span>
                                    <span class="text-sm text-earth flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Maks. <?= $kamar['kapasitas'] ?> tamu
                                    </span>
                                </div>
                                <h3 class="font-serif text-xl font-semibold text-dark mb-1"><?= e($kamar['nama']) ?></h3>
                                <p class="text-sm text-earth"><?= format_rupiah($kamar['harga']) ?>/malam</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm text-earth mb-1">Total <?= $jumlahMalam ?> malam</p>
                                <p class="text-2xl font-bold text-primary mb-3"><?= format_rupiah($kamar['harga'] * $jumlahMalam) ?></p>
                                <a href="<?= BASE_URL ?>/tamu/booking.php?kamar_id=<?= $kamar['id'] ?>&checkin=<?= e($checkin) ?>&checkout=<?= e($checkout) ?>"
                                   class="inline-block px-6 py-2.5 bg-accent hover:bg-accent-light text-dark font-semibold rounded-full transition-all hover:shadow-lg text-sm">
                                    Booking
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-16">
                    <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-cream-dark flex items-center justify-center">
                        <svg class="w-12 h-12 text-earth/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-serif text-2xl text-dark font-semibold mb-2">Pilih Tanggal Menginap</h3>
                    <p class="text-earth">Masukkan tanggal check-in dan check-out di atas untuk melihat kamar yang tersedia.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
