<?php if (!defined('INCLUDED'))
    define('INCLUDED', true);
// Share settings globally to avoid duplicate getAllSettings() calls
if (!isset($GLOBALS['_allSettings']))
    $GLOBALS['_allSettings'] = getAllSettings();
$navSettings = $GLOBALS['_allSettings'];
$siteLogo = $navSettings['site_logo'] ?? '';
$siteName = $navSettings['site_name'] ?? 'Elite Pet Design';
$siteTagline = $navSettings['site_tagline'] ?? 'เฟอร์นิเจอร์สัตว์เลี้ยงพรีเมียม';
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' | ' : '' ?><?= $siteName ?> - <?= $siteTagline ?></title>
    <meta name="description"
        content="<?= $pageDesc ?? "$siteName - Workshop where custom pet furniture is hand-crafted to perfectly suit the lives of your furry, feathered, or exotic pets." ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= isset($pageTitle) ? $pageTitle . ' | ' : '' ?><?= $siteName ?>">
    <meta property="og:description"
        content="<?= $pageDesc ?? "$siteName - เฟอร์นิเจอร์สัตว์เลี้ยง ออกแบบสั่งทำพิเศษ" ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= BASE_URL ?>">
    <?php if ($siteLogo): ?>
        <meta property="og:image" content="<?= $siteLogo ?>"><?php endif; ?>

    <!-- Favicon -->
    <?php if ($siteLogo): ?>
        <link rel="icon" type="image/png" href="<?= $siteLogo ?>">
    <?php else: ?>
        <link rel="icon"
            href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🐾</text></svg>">
    <?php endif; ?>

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

        /* Sticky Navbar */
        #navbar {
            position: sticky;
            top: 0;
            z-index: 50;
            transition: box-shadow 0.3s, background 0.3s;
        }

        #navbar.scrolled {
            box-shadow: 0 2px 20px rgba(74, 58, 44, 0.1);
            background: rgba(245, 240, 230, 0.98);
        }

        /* Mobile menu */
        #mobileMenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease;
        }

        #mobileMenu.open {
            max-height: 400px;
        }

        /* Scroll animations - only hide when JS is confirmed running */
        html.js .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }

        html.js .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Back to top */
        #backToTop {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 99;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8a7050, #6b5540);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(107, 85, 64, 0.3);
            cursor: pointer;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.3s, transform 0.3s;
        }

        #backToTop.show {
            opacity: 1;
            transform: translateY(0);
        }

        #backToTop:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(107, 85, 64, 0.4);
        }
    </style>
</head>

<body class="bg-elite-50">

    <!-- NAVBAR -->
    <nav id="navbar" class="bg-elite-100/95 backdrop-blur-sm border-b border-elite-200/50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-14">
                <!-- Logo -->
                <a href="<?= BASE_URL ?>" class="flex items-center gap-2 flex-shrink-0">
                    <?php if ($siteLogo): ?>
                        <img src="<?= $siteLogo ?>" alt="<?= $siteName ?>" class="h-12 w-auto object-contain"
                            style="mix-blend-mode:multiply" loading="lazy">
                    <?php endif; ?>
                    <span
                        class="font-serif text-base font-bold tracking-[0.1em] uppercase text-elite-800"><?= $siteName ?></span>
                </a>
                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-1">
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
                <!-- Hamburger -->
                <button id="menuToggle" class="md:hidden p-2 text-elite-700 hover:text-elite-900 transition-colors"
                    aria-label="Menu">
                    <svg id="menuIcon" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg id="closeIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="md:hidden border-t border-elite-200/50">
            <div class="px-4 py-3 space-y-1 bg-elite-100/98">
                <a href="<?= BASE_URL ?>"
                    class="block px-3 py-2.5 rounded-lg text-sm font-medium <?= ($currentPage ?? '') === 'home' ? 'bg-elite-500/10 text-elite-800' : 'text-elite-500' ?> hover:bg-elite-200/50 transition-colors">🏠
                    Home</a>
                <a href="<?= BASE_URL ?>#services"
                    class="block px-3 py-2.5 rounded-lg text-sm font-medium text-elite-500 hover:bg-elite-200/50 transition-colors">🔧
                    Our Service</a>
                <a href="<?= BASE_URL ?>#materials"
                    class="block px-3 py-2.5 rounded-lg text-sm font-medium text-elite-500 hover:bg-elite-200/50 transition-colors">🎨
                    Materials</a>
                <a href="<?= BASE_URL ?>products.php"
                    class="block px-3 py-2.5 rounded-lg text-sm font-medium <?= ($currentPage ?? '') === 'products' ? 'bg-elite-500/10 text-elite-800' : 'text-elite-500' ?> hover:bg-elite-200/50 transition-colors">🛍️
                    Products</a>
                <a href="<?= BASE_URL ?>blog.php"
                    class="block px-3 py-2.5 rounded-lg text-sm font-medium <?= ($currentPage ?? '') === 'blog' ? 'bg-elite-500/10 text-elite-800' : 'text-elite-500' ?> hover:bg-elite-200/50 transition-colors">📝
                    Blog</a>
                <a href="<?= BASE_URL ?>contact.php"
                    class="block px-3 py-2.5 rounded-lg text-sm font-medium <?= ($currentPage ?? '') === 'contact' ? 'bg-elite-500/10 text-elite-800' : 'text-elite-500' ?> hover:bg-elite-200/50 transition-colors">📞
                    Contact</a>
            </div>
        </div>
    </nav>

    <!-- Back to Top -->
    <div id="backToTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
        </svg>
    </div>