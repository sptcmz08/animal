<?php if (!defined('INCLUDED'))
    define('INCLUDED', true); ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' | ' : '' ?>ELITE PET DESIGN - เฟอร์นิเจอร์สัตว์เลี้ยงพรีเมียม</title>
    <meta name="description"
        content="<?= $pageDesc ?? 'ELITE PET DESIGN - Workshop where custom pet furniture is hand-crafted to perfectly suit the lives of your furry, feathered, or exotic pets.' ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&family=Inter:wght@300;400;500;600;700;800&family=Prompt:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        elite: {
                            50: '#faf8f4',
                            100: '#f5f0e6',
                            150: '#efe8d8',
                            200: '#e8dfc9',
                            300: '#d4c4a0',
                            400: '#b8a07a',
                            500: '#a08860',
                            600: '#8a7050',
                            700: '#6b5540',
                            800: '#4a3a2c',
                            900: '#2c2018'
                        },
                    },
                    fontFamily: {
                        th: ['Prompt', 'sans-serif'],
                        en: ['Inter', 'sans-serif'],
                        serif: ['Cormorant Garamond', 'serif']
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            color: #4a3a2c;
            -webkit-font-smoothing: antialiased;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .video-placeholder {
            background: linear-gradient(135deg, #b8a07a 0%, #a08860 50%, #8a7050 100%);
            border-radius: 20px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .play-btn {
            width: 80px;
            height: 80px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.3s, background 0.3s;
        }

        .play-btn:hover {
            transform: scale(1.1);
            background: rgba(0, 0, 0, 0.85);
        }

        .play-btn svg {
            width: 32px;
            height: 32px;
            fill: white;
            margin-left: 4px;
        }

        .section-divider {
            border: none;
            height: 1px;
            background: linear-gradient(to right, transparent, #d4c4a0, transparent);
            margin: 0;
        }
    </style>
</head>

<body class="bg-elite-50">

    <!-- NAVBAR -->
    <?php
    $navSettings = getAllSettings();
    $siteLogo = $navSettings['site_logo'] ?? '';
    ?>
    <nav id="navbar" class="bg-elite-100/95 backdrop-blur-sm border-b border-elite-200/50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-14">
                <!-- Logo -->
                <a href="<?= BASE_URL ?>" class="flex items-center gap-2 flex-shrink-0">
                    <?php if ($siteLogo): ?>
                        <img src="<?= $siteLogo ?>" alt="Elite Pet Design" class="h-12 w-auto object-contain"
                            style="mix-blend-mode:multiply">
                    <?php endif; ?>
                    <span class="font-serif text-base font-bold tracking-[0.1em] uppercase text-elite-800">Elite Pet
                        Design</span>
                </a>
                <!-- Nav Links -->
                <div class="flex items-center gap-1 overflow-x-auto">
                    <a href="<?= BASE_URL ?>"
                        class="px-3 py-1 text-xs font-medium tracking-wider uppercase transition-colors hover:text-elite-700 whitespace-nowrap <?= ($currentPage ?? '') === 'home' ? 'text-elite-800 font-semibold' : 'text-elite-500' ?>">Home</a>
                    <a href="<?= BASE_URL ?>#services"
                        class="px-3 py-1 text-xs font-medium tracking-wider uppercase transition-colors hover:text-elite-700 whitespace-nowrap text-elite-500">Our
                        Service</a>
                    <a href="<?= BASE_URL ?>#materials"
                        class="px-3 py-1 text-xs font-medium tracking-wider uppercase transition-colors hover:text-elite-700 whitespace-nowrap text-elite-500">Materials</a>
                    <a href="<?= BASE_URL ?>products.php"
                        class="px-3 py-1 text-xs font-medium tracking-wider uppercase transition-colors hover:text-elite-700 whitespace-nowrap <?= ($currentPage ?? '') === 'products' ? 'text-elite-800 font-semibold' : 'text-elite-500' ?>">Products</a>
                    <a href="<?= BASE_URL ?>blog.php"
                        class="px-3 py-1 text-xs font-medium tracking-wider uppercase transition-colors hover:text-elite-700 whitespace-nowrap <?= ($currentPage ?? '') === 'blog' ? 'text-elite-800 font-semibold' : 'text-elite-500' ?>">Blog</a>
                    <a href="<?= BASE_URL ?>contact.php"
                        class="px-3 py-1 text-xs font-medium tracking-wider uppercase transition-colors hover:text-elite-700 whitespace-nowrap <?= ($currentPage ?? '') === 'contact' ? 'text-elite-800 font-semibold' : 'text-elite-500' ?>">Contact</a>
                </div>
            </div>
        </div>
    </nav>