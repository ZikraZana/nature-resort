<?php
/** Status Kamar Real-time — Resepsionis */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('resepsionis');
$pageTitle = 'Status Kamar';
$kamarList = [
    ['nama' => 'Kabin Pinus A1', 'tipe' => 'Kabin', 'status' => 'tersedia', 'tamu' => null],
    ['nama' => 'Kabin Pinus A2', 'tipe' => 'Kabin', 'status' => 'terisi', 'tamu' => 'Andi Wisata'],
    ['nama' => 'Kamar Deluxe B1', 'tipe' => 'Deluxe', 'status' => 'tersedia', 'tamu' => null],
    ['nama' => 'Kamar Deluxe B2', 'tipe' => 'Deluxe', 'status' => 'maintenance', 'tamu' => null],
    ['nama' => 'Suite Kerinci C1', 'tipe' => 'Suite', 'status' => 'terisi', 'tamu' => 'Budi Tamu'],
    ['nama' => 'Standard Room D1', 'tipe' => 'Standard', 'status' => 'tersedia', 'tamu' => null],
];
$statusConfig = ['tersedia' => ['color' => 'border-success bg-success/5', 'badge' => 'bg-success text-white', 'label' => 'Tersedia', 'icon' => '🟢'], 'terisi' => ['color' => 'border-danger bg-danger/5', 'badge' => 'bg-danger text-white', 'label' => 'Terisi', 'icon' => '🔴'], 'maintenance' => ['color' => 'border-warning bg-warning/5', 'badge' => 'bg-warning text-white', 'label' => 'Maintenance', 'icon' => '🟡']];
$counts = array_count_values(array_column($kamarList, 'status'));
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_resepsionis.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-sans text-3xl text-dark font-bold mb-2">Status Kamar</h1>
            <p class="text-earth mb-6">Monitor ketersediaan kamar secara real-time.</p>
            <!-- Legend -->
            <div class="flex flex-wrap gap-4 mb-8">
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-full shadow-sm text-sm"><span class="w-3 h-3 rounded-full bg-success"></span> Tersedia (<?= $counts['tersedia'] ?? 0 ?>)</div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-full shadow-sm text-sm"><span class="w-3 h-3 rounded-full bg-danger"></span> Terisi (<?= $counts['terisi'] ?? 0 ?>)</div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-full shadow-sm text-sm"><span class="w-3 h-3 rounded-full bg-warning"></span> Maintenance (<?= $counts['maintenance'] ?? 0 ?>)</div>
            </div>
            <!-- Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($kamarList as $k): $sc = $statusConfig[$k['status']]; ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 <?= $sc['color'] ?>">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-dark"><?= e($k['nama']) ?></h3>
                        <span class="px-3 py-1 <?= $sc['badge'] ?> text-xs font-medium rounded-full"><?= $sc['label'] ?></span>
                    </div>
                    <p class="text-sm text-earth mb-2">Tipe: <?= e($k['tipe']) ?></p>
                    <?php if ($k['tamu']): ?>
                    <div class="mt-3 p-3 bg-cream rounded-lg"><p class="text-xs text-earth">Ditempati oleh:</p><p class="text-sm font-medium text-dark"><?= e($k['tamu']) ?></p></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
