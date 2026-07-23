<?php
/**
 * Ringkasan Booking (Step 3) — Kincay Mania Hotel & Resort
 * Hitung total harga dan konfirmasi booking (INSERT ke database).
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');
$pageTitle = 'Ringkasan Booking';

// Pastikan step 1 sudah diisi
if (empty($_SESSION['booking_data'])) {
    set_flash('warning', 'Silakan pilih kamar dan tanggal terlebih dahulu.');
    header('Location: ' . BASE_URL . '/tamu/booking.php');
    exit;
}

$bookingData = $_SESSION['booking_data'];
$bookingPaket = $_SESSION['booking_paket'] ?? [];

// Query kamar terpilih
$stmt = db()->prepare('SELECT id, nama, tipe, kapasitas, harga_per_malam, foto FROM kamar WHERE id = ?');
$stmt->execute([$bookingData['kamar_id']]);
$kamar = $stmt->fetch();

if (!$kamar) {
    set_flash('danger', 'Kamar tidak ditemukan. Silakan ulangi proses booking.');
    unset($_SESSION['booking_data'], $_SESSION['booking_paket']);
    header('Location: ' . BASE_URL . '/tamu/booking.php');
    exit;
}

// Hitung total
$jumlahMalam = (int)((strtotime($bookingData['checkout']) - strtotime($bookingData['checkin'])) / 86400);
$hargaKamarTotal = $kamar['harga_per_malam'] * $jumlahMalam;
$totalPaket = 0;
foreach ($bookingPaket as $pw) {
    $totalPaket += $pw['subtotal'];
}
$grandTotal = $hargaKamarTotal + $totalPaket;

// Query info bank dari pengaturan_sistem
$stmtBank = db()->query('SELECT nama_bank, no_rekening, nama_pemilik_rekening FROM pengaturan_sistem LIMIT 1');
$bank = $stmtBank->fetch();
if (!$bank) {
    $bank = ['nama_bank' => '-', 'no_rekening' => '-', 'nama_pemilik_rekening' => '-'];
}

// ── Proses POST — konfirmasi booking ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi_booking'])) {
    validate_csrf();

    $pdo = db();
    try {
        $pdo->beginTransaction();

        // Re-cek ketersediaan kamar (race condition guard)
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM booking
             WHERE kamar_id = ?
               AND status NOT IN (?, ?)
               AND tanggal_checkin < ?
               AND tanggal_checkout > ?'
        );
        $stmt->execute([$bookingData['kamar_id'], 'dibatalkan', 'ditolak', $bookingData['checkout'], $bookingData['checkin']]);
        if ($stmt->fetchColumn() > 0) {
            $pdo->rollBack();
            set_flash('danger', 'Maaf, kamar ini sudah dipesan oleh orang lain untuk tanggal yang Anda pilih. Silakan pilih ulang.');
            unset($_SESSION['booking_data'], $_SESSION['booking_paket']);
            header('Location: ' . BASE_URL . '/tamu/booking.php');
            exit;
        }

        // INSERT booking
        $stmt = $pdo->prepare(
            'INSERT INTO booking (user_id, kamar_id, tanggal_checkin, tanggal_checkout, jumlah_tamu, catatan, total_harga, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $_SESSION['user_id'],
            $bookingData['kamar_id'],
            $bookingData['checkin'],
            $bookingData['checkout'],
            $bookingData['jumlah_tamu'],
            $bookingData['catatan'] ?: null,
            $grandTotal,
            'menunggu_pembayaran'
        ]);
        $bookingId = (int)$pdo->lastInsertId();

        // INSERT booking_paket_wisata
        if (!empty($bookingPaket)) {
            $stmtPaket = $pdo->prepare(
                'INSERT INTO booking_paket_wisata (booking_id, jadwal_wisata_id, jumlah_peserta, subtotal)
                 VALUES (?, ?, ?, ?)'
            );
            foreach ($bookingPaket as $pw) {
                // Re-cek kuota (race condition guard)
                $stmtKuota = $pdo->prepare(
                    'SELECT jw.kuota_maksimal,
                            COALESCE(SUM(CASE WHEN b.status NOT IN (?, ?) THEN bpw.jumlah_peserta ELSE 0 END), 0) AS terisi
                     FROM jadwal_wisata jw
                     LEFT JOIN booking_paket_wisata bpw ON bpw.jadwal_wisata_id = jw.id
                     LEFT JOIN booking b ON b.id = bpw.booking_id
                     WHERE jw.id = ?
                     GROUP BY jw.kuota_maksimal'
                );
                $stmtKuota->execute(['dibatalkan', 'ditolak', $pw['jadwal_wisata_id']]);
                $kuotaRow = $stmtKuota->fetch();
                if ($kuotaRow && ($kuotaRow['kuota_maksimal'] - $kuotaRow['terisi']) < $pw['jumlah_peserta']) {
                    throw new \RuntimeException('Kuota paket wisata "' . $pw['nama'] . '" sudah penuh.');
                }

                $stmtPaket->execute([
                    $bookingId,
                    $pw['jadwal_wisata_id'],
                    $pw['jumlah_peserta'],
                    $pw['subtotal']
                ]);
            }
        }

        $pdo->commit();

        // Bersihkan session booking
        unset($_SESSION['booking_data'], $_SESSION['booking_paket']);

        set_flash('success', 'Booking berhasil dibuat! Silakan upload bukti transfer untuk melanjutkan.');
        header('Location: ' . BASE_URL . '/tamu/upload_bukti.php?booking_id=' . $bookingId);
        exit;

    } catch (\Exception $e) {
        $pdo->rollBack();
        error_log('[Booking Error] ' . $e->getMessage());
        set_flash('danger', 'Terjadi kesalahan saat memproses booking: ' . e($e->getMessage()));
        header('Location: ' . BASE_URL . '/tamu/booking_ringkasan.php');
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_tamu.php';
?>

    <!-- Flash Message -->
    <?php $flash = get_flash(); if ($flash): ?>
    <div class="pt-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 pt-4">
            <div class="p-4 bg-<?= $flash['type'] === 'danger' ? 'danger-light' : 'success-light' ?> rounded-xl text-sm text-<?= $flash['type'] === 'danger' ? 'danger' : 'success' ?>">
                <?= e($flash['message']) ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Progress Steps -->
    <div class="<?= !$flash ? 'pt-20' : '' ?> bg-white border-b border-cream-dark">
        <div class="max-w-3xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-success text-white flex items-center justify-center font-semibold text-sm">✓</div>
                    <span class="font-medium text-success text-sm hidden sm:block">Pilih Kamar</span>
                </div>
                <div class="flex-1 h-0.5 mx-4 bg-success"></div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-success text-white flex items-center justify-center font-semibold text-sm">✓</div>
                    <span class="font-medium text-success text-sm hidden sm:block">Paket Wisata</span>
                </div>
                <div class="flex-1 h-0.5 mx-4 bg-primary"></div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-semibold text-sm">3</div>
                    <span class="font-medium text-dark text-sm hidden sm:block">Ringkasan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <section class="py-12 bg-cream">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Ringkasan Booking</h1>
            <p class="text-earth mb-8">Langkah 3 dari 3 — Periksa kembali detail booking Anda sebelum konfirmasi.</p>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Detail -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Kamar -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h2 class="font-semibold text-dark mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Detail Kamar
                        </h2>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Kamar</span><span class="font-medium text-dark"><?= e($kamar['nama']) ?> (<?= e($kamar['tipe']) ?>)</span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Check-in</span><span class="font-medium text-dark"><?= date('d M Y', strtotime($bookingData['checkin'])) ?></span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Check-out</span><span class="font-medium text-dark"><?= date('d M Y', strtotime($bookingData['checkout'])) ?></span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Durasi</span><span class="font-medium text-dark"><?= $jumlahMalam ?> malam</span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Jumlah Tamu</span><span class="font-medium text-dark"><?= $bookingData['jumlah_tamu'] ?> orang</span></div>
                            <div class="flex justify-between py-2 border-b border-cream"><span class="text-earth">Catatan</span><span class="font-medium text-dark"><?= e($bookingData['catatan'] ?: '-') ?></span></div>
                            <div class="flex justify-between py-2"><span class="text-earth">Harga Kamar</span><span class="font-bold text-dark"><?= format_rupiah($kamar['harga_per_malam']) ?> × <?= $jumlahMalam ?> malam = <?= format_rupiah($hargaKamarTotal) ?></span></div>
                        </div>
                    </div>

                    <!-- Paket Wisata -->
                    <?php if (!empty($bookingPaket)): ?>
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h2 class="font-semibold text-dark mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Paket Wisata Add-on
                        </h2>
                        <div class="space-y-3">
                            <?php foreach ($bookingPaket as $pw): ?>
                            <div class="flex justify-between items-center py-3 border-b border-cream text-sm">
                                <div>
                                    <p class="font-medium text-dark"><?= e($pw['nama']) ?></p>
                                    <p class="text-earth text-xs"><?= date('d M Y', strtotime($pw['tanggal'])) ?> · <?= $pw['jumlah_peserta'] ?> peserta</p>
                                </div>
                                <span class="font-medium text-dark"><?= format_rupiah($pw['harga']) ?> × <?= $pw['jumlah_peserta'] ?> = <?= format_rupiah($pw['subtotal']) ?></span>
                            </div>
                            <?php endforeach; ?>
                            <div class="flex justify-between py-2">
                                <span class="text-earth text-sm">Subtotal Paket Wisata</span>
                                <span class="font-bold text-dark"><?= format_rupiah($totalPaket) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar: Total & Confirm -->
                <div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-28">
                        <h3 class="font-semibold text-dark mb-4">Rincian Pembayaran</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between"><span class="text-earth">Kamar (<?= $jumlahMalam ?> malam)</span><span class="text-dark"><?= format_rupiah($hargaKamarTotal) ?></span></div>
                            <?php if ($totalPaket > 0): ?>
                            <div class="flex justify-between"><span class="text-earth">Paket Wisata</span><span class="text-dark"><?= format_rupiah($totalPaket) ?></span></div>
                            <?php endif; ?>
                            <div class="border-t border-cream-dark pt-3 mt-3">
                                <div class="flex justify-between">
                                    <span class="font-semibold text-dark text-lg">Total</span>
                                    <span class="font-bold text-primary text-2xl"><?= format_rupiah($grandTotal) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Info -->
                        <div class="mt-6 p-4 bg-cream rounded-xl border border-cream-darker">
                            <p class="text-xs font-medium text-earth uppercase tracking-wider mb-2">Transfer ke:</p>
                            <p class="font-bold text-dark"><?= e($bank['nama_bank']) ?></p>
                            <p class="text-lg font-mono font-bold text-primary mt-1"><?= e($bank['no_rekening']) ?></p>
                            <p class="text-sm text-earth">a.n. <?= e($bank['nama_pemilik_rekening']) ?></p>
                        </div>

                        <!-- Confirm Button -->
                        <form method="POST" action="<?= BASE_URL ?>/tamu/booking_ringkasan.php" class="mt-6">
                            <?= csrf_field() ?>
                            <button type="submit" name="konfirmasi_booking"
                                    class="w-full py-3.5 bg-accent hover:bg-accent-light text-dark font-semibold rounded-xl transition-all hover:shadow-lg hover:shadow-accent/25 flex items-center justify-center gap-2 text-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Konfirmasi Booking
                            </button>
                            <p class="text-xs text-earth text-center mt-3">Setelah konfirmasi, lakukan transfer dan upload bukti pembayaran.</p>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Back -->
            <div class="mt-8">
                <a href="<?= BASE_URL ?>/tamu/booking_paket.php" class="text-earth hover:text-dark flex items-center gap-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Paket Wisata
                </a>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
