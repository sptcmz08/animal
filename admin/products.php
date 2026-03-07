<?php
require_once __DIR__ . '/../init.php';
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
$adminPage = 'products';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create' || $action === 'update') {
        $data = [$_POST['category_id'], $_POST['name'], generateSlug($_POST['name']), $_POST['sku'], $_POST['price'], $_POST['sale_price'] ?: null, $_POST['short_description'], $_POST['description'], $_POST['stock'], isset($_POST['featured']) ? 1 : 0, $_POST['status']];
        if ($action === 'create') {
            $pdo->prepare("INSERT INTO products (category_id,name,slug,sku,price,sale_price,short_description,description,stock,featured,status) VALUES (?,?,?,?,?,?,?,?,?,?,?)")->execute($data);
            $productId = $pdo->lastInsertId();
            $msg = 'เพิ่มสินค้าเรียบร้อย';
        } else {
            $data[] = $_POST['id'];
            $productId = $_POST['id'];
            $pdo->prepare("UPDATE products SET category_id=?,name=?,slug=?,sku=?,price=?,sale_price=?,short_description=?,description=?,stock=?,featured=?,status=? WHERE id=?")->execute($data);
            $msg = 'แก้ไขสินค้าเรียบร้อย';
        }
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
                if ($_FILES['images']['error'][$i] === 0) {
                    $path = uploadImage(['name' => $_FILES['images']['name'][$i], 'tmp_name' => $tmp]);
                    if ($path) {
                        $pdo->prepare("INSERT INTO product_images (product_id, image_path, is_primary) VALUES (?, ?, ?)")->execute([$productId, $path, $i === 0 && $action === 'create' ? 1 : 0]);
                    }
                }
            }
        }
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$_POST['id']]);
        $msg = 'ลบสินค้าเรียบร้อย';
    }
}
$products = $pdo->query("SELECT p.*, c.name as category_name, pi.image_path as primary_image FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1 ORDER BY p.created_at DESC")->fetchAll();
$categories = getCategories(false);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสินค้า | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
</head>

<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>จัดการสินค้า <span
                    class="badge bg-secondary rounded-pill"><?= count($products) ?></span></h4>
            <button class="btn btn-paw" data-bs-toggle="modal" data-bs-target="#productModal" onclick="setCreate()"><i
                    class="bi bi-plus-lg me-1"></i>เพิ่มสินค้า</button>
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
                            <th>ชื่อสินค้า</th>
                            <th>หมวดหมู่</th>
                            <th>ราคา</th>
                            <th>สต็อก</th>
                            <th>สถานะ</th>
                            <th style="width:140px">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td><img src="<?= getProductImageUrl($p['primary_image']) ?>" class="thumb"
                                        onerror="this.src='https://via.placeholder.com/45'"></td>
                                <td class="fw-semibold"><?= $p['name'] ?><?= $p['featured'] ? ' ⭐' : '' ?></td>
                                <td><span class="text-muted"><?= $p['category_name'] ?? '-' ?></span></td>
                                <td class="text-nowrap"><?php if ($p['sale_price']): ?><del
                                            class="text-muted small"><?= formatPrice($p['price']) ?></del> <span
                                            class="text-danger fw-semibold"><?= formatPrice($p['sale_price']) ?></span><?php else:
                                    echo formatPrice($p['price']); endif; ?>
                                </td>
                                <td><?= $p['stock'] ?></td>
                                <td><?= $p['status'] === 'active' ? '<span class="badge bg-success-subtle text-success">Active</span>' : '<span class="badge bg-secondary-subtle text-secondary">' . $p['status'] . '</span>' ?>
                                </td>
                                <td class="text-nowrap">
                                    <button class="btn btn-sm btn-outline-paw"
                                        onclick='editProduct(<?= json_encode($p) ?>)'><i class="bi bi-pencil"></i></button>
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

    <!-- Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">เพิ่มสินค้า</h5><button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data" id="productForm">
                        <input type="hidden" name="action" id="formAction" value="create"><input type="hidden" name="id"
                            id="formId">
                        <div class="row g-3">
                            <div class="col-md-8"><label class="form-label fw-semibold">ชื่อสินค้า *</label><input
                                    type="text" name="name" id="fName" class="form-control" required></div>
                            <div class="col-md-4"><label class="form-label fw-semibold">SKU</label><input type="text"
                                    name="sku" id="fSku" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">หมวดหมู่</label><select
                                    name="category_id" id="fCategory"
                                    class="form-select"><?php foreach ($categories as $c): ?>
                                        <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option><?php endforeach; ?>
                                </select></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">สถานะ</label><select
                                    name="status" id="fStatus" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="draft">Draft</option>
                                </select></div>
                            <div class="col-md-4"><label class="form-label fw-semibold">ราคา *</label><input
                                    type="number" name="price" id="fPrice" class="form-control" required></div>
                            <div class="col-md-4"><label class="form-label fw-semibold">ราคาลด</label><input
                                    type="number" name="sale_price" id="fSalePrice" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label fw-semibold">สต็อก</label><input
                                    type="number" name="stock" id="fStock" class="form-control" value="0"></div>
                            <div class="col-12">
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="featured"
                                        id="fFeatured"><label class="form-check-label" for="fFeatured">สินค้าแนะนำ
                                        ⭐</label></div>
                            </div>
                            <div class="col-12"><label class="form-label fw-semibold">รายละเอียดสั้น</label><textarea
                                    name="short_description" id="fShortDesc" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12"><label class="form-label fw-semibold">รายละเอียด</label><textarea
                                    name="description" id="fDesc" class="form-control" rows="4"></textarea></div>
                            <div class="col-12"><label class="form-label fw-semibold">รูปภาพ</label><input type="file"
                                    name="images[]" class="form-control" multiple accept="image/*"></div>
                        </div>
                        <div class="mt-4"><button type="submit" class="btn btn-paw w-100"><i
                                    class="bi bi-check-lg me-1"></i>บันทึก</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Init TinyMCE
        tinymce.init({
            selector: '#fDesc',
            height: 300,
            menubar: false,
            plugins: 'lists link image code table',
            toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link image table | removeformat code',
            content_style: 'body { font-family: Prompt, sans-serif; font-size: 14px; }',
            language: 'th_TH',
            branding: false,
            promotion: false,
            license_key: 'gpl'
        });

        function setCreate() {
            document.getElementById('formAction').value = 'create';
            document.getElementById('modalTitle').textContent = 'เพิ่มสินค้า';
            document.getElementById('productForm').reset();
            if (tinymce.get('fDesc')) tinymce.get('fDesc').setContent('');
        }
        function editProduct(p) {
            document.getElementById('formAction').value = 'update';
            document.getElementById('modalTitle').textContent = 'แก้ไขสินค้า';
            document.getElementById('formId').value = p.id;
            document.getElementById('fName').value = p.name;
            document.getElementById('fSku').value = p.sku || '';
            document.getElementById('fCategory').value = p.category_id;
            document.getElementById('fPrice').value = p.price;
            document.getElementById('fSalePrice').value = p.sale_price || '';
            document.getElementById('fStock').value = p.stock;
            document.getElementById('fStatus').value = p.status;
            document.getElementById('fFeatured').checked = p.featured == 1;
            document.getElementById('fShortDesc').value = p.short_description || '';
            if (tinymce.get('fDesc')) {
                tinymce.get('fDesc').setContent(p.description || '');
            } else {
                document.getElementById('fDesc').value = p.description || '';
            }
            new bootstrap.Modal(document.getElementById('productModal')).show();
        }
    </script>
</body>

</html>