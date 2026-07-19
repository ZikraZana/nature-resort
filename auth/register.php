<?php
/**
 * Register Page — Kincay Mania Hotel & Resort
 * Registrasi mandiri HANYA untuk role 'tamu'.
 * Resepsionis & admin dibuat manual via database/seed atau CRUD admin (fase berikutnya).
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Jika sudah login, langsung ke dashboard
redirect_if_logged_in();

$errors   = [];
$old      = [];   // nilai lama untuk sticky form

// ── Proses POST ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {

    validate_csrf();

    // Ambil & bersihkan input
    $nama             = trim($_POST['nama']             ?? '');
    $email            = trim(strtolower($_POST['email'] ?? ''));
    $no_hp            = trim($_POST['no_hp']            ?? '');
    $password         = $_POST['password']         ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    $old = compact('nama', 'email', 'no_hp');

    // ── Validasi server-side ─────────────────────────────────────────────────
    if (empty($nama)) {
        $errors['nama'] = 'Nama lengkap wajib diisi.';
    } elseif (mb_strlen($nama) > 100) {
        $errors['nama'] = 'Nama maksimal 100 karakter.';
    }

    if (empty($email)) {
        $errors['email'] = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    } else {
        // Cek duplikat email — prepared statement
        $stmt = db()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Email sudah terdaftar. Silakan gunakan email lain atau masuk.';
        }
    }

    if (empty($no_hp)) {
        $errors['no_hp'] = 'Nomor HP wajib diisi.';
    } elseif (!preg_match('/^[0-9+\-\s]{8,20}$/', $no_hp)) {
        $errors['no_hp'] = 'Format nomor HP tidak valid (8–20 digit).';
    }

    if (empty($password)) {
        $errors['password'] = 'Password wajib diisi.';
    } elseif (mb_strlen($password) < 8) {
        $errors['password'] = 'Password minimal 8 karakter.';
    }

    if ($password !== $password_confirm) {
        $errors['password_confirm'] = 'Konfirmasi password tidak cocok.';
    }

    // ── Simpan ke database jika valid ────────────────────────────────────────
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = db()->prepare(
            'INSERT INTO users (nama, email, no_hp, password_hash, role, created_at)
             VALUES (?, ?, ?, ?, \'tamu\', NOW())'
        );
        $stmt->execute([$nama, $email, $no_hp, $hashed]);

        set_flash('success', 'Akun berhasil dibuat! Silakan masuk menggunakan email dan password Anda.');
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit;
    }
}

$pageTitle       = 'Daftar Akun';
$pageDescription = 'Daftar akun baru di Kincay Mania Hotel & Resort.';

include __DIR__ . '/../includes/header.php';

// Helper: tampilkan kelas error pada field
function field_error(array $errors, string $key): string {
    return isset($errors[$key]) ? 'border-danger focus:border-danger focus:ring-danger/20' : 'border-cream-darker focus:border-primary focus:ring-primary/20';
}
?>

    <!-- Navbar minimal -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-dark/95 backdrop-blur-md shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="<?= BASE_URL ?>/guest/" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-accent/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                    <span class="font-sans text-lg text-white font-semibold">Kincay Mania</span>
                </a>
                <a href="<?= BASE_URL ?>/guest/" class="text-cream/60 hover:text-cream text-sm flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali
                </a>
            </div>
        </div>
    </nav>

    <!-- Register Form -->
    <div class="min-h-screen flex">
        <!-- Left: Image -->
        <div class="hidden lg:flex lg:w-1/2 relative">
            <img src="https://placehold.co/960x1080/1A2E0A/FDF6E3?text=Nature+Resort%0AKerinci"
                 alt="Hutan tropis Kerinci"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-dark/50"></div>
            <div class="absolute inset-0 flex items-center justify-center p-12">
                <div class="text-center">
                    <h2 class="font-sans text-4xl text-white font-bold mb-4">Bergabunglah Bersama Kami</h2>
                    <p class="text-cream/70 text-lg max-w-md">Buat akun untuk memesan kamar impian, menambahkan paket wisata, dan menikmati pengalaman alam Kerinci.</p>
                </div>
            </div>
        </div>

        <!-- Right: Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 pt-24 bg-cream">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                    <h1 class="font-sans text-3xl text-dark font-bold mb-2">Buat Akun Baru</h1>
                    <p class="text-earth">Sudah punya akun?
                        <a href="<?= BASE_URL ?>/auth/login.php" class="text-primary hover:text-primary-light font-medium transition-colors">Masuk di sini</a>
                    </p>
                </div>

                <!-- Error summary -->
                <?php if (!empty($errors)): ?>
                    <div class="mb-5 p-4 rounded-xl text-sm bg-danger/10 text-danger border border-danger/20">
                        <p class="font-medium mb-1">Mohon periksa kembali isian Anda:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            <?php foreach ($errors as $err): ?>
                                <li><?= e($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/auth/register.php" class="space-y-5">
                    <?= csrf_field() ?>

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="nama" class="block text-sm font-medium text-dark mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-earth/50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            <input type="text" id="nama" name="nama" required
                                   value="<?= e($old['nama'] ?? '') ?>"
                                   placeholder="Masukkan nama lengkap"
                                   class="w-full pl-12 pr-4 py-3 bg-white border <?= field_error($errors, 'nama') ?> rounded-xl text-dark placeholder-earth/40 transition-all">
                        </div>
                        <?php if (isset($errors['nama'])): ?>
                            <p class="text-danger text-xs mt-1"><?= e($errors['nama']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-dark mb-2">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-earth/50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <input type="email" id="email" name="email" required
                                   value="<?= e($old['email'] ?? '') ?>"
                                   placeholder="contoh@email.com"
                                   class="w-full pl-12 pr-4 py-3 bg-white border <?= field_error($errors, 'email') ?> rounded-xl text-dark placeholder-earth/40 transition-all">
                        </div>
                        <?php if (isset($errors['email'])): ?>
                            <p class="text-danger text-xs mt-1"><?= e($errors['email']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- No. HP -->
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-dark mb-2">No. Handphone</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-earth/50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </span>
                            <input type="tel" id="no_hp" name="no_hp" required
                                   value="<?= e($old['no_hp'] ?? '') ?>"
                                   placeholder="08xxxxxxxxxx"
                                   class="w-full pl-12 pr-4 py-3 bg-white border <?= field_error($errors, 'no_hp') ?> rounded-xl text-dark placeholder-earth/40 transition-all">
                        </div>
                        <?php if (isset($errors['no_hp'])): ?>
                            <p class="text-danger text-xs mt-1"><?= e($errors['no_hp']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-dark mb-2">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-earth/50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <input type="password" id="password" name="password" required minlength="8"
                                   placeholder="Minimal 8 karakter"
                                   class="w-full pl-12 pr-4 py-3 bg-white border <?= field_error($errors, 'password') ?> rounded-xl text-dark placeholder-earth/40 transition-all">
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <p class="text-danger text-xs mt-1"><?= e($errors['password']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-dark mb-2">Konfirmasi Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-earth/50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </span>
                            <input type="password" id="password_confirm" name="password_confirm" required minlength="8"
                                   placeholder="Ulangi password"
                                   class="w-full pl-12 pr-4 py-3 bg-white border <?= field_error($errors, 'password_confirm') ?> rounded-xl text-dark placeholder-earth/40 transition-all">
                        </div>
                        <?php if (isset($errors['password_confirm'])): ?>
                            <p class="text-danger text-xs mt-1"><?= e($errors['password_confirm']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Submit -->
                    <button type="submit" name="register"
                            class="w-full py-3.5 bg-primary hover:bg-primary-light text-white font-semibold rounded-xl transition-all hover:shadow-lg hover:shadow-primary/25 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Daftar Sekarang
                    </button>
                </form>

                <p class="text-center text-xs text-earth/60 mt-6">
                    Dengan mendaftar, Anda menyetujui <a href="#" class="text-primary hover:underline">Syarat &amp; Ketentuan</a>
                    dan <a href="#" class="text-primary hover:underline">Kebijakan Privasi</a> kami.
                </p>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
