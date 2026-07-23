<?php
/** Jadwal Wisata Harian — Resepsionis */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Jadwal Wisata Hari Ini';

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

// Query jadwal hari ini + peserta booking aktif
$stmt = db()->prepare(
    'SELECT jw.id, jw.tanggal, jw.kuota_maksimal,
            pw.nama AS paket_nama, pw.kategori
     FROM jadwal_wisata jw
     JOIN paket_wisata pw ON pw.id = jw.paket_wisata_id
     WHERE jw.tanggal = ?
     ORDER BY pw.kategori, pw.nama'
);
$stmt->execute([$tanggal]);
$jadwalRows = $stmt->fetchAll();

$jadwalHariIni = [];
foreach ($jadwalRows as $j) {
    // Query peserta
    $stmtPeserta = db()->prepare(
        'SELECT bpw.jumlah_peserta,
                COALESCE(u.nama, b.nama_tamu) AS tamu_nama,
                COALESCE(u.no_hp, b.kontak_tamu) AS tamu_kontak
         FROM booking_paket_wisata bpw
         JOIN booking b ON b.id = bpw.booking_id
         LEFT JOIN users u ON u.id = b.user_id
         WHERE bpw.jadwal_wisata_id = ? AND b.status NOT IN (?, ?)
         ORDER BY b.created_at'
    );
    $stmtPeserta->execute([$j['id'], 'dibatalkan', 'ditolak']);
    $peserta = $stmtPeserta->fetchAll();
    $totalPeserta = array_sum(array_column($peserta, 'jumlah_peserta'));

    $j['peserta'] = $peserta;
    $j['total_peserta'] = $totalPeserta;
    $jadwalHariIni[] = $j;
}

$kategoriColors = ['trekking' => 'bg-success text-white', 'perahu' => 'bg-info text-white', 'kuliner' => 'bg-secondary text-white'];
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_resepsionis.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="font-sans text-3xl text-dark font-bold">Jadwal Wisata</h1>
                    <p class="text-earth mt-1"><?= date('l, d M Y', strtotime($tanggal)) ?> — Daftar peserta paket wisata.</p>
                </div>
                <form class="flex items-center gap-2">
                    <input type="date" name="tanggal" value="<?= e($tanggal) ?>" class="px-4 py-2 bg-white border border-cream-darker rounded-xl text-sm text-dark focus:border-primary transition-colors">
                    <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl hover:bg-primary-light transition-colors">Lihat</button>
                </form>
            </div>

            <?php if (empty($jadwalHariIni)): ?>
            <div class="bg-white rounded-2xl p-12 shadow-sm text-center"><p class="text-earth">Tidak ada jadwal wisata untuk tanggal ini.</p></div>
            <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($jadwalHariIni as $j): ?>
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-cream">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 <?= $kategoriColors[$j['kategori']] ?? 'bg-earth text-white' ?> text-xs font-medium rounded-full"><?= ucfirst(e($j['kategori'])) ?></span>
                                <h2 class="font-sans text-xl font-semibold text-dark"><?= e($j['paket_nama']) ?></h2>
                            </div>
                            <span class="text-sm text-earth"><?= $j['total_peserta'] ?>/<?= $j['kuota_maksimal'] ?> peserta</span>
                        </div>
                    </div>
                    <?php if (empty($j['peserta'])): ?>
                    <div class="p-6 text-center text-sm text-earth">Belum ada peserta terdaftar.</div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead><tr class="bg-white"><th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase">Nama Tamu</th><th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase">Kontak</th><th class="px-6 py-3 text-center text-xs font-semibold text-earth uppercase">Jumlah</th></tr></thead>
                            <tbody class="divide-y divide-cream">
                                <?php foreach ($j['peserta'] as $p): ?>
                                <tr><td class="px-6 py-3 text-sm font-medium text-dark"><?= e($p['tamu_nama']) ?></td><td class="px-6 py-3 text-sm text-earth"><?= e($p['tamu_kontak'] ?: '-') ?></td><td class="px-6 py-3 text-sm text-center"><?= $p['jumlah_peserta'] ?> orang</td></tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
