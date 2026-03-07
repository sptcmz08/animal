<?php
require_once __DIR__ . '/init.php';
$orderNum = $_GET['order'] ?? '';
$order = getOrderByNumber($orderNum);
if (!$order) {
    header('Location: ' . BASE_URL);
    exit;
}
$items = getOrderItems($order['id']);
$pageTitle = 'คำสั่งซื้อ #' . $order['order_number'];
$currentPage = 'order-complete';
include __DIR__ . '/includes/header.php';
?>

<div class="bg-gradient-to-r from-paw-100 to-paw-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl lg:text-3xl font-extrabold text-paw-700">📦 รายละเอียดคำสั่งซื้อ</h1>
    </div>
</div>

<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl border border-paw-200/50 overflow-hidden shadow-md">
        <!-- Status Header -->
        <div class="text-center p-8 border-b border-paw-100">
            <?php if ($order['payment_status'] === 'paid'): ?>
                <div class="w-16 h-16 rounded-full bg-accent-500 text-white flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-paw-700 mb-1">ชำระเงินสำเร็จ!</h2>
                <p class="text-sm text-paw-700/50">ขอบคุณที่สั่งซื้อกับ PawHaven 🐾</p>
            <?php elseif ($order['payment_status'] === 'pending'): ?>
                <div class="w-16 h-16 rounded-full bg-yellow-400 text-white flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-paw-700 mb-1">รอตรวจสอบการชำระเงิน</h2>
                <p class="text-sm text-paw-700/50">ทีมงานจะตรวจสอบหลักฐานและยืนยันภายใน 15 นาที</p>
            <?php else: ?>
                <div
                    class="w-16 h-16 rounded-full bg-paw-500 text-white flex items-center justify-center mx-auto mb-4 animate-pulse">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-paw-700 mb-1">สร้างคำสั่งซื้อสำเร็จ!</h2>
                <p class="text-sm text-paw-700/50">กรุณาชำระเงินเพื่อดำเนินการต่อ</p>
            <?php endif; ?>
        </div>

        <div class="p-6 space-y-6">
            <!-- Order Progress -->
            <?php
            $steps = ['สั่งซื้อ', 'ชำระเงิน', 'ยืนยัน', 'จัดส่ง', 'สำเร็จ'];
            $statusMap = ['pending' => 1, 'confirmed' => 2, 'shipped' => 3, 'delivered' => 4];
            $current = $statusMap[$order['status']] ?? 0;
            if ($order['payment_status'] === 'paid')
                $current = max($current, 1);
            ?>
            <div class="flex items-center justify-center gap-0">
                <?php foreach ($steps as $i => $s):
                    if ($i > 0): ?>
                        <div class="w-8 h-0.5 <?= $i <= $current ? 'bg-accent-500' : 'bg-paw-200' ?>"></div><?php endif;
                    $cls = $i < $current ? 'bg-accent-500 text-white' : ($i === $current ? 'bg-paw-500 text-white animate-pulse' : 'bg-paw-200 text-paw-700/40');
                    ?>
                    <div class="flex flex-col items-center gap-1">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold <?= $cls ?>">
                            <?= $i < $current ? '✓' : ($i + 1) ?></div>
                        <span class="text-[10px] text-paw-700/40 font-medium"><?= $s ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Order Info -->
            <div class="border-b border-paw-100 pb-5">
                <h4 class="font-bold text-paw-700 mb-3 flex items-center gap-2 text-sm">📋 ข้อมูลคำสั่งซื้อ</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-paw-700/50">หมายเลข</span><span
                            class="font-semibold font-en">#<?= $order['order_number'] ?></span></div>
                    <div class="flex justify-between"><span class="text-paw-700/50">วันที่สั่งซื้อ</span><span
                            class="font-semibold"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span></div>
                    <div class="flex justify-between"><span
                            class="text-paw-700/50">สถานะ</span><?= getStatusBadge($order['status']) ?></div>
                    <div class="flex justify-between"><span class="text-paw-700/50">การชำระเงิน</span><span
                            class="font-semibold"><?= ['qr_promptpay' => 'QR PromptPay', 'transfer' => 'โอนเงิน', 'credit_card' => 'บัตรเครดิต', 'cod' => 'เก็บเงินปลายทาง'][$order['payment_method']] ?? $order['payment_method'] ?></span>
                    </div>
                    <?php if ($order['tracking_number']): ?>
                        <div class="flex justify-between"><span class="text-paw-700/50">Tracking</span><span
                                class="font-semibold font-en"><?= $order['tracking_number'] ?></span></div><?php endif; ?>
                </div>
            </div>

            <!-- Items -->
            <div class="border-b border-paw-100 pb-5">
                <h4 class="font-bold text-paw-700 mb-3 flex items-center gap-2 text-sm">📦 รายการสินค้า</h4>
                <?php foreach ($items as $item): ?>
                    <div class="flex items-center gap-3 py-2">
                        <img src="<?= getProductImageUrl($item['product_image'] ?? '') ?>"
                            class="w-12 h-12 rounded-lg object-cover" onerror="this.src='https://via.placeholder.com/50'">
                        <div class="flex-1 min-w-0"><span class="text-sm"><?= $item['product_name'] ?> <span
                                    class="text-paw-700/40">x<?= $item['quantity'] ?></span></span></div>
                        <span class="font-semibold text-sm"><?= formatPrice($item['total']) ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="flex justify-between pt-3 mt-2 border-t border-paw-100 font-bold">
                    <span>ยอดรวมทั้งหมด</span><span class="text-paw-500"><?= formatPrice($order['total']) ?></span>
                </div>
            </div>

            <!-- Shipping -->
            <div>
                <h4 class="font-bold text-paw-700 mb-3 flex items-center gap-2 text-sm">🚚 ที่อยู่จัดส่ง</h4>
                <div class="text-sm text-paw-700/70 leading-relaxed bg-paw-50 rounded-xl p-4">
                    <strong><?= $order['shipping_name'] ?></strong> | <?= $order['shipping_phone'] ?><br>
                    <?= $order['shipping_address'] ?><br>
                    <?= $order['shipping_district'] ?>, <?= $order['shipping_province'] ?>
                    <?= $order['shipping_zipcode'] ?>
                </div>
            </div>

            <?php if ($order['payment_status'] !== 'paid' && $order['payment_method'] !== 'cod'): ?>
                <div class="text-center pt-4">
                    <a href="<?= BASE_URL ?>payment.php?order=<?= $orderNum ?>"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-paw-500 text-white rounded-xl font-semibold hover:bg-paw-600 transition-colors">💳
                        ไปที่หน้าชำระเงิน</a>
                </div>
            <?php endif; ?>

            <div class="flex justify-center gap-3 pt-2">
                <a href="<?= BASE_URL ?>profile.php?tab=orders"
                    class="px-5 py-2.5 border-2 border-paw-300 text-paw-700 rounded-xl text-sm font-semibold hover:bg-paw-50 transition-colors">ดูประวัติคำสั่งซื้อ</a>
                <a href="<?= BASE_URL ?>products.php"
                    class="px-5 py-2.5 bg-paw-500 text-white rounded-xl text-sm font-semibold hover:bg-paw-600 transition-colors">ช้อปต่อ</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>