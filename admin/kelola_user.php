<?php
/** Kelola User — Admin */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');
$pageTitle = 'Kelola User';

// ── Proses POST: Toggle status ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    validate_csrf();
    $userId = (int)($_POST['user_id'] ?? 0);
    if ($userId === $_SESSION['user_id']) {
        set_flash('danger', 'Tidak bisa menonaktifkan akun sendiri.');
    } else {
        $stmt = db()->prepare('SELECT status FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $currentStatus = $stmt->fetchColumn();
        $newStatus = ($currentStatus === 'aktif') ? 'nonaktif' : 'aktif';
        $stmt = db()->prepare('UPDATE users SET status = ? WHERE id = ?');
        $stmt->execute([$newStatus, $userId]);
        set_flash('success', 'Status user berhasil diubah ke ' . $newStatus . '.');
    }
    header('Location: ' . BASE_URL . '/admin/kelola_user.php');
    exit;
}

// ── Proses POST: Update role ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    validate_csrf();
    $userId = (int)($_POST['user_id'] ?? 0);
    $newRole = $_POST['role'] ?? '';
    if ($userId === $_SESSION['user_id']) {
        set_flash('danger', 'Tidak bisa mengubah role sendiri.');
    } elseif (in_array($newRole, ['tamu', 'resepsionis', 'admin'])) {
        $stmt = db()->prepare('UPDATE users SET role = ? WHERE id = ?');
        $stmt->execute([$newRole, $userId]);
        set_flash('success', 'Role user berhasil diubah ke ' . $newRole . '.');
    }
    header('Location: ' . BASE_URL . '/admin/kelola_user.php');
    exit;
}

// ── Proses POST: Tambah user ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_user'])) {
    validate_csrf();
    $nama  = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role  = $_POST['role'] ?? 'tamu';
    $pass  = trim($_POST['password'] ?? '');

    $errU = [];
    if (empty($nama)) $errU[] = 'Nama wajib diisi.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errU[] = 'Email tidak valid.';
    if (strlen($pass) < 6) $errU[] = 'Password minimal 6 karakter.';
    // Check duplicate email
    $stmt = db()->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) $errU[] = 'Email sudah terdaftar.';

    if (empty($errU)) {
        $stmt = db()->prepare('INSERT INTO users (nama, email, password_hash, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$nama, $email, password_hash($pass, PASSWORD_DEFAULT), $role]);
        set_flash('success', 'User berhasil ditambahkan!');
    } else {
        set_flash('danger', implode(' ', $errU));
    }
    header('Location: ' . BASE_URL . '/admin/kelola_user.php');
    exit;
}

// Query users
$stmt = db()->query('SELECT id, nama, email, no_hp, role, status, created_at FROM users ORDER BY role, nama');
$userList = $stmt->fetchAll();

