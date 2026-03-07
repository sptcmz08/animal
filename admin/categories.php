<?php
require_once __DIR__ . '/../init.php';
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
$adminPage = 'categories';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $slug = generateSlug($_POST['name']) ?: 'cat-' . time();
        $pdo->prepare("INSERT INTO categories (name, slug, description, sort_order) VALUES (?, ?, ?, ?)")->execute([$_POST['name'], $slug, $_POST['description'], $_POST['sort_order']]);
        $catId = $pdo->lastInsertId();
        if (!empty($_FILES['image']['tmp_name'])) {
            $path = uploadImage($_FILES['image'], 'categories');
            if ($path)
                $pdo->prepare("UPDATE categories SET image = ? WHERE id = ?")->execute([$path, $catId]);
        }
        $msg = 'เพิ่มหมวดหมู่เรียบร้อย';
    } elseif ($action === 'update') {
        $id = (int) $_POST['id'];
        $slug = generateSlug($_POST['name']) ?: 'cat-' . time();
        $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, sort_order = ? WHERE id = ?")
            ->execute([$_POST['name'], $slug, $_POST['description'], $_POST['sort_order'], $id]);
        if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === 0) {
            $path = uploadImage($_FILES['image'], 'categories');
            if ($path)
                $pdo->prepare("UPDATE categories SET image = ? WHERE id = ?")->execute([$path, $id]);
        }
        $msg = 'แก้ไขหมวดหมู่เรียบร้อย';
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$_POST['id']]);
        $msg = 'ลบหมวดหมู่เรียบร้อย';
    }
}
$categories = getCategories(false);
$catCounts = $pdo->query("SELECT category_id, COUNT(*) as cnt FROM products WHERE status='active' GROUP BY category_id")->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการหมวดหมู่ | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
    <style>
        .cat-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e8e0d0;
        }

        .edit-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #e8e0d0;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-tags me-2"></i>จัดการหมวดหมู่ <span
                    class="badge bg-secondary rounded-pill"><?= count($categories) ?></span></h4>
            <button class="btn btn-paw" data-bs-toggle="modal" data-bs-target="#catModal"><i
                    class="bi bi-plus-lg me-1"></i>เพิ่มหมวดหมู่</button>
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
                            <th>ชื่อหมวดหมู่</th>
                            <th>จำนวนสินค้า</th>
                            <th>ลำดับ</th>
                            <th style="width:140px">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $c):
                            $imgUrl = !empty($c['image']) ? UPLOAD_URL . $c['image'] : '';
                            ?>
                            <tr>
                                <td>
                                    <?php if ($imgUrl): ?>
                                        <img src="<?= $imgUrl ?>" class="cat-thumb" onerror="this.innerHTML='🐾'">
                                    <?php else: ?>
                                        <div class="cat-thumb d-flex align-items-center justify-content-center bg-light">🐾
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="fw-semibold"><?= $c['name'] ?></span><br><small
                                        class="text-muted"><?= $c['slug'] ?></small></td>
                                <td><span class="badge bg-light text-dark"><?= $catCounts[$c['id']] ?? 0 ?> รายการ</span>
                                </td>
                                <td><?= $c['sort_order'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                        onclick="editCat(<?= htmlspecialchars(json_encode($c), ENT_QUOTES) ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('ยืนยันลบหมวดหมู่นี้?')">
                                        <input type="hidden" name="action" value="delete"><input type="hidden" name="id"
                                            value="<?= $c['id'] ?>"><button class="btn btn-sm btn-outline-danger"><i
                                                class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="catModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มหมวดหมู่</h5><button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3"><label class="form-label fw-semibold">ชื่อหมวดหมู่ *</label><input type="text"
                                name="name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label fw-semibold">คำอธิบาย</label><textarea
                                name="description" class="form-control" rows="3"></textarea></div>
                        <div class="row g-3">
                            <div class="col-6"><label class="form-label fw-semibold">ลำดับ</label><input type="number"
                                    name="sort_order" class="form-control" value="0"></div>
                            <div class="col-6"><label class="form-label fw-semibold">รูปภาพ</label><input type="file"
                                    name="image" class="form-control" accept="image/*"></div>
                        </div>
                        <div class="mt-4"><button type="submit" class="btn btn-paw w-100"><i
                                    class="bi bi-check-lg me-1"></i>บันทึก</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCatModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>แก้ไขหมวดหมู่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="editId">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ชื่อหมวดหมู่ *</label>
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">คำอธิบาย</label>
                            <textarea name="description" id="editDesc" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">ลำดับ</label>
                                <input type="number" name="sort_order" id="editOrder" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">เปลี่ยนรูปภาพ</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div id="editImgPreview" class="mt-3 text-center" style="display:none">
                            <p class="text-muted small mb-1">รูปปัจจุบัน:</p>
                            <img id="editImgSrc" class="edit-preview" onerror="this.parentElement.style.display='none'">
                        </div>
                        <div class="mt-4"><button type="submit" class="btn btn-paw w-100"><i
                                    class="bi bi-check-lg me-1"></i>บันทึก</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editCat(cat) {
            document.getElementById('editId').value = cat.id;
            document.getElementById('editName').value = cat.name;
            document.getElementById('editDesc').value = cat.description || '';
            document.getElementById('editOrder').value = cat.sort_order || 0;
            var preview = document.getElementById('editImgPreview');
            if (cat.image) {
                document.getElementById('editImgSrc').src = '<?= UPLOAD_URL ?>' + cat.image;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
            new bootstrap.Modal(document.getElementById('editCatModal')).show();
        }
    </script>
</body>

</html>