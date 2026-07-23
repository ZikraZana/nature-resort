<?php
/**
 * Booking Kamar (Step 1) — Kincay Mania Hotel & Resort
 * Pilih kamar, tanggal, jumlah tamu
 * Data kamar diambil dari database, ketersediaan dicek real-time.
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');
$pageTitle = 'Booking Kamar';

// Pre-filled data from query params (misal dari halaman kamar_detail)
$selectedKamarId = (int)($_GET['kamar_id'] ?? 0);
$checkin  = $_GET['checkin']  ?? '';
$checkout = $_GET['checkout'] ?? '';

$errors = [];

// ── Proses POST — validasi & simpan ke session ──────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();

    $kamarId     = (int)($_POST['kamar_id'] ?? 0);
    $checkin     = trim($_POST['checkin'] ?? '');
    $checkout    = trim($_POST['checkout'] ?? '');
    $jumlahTamu  = (int)($_POST['jumlah_tamu'] ?? 1);
    $catatan     = trim($_POST['catatan'] ?? '');
    $selectedKamarId = $kamarId;

    // Validasi input
    if ($kamarId <= 0) {
        $errors[] = 'Pilih kamar terlebih dahulu.';
    }
    if (empty($checkin) || empty($checkout)) {
        $errors[] = 'Tanggal check-in dan check-out wajib diisi.';
    } elseif ($checkin >= $checkout) {
        $errors[] = 'Tanggal check-out harus setelah tanggal check-in.';
    } elseif ($checkin < date('Y-m-d')) {
        $errors[] = 'Tanggal check-in tidak boleh sebelum hari ini.';
    }
    if ($jumlahTamu < 1 || $jumlahTamu > 10) {
        $errors[] = 'Jumlah tamu harus antara 1-10.';
    }

    // Validasi kamar ada & tersedia
    if (empty($errors)) {
        $stmt = db()->prepare('SELECT id, nama, tipe, kapasitas, harga_per_malam, foto FROM kamar WHERE id = ? AND status_default = ?');
        $stmt->execute([$kamarId, 'tersedia']);
        $kamar = $stmt->fetch();

        if (!$kamar) {
            $errors[] = 'Kamar tidak ditemukan atau sedang tidak tersedia.';
        } elseif ($jumlahTamu > $kamar['kapasitas']) {
            $errors[] = 'Jumlah tamu melebihi kapasitas kamar (' . $kamar['kapasitas'] . ' orang).';
        }
    }

    // Cek ketersediaan tanggal (tidak ada booking overlap yang aktif)
    if (empty($errors)) {
        $stmt = db()->prepare(
            'SELECT COUNT(*) FROM booking
             WHERE kamar_id = ?
               AND status NOT IN (?, ?)
               AND tanggal_checkin < ?
               AND tanggal_checkout > ?'
        );
        $stmt->execute([$kamarId, 'dibatalkan', 'ditolak', $checkout, $checkin]);
        $conflict = $stmt->fetchColumn();

        if ($conflict > 0) {
            $errors[] = 'Kamar ini sudah dipesan untuk tanggal yang Anda pilih. Silakan pilih tanggal atau kamar lain.';
        }
    }

    // Simpan ke session & lanjut ke step 2
    if (empty($errors)) {
        $_SESSION['booking_data'] = [
            'kamar_id'    => $kamarId,
            'checkin'     => $checkin,
            'checkout'    => $checkout,
            'jumlah_tamu' => $jumlahTamu,
            'catatan'     => $catatan,
        ];
        header('Location: ' . BASE_URL . '/tamu/booking_paket.php');
        exit;
    }
}

// ── Query daftar kamar dari database ─────────────────────────────────────────
$stmt = db()->prepare('SELECT id, nama, tipe, kapasitas, harga_per_malam, foto FROM kamar WHERE status_default = ?');
$stmt->execute(['tersedia']);
$kamarList = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_tamu.php';
?>

    <!-- Flash / Error Messages -->
    <?php if (!empty($errors)): ?>
    <div class="pt-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 pt-4">
            <div class="p-4 bg-danger-light rounded-xl text-sm text-danger">
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Progress Steps -->
    <div class="<?= empty($errors) ? 'pt-20' : '' ?> bg-white border-b border-cream-dark">
        <div class="max-w-3xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-semibold text-sm">1</div>
                    <span class="font-medium text-dark text-sm hidden sm:block">Pilih Kamar</span>
                </div>
                <div class="flex-1 h-0.5 mx-4 bg-cream-darker"></div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-cream-dark text-earth flex items-center justify-center font-semibold text-sm">2</div>
                    <span class="font-medium text-earth text-sm hidden sm:block">Paket Wisata</span>
                </div>
                <div class="flex-1 h-0.5 mx-4 bg-cream-darker"></div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-cream-dark text-earth flex items-center justify-center font-semibold text-sm">3</div>
                    <span class="font-medium text-earth text-sm hidden sm:block">Ringkasan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Form -->
    <section class="py-12 bg-cream">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Pilih Kamar & Tanggal</h1>
            <p class="text-earth mb-8">Langkah 1 dari 3 — Pilih kamar yang Anda inginkan dan tentukan tanggal menginap.</p>

            <form method="POST" action="<?= BASE_URL ?>/tamu/booking.php" class="space-y-6">
                <?= csrf_field() ?>
                <!-- Pilih Kamar -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-semibold text-dark mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Kamar yang Dipilih
                    </h2>

                    <?php if (empty($kamarList)): ?>
                    <p class="text-earth text-sm">Tidak ada kamar tersedia saat ini.</p>
                    <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($kamarList as $k): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="kamar_id" value="<?= $k['id'] ?>" class="peer hidden" <?= $k['id'] === $selectedKamarId ? 'checked' : '' ?> required>
                            <div class="relative border-2 border-cream-darker rounded-xl overflow-hidden transition-all peer-checked:border-success peer-checked:border-[3px] peer-checked:shadow-lg peer-checked:bg-success/5 hover:border-earth/40">
                                <!-- Checkmark badge -->
                                <div class="absolute top-2 right-2 z-10 w-6 h-6 rounded-full bg-success text-white items-center justify-center text-xs shadow-sm kamar-check hidden">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <img src="<?= e($k['foto'] ?: 'https://placehold.co/400x300/2D5016/FDF6E3?text=' . urlencode($k['nama'])) ?>" alt="<?= e($k['nama']) ?>" class="w-full h-32 object-cover">
                                <div class="p-3">
                                    <p class="font-medium text-dark text-sm"><?= e($k['nama']) ?></p>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-xs text-earth"><?= e($k['tipe']) ?> · <?= $k['kapasitas'] ?> tamu</span>
                                        <span class="text-sm font-bold text-primary"><?= format_rupiah($k['harga_per_malam']) ?></span>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Tanggal & Detail -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="font-semibold text-dark mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Detail Menginap
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1">Check-in</label>
                            <input type="date" name="checkin" value="<?= e($checkin) ?>" required min="<?= date('Y-m-d') ?>"
                                   class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1">Check-out</label>
                            <input type="date" name="checkout" value="<?= e($checkout) ?>" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                   class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1">Jumlah Tamu</label>
                            <select name="jumlah_tamu" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?> tamu</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-dark mb-1">Catatan Khusus (opsional)</label>
                        <textarea name="catatan" rows="3" placeholder="Contoh: minta extra bed, lantai atas, dll."
                                  class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors resize-none"><?= e($_POST['catatan'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-between items-center">
                    <a href="<?= BASE_URL ?>/tamu/kamar.php" class="text-earth hover:text-dark flex items-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali
                    </a>
                    <button type="submit" class="px-8 py-3.5 bg-primary hover:bg-primary-light text-white font-semibold rounded-xl transition-all hover:shadow-lg flex items-center gap-2">
                        Lanjut: Paket Wisata
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </section>

<script>
document.querySelectorAll('input[name="kamar_id"]').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.kamar-check').forEach(el => { el.classList.add('hidden'); el.classList.remove('flex'); });
        const badge = radio.closest('label').querySelector('.kamar-check');
        if (badge) { badge.classList.remove('hidden'); badge.classList.add('flex'); }
    });
    // Init: show badge for pre-checked
    if (radio.checked) {
        const badge = radio.closest('label').querySelector('.kamar-check');
        if (badge) { badge.classList.remove('hidden'); badge.classList.add('flex'); }
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