$roleColors = ['admin' => 'bg-danger/10 text-danger', 'resepsionis' => 'bg-info/10 text-info', 'tamu' => 'bg-primary/10 text-primary'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php $flash = get_flash(); if ($flash): ?>
            <div class="mb-6 p-4 bg-<?= $flash['type'] === 'success' ? 'success-light' : 'danger-light' ?> rounded-xl text-sm text-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>"><?= e($flash['message']) ?></div>
            <?php endif; ?>

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div><h1 class="font-sans text-3xl text-dark font-bold">Kelola User</h1><p class="text-earth mt-1"><?= count($userList) ?> user terdaftar</p></div>
                <button onclick="document.getElementById('modal-user').classList.remove('hidden')" class="px-5 py-2.5 bg-primary hover:bg-primary-light text-white font-semibold rounded-full transition-all hover:shadow-lg text-sm flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Tambah User</button>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <?php
                $countByRole = ['admin' => 0, 'resepsionis' => 0, 'tamu' => 0];
                foreach ($userList as $u) { $countByRole[$u['role']]++; }
                ?>
                <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-danger/10 flex items-center justify-center"><svg class="w-6 h-6 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div><div><p class="text-2xl font-bold text-dark"><?= $countByRole['admin'] ?></p><p class="text-xs text-earth">Admin</p></div></div>
                <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-info/10 flex items-center justify-center"><svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div><div><p class="text-2xl font-bold text-dark"><?= $countByRole['resepsionis'] ?></p><p class="text-xs text-earth">Resepsionis</p></div></div>
                <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center"><svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div><div><p class="text-2xl font-bold text-dark"><?= $countByRole['tamu'] ?></p><p class="text-xs text-earth">Tamu</p></div></div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead><tr class="bg-white border-b border-cream-dark">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-earth uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-earth uppercase tracking-wider">Telepon</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-earth uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-earth uppercase tracking-wider">Aksi</th>
                        </tr></thead>
                        <tbody class="divide-y divide-cream">
                            <?php foreach ($userList as $u): ?>
                            <tr class="hover:bg-cream/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-dark"><?= e($u['nama']) ?></td>
                                <td class="px-6 py-4 text-sm text-earth"><?= e($u['email']) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="<?= BASE_URL ?>/admin/kelola_user.php" class="inline">
                                        <?= csrf_field() ?><input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <select name="role" onchange="this.form.submit()" class="px-2 py-1 <?= $roleColors[$u['role']] ?? '' ?> text-xs font-medium rounded-full border-0 cursor-pointer">
                                            <option value="tamu" <?= $u['role'] === 'tamu' ? 'selected' : '' ?>>Tamu</option>
                                            <option value="resepsionis" <?= $u['role'] === 'resepsionis' ? 'selected' : '' ?>>Resepsionis</option>
                                            <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                        <input type="hidden" name="update_role" value="1">
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-sm text-earth"><?= e($u['no_hp'] ?: '-') ?></td>
                                <td class="px-6 py-4 text-center"><span class="px-3 py-1 <?= $u['status'] === 'aktif' ? 'bg-success-light text-success' : 'bg-gray-100 text-gray-500' ?> text-xs font-medium rounded-full"><?= ucfirst($u['status']) ?></span></td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/kelola_user.php" class="inline"><?= csrf_field() ?><input type="hidden" name="user_id" value="<?= $u['id'] ?>"><button type="submit" name="toggle_status" class="px-3 py-1 <?= $u['status'] === 'aktif' ? 'text-danger hover:bg-danger/10' : 'text-success hover:bg-success/10' ?> text-xs font-medium rounded-lg transition-colors" onclick="return confirm('<?= $u['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?> user ini?')"><?= $u['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?></button></form>
                                    <?php else: ?>
                                    <span class="text-xs text-earth">—</span>
                                    <?php endif; ?>
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
            <div class="flex items-center justify-between mb-6"><h2 class="font-sans text-xl font-bold text-dark">Tambah User</h2><button onclick="document.getElementById('modal-user').classList.add('hidden')" class="p-2 text-earth hover:text-dark rounded-lg transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
            <form method="POST" action="<?= BASE_URL ?>/admin/kelola_user.php" class="space-y-4">
                <?= csrf_field() ?>
                <div><label class="block text-sm font-medium text-dark mb-1">Nama</label><input type="text" name="nama" required placeholder="Nama lengkap" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                <div><label class="block text-sm font-medium text-dark mb-1">Email</label><input type="email" name="email" required placeholder="email@contoh.com" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                <div><label class="block text-sm font-medium text-dark mb-1">Password</label><input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors"></div>
                <div><label class="block text-sm font-medium text-dark mb-1">Role</label>
                    <select name="role" class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                        <option value="tamu">Tamu</option><option value="resepsionis">Resepsionis</option><option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" name="tambah_user" class="w-full py-3 bg-primary hover:bg-primary-light text-white font-semibold rounded-xl transition-all hover:shadow-lg flex items-center justify-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Simpan User</button>
            </form>
        </div>
    </div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
