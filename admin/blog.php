<?php
require_once __DIR__ . '/../init.php';
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
$adminPage = 'blog';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $slug = generateSlug($_POST['title']) ?: 'post-' . time();
        $pdo->prepare("INSERT INTO blog_posts (title, slug, excerpt, content, author_id, status) VALUES (?, ?, ?, ?, ?, ?)")->execute([$_POST['title'], $slug, $_POST['excerpt'], $_POST['content'], $_SESSION['user_id'], $_POST['status']]);
        $postId = $pdo->lastInsertId();
        if (!empty($_FILES['image']['tmp_name'])) {
            $path = uploadImage($_FILES['image'], 'blog');
            if ($path)
                $pdo->prepare("UPDATE blog_posts SET image = ? WHERE id = ?")->execute([$path, $postId]);
        }
        $msg = 'เพิ่มบทความเรียบร้อย';
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([$_POST['id']]);
        $msg = 'ลบบทความเรียบร้อย';
    }
}
$posts = $pdo->query("SELECT bp.*, u.name as author_name FROM blog_posts bp LEFT JOIN users u ON bp.author_id = u.id ORDER BY bp.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการบทความ | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
</head>

<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-journal-text me-2"></i>จัดการบทความ <span
                    class="badge bg-secondary rounded-pill"><?= count($posts) ?></span></h4>
            <button class="btn btn-paw" data-bs-toggle="modal" data-bs-target="#blogModal"><i
                    class="bi bi-plus-lg me-1"></i>เพิ่มบทความ</button>
        </div>
        <?php if ($msg): ?>
            <div class="alert alert-success alert-dismissible fade show"><i
                    class="bi bi-check-circle me-1"></i><?= $msg ?><button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button></div><?php endif; ?>

        <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden">
            <div class="table-responsive">
                <table class="table table-admin table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:60px">รูป</th>
                            <th>หัวข้อ</th>
                            <th>ผู้เขียน</th>
                            <th>สถานะ</th>
                            <th>วันที่</th>
                            <th style="width:80px">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $p): ?>
                            <tr>
                                <td><img src="<?= getProductImageUrl($p['image'] ?? '') ?>" class="thumb"
                                        onerror="this.src='https://via.placeholder.com/45'"></td>
                                <td class="fw-semibold"><?= $p['title'] ?></td>
                                <td class="text-muted"><?= $p['author_name'] ?? '-' ?></td>
                                <td><?= $p['status'] === 'published' ? '<span class="badge bg-success">Published</span>' : '<span class="badge bg-secondary">Draft</span>' ?>
                                </td>
                                <td class="text-muted"><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                                <td>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('ยืนยันลบ?')"><input
                                            type="hidden" name="action" value="delete"><input type="hidden" name="id"
                                            value="<?= $p['id'] ?>"><button class="btn btn-sm btn-outline-danger"><i
                                                class="bi bi-trash"></i></button></form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="blogModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มบทความ</h5><button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3"><label class="form-label fw-semibold">หัวข้อ *</label><input type="text"
                                name="title" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label fw-semibold">เนื้อหาย่อ</label><textarea
                                name="excerpt" class="form-control" rows="2"></textarea></div>
                        <div class="mb-3"><label class="form-label fw-semibold">เนื้อหา</label><textarea name="content"
                                class="form-control" rows="6"></textarea></div>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label fw-semibold">สถานะ</label><select
                                    name="status" class="form-select">
                                    <option value="published">Published</option>
                                    <option value="draft">Draft</option>
                                </select></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">รูปภาพ</label><input type="file"
                                    name="image" class="form-control" accept="image/*"></div>
                        </div>
                        <div class="mt-4"><button type="submit" class="btn btn-paw w-100"><i
                                    class="bi bi-check-lg me-1"></i>บันทึก</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>