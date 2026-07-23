<?php
/**
 * Profil Tamu — Kincay Mania Hotel & Resort
 * Query dan update profil user dari database.
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');
$pageTitle = 'Profil Saya';

// Query user dari database
$stmt = db()->prepare('SELECT id, nama, email, no_hp, alamat, created_at FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    set_flash('danger', 'User tidak ditemukan.');
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

// Hitung total booking user
$stmtStat = db()->prepare('SELECT COUNT(*) FROM booking WHERE user_id = ?');
$stmtStat->execute([$_SESSION['user_id']]);
$totalBooking = $stmtStat->fetchColumn();

$stmtStat2 = db()->prepare("SELECT COUNT(*) FROM booking WHERE user_id = ? AND status IN ('dikonfirmasi','checkin','selesai')");
$stmtStat2->execute([$_SESSION['user_id']]);
$bookingBerhasil = $stmtStat2->fetchColumn();

$errors = [];
$errorsPassword = [];
$successMessage = '';

// ── Proses POST: Update Profil ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profil'])) {
    validate_csrf();

    $nama    = trim($_POST['nama'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $no_hp   = trim($_POST['no_hp'] ?? '');
    $alamat  = trim($_POST['alamat'] ?? '');

    // Validasi
    if (empty($nama)) $errors[] = 'Nama wajib diisi.';
    if (empty($email)) $errors[] = 'Email wajib diisi.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';

    // Cek email unik (kecuali milik sendiri)
    if (!empty($email) && $email !== $user['email']) {
        $stmtCek = db()->prepare('SELECT COUNT(*) FROM users WHERE email = ? AND id != ?');
        $stmtCek->execute([$email, $_SESSION['user_id']]);
        if ($stmtCek->fetchColumn() > 0) {
            $errors[] = 'Email ini sudah digunakan akun lain.';
        }
    }

    if (empty($errors)) {
        $stmt = db()->prepare('UPDATE users SET nama = ?, email = ?, no_hp = ?, alamat = ? WHERE id = ?');
        $stmt->execute([$nama, $email, $no_hp ?: null, $alamat ?: null, $_SESSION['user_id']]);

        // Update session
        $_SESSION['nama'] = $nama;
        $_SESSION['email'] = $email;

        // Re-fetch user
        $stmt = db()->prepare('SELECT id, nama, email, no_hp, alamat, created_at FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        set_flash('success', 'Profil berhasil diperbarui!');
        header('Location: ' . BASE_URL . '/tamu/profil.php');
        exit;
    }
}

// ── Proses POST: Ganti Password ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ganti_password'])) {
    validate_csrf();

    $passLama = $_POST['password_lama'] ?? '';
    $passBaru = $_POST['password_baru'] ?? '';
    $passKonfirmasi = $_POST['password_konfirmasi'] ?? '';

    if (empty($passLama)) $errorsPassword[] = 'Password lama wajib diisi.';
    if (empty($passBaru)) $errorsPassword[] = 'Password baru wajib diisi.';
    elseif (strlen($passBaru) < 8) $errorsPassword[] = 'Password baru minimal 8 karakter.';
    if ($passBaru !== $passKonfirmasi) $errorsPassword[] = 'Konfirmasi password tidak cocok.';

    // Verifikasi password lama
    if (empty($errorsPassword)) {
        $stmtPass = db()->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmtPass->execute([$_SESSION['user_id']]);
        $currentHash = $stmtPass->fetchColumn();

        if (!password_verify($passLama, $currentHash)) {
            $errorsPassword[] = 'Password lama salah.';
        }
    }

    if (empty($errorsPassword)) {
        $newHash = password_hash($passBaru, PASSWORD_DEFAULT);
        $stmt = db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->execute([$newHash, $_SESSION['user_id']]);

        set_flash('success', 'Password berhasil diubah!');
        header('Location: ' . BASE_URL . '/tamu/profil.php');
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_tamu.php';
?>

    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Message -->
            <?php $flash = get_flash(); if ($flash): ?>
            <div class="mb-6 p-4 bg-<?= $flash['type'] === 'success' ? 'success-light' : 'danger-light' ?> rounded-xl text-sm text-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
                <?= e($flash['message']) ?>
            </div>
            <?php endif; ?>

            <h1 class="font-sans text-3xl text-dark font-bold mb-8">Profil Saya</h1>

            <!-- Info Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center">
                        <span class="text-2xl font-bold text-primary"><?= strtoupper(substr($user['nama'], 0, 1)) ?></span>
                    </div>
                    <div>
                        <h2 class="font-semibold text-dark text-lg"><?= e($user['nama']) ?></h2>
                        <p class="text-earth text-sm"><?= e($user['email']) ?></p>
                        <p class="text-earth text-xs mt-1">Member sejak <?= date('d M Y', strtotime($user['created_at'])) ?></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="p-4 bg-cream rounded-xl text-center">
                        <p class="text-2xl font-bold text-primary"><?= $totalBooking ?></p>
                        <p class="text-earth text-xs">Total Booking</p>
                    </div>
                    <div class="p-4 bg-cream rounded-xl text-center">
                        <p class="text-2xl font-bold text-success"><?= $bookingBerhasil ?></p>
                        <p class="text-earth text-xs">Booking Berhasil</p>
                    </div>
                </div>
            </div>

            <!-- Edit Profil -->
            <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
                <h3 class="font-semibold text-dark mb-4">Edit Profil</h3>

                <?php if (!empty($errors)): ?>
                <div class="mb-4 p-4 bg-danger-light rounded-xl text-sm text-danger">
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach ($errors as $err): ?>
                        <li><?= e($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/tamu/profil.php" class="space-y-4">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Nama Lengkap</label>
                        <input type="text" name="nama" value="<?= e($_POST['nama'] ?? $user['nama']) ?>" required
                               class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Email</label>
                        <input type="email" name="email" value="<?= e($_POST['email'] ?? $user['email']) ?>" required
                               class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1">No. Telepon</label>
                            <input type="tel" name="no_hp" value="<?= e($_POST['no_hp'] ?? $user['no_hp']) ?>"
                                   placeholder="08xxxxxxxxxx"
                                   class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1">Alamat</label>
                            <input type="text" name="alamat" value="<?= e($_POST['alamat'] ?? $user['alamat']) ?>"
                                   placeholder="Kota/Kabupaten"
                                   class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors">
                        </div>
                    </div>
                    <button type="submit" name="update_profil" class="w-full py-3 bg-primary hover:bg-primary-light text-white font-semibold rounded-xl transition-all hover:shadow-lg">
                        Simpan Perubahan
                    </button>
                </form>
            </div>

            <!-- Ganti Password -->
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h3 class="font-semibold text-dark mb-4">Ganti Password</h3>

                <?php if (!empty($errorsPassword)): ?>
                <div class="mb-4 p-4 bg-danger-light rounded-xl text-sm text-danger">
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach ($errorsPassword as $err): ?>
                        <li><?= e($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/tamu/profil.php" class="space-y-4">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Password Lama</label>
                        <input type="password" name="password_lama" required
                               class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Password Baru</label>
                        <input type="password" name="password_baru" required minlength="8" placeholder="Minimal 8 karakter"
                               class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_konfirmasi" required
                               class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                    </div>
                    <button type="submit" name="ganti_password" class="w-full py-3 border border-primary text-primary hover:bg-primary hover:text-white font-semibold rounded-xl transition-all">
                        Ubah Password
                    </button>
                </form>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
