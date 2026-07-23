<?php
/** Daftar Refund — Resepsionis */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Proses Refund';

// Query refund yang menunggu diproses
$stmt = db()->prepare(
    'SELECT r.id, r.nominal_refund, r.status AS refund_status, r.tanggal_refund,
            b.id AS booking_id, b.total_harga,
            k.nama AS kamar_nama,
            COALESCE(u.nama, b.nama_tamu) AS tamu_nama
     FROM refund r
     JOIN booking b ON b.id = r.booking_id
     JOIN kamar k ON k.id = b.kamar_id
     LEFT JOIN users u ON u.id = b.user_id
     WHERE r.status = ?
     ORDER BY r.tanggal_refund ASC'
);
$stmt->execute(['menunggu']);
$daftarRefund = $stmt->fetchAll();

// Query refund yang sudah selesai (riwayat)
$stmtDone = db()->prepare(
    'SELECT r.id, r.nominal_refund, r.status AS refund_status, r.tanggal_refund,
            b.id AS booking_id, b.total_harga,
            k.nama AS kamar_nama,
            COALESCE(u.nama, b.nama_tamu) AS tamu_nama
     FROM refund r
     JOIN booking b ON b.id = r.booking_id
     JOIN kamar k ON k.id = b.kamar_id
     LEFT JOIN users u ON u.id = b.user_id
     WHERE r.status = ?
     ORDER BY r.tanggal_refund DESC
     LIMIT 10'
);
$stmtDone->execute(['selesai']);
$riwayatRefund = $stmtDone->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_resepsionis.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Message -->
            <?php $flash = get_flash(); if ($flash): ?>
            <div class="mb-6 p-4 bg-<?= $flash['type'] === 'success' ? 'success-light' : 'danger-light' ?> rounded-xl text-sm text-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
                <?= e($flash['message']) ?>
            </div>
            <?php endif; ?>

            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Proses Refund</h1>
            <p class="text-earth mb-8"><?= count($daftarRefund) ?> refund menunggu diproses.</p>

            <?php if (empty($daftarRefund)): ?>
            <div class="bg-white rounded-2xl p-12 shadow-sm text-center mb-8">
                <svg class="w-16 h-16 mx-auto text-earth/20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-earth">Tidak ada refund yang perlu diproses saat ini.</p>
            </div>
            <?php else: ?>
            <div class="space-y-4 mb-8">
                <?php foreach ($daftarRefund as $r): ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1"><span class="text-sm font-mono text-earth">#BK-<?= str_pad($r['booking_id'], 4, '0', STR_PAD_LEFT) ?></span><span class="px-2 py-0.5 bg-warning-light text-warning text-xs rounded-full">Menunggu Refund</span></div>
                        <h3 class="font-semibold text-dark text-lg"><?= e($r['tamu_nama']) ?></h3>
                        <p class="text-sm text-earth"><?= e($r['kamar_nama']) ?> · Total booking: <?= format_rupiah($r['total_harga']) ?></p>
                        <p class="text-lg font-bold text-primary mt-1">Refund: <?= format_rupiah($r['nominal_refund']) ?></p>
                    </div>
                    <a href="<?= BASE_URL ?>/resepsionis/refund_proses.php?id=<?= $r['id'] ?>" class="px-6 py-2.5 bg-accent hover:bg-accent-light text-dark font-semibold rounded-full transition-all hover:shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        Proses Refund
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Riwayat Refund -->
            <?php if (!empty($riwayatRefund)): ?>
            <h2 class="font-semibold text-dark text-xl mb-4">Riwayat Refund Selesai</h2>
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead><tr class="bg-white">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase tracking-wider">Booking</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase tracking-wider">Tamu</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-earth uppercase tracking-wider">Nominal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-earth uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-earth uppercase tracking-wider">Status</th>
                        </tr></thead>
                        <tbody class="divide-y divide-cream">
                            <?php foreach ($riwayatRefund as $rr): ?>
                            <tr class="hover:bg-cream/30 transition-colors">
                                <td class="px-6 py-3 text-sm font-mono text-dark">#BK-<?= str_pad($rr['booking_id'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td class="px-6 py-3 text-sm text-dark"><?= e($rr['tamu_nama']) ?></td>
                                <td class="px-6 py-3 text-sm text-right font-bold text-primary"><?= format_rupiah($rr['nominal_refund']) ?></td>
                                <td class="px-6 py-3 text-sm text-earth"><?= date('d M Y', strtotime($rr['tanggal_refund'])) ?></td>
                                <td class="px-6 py-3 text-center"><span class="px-3 py-1 bg-success-light text-success text-xs font-medium rounded-full">Selesai</span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
