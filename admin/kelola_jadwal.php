<?php
/** Kelola Jadwal Wisata — Admin */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');
$pageTitle = 'Kelola Jadwal Wisata';

// ── Proses POST: Tambah Jadwal ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_jadwal'])) {
    validate_csrf();
    $paketId = (int)($_POST['paket_id'] ?? 0);
    $tanggal = trim($_POST['tanggal'] ?? '');
    $kuota   = (int)($_POST['kuota'] ?? 10);
    $guide   = trim($_POST['guide'] ?? '');

    $errJ = [];
    if ($paketId <= 0) $errJ[] = 'Pilih paket wisata.';
    if (empty($tanggal)) $errJ[] = 'Tanggal wajib diisi.';
    if ($kuota < 1) $errJ[] = 'Kuota minimal 1.';

    // Cek duplikat
    if (empty($errJ)) {
        $stmt = db()->prepare('SELECT COUNT(*) FROM jadwal_wisata WHERE paket_wisata_id = ? AND tanggal = ?');
        $stmt->execute([$paketId, $tanggal]);
        if ($stmt->fetchColumn() > 0) $errJ[] = 'Jadwal paket ini sudah ada di tanggal tersebut.';
    }

    if (empty($errJ)) {
        $stmt = db()->prepare('INSERT INTO jadwal_wisata (paket_wisata_id, tanggal, kuota_maksimal, guide) VALUES (?, ?, ?, ?)');
        $stmt->execute([$paketId, $tanggal, $kuota, $guide ?: null]);
        set_flash('success', 'Jadwal berhasil ditambahkan!');
    } else {
        set_flash('danger', implode(' ', $errJ));
    }
    header('Location: ' . BASE_URL . '/admin/kelola_jadwal.php' . (!empty($_POST['tanggal']) ? '?dari=' . $tanggal . '&sampai=' . $tanggal : ''));
    exit;
}

// ── Proses POST: Hapus Jadwal ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_jadwal'])) {
    validate_csrf();
    $jadwalId = (int)($_POST['jadwal_id'] ?? 0);

    // Cek peserta aktif
    $stmt = db()->prepare("SELECT COUNT(*) FROM booking_paket_wisata bpw JOIN booking b ON b.id = bpw.booking_id WHERE bpw.jadwal_wisata_id = ? AND b.status NOT IN ('dibatalkan','ditolak')");
    $stmt->execute([$jadwalId]);
    if ($stmt->fetchColumn() > 0) {
        set_flash('danger', 'Tidak bisa hapus jadwal yang sudah ada peserta terdaftar.');
    } else {
        $stmt = db()->prepare('DELETE FROM jadwal_wisata WHERE id = ?');
        $stmt->execute([$jadwalId]);
        set_flash('success', 'Jadwal berhasil dihapus.');
    }
    header('Location: ' . BASE_URL . '/admin/kelola_jadwal.php');
    exit;
}

// Filter tanggal
$dari   = $_GET['dari'] ?? date('Y-m-d');
$sampai = $_GET['sampai'] ?? date('Y-m-d', strtotime('+7 days'));

// Query jadwal
$stmt = db()->prepare(
    'SELECT jw.id, jw.tanggal, jw.kuota_maksimal, jw.guide,
            pw.nama AS paket_nama, pw.kategori
     FROM jadwal_wisata jw
     JOIN paket_wisata pw ON pw.id = jw.paket_wisata_id
     WHERE jw.tanggal BETWEEN ? AND ?
     ORDER BY jw.tanggal, pw.kategori, pw.nama'
);
$stmt->execute([$dari, $sampai]);
$jadwalRows = $stmt->fetchAll();

// Hitung peserta per jadwal
foreach ($jadwalRows as &$j) {
    $stmtPeserta = db()->prepare(
        "SELECT COALESCE(SUM(bpw.jumlah_peserta), 0) FROM booking_paket_wisata bpw JOIN booking b ON b.id = bpw.booking_id WHERE bpw.jadwal_wisata_id = ? AND b.status NOT IN ('dibatalkan','ditolak')"
    );
    $stmtPeserta->execute([$j['id']]);
    $j['terisi'] = (int)$stmtPeserta->fetchColumn();
}
unset($j);

// Group by date
$jadwalByDate = [];
foreach ($jadwalRows as $j) { $jadwalByDate[$j['tanggal']][] = $j; }

// Paket list for modal
$stmtPaket = db()->prepare("SELECT id, nama FROM paket_wisata WHERE status = 'aktif' ORDER BY nama");
$stmtPaket->execute();
$paketAktif = $stmtPaket->fetchAll();

