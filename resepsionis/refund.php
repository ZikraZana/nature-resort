<?php
/** Daftar Refund — Resepsionis */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Proses Refund';
$daftarRefund = [
    ['id' => 1, 'booking_id' => 6, 'nama' => 'Budi Tamu', 'kamar' => 'Kamar Deluxe B1', 'total_booking' => 1500000, 'nominal_refund' => 750000, 'status' => 'menunggu'],
];
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_resepsionis.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Proses Refund</h1>
            <p class="text-earth mb-8"><?= count($daftarRefund) ?> refund menunggu diproses.</p>
            <div class="space-y-4">
                <?php foreach ($daftarRefund as $r): ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1"><span class="text-sm font-mono text-earth">#BK-<?= str_pad($r['booking_id'], 4, '0', STR_PAD_LEFT) ?></span><span class="px-2 py-0.5 bg-warning-light text-warning text-xs rounded-full">Menunggu Refund</span></div>
                        <h3 class="font-semibold text-dark text-lg"><?= e($r['nama']) ?></h3>
                        <p class="text-sm text-earth"><?= e($r['kamar']) ?> · Total booking: <?= format_rupiah($r['total_booking']) ?></p>
                        <p class="text-lg font-bold text-primary mt-1">Refund: <?= format_rupiah($r['nominal_refund']) ?></p>
                    </div>
                    <a href="<?= BASE_URL ?>/resepsionis/refund_proses.php?id=<?= $r['id'] ?>" class="px-6 py-2.5 bg-accent hover:bg-accent-light text-dark font-semibold rounded-full transition-all hover:shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        Proses Refund
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
