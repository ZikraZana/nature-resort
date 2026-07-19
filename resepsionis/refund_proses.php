<?php
/** Proses Refund — Upload Bukti — Resepsionis */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Proses Refund';
$id = (int)($_GET['id'] ?? 1);
$refund = ['booking_id' => 6, 'nama' => 'Budi Tamu', 'no_hp' => '081234567890', 'kamar' => 'Kamar Deluxe B1', 'total' => 1500000, 'nominal_refund' => 750000];
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_resepsionis.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-xl mx-auto px-4">
            <a href="<?= BASE_URL ?>/resepsionis/refund.php" class="inline-flex items-center gap-2 text-earth hover:text-primary text-sm mb-6 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali</a>
            <div class="bg-white rounded-2xl p-8 shadow-sm">
                <h1 class="font-sans text-2xl text-dark font-bold mb-6">Proses Refund</h1>
                <div class="p-4 bg-cream rounded-xl mb-6 text-sm space-y-2">
                    <div class="flex justify-between"><span class="text-earth">Booking:</span><span class="font-medium">#BK-<?= str_pad($refund['booking_id'], 4, '0', STR_PAD_LEFT) ?></span></div>
                    <div class="flex justify-between"><span class="text-earth">Tamu:</span><span class="font-medium"><?= e($refund['nama']) ?></span></div>
                    <div class="flex justify-between"><span class="text-earth">Kontak:</span><span class="font-medium"><?= e($refund['no_hp']) ?></span></div>
                    <div class="flex justify-between"><span class="text-earth">Total Booking:</span><span class="font-medium"><?= format_rupiah($refund['total']) ?></span></div>
                    <div class="flex justify-between border-t border-cream-darker pt-2"><span class="text-earth font-medium">Nominal Refund (50%):</span><span class="font-bold text-primary text-lg"><?= format_rupiah($refund['nominal_refund']) ?></span></div>
                </div>
                <form method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>/resepsionis/refund_proses.php" class="space-y-5">
                    <?= csrf_field() ?>
                    <input type="hidden" name="refund_id" value="<?= $id ?>">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Upload Bukti Transfer Refund</label>
                        <div class="border-2 border-dashed border-cream-darker rounded-xl p-6 text-center hover:border-primary/50 transition-colors">
                            <p class="text-sm text-earth mb-2">Screenshot bukti transfer balik ke tamu</p>
                            <input type="file" name="bukti_refund" accept=".jpg,.jpeg,.png,.pdf" required class="mt-2 w-full text-sm text-earth file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                        </div>
                    </div>
                    <button type="submit" name="proses_refund" class="w-full py-3.5 bg-success hover:bg-success/90 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Konfirmasi Refund Selesai
                    </button>
                </form>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