$kategoriColors = ['trekking' => 'bg-success/10 text-success', 'perahu' => 'bg-info/10 text-info', 'kuliner' => 'bg-secondary/10 text-secondary'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php $flash = get_flash(); if ($flash): ?>
            <div class="mb-6 p-4 bg-<?= $flash['type'] === 'success' ? 'success-light' : 'danger-light' ?> rounded-xl text-sm text-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>"><?= e($flash['message']) ?></div>
            <?php endif; ?>

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div><h1 class="font-sans text-3xl text-dark font-bold">Kelola Jadwal Wisata</h1><p class="text-earth mt-1">Atur jadwal harian paket wisata</p></div>
                <button onclick="document.getElementById('modal-jadwal').classList.remove('hidden')" class="px-5 py-2.5 bg-primary hover:bg-primary-light text-white font-semibold rounded-full transition-all hover:shadow-lg text-sm flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Tambah Jadwal</button>
            </div>

            <!-- Filter tanggal -->
            <form class="bg-white rounded-2xl p-4 shadow-sm mb-8 flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2"><svg class="w-5 h-5 text-earth" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg><span class="text-sm font-medium text-dark">Filter Tanggal:</span></div>
                <input type="date" name="dari" value="<?= e($dari) ?>" class="px-4 py-2 bg-cream border border-cream-darker rounded-xl text-sm text-dark focus:border-primary transition-colors">
                <span class="text-earth text-sm">s/d</span>
                <input type="date" name="sampai" value="<?= e($sampai) ?>" class="px-4 py-2 bg-cream border border-cream-darker rounded-xl text-sm text-dark focus:border-primary transition-colors">
                <button type="submit" class="px-4 py-2 bg-primary/10 text-primary text-sm font-medium rounded-xl hover:bg-primary/20 transition-colors">Terapkan</button>
            </form>

            <!-- Jadwal grouped by date -->
            <?php if (empty($jadwalByDate)): ?>
            <div class="bg-white rounded-2xl p-12 shadow-sm text-center"><p class="text-earth">Tidak ada jadwal di rentang tanggal ini.</p></div>
            <?php endif; ?>

            <?php foreach ($jadwalByDate as $tanggal => $jadwals): ?>
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center"><svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                    <div><h2 class="font-sans text-xl font-semibold text-dark"><?= date('l, d M Y', strtotime($tanggal)) ?></h2><p class="text-xs text-earth"><?= count($jadwals) ?> jadwal</p></div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead><tr class="bg-cream/50">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase tracking-wider">Paket</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase tracking-wider">Guide</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-earth uppercase tracking-wider">Peserta</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-earth uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-earth uppercase tracking-wider">Aksi</th>
                            </tr></thead>
                            <tbody class="divide-y divide-cream">
                                <?php foreach ($jadwals as $j):
                                    $persen = $j['kuota_maksimal'] > 0 ? round(($j['terisi'] / $j['kuota_maksimal']) * 100) : 0;
                                ?>
                                <tr class="hover:bg-cream/30 transition-colors">
                                    <td class="px-6 py-4"><div class="flex items-center gap-2"><span class="px-2 py-0.5 <?= $kategoriColors[$j['kategori']] ?? 'bg-earth/10 text-earth' ?> text-xs font-medium rounded-full"><?= ucfirst(e($j['kategori'])) ?></span><span class="text-sm font-medium text-dark"><?= e($j['paket_nama']) ?></span></div></td>
                                    <td class="px-6 py-4 text-sm text-earth"><?= e($j['guide'] ?: '-') ?></td>
                                    <td class="px-6 py-4"><div class="text-center"><span class="text-sm font-bold text-dark"><?= $j['terisi'] ?>/<?= $j['kuota_maksimal'] ?></span><div class="w-full bg-cream rounded-full h-1.5 mt-1"><div class="h-1.5 rounded-full transition-all <?= $persen >= 100 ? 'bg-danger' : ($persen >= 70 ? 'bg-warning' : 'bg-success') ?>" style="width: <?= min($persen, 100) ?>%"></div></div></div></td>
                                    <td class="px-6 py-4 text-center"><?php if ($persen >= 100): ?><span class="px-3 py-1 bg-danger-light text-danger text-xs font-medium rounded-full">Penuh</span><?php else: ?><span class="px-3 py-1 bg-success-light text-success text-xs font-medium rounded-full">Tersedia</span><?php endif; ?></td>
                                    <td class="px-6 py-4 text-center"><form method="POST" action="<?= BASE_URL ?>/admin/kelola_jadwal.php" class="inline"><?= csrf_field() ?><input type="hidden" name="jadwal_id" value="<?= $j['id'] ?>"><button type="submit" name="hapus_jadwal" class="p-2 text-danger hover:bg-danger/10 rounded-lg transition-colors" title="Hapus" onclick="return confirm('Hapus jadwal ini?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Modal Tambah Jadwal -->
    <div id="modal-jadwal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-dark/60 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <div class="flex items-center justify-between mb-6"><h2 class="font-sans text-xl font-bold text-dark">Tambah Jadwal Wisata</h2><button onclick="document.getElementById('modal-jadwal').classList.add('hidden')" class="p-2 text-earth hover:text-dark rounded-lg transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
            <form method="POST" action="<?= BASE_URL ?>/admin/kelola_jadwal.php" class="space-y-4">
                <?= csrf_field() ?>
                <div><label class="block text-sm font-medium text-dark mb-1">Paket Wisata</label>
                    <select name="paket_id" required class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                        <option value="">Pilih paket</option>
                        <?php foreach ($paketAktif as $pa): ?><option value="<?= $pa['id'] ?>"><?= e($pa['nama']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-dark mb-1">Tanggal</label><input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                    <div><label class="block text-sm font-medium text-dark mb-1">Kuota</label><input type="number" name="kuota" required value="10" min="1" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors"></div>
                </div>
                <div><label class="block text-sm font-medium text-dark mb-1">Guide / Pemandu</label><input type="text" name="guide" placeholder="Nama guide" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                <button type="submit" name="tambah_jadwal" class="w-full py-3 bg-primary hover:bg-primary-light text-white font-semibold rounded-xl transition-all hover:shadow-lg flex items-center justify-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Simpan Jadwal</button>
            </form>
        </div>
    </div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
