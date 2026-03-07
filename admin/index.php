<?php
require_once __DIR__ . '/../init.php';
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
$adminPage = 'dashboard';
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalBlogPosts = $pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
$totalMessages = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$recentMessages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Elite Pet Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
</head>

<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-speedometer2 me-2"></i>แดชบอร์ด</h4>
            <span class="text-muted">ยินดีต้อนรับ, <?= $_SESSION['user_name'] ?></span>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="card stat-card p-3">
                    <div class="stat-icon mb-2" style="background:rgba(139,105,20,0.1)"><i class="bi bi-box-seam fs-5"
                            style="color:#8B6914"></i></div>
                    <div class="fs-2 fw-bold"><?= $totalProducts ?></div>
                    <div class="text-muted small">สินค้าทั้งหมด</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card stat-card p-3">
                    <div class="stat-icon mb-2" style="background:rgba(59,130,246,0.1)"><i class="bi bi-tags fs-5"
                            style="color:#3B82F6"></i></div>
                    <div class="fs-2 fw-bold"><?= $totalCategories ?></div>
                    <div class="text-muted small">หมวดหมู่</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card stat-card p-3">
                    <div class="stat-icon mb-2" style="background:rgba(16,185,129,0.1)"><i
                            class="bi bi-file-text fs-5" style="color:#10B981"></i></div>
                    <div class="fs-2 fw-bold"><?= $totalBlogPosts ?></div>
                    <div class="text-muted small">บทความ</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card stat-card p-3">
                    <div class="stat-icon mb-2" style="background:rgba(139,92,246,0.1)"><i class="bi bi-envelope fs-5"
                            style="color:#8B5CF6"></i></div>
                    <div class="fs-2 fw-bold"><?= $totalMessages ?></div>
                    <div class="text-muted small">ข้อความติดต่อ</div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden">
            <div class="card-header bg-white fw-bold py-3"><i class="bi bi-envelope me-2"></i>ข้อความติดต่อล่าสุด
            </div>
            <div class="table-responsive">
                <table class="table table-admin table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ชื่อ</th>
                            <th>อีเมล</th>
                            <th>หัวข้อ</th>
                            <th>วันที่</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentMessages as $m): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($m['name']) ?></td>
                                <td><?= htmlspecialchars($m['email'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($m['subject'] ?? '-') ?></td>
                                <td class="text-muted"><?= timeAgo($m['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentMessages)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">ยังไม่มีข้อความ</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>