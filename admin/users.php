<?php
require_once __DIR__ . '/../init.php';
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
$adminPage = 'users';
$users = $pdo->query("SELECT u.*, (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count FROM users u ORDER BY u.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสมาชิก | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css?v=2">
</head>

<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-people me-2"></i>จัดการสมาชิก <span
                    class="badge bg-secondary rounded-pill"><?= count($users) ?></span></h4>
        </div>
        <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden">
            <div class="table-responsive">
                <table class="table table-admin table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ชื่อ</th>
                            <th>อีเมล</th>
                            <th>โทรศัพท์</th>
                            <th>บทบาท</th>
                            <th>คำสั่งซื้อ</th>
                            <th>สมัครเมื่อ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="fw-semibold"><?= $u['name'] ?></td>
                                <td><?= $u['email'] ?></td>
                                <td><?= $u['phone'] ?: '-' ?></td>
                                <td><?= $u['role'] === 'admin' ? '<span class="badge bg-warning text-dark">Admin</span>' : '<span class="badge bg-light text-dark">Customer</span>' ?>
                                </td>
                                <td><?= $u['order_count'] ?></td>
                                <td class="text-muted"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>