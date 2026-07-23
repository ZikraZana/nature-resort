<?php
/**
 * Booking Paket Wisata (Step 2) — Kincay Mania Hotel & Resort
 * Add-on paket wisata opsional ke booking kamar.
 * Data paket & jadwal dari database, sisa kuota dihitung real-time.
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');
$pageTitle = 'Tambah Paket Wisata';

// Pastikan step 1 sudah diisi
if (empty($_SESSION['booking_data'])) {
    set_flash('warning', 'Silakan pilih kamar dan tanggal terlebih dahulu.');
    header('Location: ' . BASE_URL . '/tamu/booking.php');
    exit;
}

$bookingData = $_SESSION['booking_data'];
$errors = [];

// ── Proses POST — validasi paket & simpan ke session ────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();

    $paketInput = $_POST['paket'] ?? [];
    $selectedPaket = [];

    foreach ($paketInput as $paketId => $jadwalData) {
        foreach ($jadwalData as $key => $value) {
            // Format: paket[<paket_id>][jadwal_<jadwal_id>] = <jadwal_id> (checkbox)
            //         paket[<paket_id>][peserta_<jadwal_id>] = <jumlah> (select)
            if (strpos($key, 'jadwal_') === 0 && !empty($value)) {
                $jadwalId = (int)$value;
                $pesertaKey = 'peserta_' . $jadwalId;
                $jumlahPeserta = (int)($jadwalData[$pesertaKey] ?? 1);

                if ($jumlahPeserta < 1) continue;

                // Validasi: jadwal ini benar-benar ada & punya kuota
                $stmt = db()->prepare(
                    'SELECT jw.id, jw.kuota_maksimal, jw.tanggal, pw.id AS paket_id, pw.nama, pw.harga
                     FROM jadwal_wisata jw
                     JOIN paket_wisata pw ON pw.id = jw.paket_wisata_id
                     WHERE jw.id = ? AND pw.id = ?'
                );
                $stmt->execute([$jadwalId, (int)$paketId]);
                $jadwal = $stmt->fetch();

                if (!$jadwal) {
                    $errors[] = 'Jadwal wisata tidak valid.';
                    continue;
                }

                // Hitung sisa kuota real
                $stmt = db()->prepare(
                    'SELECT COALESCE(SUM(bpw.jumlah_peserta), 0)
                     FROM booking_paket_wisata bpw
                     JOIN booking b ON b.id = bpw.booking_id
                     WHERE bpw.jadwal_wisata_id = ?
                       AND b.status NOT IN (?, ?)'
                );
                $stmt->execute([$jadwalId, 'dibatalkan', 'ditolak']);
                $terisi = (int)$stmt->fetchColumn();
                $sisa = $jadwal['kuota_maksimal'] - $terisi;

                if ($jumlahPeserta > $sisa) {
                    $errors[] = 'Kuota untuk "' . $jadwal['nama'] . '" tanggal ' . date('d M Y', strtotime($jadwal['tanggal'])) . ' hanya tersisa ' . $sisa . ' orang.';
                    continue;
                }

                $selectedPaket[] = [
                    'jadwal_wisata_id' => $jadwalId,
                    'paket_id'        => $jadwal['paket_id'],
                    'nama'            => $jadwal['nama'],
                    'tanggal'         => $jadwal['tanggal'],
                    'harga'           => $jadwal['harga'],
                    'jumlah_peserta'  => $jumlahPeserta,
                    'subtotal'        => $jadwal['harga'] * $jumlahPeserta,
                ];
            }
        }
    }

    if (empty($errors)) {
        $_SESSION['booking_paket'] = $selectedPaket;
        header('Location: ' . BASE_URL . '/tamu/booking_ringkasan.php');
        exit;
    }
}

// ── Query paket wisata + jadwal + sisa kuota ─────────────────────────────────
$stmtPaket = db()->prepare(
    'SELECT id, nama, kategori, deskripsi, harga, foto
     FROM paket_wisata
     WHERE status = ? OR status IS NULL
     ORDER BY kategori, nama'
);
$stmtPaket->execute(['aktif']);
$paketRows = $stmtPaket->fetchAll();

$paketList = [];
foreach ($paketRows as $p) {
    // Ambil jadwal yang belum lewat
    $stmtJadwal = db()->prepare(
        'SELECT jw.id, jw.tanggal, jw.kuota_maksimal,
                COALESCE(SUM(CASE WHEN b.status NOT IN (?, ?) THEN bpw.jumlah_peserta ELSE 0 END), 0) AS terisi
         FROM jadwal_wisata jw
         LEFT JOIN booking_paket_wisata bpw ON bpw.jadwal_wisata_id = jw.id
         LEFT JOIN booking b ON b.id = bpw.booking_id
         WHERE jw.paket_wisata_id = ? AND jw.tanggal >= ?
         GROUP BY jw.id, jw.tanggal, jw.kuota_maksimal
         ORDER BY jw.tanggal'
    );
    $stmtJadwal->execute(['dibatalkan', 'ditolak', $p['id'], date('Y-m-d')]);
    $jadwalRows = $stmtJadwal->fetchAll();

    $jadwals = [];
    foreach ($jadwalRows as $j) {
        $jadwals[] = [
            'id'      => $j['id'],
            'tanggal' => $j['tanggal'],
            'sisa'    => $j['kuota_maksimal'] - $j['terisi'],
        ];
    }

    if (!empty($jadwals)) {
        $p['jadwal'] = $jadwals;
        $paketList[] = $p;
    }
}

$kategoriColors = ['trekking' => 'bg-success text-white', 'perahu' => 'bg-info text-white', 'kuliner' => 'bg-secondary text-white'];
$kategoriLabels = ['trekking' => 'Trekking', 'perahu' => 'River / Perahu', 'kuliner' => 'Kuliner'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_tamu.php';
?>

    <!-- Error Messages -->
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
                    <div class="w-10 h-10 rounded-full bg-success text-white flex items-center justify-center font-semibold text-sm">✓</div>
                    <span class="font-medium text-success text-sm hidden sm:block">Pilih Kamar</span>
                </div>
                <div class="flex-1 h-0.5 mx-4 bg-primary"></div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-semibold text-sm">2</div>
                    <span class="font-medium text-dark text-sm hidden sm:block">Paket Wisata</span>
                </div>
                <div class="flex-1 h-0.5 mx-4 bg-cream-darker"></div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-cream-dark text-earth flex items-center justify-center font-semibold text-sm">3</div>
                    <span class="font-medium text-earth text-sm hidden sm:block">Ringkasan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Paket Wisata Selection -->
    <section class="py-12 bg-cream">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Tambah Paket Wisata</h1>
            <p class="text-earth mb-8">Langkah 2 dari 3 — Pilih paket wisata sebagai add-on (opsional). Anda bisa melewati langkah ini.</p>

            <form method="POST" action="<?= BASE_URL ?>/tamu/booking_paket.php">
                <?= csrf_field() ?>

                <?php if (empty($paketList)): ?>
                <div class="bg-white rounded-2xl p-8 shadow-sm text-center">
                    <p class="text-earth">Belum ada paket wisata dengan jadwal tersedia saat ini.</p>
                </div>
                <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($paketList as $paket): ?>
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm">
                        <div class="flex flex-col sm:flex-row">
                            <div class="w-full sm:w-48 h-40 sm:h-auto shrink-0">
                                <img src="<?= e($paket['foto'] ?: 'https://placehold.co/400x250/15803D/FFFFFF?text=' . urlencode($paket['nama'])) ?>" alt="<?= e($paket['nama']) ?>" class="w-full h-full object-cover">
                            </div>
                            <div class="p-6 flex-1">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <span class="px-2.5 py-1 <?= $kategoriColors[$paket['kategori']] ?? 'bg-earth text-white' ?> text-xs font-medium rounded-full"><?= e($kategoriLabels[$paket['kategori']] ?? ucfirst($paket['kategori'])) ?></span>
                                        <h3 class="font-sans text-lg font-semibold text-dark mt-2"><?= e($paket['nama']) ?></h3>
                                        <p class="text-sm text-earth mt-1"><?= e($paket['deskripsi']) ?></p>
                                    </div>
                                    <span class="text-xl font-bold text-primary whitespace-nowrap ml-4"><?= format_rupiah($paket['harga']) ?><span class="text-sm text-earth font-normal">/org</span></span>
                                </div>

                                <!-- Jadwal Selection -->
                                <div class="mt-4 p-4 bg-cream rounded-xl">
                                    <p class="text-xs font-medium text-earth uppercase tracking-wider mb-3">Pilih Jadwal & Jumlah Peserta</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <?php foreach ($paket['jadwal'] as $j): ?>
                                        <div class="flex items-center gap-3 p-3 bg-white rounded-lg <?= $j['sisa'] <= 0 ? 'opacity-50' : '' ?>">
                                            <input type="checkbox" name="paket[<?= $paket['id'] ?>][jadwal_<?= $j['id'] ?>]" value="<?= $j['id'] ?>"
                                                   <?= $j['sisa'] <= 0 ? 'disabled' : '' ?>
                                                   class="w-4 h-4 text-primary rounded border-cream-darker focus:ring-primary/20">
                                            <div class="flex-1">
                                                <span class="text-sm font-medium text-dark"><?= date('d M Y', strtotime($j['tanggal'])) ?></span>
                                                <span class="text-xs text-earth block">Sisa <?= max(0, $j['sisa']) ?> kuota</span>
                                            </div>
                                            <select name="paket[<?= $paket['id'] ?>][peserta_<?= $j['id'] ?>]" <?= $j['sisa'] <= 0 ? 'disabled' : '' ?>
                                                    class="w-20 px-2 py-1 text-sm bg-cream border border-cream-darker rounded-lg">
                                                <?php for ($i = 1; $i <= min(max(0, $j['sisa']), 10); $i++): ?>
                                                <option value="<?= $i ?>"><?= $i ?></option>
                                                <?php endfor; ?>
                                                <?php if ($j['sisa'] <= 0): ?>
                                                <option value="0">Penuh</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Navigation -->
                <div class="flex justify-between items-center mt-8">
                    <a href="<?= BASE_URL ?>/tamu/booking.php" class="text-earth hover:text-dark flex items-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali
                    </a>
                    <div class="flex gap-3">
                        <a href="<?= BASE_URL ?>/tamu/booking_ringkasan.php" class="px-6 py-3 border border-earth/30 text-earth hover:text-dark hover:border-earth/50 rounded-xl transition-colors">
                            Lewati
                        </a>
                        <button type="submit" class="px-8 py-3.5 bg-primary hover:bg-primary-light text-white font-semibold rounded-xl transition-all hover:shadow-lg flex items-center gap-2">
                            Lanjut: Ringkasan
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
