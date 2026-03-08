<?php
$adminPage = $adminPage ?? '';
$settingsSection = $_GET['section'] ?? '';
$isSettings = $adminPage === 'settings';

if (!isset($GLOBALS['_allSettings']))
        $GLOBALS['_allSettings'] = getAllSettings();
$navSettings = $GLOBALS['_allSettings'];
$siteLogo = $navSettings['site_logo'] ?? '';
$siteName = $navSettings['site_name'] ?? 'Elite Pet Design';

$settingsMenu = [
        ['section' => 'branding', 'icon' => 'bi-building', 'label' => 'Branding'],
        ['section' => 'hero', 'icon' => 'bi-image', 'label' => 'Hero Section'],
        ['section' => 'intro', 'icon' => 'bi-text-paragraph', 'label' => 'Intro Text'],
        ['section' => 'products', 'icon' => 'bi-collection-play', 'label' => 'Products'],
        ['section' => 'services', 'icon' => 'bi-tools', 'label' => 'Services'],
        ['section' => 'materials', 'icon' => 'bi-palette', 'label' => 'Materials'],
        ['section' => 'reviews', 'icon' => 'bi-star', 'label' => 'Reviews'],
        ['section' => 'social', 'icon' => 'bi-share', 'label' => 'Social Media'],
        ['section' => 'contact', 'icon' => 'bi-telephone', 'label' => 'Contact Info'],
];
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<!-- Mobile Toggle -->
<button id="sidebarToggle" class="sidebar-toggle" aria-label="Toggle Menu">
        <i class="bi bi-list"></i>
</button>
<div id="sidebarOverlay" class="sidebar-overlay"></div>

<aside class="admin-sidebar" id="adminSidebar">
        <!-- Logo -->
        <div class="sidebar-brand">
                <?php if ($siteLogo): ?>
                        <img src="<?= $siteLogo ?>" alt="<?= $siteName ?>" class="brand-logo">
                <?php else: ?>
                        <div class="brand-icon">🐾</div>
                <?php endif; ?>
                <div class="brand-text">
                        <span class="brand-name"><?= $siteName ?></span>
                        <span class="brand-badge">Admin Panel</span>
                </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
                <!-- Dashboard -->
                <a href="<?= BASE_URL ?>admin/" class="nav-item <?= $adminPage === 'dashboard' ? 'active' : '' ?>">
                        <div class="nav-icon"><i class="bi bi-grid-1x2-fill"></i></div>
                        <span>แดชบอร์ด</span>
                </a>

                <!-- Web Settings Group -->
                <div class="nav-group-label">
                        <span class="nav-group-line"></span>
                        <span class="nav-group-text">จัดการหน้าเว็บ</span>
                        <span class="nav-group-line"></span>
                </div>
                <?php foreach ($settingsMenu as $item): ?>
                        <a href="<?= BASE_URL ?>admin/settings.php?section=<?= $item['section'] ?>"
                                class="nav-item nav-sub <?= ($isSettings && $settingsSection === $item['section']) ? 'active' : '' ?>">
                                <div class="nav-icon"><i class="bi <?= $item['icon'] ?>"></i></div>
                                <span><?= $item['label'] ?></span>
                        </a>
                <?php endforeach; ?>

                <!-- Products Group -->
                <div class="nav-group-label">
                        <span class="nav-group-line"></span>
                        <span class="nav-group-text">จัดการสินค้า</span>
                        <span class="nav-group-line"></span>
                </div>
                <a href="<?= BASE_URL ?>admin/products.php"
                        class="nav-item <?= $adminPage === 'products' ? 'active' : '' ?>">
                        <div class="nav-icon"><i class="bi bi-box-seam-fill"></i></div>
                        <span>สินค้า</span>
                </a>
                <a href="<?= BASE_URL ?>admin/categories.php"
                        class="nav-item <?= $adminPage === 'categories' ? 'active' : '' ?>">
                        <div class="nav-icon"><i class="bi bi-tags-fill"></i></div>
                        <span>หมวดหมู่</span>
                </a>
                <a href="<?= BASE_URL ?>admin/blog.php" class="nav-item <?= $adminPage === 'blog' ? 'active' : '' ?>">
                        <div class="nav-icon"><i class="bi bi-journal-richtext"></i></div>
                        <span>บทความ</span>
                </a>
                <a href="<?= BASE_URL ?>admin/orders.php"
                        class="nav-item <?= $adminPage === 'orders' ? 'active' : '' ?>">
                        <div class="nav-icon"><i class="bi bi-receipt-cutoff"></i></div>
                        <span>คำสั่งซื้อ</span>
                </a>
                <a href="<?= BASE_URL ?>admin/users.php" class="nav-item <?= $adminPage === 'users' ? 'active' : '' ?>">
                        <div class="nav-icon"><i class="bi bi-people-fill"></i></div>
                        <span>ผู้ใช้งาน</span>
                </a>
        </nav>

        <!-- Bottom Actions -->
        <div class="sidebar-footer">
                <a href="<?= BASE_URL ?>" class="nav-item" target="_blank">
                        <div class="nav-icon"><i class="bi bi-box-arrow-up-right"></i></div>
                        <span>ดูหน้าเว็บ</span>
                </a>
                <a href="<?= BASE_URL ?>profile.php?logout=1" class="nav-item nav-logout">
                        <div class="nav-icon"><i class="bi bi-power"></i></div>
                        <span>ออกจากระบบ</span>
                </a>
        </div>
</aside>

<script>
        (function () {
                const toggle = document.getElementById('sidebarToggle');
                const sidebar = document.getElementById('adminSidebar');
                const overlay = document.getElementById('sidebarOverlay');
                if (!toggle) return;
                function open() { sidebar.classList.add('open'); overlay.classList.add('show'); toggle.classList.add('active'); }
                function close() { sidebar.classList.remove('open'); overlay.classList.remove('show'); toggle.classList.remove('active'); }
                toggle.addEventListener('click', () => sidebar.classList.contains('open') ? close() : open());
                overlay.addEventListener('click', close);
        })();
</script>