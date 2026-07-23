<?php
/**
 * Booking Walk-in — Resepsionis
 * Input booking manual untuk tamu walk-in. Status langsung 'dikonfirmasi' (tunai)
 * atau 'menunggu_pembayaran' (transfer).
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Booking Walk-in';

$errors = [];

// ── Proses POST — simpan walk-in ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_walkin'])) {
    validate_csrf();

    $namaTamu   = trim($_POST['nama_tamu'] ?? '');
    $kontakTamu = trim($_POST['kontak_tamu'] ?? '');
    $kamarId    = (int)($_POST['kamar_id'] ?? 0);
    $jumlahTamu = (int)($_POST['jumlah_tamu'] ?? 1);
    $checkin    = trim($_POST['checkin'] ?? '');
    $checkout   = trim($_POST['checkout'] ?? '');
    $catatan    = trim($_POST['catatan'] ?? '');
    $metodeBayar = $_POST['metode_bayar'] ?? 'tunai';

    // Validasi
    if (empty($namaTamu)) $errors[] = 'Nama tamu wajib diisi.';
    if (empty($kontakTamu)) $errors[] = 'Kontak tamu wajib diisi.';
    if ($kamarId <= 0) $errors[] = 'Pilih kamar.';
    if (empty($checkin) || empty($checkout)) $errors[] = 'Tanggal check-in dan check-out wajib diisi.';
    elseif ($checkin >= $checkout) $errors[] = 'Tanggal check-out harus setelah tanggal check-in.';
    if ($jumlahTamu < 1) $errors[] = 'Jumlah tamu minimal 1.';

    // Validasi kamar
    if (empty($errors) && $kamarId > 0) {
        $stmt = db()->prepare('SELECT id, nama, harga_per_malam, kapasitas FROM kamar WHERE id = ? AND status_default = ?');
        $stmt->execute([$kamarId, 'tersedia']);
        $kamar = $stmt->fetch();

        if (!$kamar) {
            $errors[] = 'Kamar tidak ditemukan atau sedang maintenance.';
        } elseif ($jumlahTamu > $kamar['kapasitas']) {
            $errors[] = 'Jumlah tamu melebihi kapasitas kamar (' . $kamar['kapasitas'] . ').';
        }
    }

    // Cek ketersediaan
    if (empty($errors)) {
        $stmt = db()->prepare(
            'SELECT COUNT(*) FROM booking WHERE kamar_id = ? AND status NOT IN (?,?) AND tanggal_checkin < ? AND tanggal_checkout > ?'
        );
        $stmt->execute([$kamarId, 'dibatalkan', 'ditolak', $checkout, $checkin]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Kamar sudah dipesan untuk tanggal tersebut.';
        }
    }

    if (empty($errors)) {
        $jumlahMalam = (int)((strtotime($checkout) - strtotime($checkin)) / 86400);
        $totalHarga = $kamar['harga_per_malam'] * $jumlahMalam;

        // Status: tunai → langsung dikonfirmasi, transfer → menunggu_pembayaran
        $status = ($metodeBayar === 'tunai') ? 'dikonfirmasi' : 'menunggu_pembayaran';

        $pdo = db();
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'INSERT INTO booking (user_id, kamar_id, dibuat_oleh, nama_tamu, kontak_tamu,
                    tanggal_checkin, tanggal_checkout, jumlah_tamu, catatan, total_harga, status)
                 VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $kamarId, $_SESSION['user_id'],
                $namaTamu, $kontakTamu,
                $checkin, $checkout, $jumlahTamu,
                $catatan ?: null, $totalHarga, $status
            ]);
            $bookingId = (int)$pdo->lastInsertId();

            // Jika tunai, insert pembayaran langsung diterima
            if ($metodeBayar === 'tunai') {
                $stmtBayar = $pdo->prepare(
                    'INSERT INTO pembayaran (booking_id, bukti_transfer, nominal, status, diverifikasi_oleh)
                     VALUES (?, ?, ?, ?, ?)'
                );
                $stmtBayar->execute([$bookingId, 'tunai_walkin', $totalHarga, 'diterima', $_SESSION['user_id']]);
            }

            $pdo->commit();
            set_flash('success', 'Booking walk-in berhasil! #BK-' . str_pad($bookingId, 4, '0', STR_PAD_LEFT) . ($status === 'dikonfirmasi' ? ' — langsung dikonfirmasi.' : ' — menunggu pembayaran transfer.'));
            header('Location: ' . BASE_URL . '/resepsionis/walkin.php');
            exit;

        } catch (\Exception $e) {
            $pdo->rollBack();
            error_log('[Walk-in Error] ' . $e->getMessage());
            $errors[] = 'Terjadi kesalahan saat menyimpan booking.';
        }
    }
}

// Query kamar tersedia
$stmt = db()->prepare('SELECT id, nama, tipe, kapasitas, harga_per_malam FROM kamar WHERE status_default = ? ORDER BY tipe, nama');
$stmt->execute(['tersedia']);
$kamarList = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_resepsionis.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php $flash = get_flash(); if ($flash): ?>
            <div class="mb-6 p-4 bg-<?= $flash['type'] === 'success' ? 'success-light' : 'danger-light' ?> rounded-xl text-sm text-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
                <?= e($flash['message']) ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
            <div class="mb-6 p-4 bg-danger-light rounded-xl text-sm text-danger">
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Booking Walk-in</h1>
            <p class="text-earth mb-8">Input booking manual untuk tamu walk-in atau reservasi telepon. Tidak perlu akun tamu.</p>

            <form method="POST" action="<?= BASE_URL ?>/resepsionis/walkin.php" class="space-y-6">
                <?= csrf_field() ?>

                <!-- Data Tamu -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-semibold text-dark mb-4 flex items-center gap-2"><svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> Data Tamu</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-dark mb-1">Nama Tamu</label><input type="text" name="nama_tamu" required placeholder="Nama lengkap" value="<?= e($_POST['nama_tamu'] ?? '') ?>" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Kontak (No. HP / KTP)</label><input type="text" name="kontak_tamu" required placeholder="08xxx atau No. KTP" value="<?= e($_POST['kontak_tamu'] ?? '') ?>" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                    </div>
                </div>

                <!-- Detail Booking -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-semibold text-dark mb-4">Detail Booking</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-dark mb-1">Kamar</label>
                            <select name="kamar_id" required class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                                <option value="">Pilih kamar</option>
                                <?php foreach ($kamarList as $k): ?><option value="<?= $k['id'] ?>" <?= (int)($_POST['kamar_id'] ?? 0) === $k['id'] ? 'selected' : '' ?>><?= e($k['nama']) ?> (<?= e($k['tipe']) ?>) — <?= format_rupiah($k['harga_per_malam']) ?>/mlm</option><?php endforeach; ?>
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Jumlah Tamu</label><input type="number" name="jumlah_tamu" value="<?= e($_POST['jumlah_tamu'] ?? '1') ?>" min="1" max="6" required class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Check-in</label><input type="date" name="checkin" required value="<?= e($_POST['checkin'] ?? date('Y-m-d')) ?>" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                        <div><label class="block text-sm font-medium text-dark mb-1">Check-out</label><input type="date" name="checkout" required value="<?= e($_POST['checkout'] ?? date('Y-m-d', strtotime('+1 day'))) ?>" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                    </div>
                    <div class="mt-4"><label class="block text-sm font-medium text-dark mb-1">Catatan</label><textarea name="catatan" rows="2" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors resize-none" placeholder="Opsional"><?= e($_POST['catatan'] ?? '') ?></textarea></div>
                </div>

                <!-- Metode Bayar -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-semibold text-dark mb-4">Metode Pembayaran</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer"><input type="radio" name="metode_bayar" value="tunai" class="peer hidden" <?= ($_POST['metode_bayar'] ?? 'tunai') === 'tunai' ? 'checked' : '' ?>>
                            <div class="border-2 border-cream-darker rounded-xl p-4 text-center transition-all peer-checked:border-primary peer-checked:bg-primary/5">
                                <svg class="w-8 h-8 mx-auto mb-2 text-earth" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <p class="font-medium text-dark text-sm">Tunai</p><p class="text-xs text-earth">Langsung dikonfirmasi</p>
                            </div>
                        </label>
                        <label class="cursor-pointer"><input type="radio" name="metode_bayar" value="transfer" class="peer hidden" <?= ($_POST['metode_bayar'] ?? '') === 'transfer' ? 'checked' : '' ?>>
                            <div class="border-2 border-cream-darker rounded-xl p-4 text-center transition-all peer-checked:border-primary peer-checked:bg-primary/5">
                                <svg class="w-8 h-8 mx-auto mb-2 text-earth" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <p class="font-medium text-dark text-sm">Transfer</p><p class="text-xs text-earth">Masuk alur verifikasi</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Ringkasan Total -->
                <div id="ringkasan-walkin" class="bg-white rounded-2xl p-6 shadow-sm hidden">
                    <h2 class="font-semibold text-dark mb-4">Ringkasan Biaya</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-earth">Kamar</span><span id="sum-kamar-nama" class="text-dark font-medium">-</span></div>
                        <div class="flex justify-between"><span class="text-earth">Harga/malam</span><span id="sum-harga-malam" class="text-dark">-</span></div>
                        <div class="flex justify-between"><span class="text-earth">Jumlah malam</span><span id="sum-jumlah-malam" class="text-dark">-</span></div>
                        <div class="border-t border-cream-dark pt-2 mt-2 flex justify-between"><span class="text-dark font-bold">Total</span><span id="sum-total" class="text-primary font-bold text-lg">-</span></div>
                    </div>
                </div>

                <button type="submit" name="simpan_walkin" class="w-full py-3.5 bg-accent hover:bg-accent-light text-dark font-semibold rounded-xl transition-all hover:shadow-lg flex items-center justify-center gap-2 text-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Booking Walk-in
                </button>
            </form>

            <script>
            (function() {
                const kamarPrices = {<?php foreach ($kamarList as $k): ?><?= $k['id'] ?>:{nama:<?= json_encode($k['nama']) ?>,harga:<?= $k['harga_per_malam'] ?>},<?php endforeach; ?>};
                const kamarSel = document.querySelector('[name="kamar_id"]');
                const checkinEl = document.querySelector('[name="checkin"]');
                const checkoutEl = document.querySelector('[name="checkout"]');
                const ringkasan = document.getElementById('ringkasan-walkin');

                function formatRupiah(n) { return 'Rp ' + n.toLocaleString('id-ID'); }

                function updateTotal() {
                    const kid = parseInt(kamarSel.value);
                    const ci = checkinEl.value;
                    const co = checkoutEl.value;
                    if (!kid || !ci || !co || !kamarPrices[kid]) { ringkasan.classList.add('hidden'); return; }
                    const malam = Math.ceil((new Date(co) - new Date(ci)) / 86400000);
                    if (malam <= 0) { ringkasan.classList.add('hidden'); return; }
                    const k = kamarPrices[kid];
                    document.getElementById('sum-kamar-nama').textContent = k.nama;
                    document.getElementById('sum-harga-malam').textContent = formatRupiah(k.harga);
                    document.getElementById('sum-jumlah-malam').textContent = malam + ' malam';
                    document.getElementById('sum-total').textContent = formatRupiah(k.harga * malam);
                    ringkasan.classList.remove('hidden');
                }

                kamarSel.addEventListener('change', updateTotal);
                checkinEl.addEventListener('change', updateTotal);
                checkoutEl.addEventListener('change', updateTotal);
                updateTotal();
            })();
            </script>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
