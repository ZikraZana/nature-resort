/**
 * main.js — Kincay Mania Hotel & Resort
 * JavaScript seminimal mungkin, hanya untuk:
 * 1. Mobile menu toggle
 * 2. Navbar scroll effect
 * 3. Section reveal animation
 * 4. Confirm dialog untuk aksi berbahaya
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── 1. Mobile Menu Toggle ──
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIconOpen = document.getElementById('menu-icon-open');
    const menuIconClose = document.getElementById('menu-icon-close');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden');
            if (menuIconOpen && menuIconClose) {
                menuIconOpen.classList.toggle('hidden');
                menuIconClose.classList.toggle('hidden');
            }
        });
    }

    // ── 2. Navbar Scroll Effect ──
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
                navbar.classList.remove('bg-transparent');
            } else {
                navbar.classList.remove('navbar-scrolled');
                // Only re-add transparent if it was originally transparent
                if (navbar.dataset.transparent === 'true') {
                    navbar.classList.add('bg-transparent');
                }
            }
        });

        // Set data attribute for transparent navbars
        if (navbar.classList.contains('bg-transparent')) {
            navbar.dataset.transparent = 'true';
        }
    }

    // ── 3. Section Reveal on Scroll ──
    const revealElements = document.querySelectorAll('.reveal');
    if (revealElements.length > 0) {
        const revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        revealElements.forEach(function (el) {
            revealObserver.observe(el);
        });
    }

    // ── 4. Confirm Dialog for Dangerous Actions ──
    const confirmButtons = document.querySelectorAll('[data-confirm]');
    confirmButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            const message = this.getAttribute('data-confirm') || 'Apakah Anda yakin?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // ── 5. Auto-hide flash messages ──
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(function (msg) {
        setTimeout(function () {
            msg.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            msg.style.opacity = '0';
            msg.style.transform = 'translateY(-10px)';
            setTimeout(function () { msg.remove(); }, 500);
        }, 5000);
    });

});
