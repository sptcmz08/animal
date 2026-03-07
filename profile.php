<?php
require_once __DIR__ . '/init.php';
if (isset($_GET['logout'])) {
    logoutUser();
    header('Location: ' . BASE_URL);
    exit;
}
if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
$user = getCurrentUser();
$tab = $_GET['tab'] ?? 'profile';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update_profile') {
    $pdo->prepare("UPDATE users SET name=?, phone=?, address=?, province=?, district=?, zipcode=? WHERE id=?")->execute([$_POST['name'], $_POST['phone'], $_POST['address'], $_POST['province'], $_POST['district'], $_POST['zipcode'], $user['id']]);
    $_SESSION['user_name'] = $_POST['name'];
    $msg = 'บันทึกข้อมูลเรียบร้อย';
    $user = getCurrentUser();
}
$orders = getUserOrders($user['id']);
$pageTitle = 'บัญชีของฉัน';
$currentPage = 'profile';
include __DIR__ . '/includes/header.php';
?>

<div class="bg-gradient-to-r from-paw-100 to-paw-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl lg:text-3xl font-extrabold text-paw-700">👤 บัญชีของฉัน</h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid lg:grid-cols-[260px_1fr] gap-8">
        <!-- Sidebar -->
        <div>
            <div class="bg-white rounded-2xl border border-paw-200/50 p-5 text-center mb-4 shadow-sm">
                <div
                    class="w-16 h-16 rounded-full bg-gradient-to-br from-paw-500 to-paw-400 text-white flex items-center justify-center text-2xl font-bold mx-auto mb-3">
                    <?= mb_substr($user['name'], 0, 1) ?></div>
                <div class="font-bold text-paw-700"><?= $user['name'] ?></div>
                <div class="text-sm text-paw-700/40"><?= $user['email'] ?></div>
            </div>
            <div class="bg-white rounded-2xl border border-paw-200/50 overflow-hidden shadow-sm">
                <a href="?tab=profile"
                    class="flex items-center gap-3 px-5 py-3.5 text-sm font-medium transition-colors border-b border-paw-100 <?= $tab === 'profile' ? 'bg-paw-500/10 text-paw-500 font-semibold' : 'text-paw-700/60 hover:bg-paw-50' ?>">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0" />
                    </svg>
                    ข้อมูลส่วนตัว
                </a>
                <a href="?tab=orders"
                    class="flex items-center gap-3 px-5 py-3.5 text-sm font-medium transition-colors border-b border-paw-100 <?= $tab === 'orders' ? 'bg-paw-500/10 text-paw-500 font-semibold' : 'text-paw-700/60 hover:bg-paw-50' ?>">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                    </svg>
                    คำสั่งซื้อ
                </a>
                <a href="<?= BASE_URL ?>wishlist.php"
                    class="flex items-center gap-3 px-5 py-3.5 text-sm font-medium text-paw-700/60 hover:bg-paw-50 transition-colors border-b border-paw-100">❤️
                    รายการโปรด</a>
                <a href="?logout=1"
                    class="flex items-center gap-3 px-5 py-3.5 text-sm font-medium text-red-500 hover:bg-red-50 transition-colors">🚪
                    ออกจากระบบ</a>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-2xl border border-paw-200/50 p-6 lg:p-8 shadow-sm">
            <?php if ($msg): ?>
                <div
                    class="mb-4 px-4 py-3 rounded-xl bg-green-50 text-green-700 border border-green-200 text-sm font-semibold">
                    ✅ <?= $msg ?></div><?php endif; ?>

            <?php if ($tab === 'profile'): ?>
                <h2 class="text-xl font-bold text-paw-700 mb-6">ข้อมูลส่วนตัว</h2>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-semibold text-paw-700 mb-1.5">ชื่อ-นามสกุล</label><input
                                type="text" name="name" value="<?= $user['name'] ?>"
                                class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 focus:ring-2 focus:ring-paw-500/10 transition">
                        </div>
                        <div><label class="block text-sm font-semibold text-paw-700 mb-1.5">เบอร์โทร</label><input
                                type="tel" name="phone" value="<?= $user['phone'] ?>"
                                class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 focus:ring-2 focus:ring-paw-500/10 transition">
                        </div>
                    </div>
                    <div><label class="block text-sm font-semibold text-paw-700 mb-1.5">อีเมล</label><input type="email"
                            value="<?= $user['email'] ?>" disabled
                            class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm bg-paw-50 text-paw-700/40">
                    </div>
                    <div><label class="block text-sm font-semibold text-paw-700 mb-1.5">ที่อยู่</label><textarea
                            name="address" rows="3"
                            class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 focus:ring-2 focus:ring-paw-500/10 transition resize-none"><?= $user['address'] ?></textarea>
                    </div>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div><label class="block text-sm font-semibold text-paw-700 mb-1.5">จังหวัด</label><input
                                type="text" name="province" value="<?= $user['province'] ?>"
                                class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 focus:ring-2 focus:ring-paw-500/10 transition">
                        </div>
                        <div><label class="block text-sm font-semibold text-paw-700 mb-1.5">เขต/อำเภอ</label><input
                                type="text" name="district" value="<?= $user['district'] ?>"
                                class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 focus:ring-2 focus:ring-paw-500/10 transition">
                        </div>
                        <div><label class="block text-sm font-semibold text-paw-700 mb-1.5">รหัสไปรษณีย์</label><input
                                type="text" name="zipcode" value="<?= $user['zipcode'] ?>"
                                class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 focus:ring-2 focus:ring-paw-500/10 transition">
                        </div>
                    </div>
                    <button type="submit"
                        class="px-8 py-3.5 bg-paw-500 text-white rounded-xl font-semibold hover:bg-paw-600 transition-colors shadow-lg shadow-paw-500/20">บันทึกข้อมูล</button>
                </form>

            <?php elseif ($tab === 'orders'): ?>
                <h2 class="text-xl font-bold text-paw-700 mb-6">ประวัติคำสั่งซื้อ (<?= count($orders) ?>)</h2>
                <?php if (empty($orders)): ?>
                    <div class="text-center py-12">
                        <div class="text-5xl mb-4">📦</div>
                        <h3 class="text-lg font-bold text-paw-700 mb-2">ยังไม่มีคำสั่งซื้อ</h3><a
                            href="<?= BASE_URL ?>products.php"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-paw-500 text-white rounded-xl font-semibold mt-4">เลือกซื้อสินค้า</a>
                    </div>
                <?php else:
                    foreach ($orders as $o):
                        $oi = getOrderItems($o['id']); ?>
                        <div class="border border-paw-200/50 rounded-2xl p-5 mb-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                                <div><span class="font-bold text-paw-700">#<?= $o['order_number'] ?></span><span
                                        class="text-xs text-paw-700/40 ml-3"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></span>
                                </div>
                                <?= getStatusBadge($o['status']) ?>
                            </div>
                            <?php foreach ($oi as $item): ?>
                                <div class="flex items-center justify-between py-2 text-sm"><span
                                        class="text-paw-700/60"><?= $item['product_name'] ?> x<?= $item['quantity'] ?></span><span
                                        class="font-semibold"><?= formatPrice($item['total']) ?></span></div>
                            <?php endforeach; ?>
                            <div class="border-t border-paw-100 mt-2 pt-3 text-right font-bold text-paw-500">รวม:
                                <?= formatPrice($o['total']) ?></div>
                        </div>
                    <?php endforeach; endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>