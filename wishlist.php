<?php
require_once __DIR__ . '/init.php';
if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
$pageTitle = 'รายการโปรด';
$currentPage = 'wishlist';
$wishlists = $pdo->prepare("SELECT p.*, pi.image_path as primary_image, c.name as category_name FROM wishlists w JOIN products p ON w.product_id = p.id LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1 LEFT JOIN categories c ON p.category_id = c.id WHERE w.user_id = ? ORDER BY w.created_at DESC");
$wishlists->execute([$_SESSION['user_id']]);
$wishlists = $wishlists->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<div class="bg-gradient-to-r from-paw-100 to-paw-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl lg:text-3xl font-extrabold text-paw-700">❤️ รายการโปรด</h1>
    </div>
</div>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <?php if (empty($wishlists)): ?>
        <div class="text-center py-20">
            <div class="text-5xl mb-4">❤️</div>
            <h3 class="text-xl font-bold text-paw-700 mb-2">ยังไม่มีรายการโปรด</h3>
            <p class="text-paw-700/50 mb-6">กดหัวใจที่สินค้าเพื่อเพิ่มลงรายการโปรด</p><a href="<?= BASE_URL ?>products.php"
                class="inline-flex items-center gap-2 px-6 py-3 bg-paw-500 text-white rounded-xl font-semibold hover:bg-paw-600 transition-colors">ไปช้อปเลย</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            <?php foreach ($wishlists as $p): ?>
                <div
                    class="group bg-white rounded-2xl border border-paw-200/60 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="relative overflow-hidden aspect-[4/3]">
                        <a href="<?= BASE_URL ?>product.php?slug=<?= $p['slug'] ?>"><img
                                src="<?= getProductImageUrl($p['primary_image']) ?>" alt="<?= $p['name'] ?>"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                onerror="this.src='https://images.unsplash.com/photo-1545249390-6bdfa286032f?w=400&h=260&fit=crop'"></a>
                        <button onclick="toggleWishlist(<?= $p['id'] ?>);this.closest('.group').remove()"
                            class="absolute top-3 right-3 w-9 h-9 bg-white rounded-full flex items-center justify-center text-red-500 shadow-md hover:bg-red-500 hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                        </button>
                    </div>
                    <div class="p-4">
                        <div class="text-xs text-paw-500 font-semibold mb-1"><?= $p['category_name'] ?? '' ?></div>
                        <h3 class="font-semibold text-paw-700 mb-2 leading-snug line-clamp-2"><a
                                href="<?= BASE_URL ?>product.php?slug=<?= $p['slug'] ?>"
                                class="hover:text-paw-500"><?= $p['name'] ?></a></h3>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-paw-700"><?= formatPrice($p['sale_price'] ?: $p['price']) ?></span>
                            <button onclick="addToCart(<?= $p['id'] ?>)"
                                class="w-9 h-9 bg-paw-500/10 text-paw-500 rounded-lg flex items-center justify-center hover:bg-paw-500 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121 0 2.09-.773 2.34-1.872l1.553-6.832c.136-.597-.27-1.171-.883-1.171H6.106l-.468-1.75" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>