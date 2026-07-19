<?php
/** Profil Akun — Kincay Mania Hotel & Resort */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');
$pageTitle = 'Profil Saya';
$user = ['nama' => 'Budi Tamu', 'email' => 'tamu@kincaymania.com', 'no_hp' => '081234567890', 'created' => '2026-01-15'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_tamu.php';
?>
    <section class="pt-24 pb-16 bg-cream min-h-screen">
        <div class="max-w-2xl mx-auto px-4">
            <h1 class="font-sans text-3xl text-dark font-bold mb-8">Profil Saya</h1>

            <!-- Profile Card -->
            <div class="bg-white rounded-2xl p-8 shadow-sm mb-6">
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-cream">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center">
                        <span class="text-2xl font-bold text-primary"><?= strtoupper(substr($user['nama'], 0, 1)) ?></span>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-dark"><?= e($user['nama']) ?></h2>
                        <p class="text-sm text-earth">Anggota sejak <?= date('M Y', strtotime($user['created'])) ?></p>
                    </div>
                </div>

                <form method="POST" action="<?= BASE_URL ?>/tamu/profil.php" class="space-y-5">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Nama Lengkap</label>
                        <input type="text" name="nama" value="<?= e($user['nama']) ?>" required
                               class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Email</label>
                        <input type="email" name="email" value="<?= e($user['email']) ?>" required
                               class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">No. Handphone</label>
                        <input type="tel" name="no_hp" value="<?= e($user['no_hp']) ?>" required
                               class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                    </div>
                    <button type="submit" name="update_profil"
                            class="w-full py-3 bg-primary hover:bg-primary-light text-white font-semibold rounded-xl transition-all hover:shadow-lg">
                        Simpan Perubahan
                    </button>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-2xl p-8 shadow-sm">
                <h2 class="font-semibold text-dark mb-4">Ganti Password</h2>
                <form method="POST" action="<?= BASE_URL ?>/tamu/profil.php" class="space-y-5">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Password Lama</label>
                        <input type="password" name="password_lama" required
                               class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Password Baru</label>
                        <input type="password" name="password_baru" required minlength="8"
                               class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_konfirmasi" required minlength="8"
                               class="w-full px-4 py-3 bg-cream border border-cream-darker rounded-xl text-dark focus:border-primary transition-colors">
                    </div>
                    <button type="submit" name="ganti_password"
                            class="w-full py-3 bg-earth hover:bg-earth-light text-white font-semibold rounded-xl transition-all">
                        Ganti Password
                    </button>
                </form>
            </div>
        </div>
    </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
