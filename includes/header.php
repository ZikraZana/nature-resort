<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($pageDescription ?? 'Kincay Mania Hotel & Resort — Nature Resort di jantung Kerinci. Nikmati penginapan kabin alam, paket wisata trekking & river tubing, serta kuliner lokal autentik.') ?>">
    <title><?= e(($pageTitle ?? 'Beranda') . ' — ' . SITE_NAME) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: { DEFAULT: '#2D5016', light: '#4A7C29', dark: '#1A3A0A' },
                    secondary: { DEFAULT: '#8B6914', light: '#A88A3D' },
                    accent: { DEFAULT: '#D4A847', light: '#E5C878' },
                    earth: { DEFAULT: '#6B4E2E', light: '#8B7355' },
                    cream: { DEFAULT: '#FDF6E3', dark: '#F5E6C8', darker: '#E8D5A8' },
                    dark: '#1A2E0A',
                    danger: { DEFAULT: '#B91C1C', light: '#FEE2E2' },
                    warning: { DEFAULT: '#D97706', light: '#FEF3C7' },
                    success: { DEFAULT: '#15803D', light: '#DCFCE7' },
                    info: { DEFAULT: '#1D4ED8', light: '#DBEAFE' },
                },
                fontFamily: {
                    sans: ['Outfit', 'sans-serif'],
                    serif: ['Playfair Display', 'serif'],
                },
                animation: {
                    'fade-in': 'fadeIn 0.6s ease-out',
                    'slide-up': 'slideUp 0.6s ease-out',
                    'float': 'float 3s ease-in-out infinite',
                },
                keyframes: {
                    fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                    slideUp: { '0%': { opacity: '0', transform: 'translateY(20px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                    float: { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-10px)' } },
                }
            }
        }
    }
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/custom.css">
</head>
<body class="font-sans bg-cream text-dark antialiased">
