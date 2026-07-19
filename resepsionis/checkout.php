<?php
/** Check-out — Resepsionis */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Proses Check-out';
$daftarCheckout = [
    ['id' => 7, 'nama' => 'Andi Wisata', 'kamar' => 'Kabin Pinus A2', 'checkin' => '2026-07-14', 'checkout' => '2026-07-16', 'tamu' => 2, 'total' => 900000],
];
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_resepsionis.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Proses Check-out</h1>
            <p class="text-earth mb-8">Booking berstatus check-in yang siap untuk di-checkout.</p>
            <?php if (empty($daftarCheckout)): ?>
            <div class="bg-white rounded-2xl p-12 shadow-sm text-center"><p class="text-earth">Tidak ada tamu yang perlu check-out saat ini.</p></div>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($daftarCheckout as $c): ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1"><span class="text-sm font-mono text-earth">#BK-<?= str_pad($c['id'], 4, '0', STR_PAD_LEFT) ?></span><span class="px-2 py-0.5 bg-primary/10 text-primary text-xs rounded-full">Check-in</span></div>
                        <h3 class="font-semibold text-dark text-lg"><?= e($c['nama']) ?></h3>
                        <p class="text-sm text-earth"><?= e($c['kamar']) ?> · <?= date('d M', strtotime($c['checkin'])) ?> — <?= date('d M Y', strtotime($c['checkout'])) ?></p>
                    </div>
                    <div class="flex gap-2">
                        <a href="<?= BASE_URL ?>/resepsionis/invoice.php?id=<?= $c['id'] ?>" class="px-4 py-2 bg-cream hover:bg-cream-dark text-dark text-xs font-medium rounded-full transition-colors">Cetak Invoice</a>
                        <form method="POST" action="<?= BASE_URL ?>/resepsionis/checkout.php">
                            <?= csrf_field() ?>
                            <input type="hidden" name="booking_id" value="<?= $c['id'] ?>">
                            <button type="submit" name="proses_checkout" class="px-6 py-2 bg-earth hover:bg-earth-light text-white font-semibold rounded-full transition-colors flex items-center gap-2" data-confirm="Proses check-out untuk <?= e($c['nama']) ?>?">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Check-out
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
