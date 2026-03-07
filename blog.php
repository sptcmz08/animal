<?php
require_once __DIR__ . '/init.php';
$pageTitle = 'บทความ';
$currentPage = 'blog';
$posts = $pdo->query("SELECT bp.*, u.name as author_name FROM blog_posts bp LEFT JOIN users u ON bp.author_id = u.id WHERE bp.status = 'published' ORDER BY bp.created_at DESC")->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<div class="bg-gradient-to-r from-paw-100 to-paw-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl lg:text-3xl font-extrabold text-paw-700">📝 บทความ</h1>
        <p class="text-paw-700/50 mt-1">เคล็ดลับ และข้อมูลดีๆ สำหรับคนรักสัตว์เลี้ยง</p>
    </div>
</div>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <?php if (empty($posts)): ?>
        <div class="text-center py-20">
            <div class="text-5xl mb-4">📝</div>
            <h3 class="text-xl font-bold text-paw-700">ยังไม่มีบทความ</h3>
        </div>
    <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($posts as $p): ?>
                <a href="<?= BASE_URL ?>blog-post.php?slug=<?= urlencode($p['slug']) ?>"
                    class="group bg-white rounded-2xl border border-elite-200/50 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 block">
                    <div class="aspect-[16/9] overflow-hidden">
                        <img src="<?= getProductImageUrl($p['image'] ?? '') ?>" alt="<?= $p['title'] ?>"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            onerror="this.src='https://images.unsplash.com/photo-1543852786-1cf6624b9987?w=600&h=340&fit=crop'">
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-3 text-xs text-elite-400 mb-3">
                            <span><?= $p['author_name'] ?? 'Admin' ?></span>
                            <span>•</span>
                            <span><?= date('d/m/Y', strtotime($p['created_at'])) ?></span>
                        </div>
                        <h3 class="font-bold text-lg text-elite-700 mb-2 line-clamp-2 group-hover:text-elite-500 transition-colors">
                            <?= $p['title'] ?></h3>
                        <p class="text-sm text-elite-500/50 line-clamp-3 mb-3">
                            <?= $p['excerpt'] ?: mb_substr(strip_tags($p['content']), 0, 120) . '...' ?></p>
                        <span class="text-xs font-semibold text-elite-500">อ่านเพิ่มเติม →</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>