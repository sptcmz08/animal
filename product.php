<?php
require_once __DIR__ . '/init.php';
$slug = $_GET['slug'] ?? '';
$product = getProductBySlug($slug);
if (!$product) {
    header('Location: ' . BASE_URL . 'products.php');
    exit;
}
$pdo->prepare("UPDATE products SET views = views + 1 WHERE id = ?")->execute([$product['id']]);
$images = getProductImages($product['id']);
$related = getRelatedProducts($product['id'], $product['category_id']);
$s = getAllSettings();
$g = function ($key, $default = '') use ($s) {
    return $s[$key] ?? $default; };
$pageTitle = $product['name'];
$currentPage = 'product';
include __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-elite-400 mb-6">
        <a href="<?= BASE_URL ?>" class="hover:text-elite-600">หน้าแรก</a><span>/</span>
        <a href="<?= BASE_URL ?>products.php?category=<?= $product['category_slug'] ?>"
            class="hover:text-elite-600"><?= $product['category_name'] ?></a><span>/</span>
        <span class="text-elite-700"><?= $product['name'] ?></span>
    </div>

    <div class="grid lg:grid-cols-2 gap-10">
        <!-- Gallery -->
        <div>
            <div
                class="bg-white rounded-2xl overflow-hidden border border-elite-200/50 aspect-square flex items-center justify-center">
                <img src="<?= getProductImageUrl($images[0]['image_path'] ?? '') ?>" alt="<?= $product['name'] ?>"
                    id="mainImage" class="w-full h-full object-cover"
                    onerror="this.src='https://images.unsplash.com/photo-1545249390-6bdfa286032f?w=600&h=500&fit=crop'">
            </div>
            <?php if (count($images) > 1): ?>
                <div class="flex gap-3 mt-4">
                    <?php foreach ($images as $i => $img): ?>
                        <button onclick="changeImage(this, '<?= getProductImageUrl($img['image_path']) ?>')"
                            class="w-20 h-20 rounded-xl overflow-hidden border-2 transition-colors gallery-thumb <?= $i === 0 ? 'border-elite-500' : 'border-elite-200 hover:border-elite-400' ?>">
                            <img src="<?= getProductImageUrl($img['image_path']) ?>" class="w-full h-full object-cover"
                                onerror="this.src='https://via.placeholder.com/80'">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Info -->
        <div>
            <h1 class="text-2xl lg:text-3xl font-serif font-bold text-elite-800 mb-3 italic"><?= $product['name'] ?>
            </h1>

            <?php if ($rating['count'] > 0): ?>
                <div class="flex items-center gap-2 mb-4">
                    <span
                        class="text-yellow-400"><?= str_repeat('★', round($rating['avg_rating'])) . str_repeat('☆', 5 - round($rating['avg_rating'])) ?></span>
                    <span class="text-sm text-elite-400">(<?= $rating['count'] ?> รีวิว)</span>
                </div>
            <?php endif; ?>

            <div class="flex items-center gap-3 mb-5">
                <span
                    class="text-3xl font-extrabold text-elite-700"><?= formatPrice($product['sale_price'] ?: $product['price']) ?></span>
                <?php if ($product['sale_price']): ?>
                    <span class="text-lg text-elite-300 line-through"><?= formatPrice($product['price']) ?></span>
                    <span class="bg-red-500/10 text-red-500 text-sm font-bold px-3 py-1 rounded-lg">ประหยัด
                        <?= formatPrice($product['price'] - $product['sale_price']) ?></span>
                <?php endif; ?>
            </div>

            <p class="text-elite-500 leading-relaxed mb-6"><?= $product['short_description'] ?></p>

            <!-- Features -->
            <div class="bg-elite-50 rounded-2xl p-5 mb-6">
                <div class="space-y-2.5">
                    <div class="flex items-center gap-3 text-sm text-elite-600"><span class="text-lg">📂</span>
                        หมวดหมู่: <span class="font-semibold"><?= $product['category_name'] ?></span></div>
                    <div class="flex items-center gap-3 text-sm text-elite-600"><span class="text-lg">✨</span>
                        สั่งทำพิเศษตามขนาดและดีไซน์ที่ต้องการ</div>
                    <div class="flex items-center gap-3 text-sm text-elite-600"><span class="text-lg">🚚</span>
                        จัดส่งทั่วประเทศ</div>
                    <div class="flex items-center gap-3 text-sm text-elite-600"><span class="text-lg">🛡️</span>
                        รับประกันคุณภาพงานฝีมือ</div>
                </div>
            </div>

            <!-- Contact Social Links -->
            <div class="bg-elite-50 rounded-2xl p-5">
                <h3 class="text-sm font-bold text-elite-700 uppercase tracking-wide mb-3">💬 สนใจสินค้า? ติดต่อเราได้เลย
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <a href="<?= $g('social_line_url', '#') ?>" target="_blank"
                        class="flex items-center gap-3 px-5 py-3.5 bg-[#06C755] text-white rounded-xl font-semibold hover:brightness-110 transition-all shadow-lg shadow-[#06C755]/20">
                        <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M24 10.304c0-5.369-5.383-9.738-12-9.738-6.616 0-12 4.369-12 9.738 0 4.814 4.269 8.846 10.036 9.608.391.084.922.258 1.057.592.121.303.079.778.039 1.085l-.171 1.027c-.053.303-.242 1.186 1.039.647 1.281-.54 6.911-4.069 9.428-6.967C23.309 14.254 24 12.382 24 10.304z" />
                        </svg>
                        <span class="text-sm">LINE</span>
                    </a>
                    <a href="<?= $g('social_facebook_url', '#') ?>" target="_blank"
                        class="flex items-center gap-3 px-5 py-3.5 bg-[#1877F2] text-white rounded-xl font-semibold hover:brightness-110 transition-all shadow-lg shadow-[#1877F2]/20">
                        <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                        <span class="text-sm">Facebook</span>
                    </a>
                    <a href="<?= $g('social_instagram_url', '#') ?>" target="_blank"
                        class="flex items-center gap-3 px-5 py-3.5 bg-gradient-to-r from-[#833AB4] via-[#E1306C] to-[#F77737] text-white rounded-xl font-semibold hover:brightness-110 transition-all shadow-lg shadow-[#E1306C]/20">
                        <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                        </svg>
                        <span class="text-sm">Instagram</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Description -->
    <div class="mt-10 bg-white rounded-2xl border border-elite-200/50 p-6 lg:p-8">
        <h2 class="text-xl font-serif font-bold text-elite-800 mb-4 italic">📋 รายละเอียดสินค้า</h2>
        <div class="text-elite-600 leading-relaxed prose max-w-none"><?= $product['description'] ?></div>
    </div>

    <!-- Related -->
    <?php if (!empty($related)): ?>
        <div class="mt-10">
            <h2 class="text-2xl font-serif font-bold text-elite-800 mb-6 italic">สินค้าที่เกี่ยวข้อง</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                <?php foreach ($related as $p): ?>
                    <a href="<?= BASE_URL ?>product.php?slug=<?= $p['slug'] ?>"
                        class="group bg-white rounded-2xl border border-elite-200/60 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 block">
                        <div class="relative overflow-hidden aspect-[4/3]">
                            <img src="<?= getProductImageUrl($p['primary_image']) ?>" alt="<?= $p['name'] ?>"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                onerror="this.src='https://images.unsplash.com/photo-1545249390-6bdfa286032f?w=400&h=260&fit=crop'">
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-elite-800 mb-2 leading-snug line-clamp-2"><?= $p['name'] ?></h3>
                            <span class="font-bold text-elite-700"><?= formatPrice($p['sale_price'] ?: $p['price']) ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function changeImage(el, src) { document.getElementById('mainImage').src = src; document.querySelectorAll('.gallery-thumb').forEach(t => t.className = t.className.replace('border-elite-500', 'border-elite-200')); el.className = el.className.replace('border-elite-200', 'border-elite-500'); }
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>