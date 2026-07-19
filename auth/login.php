<?php

/**
 * Login Page — Kincay Mania Hotel & Resort
 * Mendukung multi-role: tamu, resepsionis, admin.
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Jika sudah login, langsung ke dashboard
redirect_if_logged_in();

$errors  = [];
$old_email = '';

// ── Proses POST ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {

    validate_csrf();

    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password'] ?? '';
    $old_email = $email;

    // Validasi input dasar
    if (empty($email) || empty($password)) {
        $errors[] = 'Email dan password wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    if (empty($errors)) {
        // Cari user di database — prepared statement, tidak ada string concat
        $stmt = db()->prepare('SELECT id, nama, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Login berhasil — regenerate session ID untuk mencegah session fixation
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];

            // Redirect sesuai role
            $redirect = dashboard_url($user['role']);
            header('Location: ' . $redirect);
            exit;
        } else {
            // Pesan generik — tidak spesifik email atau password (security best practice)
            $errors[] = 'Email atau password salah.';

            // Delay kecil sebagai perlindungan brute-force sederhana
            usleep(300_000); // 300ms
        }
    }
}

// ── Ambil flash message (dari register, dll.) ───────────────────────────────
$flash = get_flash();

$pageTitle       = 'Masuk';
$pageDescription = 'Masuk ke akun Kincay Mania Hotel & Resort Anda.';

include __DIR__ . '/../includes/header.php';
?>

<!-- Navbar minimal untuk auth -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-dark/95 backdrop-blur-md shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="<?= BASE_URL ?>/guest/" class="flex items-center gap-3 group">
                <div class="w-8 h-8 rounded-full bg-accent/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <span class="font-sans text-lg text-white font-semibold">Kincay Mania</span>
            </a>
            <a href="<?= BASE_URL ?>/guest/" class="text-cream/60 hover:text-cream text-sm flex items-center gap-1 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>
</nav>

<!-- Login Form -->
<div class="min-h-screen flex">
    <!-- Left: Image -->
    <div class="hidden lg:flex lg:w-1/2 relative">
        <img src="https://placehold.co/960x1080/2D5016/FDF6E3?text=Kincay+Mania%0AHotel+%26+Resort"
            alt="Alam Kerinci"
            class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-dark/50"></div>
        <div class="absolute inset-0 flex items-center justify-center p-12">
            <div class="text-center">
                <h2 class="font-sans text-4xl text-white font-bold mb-4">Selamat Datang Kembali</h2>
                <p class="text-cream/70 text-lg max-w-md">Masuk ke akun Anda untuk memesan kamar, menambahkan paket wisata, dan mengelola reservasi.</p>
            </div>
        </div>
    </div>

    <!-- Right: Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 pt-24 bg-cream">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="font-sans text-3xl text-dark font-bold mb-2">Masuk ke Akun</h1>
                <p class="text-earth">Belum punya akun?
                    <a href="<?= BASE_URL ?>/auth/register.php" class="text-primary hover:text-primary-light font-medium transition-colors">Daftar di sini</a>
                </p>
            </div>

            <!-- Flash / Error Messages -->
            <div id="flash-area" class="mb-5">
                <?php if ($flash): ?>
                    <div class="p-4 rounded-xl text-sm font-medium
                            <?= $flash['type'] === 'success' ? 'bg-success/10 text-success border border-success/20' : '' ?>
                            <?= $flash['type'] === 'warning' ? 'bg-warning/10 text-warning border border-warning/20' : '' ?>
                            <?= $flash['type'] === 'error'   ? 'bg-danger/10 text-danger border border-danger/20'   : '' ?>
                        ">
                        <?= e($flash['message']) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="p-4 rounded-xl text-sm bg-danger/10 text-danger border border-danger/20">
                        <?php foreach ($errors as $err): ?>
                            <p><?= e($err) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <form method="POST" action="<?= BASE_URL ?>/auth/login.php" class="space-y-5">
                <?= csrf_field() ?>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-dark mb-2">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-earth/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <input type="email" id="email" name="email" required
                            value="<?= e($old_email) ?>"
                            placeholder="contoh@email.com"
                            class="w-full pl-12 pr-4 py-3 bg-white border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-dark mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-earth/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input type="password" id="password" name="password" required
                            placeholder="Masukkan password"
                            class="w-full pl-12 pr-4 py-3 bg-white border border-cream-darker rounded-xl text-dark placeholder-earth/40 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>
                </div>

                <!-- Remember -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-primary border-cream-darker rounded focus:ring-primary/20">
                        <span class="text-sm text-earth">Ingat saya</span>
                    </label>
                </div>

                <!-- Submit -->
                <button type="submit" name="login"
                    class="w-full py-3.5 bg-primary hover:bg-primary-light text-white font-semibold rounded-xl transition-all hover:shadow-lg hover:shadow-primary/25 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Masuk
                </button>
            </form>

        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>