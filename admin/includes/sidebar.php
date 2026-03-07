<?php
$adminPage = $adminPage ?? '';
$settingsSection = $_GET['section'] ?? '';
$isSettings = $adminPage === 'settings';

$settingsMenu = [
        ['section' => 'branding', 'icon' => 'bi-building', 'label' => 'Branding'],
        ['section' => 'hero', 'icon' => 'bi-image', 'label' => 'Hero Section'],
        ['section' => 'intro', 'icon' => 'bi-text-paragraph', 'label' => 'Intro Text'],
        ['section' => 'products', 'icon' => 'bi-collection-play', 'label' => 'Products Section'],
        ['section' => 'services', 'icon' => 'bi-tools', 'label' => 'Services Section'],
        ['section' => 'materials', 'icon' => 'bi-palette', 'label' => 'Materials'],
        ['section' => 'reviews', 'icon' => 'bi-star', 'label' => 'Reviews'],
        ['section' => 'social', 'icon' => 'bi-share', 'label' => 'Social Media'],
        ['section' => 'contact', 'icon' => 'bi-telephone', 'label' => 'Contact Info'],
];
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<div class="admin-sidebar">
        <div class="logo">🐾 Elite Pet Design</div>
        <ul class="admin-nav">
                <li><a href="<?= BASE_URL ?>admin/" class="<?= $adminPage === 'dashboard' ? 'active' : '' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                </svg>
                                แดชบอร์ด</a></li>

                <!-- Settings Group -->
                <li class="nav-group-label">จัดการหน้าเว็บ</li>
                <?php foreach ($settingsMenu as $item): ?>
                        <li><a href="<?= BASE_URL ?>admin/settings.php?section=<?= $item['section'] ?>"
                                        class="nav-sub <?= ($isSettings && $settingsSection === $item['section']) ? 'active' : '' ?>">
                                        <i class="bi <?= $item['icon'] ?>"></i>
                                        <?= $item['label'] ?></a></li>
                <?php endforeach; ?>

                <!-- Divider -->
                <li class="nav-group-label" style="margin-top:12px">จัดการสินค้า</li>
                <li><a href="<?= BASE_URL ?>admin/products.php"
                                class="<?= $adminPage === 'products' ? 'active' : '' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                                สินค้า</a></li>
                <li><a href="<?= BASE_URL ?>admin/categories.php"
                                class="<?= $adminPage === 'categories' ? 'active' : '' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                </svg>
                                หมวดหมู่</a></li>
                <li><a href="<?= BASE_URL ?>admin/blog.php" class="<?= $adminPage === 'blog' ? 'active' : '' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25H5.625a2.25 2.25 0 01-2.25-2.25V6.375c0-.621.504-1.125 1.125-1.125H7.5" />
                                </svg>
                                บทความ</a></li>
                <li style="margin-top:auto;border-top:1px solid rgba(255,255,255,0.06);padding-top:12px">
                        <a href="<?= BASE_URL ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                                หน้าเว็บไซต์</a>
                        <a href="<?= BASE_URL ?>profile.php?logout=1" style="color:#EF4444">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                </svg>
                                ออกจากระบบ</a>
                </li>
        </ul>
</div>