<?php
/**
 * Halaman Informasi — Kincay Mania Hotel & Resort
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role('tamu');

$pageTitle = 'Informasi & Kontak';
$pageDescription = 'Informasi lengkap tentang Kincay Mania Hotel & Resort — lokasi, kontak, dan kebijakan pembatalan.';

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_tamu.php';
?>

    <!-- Page Header -->
    <section class="pt-28 pb-12 bg-dark relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <img src="https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=1920&h=400&fit=crop" alt="" class="w-full h-full object-cover">
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-accent font-medium tracking-wider uppercase text-sm mb-3">Tentang Kami</p>
            <h1 class="font-serif text-4xl sm:text-5xl text-white font-bold mb-4">Informasi & Kontak</h1>
            <p class="text-cream/60 max-w-2xl mx-auto">Semua yang perlu Anda ketahui tentang Kincay Mania Hotel & Resort.</p>
        </div>
    </section>

    <section class="py-16 bg-cream">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Tentang -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm">
                        <h2 class="font-serif text-2xl text-dark font-semibold mb-4">Tentang Kincay Mania</h2>
                        <p class="text-earth leading-relaxed mb-4">
                            Kincay Mania Hotel & Resort adalah nature resort bertema alam Kerinci yang menawarkan pengalaman menginap unik di tengah keindahan alam Sumatera. Terletak di kawasan strategis dekat Taman Nasional Kerinci Seblat, resort kami memadukan kenyamanan modern dengan keindahan alam yang masih asri.
                        </p>
                        <p class="text-earth leading-relaxed">
                            Kami menyediakan tiga lini layanan utama: penginapan berupa kamar dan kabin eksklusif, paket wisata alam (trekking Gunung Kerinci, river tubing, susur perahu), dan pengalaman kuliner lokal autentik khas Kerinci.
                        </p>
                    </div>

                    <!-- Kebijakan Pembatalan -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm">
                        <h2 class="font-serif text-2xl text-dark font-semibold mb-4">Kebijakan Pembatalan</h2>
                        <div class="space-y-4">
                            <div class="flex gap-4 p-4 bg-success-light rounded-xl">
                                <div class="w-10 h-10 rounded-full bg-success/20 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <p class="font-medium text-dark mb-1">Sebelum Pembayaran Diverifikasi</p>
                                    <p class="text-sm text-earth">Pembatalan gratis tanpa biaya. Status langsung berubah menjadi "Dibatalkan".</p>
                                </div>
                            </div>
                            <div class="flex gap-4 p-4 bg-warning-light rounded-xl">
                                <div class="w-10 h-10 rounded-full bg-warning/20 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                </div>
                                <div>
                                    <p class="font-medium text-dark mb-1">Setelah Dikonfirmasi (Maks. H-2)</p>
                                    <p class="text-sm text-earth">Pembatalan dikenakan biaya 50% dari total yang dibayarkan. Refund 50% akan diproses manual oleh tim kami.</p>
                                </div>
                            </div>
                            <div class="flex gap-4 p-4 bg-danger-light rounded-xl">
                                <div class="w-10 h-10 rounded-full bg-danger/20 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <div>
                                    <p class="font-medium text-dark mb-1">Kurang dari H-2 Sebelum Check-in</p>
                                    <p class="text-sm text-earth">Pembatalan tidak dapat dilakukan. Tidak ada refund.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm">
                        <h2 class="font-serif text-2xl text-dark font-semibold mb-4">Metode Pembayaran</h2>
                        <p class="text-earth mb-4">Pembayaran dilakukan melalui transfer bank manual ke rekening berikut:</p>
                        <div class="p-6 bg-cream rounded-xl border border-cream-darker">
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between"><span class="text-earth">Bank:</span> <span class="font-medium text-dark">Bank Mandiri</span></div>
                                <div class="flex justify-between"><span class="text-earth">No. Rekening:</span> <span class="font-medium text-dark">1234-5678-9012</span></div>
                                <div class="flex justify-between"><span class="text-earth">Atas Nama:</span> <span class="font-medium text-dark">PT Kincay Mania Resort</span></div>
                            </div>
                        </div>
                        <p class="text-sm text-earth mt-4">Setelah transfer, unggah bukti pembayaran melalui halaman riwayat booking. Verifikasi akan dilakukan oleh tim kami dalam 1x24 jam.</p>
                    </div>

                    <!-- Maps (iframe placeholder) -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm">
                        <h2 class="font-serif text-2xl text-dark font-semibold mb-4">Lokasi Kami</h2>
                        <div class="rounded-xl overflow-hidden h-80 bg-cream-dark">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127672.7!2d101.3!3d-1.7!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e2d4c9f4e4d1e7%3A0x1!2sKerinci%2C+Jambi!5e0!3m2!1sid!2sid!4v1" 
                                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <p class="text-sm text-earth mt-4">Jl. Raya Kerinci No. 88, Kabupaten Kerinci, Jambi 37171</p>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Contact Card -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm sticky top-28">
                        <h3 class="font-serif text-xl text-dark font-semibold mb-6">Hubungi Kami</h3>
                        <div class="space-y-5">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-earth mb-1">Telepon</p>
                                    <p class="font-medium text-dark">+62 812-3456-7890</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-earth mb-1">Email</p>
                                    <p class="font-medium text-dark">info@kincaymania.com</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-earth mb-1">Jam Operasional</p>
                                    <p class="font-medium text-dark">24 Jam (Front Desk)</p>
                                    <p class="text-sm text-earth">Check-in: 14:00 | Check-out: 12:00</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-success/10 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-success" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-earth mb-1">WhatsApp</p>
                                    <a href="https://wa.me/6281234567890" class="font-medium text-success hover:underline">+62 812-3456-7890</a>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-cream-dark">
                            <a href="<?= BASE_URL ?>/tamu/cek_ketersediaan.php"
                               class="w-full py-3 bg-accent hover:bg-accent-light text-dark font-semibold rounded-xl text-center flex items-center justify-center gap-2 transition-all hover:shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Cek Ketersediaan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
