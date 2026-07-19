<?php
/** Detail Verifikasi Pembayaran — Resepsionis */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Detail Verifikasi';
$id = (int)($_GET['id'] ?? 1);
$data = ['booking_id' => 2, 'nama' => 'Budi Tamu', 'email' => 'tamu@kincaymania.com', 'no_hp' => '081234567890', 'kamar' => 'Kamar Deluxe B1', 'checkin' => '2026-07-25', 'checkout' => '2026-07-27', 'total' => 1500000, 'nominal_transfer' => 1500000, 'tanggal_upload' => '2026-07-15 14:30', 'bukti' => 'https://placehold.co/400x600/E8D5A8/6B4E2E?text=Bukti+Transfer'];
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_resepsionis.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="<?= BASE_URL ?>/resepsionis/verifikasi.php" class="inline-flex items-center gap-2 text-earth hover:text-primary text-sm mb-6 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Kembali</a>
            <h1 class="font-sans text-3xl text-dark font-bold mb-8">Review Pembayaran #BK-<?= str_pad($data['booking_id'], 4, '0', STR_PAD_LEFT) ?></h1>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Bukti Transfer -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-semibold text-dark mb-4">Bukti Transfer</h2>
                    <div class="rounded-xl overflow-hidden border border-cream-dark"><img src="<?= e($data['bukti']) ?>" alt="Bukti transfer" class="w-full"></div>
                    <div class="mt-4 p-4 bg-cream rounded-xl text-sm">
                        <div class="flex justify-between mb-1"><span class="text-earth">Nominal Transfer:</span><span class="font-bold text-primary text-lg"><?= format_rupiah($data['nominal_transfer']) ?></span></div>
                        <div class="flex justify-between"><span class="text-earth">Total Booking:</span><span class="font-bold text-dark"><?= format_rupiah($data['total']) ?></span></div>
                        <?php if ($data['nominal_transfer'] === $data['total']): ?>
                        <p class="mt-2 text-success text-xs font-medium flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Nominal sesuai</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Detail Booking & Aksi -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h2 class="font-semibold text-dark mb-4">Detail Booking</h2>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Tamu</span><span class="font-medium"><?= e($data['nama']) ?></span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Email</span><span class="font-medium"><?= e($data['email']) ?></span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">No. HP</span><span class="font-medium"><?= e($data['no_hp']) ?></span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Kamar</span><span class="font-medium"><?= e($data['kamar']) ?></span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Check-in</span><span class="font-medium"><?= date('d M Y', strtotime($data['checkin'])) ?></span></div>
                            <div class="flex justify-between py-2"><span class="text-earth">Check-out</span><span class="font-medium"><?= date('d M Y', strtotime($data['checkout'])) ?></span></div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h2 class="font-semibold text-dark mb-4">Keputusan Verifikasi</h2>
                        <form method="POST" action="<?= BASE_URL ?>/resepsionis/verifikasi_detail.php" class="space-y-4">
                            <?= csrf_field() ?>
                            <input type="hidden" name="pembayaran_id" value="<?= $id ?>">
                            <button type="submit" name="aksi" value="approve" class="w-full py-3 bg-success hover:bg-success/90 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Approve — Konfirmasi Pembayaran
                            </button>
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1">Alasan Penolakan (wajib jika tolak)</label>
                                <textarea name="alasan" rows="3" placeholder="Contoh: Nominal tidak sesuai, bukti tidak jelas, dll."
                                          class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-sm text-dark placeholder-earth/40 focus:border-primary transition-colors resize-none"></textarea>
                            </div>
                            <button type="submit" name="aksi" value="reject" class="w-full py-3 bg-danger hover:bg-danger/90 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Tolak Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
