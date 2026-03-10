<?php
require_once __DIR__ . '/init.php';
$catSlug = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 12;
$options = ['limit' => $perPage, 'offset' => ($page - 1) * $perPage, 'sort' => $sort];
$countOpts = [];
if ($catSlug) {
    $cat = getCategoryBySlug($catSlug);
    if ($cat) {
        $options['category_id'] = $cat['id'];
        $countOpts['category_id'] = $cat['id'];
    }
}
if ($search) {
    $options['search'] = $search;
    $countOpts['search'] = $search;
}
if (!empty($_GET['min_price'])) {
    $options['min_price'] = $_GET['min_price'];
    $countOpts['min_price'] = $_GET['min_price'];
}
if (!empty($_GET['max_price'])) {
    $options['max_price'] = $_GET['max_price'];
    $countOpts['max_price'] = $_GET['max_price'];
}
$products = getProducts($options);
$total = countProducts($countOpts);
$totalPages = ceil($total / $perPage);
$categories = getCategories();
$pageTitle = $cat['name'] ?? ($search ? "ค้นหา: $search" : 'สินค้าทั้งหมด');
$currentPage = 'products';
include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="bg-gradient-to-r from-elite-150 to-elite-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl lg:text-3xl font-serif font-bold text-elite-800 italic"><?= sanitize($pageTitle) ?></h1>
        <div class="flex items-center gap-2 mt-2 text-sm text-elite-500">
            <a href="<?= BASE_URL ?>" class="hover:text-elite-700">หน้าแรก</a>
            <span>/</span>
            <span class="text-elite-700"><?= sanitize($pageTitle) ?></span>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid md:grid-cols-[240px_1fr] lg:grid-cols-[260px_1fr] gap-6 md:gap-8">
        <!-- Sidebar -->
        <aside>
            <!-- Mobile Filter Toggle -->
            <button onclick="this.nextElementSibling.classList.toggle('hidden');this.querySelector('span').textContent=this.nextElementSibling.classList.contains('hidden')?'แสดงตัวกรอง':'ซ่อนตัวกรอง'" class="md:hidden w-full flex items-center justify-between px-4 py-3 bg-white rounded-xl border border-elite-200/50 text-sm font-semibold text-elite-700 mb-3">
                <span>แสดงตัวกรอง</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" /></svg>
            </button>
            <div class="hidden md:block space-y-4 md:space-y-6">
            <form method="GET" action="products.php" class="space-y-6">
                <div class="bg-white rounded-2xl p-5 border border-elite-200/50 shadow-sm">
                    <h3 class="font-bold text-sm text-elite-700 mb-3">🔍 ค้นหา</h3>
                    <input type="text" name="search" placeholder="ค้นหาสินค้า..." value="<?= sanitize($search) ?>"
                        class="w-full px-4 py-2.5 rounded-xl border border-elite-200 text-sm focus:border-elite-500 focus:ring-2 focus:ring-elite-500/10 outline-none transition">
                </div>
                <div class="bg-white rounded-2xl p-5 border border-elite-200/50 shadow-sm">
                    <h3 class="font-bold text-sm text-elite-700 mb-3">📁 หมวดหมู่</h3>
                    <ul class="space-y-1">
                        <li><a href="<?= BASE_URL ?>products.php"
                                class="block px-3 py-2 rounded-lg text-sm transition-colors <?= !$catSlug ? 'bg-elite-500/10 text-elite-600 font-semibold' : 'text-elite-500 hover:bg-elite-50' ?>">ทั้งหมด</a>
                        </li>
                        <?php foreach ($categories as $c): ?>
                            <li><a href="<?= BASE_URL ?>products.php?category=<?= $c['slug'] ?>"
                                    class="block px-3 py-2 rounded-lg text-sm transition-colors <?= $catSlug === $c['slug'] ? 'bg-elite-500/10 text-elite-600 font-semibold' : 'text-elite-500 hover:bg-elite-50' ?>"><?= $c['name'] ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-elite-200/50 shadow-sm">
                    <h3 class="font-bold text-sm text-elite-700 mb-3">💰 ช่วงราคา</h3>
                    <div class="flex items-center gap-2">
                        <input type="number" name="min_price" placeholder="฿ ต่ำสุด"
                            value="<?= $_GET['min_price'] ?? '' ?>"
                            class="w-full px-3 py-2 rounded-lg border border-elite-200 text-sm outline-none focus:border-elite-500">
                        <span class="text-elite-300">-</span>
                        <input type="number" name="max_price" placeholder="฿ สูงสุด"
                            value="<?= $_GET['max_price'] ?? '' ?>"
                            class="w-full px-3 py-2 rounded-lg border border-elite-200 text-sm outline-none focus:border-elite-500">
                    </div>
                    <?php if ($catSlug): ?><input type="hidden" name="category" value="<?= $catSlug ?>"><?php endif; ?>
                    <button type="submit"
                        class="w-full mt-3 px-4 py-2.5 bg-elite-600 text-white rounded-xl text-sm font-semibold hover:bg-elite-700 transition-colors">กรองราคา</button>
                </div>
            </form>
            </div>
        </aside>

        <!-- Products -->
        <div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6 bg-white rounded-xl px-4 md:px-5 py-3 border border-elite-200/50">
                <span class="text-sm text-elite-500">พบ <b class="text-elite-800"><?= $total ?></b> รายการ</span>
                <select onchange="window.location.href=this.value"
                    class="text-sm border border-elite-200 rounded-lg px-3 py-2 outline-none focus:border-elite-500 bg-white">
                    <?php foreach (['newest' => 'ใหม่ล่าสุด', 'popular' => 'ยอดนิยม', 'price_asc' => 'ราคาต่ำ-สูง', 'price_desc' => 'ราคาสูง-ต่ำ', 'name' => 'ชื่อ A-Z'] as $k => $v): ?>
                        <option value="?<?= http_build_query(array_merge($_GET, ['sort' => $k])) ?>"
                            <?= $sort === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (empty($products)): ?>
                <div class="text-center py-20">
                    <div class="text-5xl mb-4">🔍</div>
                    <h3 class="text-xl font-bold text-elite-800 mb-2">ไม่พบสินค้า</h3>
                    <p class="text-elite-400">ลองค้นหาด้วยคำค้นอื่นหรือเลือกหมวดหมู่อื่น</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                    <?php foreach ($products as $p): ?>
                        <a href="<?= BASE_URL ?>product.php?slug=<?= $p['slug'] ?>"
                            class="group bg-white rounded-2xl border border-elite-200/60 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 block">
                            <div class="relative overflow-hidden aspect-[4/3]">
                                <img src="<?= getProductImageUrl($p['primary_image']) ?>" alt="<?= $p['name'] ?>"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy"
                                    onerror="this.src='https://images.unsplash.com/photo-1545249390-6bdfa286032f?w=400&h=260&fit=crop'">
                                <div
                                    class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex justify-center">
                                    <span class="px-4 py-2 bg-white/90 rounded-full text-elite-700 text-xs font-semibold">ดูรายละเอียด →</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="text-sm text-elite-500 font-semibold mb-1"><?= $p['category_name'] ?? '' ?></div>
                                <h3 class="font-semibold text-elite-800 mb-2 leading-snug line-clamp-2"><?= $p['name'] ?></h3>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-elite-700"><?= formatPrice($p['sale_price'] ?: $p['price']) ?></span>
                                    <?php if ($p['sale_price']): ?><span
                                            class="text-sm text-elite-300 line-through"><?= formatPrice($p['price']) ?></span><?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="flex justify-center gap-2 mt-8">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span
                                    class="w-10 h-10 rounded-xl bg-elite-600 text-white flex items-center justify-center font-semibold text-sm"><?= $i ?></span>
                            <?php else: ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                                    class="w-10 h-10 rounded-xl bg-white border border-elite-200 text-elite-700 flex items-center justify-center font-semibold text-sm hover:bg-elite-50 transition-colors"><?= $i ?></a>
                            <?php endif; endfor; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>