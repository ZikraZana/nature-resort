<?php
/** Verifikasi Pembayaran — Resepsionis */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Verifikasi Pembayaran';

// Query pembayaran yang menunggu verifikasi
$stmt = db()->prepare(
    'SELECT p.id, p.nominal, p.created_at AS tanggal_upload,
            b.id AS booking_id, b.total_harga,
            k.nama AS kamar_nama,
            COALESCE(u.nama, b.nama_tamu) AS tamu_nama
     FROM pembayaran p
     JOIN booking b ON b.id = p.booking_id
     JOIN kamar k ON k.id = b.kamar_id
     LEFT JOIN users u ON u.id = b.user_id
     WHERE p.status = ? AND b.status = ?
     ORDER BY p.created_at ASC'
);
$stmt->execute(['menunggu', 'menunggu_verifikasi']);
$daftarVerifikasi = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_resepsionis.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Message -->
            <?php $flash = get_flash(); if ($flash): ?>
            <div class="mb-6 p-4 bg-<?= $flash['type'] === 'success' ? 'success-light' : 'danger-light' ?> rounded-xl text-sm text-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
                <?= e($flash['message']) ?>
            </div>
            <?php endif; ?>

            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Verifikasi Pembayaran</h1>
            <p class="text-earth mb-8"><?= count($daftarVerifikasi) ?> booking menunggu verifikasi.</p>

            <?php if (empty($daftarVerifikasi)): ?>
            <div class="bg-white rounded-2xl p-12 shadow-sm text-center">
                <svg class="w-16 h-16 mx-auto text-earth/20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-earth">Tidak ada pembayaran yang perlu diverifikasi.</p>
            </div>
            <?php else: ?>
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead><tr class="bg-white border-b border-cream-dark">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Booking</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Tamu</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Kamar</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-earth uppercase tracking-wider">Nominal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Upload</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-earth uppercase tracking-wider">Aksi</th>
                        </tr></thead>
                        <tbody class="divide-y divide-cream">
                            <?php foreach ($daftarVerifikasi as $v): ?>
                            <tr class="hover:bg-cream/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-mono text-dark">#BK-<?= str_pad($v['booking_id'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td class="px-6 py-4 text-sm font-medium text-dark"><?= e($v['tamu_nama']) ?></td>
                                <td class="px-6 py-4 text-sm text-earth"><?= e($v['kamar_nama']) ?></td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-primary"><?= format_rupiah($v['nominal']) ?></td>
                                <td class="px-6 py-4 text-sm text-earth"><?= date('d M H:i', strtotime($v['tanggal_upload'])) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <a href="<?= BASE_URL ?>/resepsionis/verifikasi_detail.php?id=<?= $v['id'] ?>" class="px-4 py-2 bg-primary hover:bg-primary-light text-white text-xs font-medium rounded-full transition-colors">Review</a>
                                </td>
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
