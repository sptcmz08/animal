<?php
require_once __DIR__ . '/../init.php';
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
$adminPage = 'orders';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update_status') {
        $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$_POST['status'], $_POST['id']]);
        $msg = 'อัพเดทสถานะเรียบร้อย';
    } elseif ($action === 'confirm_payment') {
        $pdo->prepare("UPDATE orders SET payment_status = 'paid', status = 'confirmed' WHERE id = ?")->execute([$_POST['id']]);
        $msg = 'ยืนยันชำระเงินเรียบร้อย';
    } elseif ($action === 'reject_payment') {
        $pdo->prepare("UPDATE orders SET payment_status = 'pending', payment_proof = NULL WHERE id = ?")->execute([$_POST['id']]);
        $msg = 'ปฏิเสธหลักฐานชำระเงิน';
    }
}
$filter = $_GET['filter'] ?? 'all';
$where = '';
if ($filter === 'pending_payment')
    $where = "WHERE o.payment_status = 'pending' AND o.payment_proof IS NOT NULL";
elseif ($filter === 'paid')
    $where = "WHERE o.payment_status = 'paid'";
$orders = $pdo->query("SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id $where ORDER BY o.created_at DESC")->fetchAll();
$countAll = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$countPending = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'pending' AND payment_proof IS NOT NULL")->fetchColumn();
$countPaid = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'paid'")->fetchColumn();
$mLabels = ['qr_promptpay' => 'QR พร้อมเพย์', 'transfer' => 'โอนเงิน', 'credit_card' => 'บัตรเครดิต', 'cod' => 'COD'];
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการคำสั่งซื้อ | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css?v=2">
</head>

<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-bag-check me-2"></i>จัดการคำสั่งซื้อ</h4>
        </div>
        <?php if ($msg): ?>
            <div class="alert alert-success alert-dismissible fade show"><i
                    class="bi bi-check-circle me-1"></i><?= $msg ?><button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button></div><?php endif; ?>

        <!-- Filter Tabs -->
        <div class="d-flex gap-2 mb-4 flex-wrap">
            <a href="?filter=all"
                class="btn btn-sm order-tab <?= $filter === 'all' ? 'active' : 'btn-outline-secondary' ?>">ทั้งหมด <span
                    class="badge bg-light text-dark ms-1"><?= $countAll ?></span></a>
            <a href="?filter=pending_payment"
                class="btn btn-sm order-tab <?= $filter === 'pending_payment' ? 'active' : 'btn-outline-warning' ?>"><i
                    class="bi bi-hourglass-split me-1"></i>รอตรวจสอบ <span
                    class="badge bg-light text-dark ms-1"><?= $countPending ?></span></a>
            <a href="?filter=paid"
                class="btn btn-sm order-tab <?= $filter === 'paid' ? 'active' : 'btn-outline-success' ?>"><i
                    class="bi bi-check-circle me-1"></i>ชำระแล้ว <span
                    class="badge bg-light text-dark ms-1"><?= $countPaid ?></span></a>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden">
            <div class="table-responsive">
                <table class="table table-admin table-hover mb-0">
                    <thead>
                        <tr>
                            <th>หมายเลข</th>
                            <th>ลูกค้า</th>
                            <th>ยอดรวม</th>
                            <th>ชำระเงิน</th>
                            <th>หลักฐาน</th>
                            <th>สถานะ</th>
                            <th>วันที่</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td class="fw-semibold"><?= $o['order_number'] ?></td>
                                <td><?= $o['user_name'] ?? 'Guest' ?><br><small
                                        class="text-muted"><?= $o['shipping_phone'] ?? '' ?></small></td>
                                <td class="fw-semibold text-nowrap"><?= formatPrice($o['total']) ?></td>
                                <td>
                                    <span
                                        class="badge bg-light text-dark mb-1"><?= $mLabels[$o['payment_method']] ?? $o['payment_method'] ?></span><br>
                                    <?= $o['payment_status'] === 'paid' ? '<span class="badge bg-success-subtle text-success">✅ ชำระแล้ว</span>' : '<span class="badge bg-warning-subtle text-warning">⏳ รอชำระ</span>' ?>
                                </td>
                                <td>
                                    <?php if ($o['payment_proof']): ?>
                                        <?php if (str_starts_with($o['payment_proof'], 'CARD_MOCK')): ?><small
                                                class="text-success"><i class="bi bi-credit-card me-1"></i>Mock</small>
                                        <?php elseif (str_starts_with($o['payment_proof'], 'QR_')): ?><small
                                                class="text-primary"><i class="bi bi-qr-code me-1"></i>QR</small>
                                        <?php else: ?><img src="<?= getProductImageUrl($o['payment_proof']) ?>"
                                                class="slip-thumb" data-bs-toggle="modal" data-bs-target="#slipModal"
                                                onclick="document.getElementById('slipImg').src=this.src"
                                                onerror="this.style.display='none'"><?php endif; ?>
                                    <?php else: ?><small class="text-muted">—</small><?php endif; ?>
                                </td>
                                <td><?= getStatusBadge($o['status']) ?></td>
                                <td class="text-muted text-nowrap" style="font-size:0.82rem">
                                    <?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap align-items-center">
                                        <?php if ($o['payment_proof'] && $o['payment_status'] !== 'paid'): ?>
                                            <form method="POST" class="d-inline"><input type="hidden" name="action"
                                                    value="confirm_payment"><input type="hidden" name="id"
                                                    value="<?= $o['id'] ?>"><button class="btn btn-sm btn-success"
                                                    title="ยืนยัน"><i class="bi bi-check-lg"></i></button></form>
                                            <form method="POST" class="d-inline"><input type="hidden" name="action"
                                                    value="reject_payment"><input type="hidden" name="id"
                                                    value="<?= $o['id'] ?>"><button class="btn btn-sm btn-outline-danger"
                                                    title="ปฏิเสธ"><i class="bi bi-x-lg"></i></button></form>
                                        <?php endif; ?>
                                        <form method="POST" class="d-flex gap-1 align-items-center"><input type="hidden"
                                                name="action" value="update_status"><input type="hidden" name="id"
                                                value="<?= $o['id'] ?>">
                                            <select name="status" class="form-select form-select-sm"
                                                style="width:auto;font-size:0.78rem">
                                                <?php foreach (['pending' => 'รอ', 'confirmed' => 'ยืนยัน', 'processing' => 'จัด', 'shipped' => 'ส่งแล้ว', 'delivered' => 'สำเร็จ', 'cancelled' => 'ยกเลิก'] as $k => $v): ?>
                                                    <option value="<?= $k ?>" <?= $o['status'] === $k ? 'selected' : '' ?>><?= $v ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select><button class="btn btn-sm btn-paw"><i class="bi bi-check"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">ไม่พบคำสั่งซื้อ</td>
                            </tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Slip Modal -->
    <div class="modal fade" id="slipModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body text-center p-0"><img id="slipImg" src="" class="img-fluid rounded-3"></div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>