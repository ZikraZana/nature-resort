<?php
/** Booking Walk-in — Resepsionis */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Booking Walk-in';
$kamarList = [
    ['id' => 1, 'nama' => 'Kabin Pinus A1', 'tipe' => 'Kabin', 'kapasitas' => 2, 'harga' => 450000],
    ['id' => 3, 'nama' => 'Kamar Deluxe B1', 'tipe' => 'Deluxe', 'kapasitas' => 4, 'harga' => 750000],
    ['id' => 6, 'nama' => 'Standard Room D1', 'tipe' => 'Standard', 'kapasitas' => 2, 'harga' => 300000],
];
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_resepsionis.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Booking Walk-in</h1>
            <p class="text-earth mb-8">Input booking manual untuk tamu walk-in atau reservasi telepon. Tidak perlu akun tamu.</p>

            <form method="POST" action="<?= BASE_URL ?>/resepsionis/walkin.php" class="space-y-6">
                <?= csrf_field() ?>

                <!-- Data Tamu -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-semibold text-dark mb-4 flex items-center gap-2"><svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> Data Tamu</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-dark mb-1">Nama Tamu</label><input type="text" name="nama_tamu" required placeholder="Nama lengkap" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Kontak (No. HP / KTP)</label><input type="text" name="kontak_tamu" required placeholder="08xxx atau No. KTP" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                    </div>
                </div>

                <!-- Detail Booking -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-semibold text-dark mb-4">Detail Booking</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-dark mb-1">Kamar</label>
                            <select name="kamar_id" required class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                                <option value="">Pilih kamar</option>
                                <?php foreach ($kamarList as $k): ?><option value="<?= $k['id'] ?>"><?= e($k['nama']) ?> (<?= e($k['tipe']) ?>) — <?= format_rupiah($k['harga']) ?>/mlm</option><?php endforeach; ?>
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Jumlah Tamu</label><input type="number" name="jumlah_tamu" value="1" min="1" max="6" required class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Check-in</label><input type="date" name="checkin" required value="<?= date('Y-m-d') ?>" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Check-out</label><input type="date" name="checkout" required value="<?= date('Y-m-d', strtotime('+1 day')) ?>" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                    </div>
                    <div class="mt-4"><label class="block text-sm font-medium text-dark mb-1">Catatan</label><textarea name="catatan" rows="2" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors resize-none" placeholder="Opsional"></textarea></div>
                </div>

                <!-- Metode Bayar -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-semibold text-dark mb-4">Metode Pembayaran</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer"><input type="radio" name="metode_bayar" value="tunai" class="peer hidden" checked>
                            <div class="border-2 border-cream-darker rounded-xl p-4 text-center transition-all peer-checked:border-primary peer-checked:bg-primary/5">
                                <svg class="w-8 h-8 mx-auto mb-2 text-earth peer-checked:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <p class="font-medium text-dark text-sm">Tunai</p><p class="text-xs text-earth">Langsung dikonfirmasi</p>
                            </div>
                        </label>
                        <label class="cursor-pointer"><input type="radio" name="metode_bayar" value="transfer" class="peer hidden">
                            <div class="border-2 border-cream-darker rounded-xl p-4 text-center transition-all peer-checked:border-primary peer-checked:bg-primary/5">
                                <svg class="w-8 h-8 mx-auto mb-2 text-earth" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <p class="font-medium text-dark text-sm">Transfer</p><p class="text-xs text-earth">Masuk alur verifikasi</p>
                            </div>
                        </label>
                    </div>
                </div>

                <button type="submit" name="simpan_walkin" class="w-full py-3.5 bg-accent hover:bg-accent-light text-dark font-semibold rounded-xl transition-all hover:shadow-lg flex items-center justify-center gap-2 text-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Booking Walk-in
                </button>
            </form>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
