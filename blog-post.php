<?php
require_once __DIR__ . '/init.php';
$currentPage = 'blog';

$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: ' . BASE_URL . 'blog.php'); exit; }

$post = getBlogPost($slug);
if (!$post) { header('Location: ' . BASE_URL . 'blog.php'); exit; }

// Update view count
$pdo->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?")->execute([$post['id']]);

$pageTitle = $post['title'];

// Get related posts
$relatedStmt = $pdo->prepare("SELECT bp.*, u.name as author_name FROM blog_posts bp LEFT JOIN users u ON bp.author_id = u.id WHERE bp.status = 'published' AND bp.id != ? ORDER BY bp.created_at DESC LIMIT 3");
$relatedStmt->execute([$post['id']]);
$relatedPosts = $relatedStmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-gradient-to-r from-elite-150 to-elite-50 py-4">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-sm text-elite-400">
            <a href="<?= BASE_URL ?>" class="hover:text-elite-600 transition-colors">หน้าแรก</a>
            <span>›</span>
            <a href="<?= BASE_URL ?>blog.php" class="hover:text-elite-600 transition-colors">บทความ</a>
            <span>›</span>
            <span class="text-elite-600 font-medium truncate max-w-[200px]"><?= htmlspecialchars($post['title']) ?></span>
        </nav>
    </div>
</div>

<!-- Article Content -->
<article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Cover Image -->
    <?php if (!empty($post['image'])): ?>
        <div class="aspect-[21/9] rounded-2xl overflow-hidden mb-8 shadow-lg">
            <img src="<?= getProductImageUrl($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>"
                class="w-full h-full object-cover"
                onerror="this.src='https://images.unsplash.com/photo-1543852786-1cf6624b9987?w=1200&h=500&fit=crop'">
        </div>
    <?php endif; ?>

    <!-- Article Header -->
    <header class="mb-8">
        <h1 class="text-2xl lg:text-4xl font-serif font-bold text-elite-800 italic leading-tight mb-4">
            <?= htmlspecialchars($post['title']) ?>
        </h1>

        <div class="flex flex-wrap items-center gap-4 text-sm text-elite-400">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-elite-200 flex items-center justify-center text-elite-600 font-bold text-xs">
                    <?= mb_substr($post['author_name'] ?? 'A', 0, 1) ?>
                </div>
                <span class="font-medium text-elite-600"><?= htmlspecialchars($post['author_name'] ?? 'Admin') ?></span>
            </div>
            <span>•</span>
            <span>📅 <?= date('d F Y', strtotime($post['created_at'])) ?></span>
            <span>•</span>
            <span>👁️ <?= number_format($post['views'] + 1) ?> ครั้ง</span>
        </div>
    </header>

    <!-- Excerpt -->
    <?php if (!empty($post['excerpt'])): ?>
        <div class="bg-elite-100/50 border-l-4 border-elite-400 rounded-r-xl p-5 mb-8">
            <p class="text-elite-600 italic text-base leading-relaxed"><?= htmlspecialchars($post['excerpt']) ?></p>
        </div>
    <?php endif; ?>

    <!-- Article Body -->
    <div class="prose prose-lg max-w-none
                prose-headings:text-elite-800 prose-headings:font-serif prose-headings:italic
                prose-p:text-elite-700 prose-p:leading-relaxed
                prose-a:text-elite-500 prose-a:underline
                prose-img:rounded-xl prose-img:shadow-md
                prose-strong:text-elite-800
                prose-ul:text-elite-700 prose-ol:text-elite-700
                prose-li:leading-relaxed
                mb-12"
        style="font-size: 1.05rem; line-height: 1.85;">
        <?= $post['content'] ?>
    </div>

    <!-- Share / Back -->
    <div class="flex items-center justify-between border-t border-elite-200/50 pt-6 mb-12">
        <a href="<?= BASE_URL ?>blog.php"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-elite-100 text-elite-600 rounded-xl font-semibold text-sm hover:bg-elite-200 transition-colors">
            ← กลับไปหน้าบทความ
        </a>
    </div>
</article>

<!-- Related Posts -->
<?php if (!empty($relatedPosts)): ?>
    <section class="bg-gradient-to-b from-elite-50 to-elite-100/50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-serif font-bold text-elite-800 italic mb-6">📖 บทความอื่นที่น่าสนใจ</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <?php foreach ($relatedPosts as $rp): ?>
                    <a href="<?= BASE_URL ?>blog-post.php?slug=<?= urlencode($rp['slug']) ?>"
                        class="group bg-white rounded-2xl border border-elite-200/50 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="aspect-[16/9] overflow-hidden">
                            <img src="<?= getProductImageUrl($rp['image'] ?? '') ?>" alt="<?= htmlspecialchars($rp['title']) ?>"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                onerror="this.src='https://images.unsplash.com/photo-1543852786-1cf6624b9987?w=600&h=340&fit=crop'">
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-3 text-xs text-elite-400 mb-2">
                                <span><?= $rp['author_name'] ?? 'Admin' ?></span>
                                <span>•</span>
                                <span><?= date('d/m/Y', strtotime($rp['created_at'])) ?></span>
                            </div>
                            <h3 class="font-bold text-elite-700 mb-1 line-clamp-2 group-hover:text-elite-500 transition-colors">
                                <?= htmlspecialchars($rp['title']) ?>
                            </h3>
                            <span class="text-xs font-semibold text-elite-500">อ่านเพิ่มเติม →</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
