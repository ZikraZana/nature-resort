<?php
/** Kelola User — Admin */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');
$pageTitle = 'Kelola User';

$userList = [
    ['id' => 1, 'nama' => 'Admin Utama', 'email' => 'admin@kincaymania.com', 'role' => 'admin', 'telepon' => '081200000001', 'terdaftar' => '2026-01-01', 'status' => 'aktif'],
    ['id' => 2, 'nama' => 'Siti Resepsionis', 'email' => 'siti@kincaymania.com', 'role' => 'resepsionis', 'telepon' => '081200000002', 'terdaftar' => '2026-02-15', 'status' => 'aktif'],
    ['id' => 3, 'nama' => 'Budi Tamu', 'email' => 'budi@email.com', 'role' => 'tamu', 'telepon' => '081234567890', 'terdaftar' => '2026-03-10', 'status' => 'aktif'],
    ['id' => 4, 'nama' => 'Dewi Anggraini', 'email' => 'dewi@email.com', 'role' => 'tamu', 'telepon' => '082345678901', 'terdaftar' => '2026-04-22', 'status' => 'aktif'],
    ['id' => 5, 'nama' => 'Andi Wisata', 'email' => 'andi@email.com', 'role' => 'tamu', 'telepon' => '083456789012', 'terdaftar' => '2026-05-08', 'status' => 'aktif'],
    ['id' => 6, 'nama' => 'Sari Lestari', 'email' => 'sari@email.com', 'role' => 'tamu', 'telepon' => '084567890123', 'terdaftar' => '2026-05-20', 'status' => 'nonaktif'],
    ['id' => 7, 'nama' => 'Rudi Hartono', 'email' => 'rudi@email.com', 'role' => 'tamu', 'telepon' => '085678901234', 'terdaftar' => '2026-06-14', 'status' => 'aktif'],
    ['id' => 8, 'nama' => 'Rina Resepsionis', 'email' => 'rina@kincaymania.com', 'role' => 'resepsionis', 'telepon' => '081200000003', 'terdaftar' => '2026-06-01', 'status' => 'aktif'],
];

$roleColors = [
    'admin'       => 'bg-danger/10 text-danger',
    'resepsionis' => 'bg-info/10 text-info',
    'tamu'        => 'bg-primary/10 text-primary',
];

$roleCounts = array_count_values(array_column($userList, 'role'));

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div>
                    <h1 class="font-sans text-3xl text-dark font-bold">Kelola User</h1>
                    <p class="text-earth mt-1"><?= count($userList) ?> pengguna terdaftar</p>
                </div>
                <button onclick="document.getElementById('modal-user').classList.remove('hidden')" class="px-5 py-2.5 bg-primary hover:bg-primary-light text-white font-semibold rounded-full transition-all hover:shadow-lg text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Tambah User
                </button>
            </div>

            <!-- Role Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-danger/10 flex items-center justify-center"><svg class="w-6 h-6 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
                    <div><p class="text-2xl font-bold text-dark"><?= $roleCounts['admin'] ?? 0 ?></p><p class="text-xs text-earth">Admin</p></div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-info/10 flex items-center justify-center"><svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                    <div><p class="text-2xl font-bold text-dark"><?= $roleCounts['resepsionis'] ?? 0 ?></p><p class="text-xs text-earth">Resepsionis</p></div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center"><svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                    <div><p class="text-2xl font-bold text-dark"><?= $roleCounts['tamu'] ?? 0 ?></p><p class="text-xs text-earth">Tamu</p></div>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white rounded-2xl p-4 shadow-sm mb-6 flex flex-wrap items-center gap-3">
                <span class="text-sm font-medium text-dark">Filter:</span>
                <button class="px-4 py-1.5 bg-primary text-white text-xs font-medium rounded-full">Semua</button>
                <button class="px-4 py-1.5 bg-cream text-earth text-xs font-medium rounded-full hover:bg-primary/10 hover:text-primary transition-colors">Admin</button>
                <button class="px-4 py-1.5 bg-cream text-earth text-xs font-medium rounded-full hover:bg-primary/10 hover:text-primary transition-colors">Resepsionis</button>
                <button class="px-4 py-1.5 bg-cream text-earth text-xs font-medium rounded-full hover:bg-primary/10 hover:text-primary transition-colors">Tamu</button>
                <div class="ml-auto"><input type="text" placeholder="Cari nama / email..." class="px-4 py-2 bg-cream border border-cream-darker rounded-xl text-sm text-dark placeholder-earth/40 focus:border-primary transition-colors w-56"></div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead><tr class="bg-cream border-b border-cream-dark">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Telepon</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-earth uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-earth uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Terdaftar</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-earth uppercase tracking-wider">Aksi</th>
                        </tr></thead>
                        <tbody class="divide-y divide-cream">
                            <?php foreach ($userList as $u):
                                $initial = strtoupper(substr($u['nama'], 0, 1));
                                $avatarColors = ['admin' => 'bg-danger/20 text-danger', 'resepsionis' => 'bg-info/20 text-info', 'tamu' => 'bg-primary/20 text-primary'];
                            ?>
                            <tr class="hover:bg-cream/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full <?= $avatarColors[$u['role']] ?? 'bg-earth/20 text-earth' ?> flex items-center justify-center flex-shrink-0"><span class="text-sm font-bold"><?= $initial ?></span></div>
                                        <span class="text-sm font-medium text-dark"><?= e($u['nama']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-earth"><?= e($u['email']) ?></td>
                                <td class="px-6 py-4 text-sm text-earth"><?= e($u['telepon']) ?></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 <?= $roleColors[$u['role']] ?? 'bg-earth/10 text-earth' ?> text-xs font-medium rounded-full"><?= ucfirst(e($u['role'])) ?></span></td>
                                <td class="px-6 py-4 text-center"><span class="px-3 py-1 <?= $u['status'] === 'aktif' ? 'bg-success-light text-success' : 'bg-gray-100 text-gray-500' ?> text-xs font-medium rounded-full"><?= ucfirst($u['status']) ?></span></td>
                                <td class="px-6 py-4 text-sm text-earth"><?= date('d M Y', strtotime($u['terdaftar'])) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button class="p-2 text-info hover:bg-info/10 rounded-lg transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                        <?php if ($u['role'] !== 'admin'): ?>
                                        <button class="p-2 text-danger hover:bg-danger/10 rounded-lg transition-colors" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Tambah User -->
    <div id="modal-user" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-dark/60 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-sans text-xl font-bold text-dark">Tambah User Baru</h2>
                <button onclick="document.getElementById('modal-user').classList.add('hidden')" class="p-2 text-earth hover:text-dark rounded-lg transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form class="space-y-4">
                <?= csrf_field() ?>
                <div><label class="block text-sm font-medium text-dark mb-1">Nama Lengkap</label><input type="text" name="nama" required placeholder="Nama lengkap" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                <div><label class="block text-sm font-medium text-dark mb-1">Email</label><input type="email" name="email" required placeholder="email@contoh.com" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-dark mb-1">No. Telepon</label><input type="tel" name="telepon" required placeholder="08xxxxxxxxxx" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                    <div><label class="block text-sm font-medium text-dark mb-1">Role</label>
                        <select name="role" required class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                            <option value="tamu">Tamu</option>
                            <option value="resepsionis">Resepsionis</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div><label class="block text-sm font-medium text-dark mb-1">Password</label><input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                <button type="submit" class="w-full py-3 bg-primary hover:bg-primary-light text-white font-semibold rounded-xl transition-all hover:shadow-lg flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan User
                </button>
            </form>
        </div>
    </div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
