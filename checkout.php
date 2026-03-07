<?php
require_once __DIR__ . '/init.php';
$pageTitle = 'ชำระเงิน';
$currentPage = 'checkout';
$cart = getCart();
if (empty($cart)) {
    header('Location: ' . BASE_URL . 'cart.php');
    exit;
}
$user = isLoggedIn() ? getCurrentUser() : null;
$cartTotal = getCartTotal();
$shipping = getShippingCost();
$discount = getDiscount();
$grandTotal = $cartTotal - $discount + $shipping;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = createOrder(['shipping_name' => $_POST['shipping_name'], 'shipping_phone' => $_POST['shipping_phone'], 'shipping_address' => $_POST['shipping_address'], 'shipping_province' => $_POST['shipping_province'], 'shipping_district' => $_POST['shipping_district'], 'shipping_zipcode' => $_POST['shipping_zipcode'], 'payment_method' => $_POST['payment_method'], 'notes' => $_POST['notes'] ?? '']);
    if ($orderId) {
        $order = getOrder($orderId);
        if (in_array($_POST['payment_method'], ['transfer', 'qr_promptpay', 'credit_card'])) {
            header('Location: ' . BASE_URL . 'payment.php?order=' . $order['order_number']);
        } else {
            header('Location: ' . BASE_URL . 'order-complete.php?order=' . $order['order_number']);
        }
        exit;
    }
}
include __DIR__ . '/includes/header.php';
?>

<div class="bg-gradient-to-r from-paw-100 to-paw-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl lg:text-3xl font-extrabold text-paw-700">💳 ชำระเงิน</h1>
        <div class="flex items-center gap-2 mt-2 text-sm text-paw-700/50"><a href="<?= BASE_URL ?>"
                class="hover:text-paw-500">หน้าแรก</a><span>/</span><a href="<?= BASE_URL ?>cart.php"
                class="hover:text-paw-500">ตะกร้า</a><span>/</span><span class="text-paw-500">ชำระเงิน</span></div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form method="POST" class="grid lg:grid-cols-[1fr_400px] gap-8">
        <div class="space-y-6">
            <!-- Step 1: Shipping -->
            <div class="bg-white rounded-2xl border border-paw-200/50 p-6 shadow-sm">
                <h3 class="flex items-center gap-3 text-lg font-bold text-paw-700 mb-5"><span
                        class="w-8 h-8 rounded-full bg-paw-500 text-white flex items-center justify-center text-sm font-bold">1</span>ข้อมูลจัดส่ง
                </h3>
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div><label class="block text-sm font-semibold text-paw-700 mb-1.5">ชื่อ-นามสกุล *</label><input
                            type="text" name="shipping_name" value="<?= $user['name'] ?? '' ?>" required
                            class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 focus:ring-2 focus:ring-paw-500/10 transition">
                    </div>
                    <div><label class="block text-sm font-semibold text-paw-700 mb-1.5">เบอร์โทร *</label><input
                            type="tel" name="shipping_phone" value="<?= $user['phone'] ?? '' ?>" required
                            class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 focus:ring-2 focus:ring-paw-500/10 transition">
                    </div>
                </div>
                <div class="mb-4"><label class="block text-sm font-semibold text-paw-700 mb-1.5">ที่อยู่
                        *</label><textarea name="shipping_address" rows="3" required
                        class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 resize-none"><?= $user['address'] ?? '' ?></textarea>
                </div>
                <div class="grid md:grid-cols-3 gap-4">
                    <div><label class="block text-sm font-semibold text-paw-700 mb-1.5">จังหวัด *</label><input
                            type="text" name="shipping_province" value="<?= $user['province'] ?? '' ?>" required
                            class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 transition">
                    </div>
                    <div><label class="block text-sm font-semibold text-paw-700 mb-1.5">เขต/อำเภอ *</label><input
                            type="text" name="shipping_district" value="<?= $user['district'] ?? '' ?>" required
                            class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 transition">
                    </div>
                    <div><label class="block text-sm font-semibold text-paw-700 mb-1.5">รหัสไปรษณีย์ *</label><input
                            type="text" name="shipping_zipcode" value="<?= $user['zipcode'] ?? '' ?>" required
                            class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 transition">
                    </div>
                </div>
            </div>

            <!-- Step 2: Payment -->
            <div class="bg-white rounded-2xl border border-paw-200/50 p-6 shadow-sm">
                <h3 class="flex items-center gap-3 text-lg font-bold text-paw-700 mb-5"><span
                        class="w-8 h-8 rounded-full bg-paw-500 text-white flex items-center justify-center text-sm font-bold">2</span>วิธีชำระเงิน
                </h3>
                <div class="space-y-3" id="paymentOptions">
                    <?php
                    $methods = [
                        ['val' => 'qr_promptpay', 'icon' => '💳', 'name' => 'QR PromptPay / พร้อมเพย์', 'desc' => 'สแกน QR Code ชำระเงินทันที', 'badge' => '✨ แนะนำ', 'color' => 'bg-blue-500'],
                        ['val' => 'transfer', 'icon' => '🏦', 'name' => 'โอนเงินผ่านธนาคาร', 'desc' => 'โอนเงินแล้วแนบสลิปหลักฐาน', 'badge' => '', 'color' => 'bg-accent-500'],
                        ['val' => 'credit_card', 'icon' => '💎', 'name' => 'บัตรเครดิต / เดบิต', 'desc' => 'Visa, Mastercard, JCB', 'badge' => '', 'color' => 'bg-paw-500'],
                        ['val' => 'cod', 'icon' => '🚚', 'name' => 'ชำระเงินปลายทาง (COD)', 'desc' => 'ชำระเงินเมื่อได้รับสินค้า +฿50', 'badge' => '', 'color' => 'bg-gray-500'],
                    ];
                    foreach ($methods as $i => $m): ?>
                        <label
                            class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all payment-opt <?= $i === 0 ? 'border-paw-500 bg-paw-500/5' : 'border-paw-200 hover:border-paw-300' ?>"
                            onclick="selectPay(this)">
                            <input type="radio" name="payment_method" value="<?= $m['val'] ?>" <?= $i === 0 ? 'checked' : '' ?>
                                class="hidden">
                            <div class="text-2xl flex-shrink-0"><?= $m['icon'] ?></div>
                            <div class="flex-1">
                                <div class="font-semibold text-sm text-paw-700"><?= $m['name'] ?>
                                    <?= $m['badge'] ? '<span class="text-paw-500 text-xs">' . $m['badge'] . '</span>' : '' ?>
                                </div>
                                <div class="text-xs text-paw-700/40"><?= $m['desc'] ?></div>
                            </div>
                            <div
                                class="w-5 h-5 rounded-full border-2 border-paw-300 flex items-center justify-center pay-check <?= $i === 0 ? 'border-paw-500' : '' ?>">
                                <div class="w-2.5 h-2.5 rounded-full <?= $i === 0 ? 'bg-paw-500' : '' ?>"></div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Step 3: Notes -->
            <div class="bg-white rounded-2xl border border-paw-200/50 p-6 shadow-sm">
                <h3 class="flex items-center gap-3 text-lg font-bold text-paw-700 mb-5"><span
                        class="w-8 h-8 rounded-full bg-paw-500 text-white flex items-center justify-center text-sm font-bold">3</span>หมายเหตุ
                </h3>
                <textarea name="notes" rows="3"
                    class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 resize-none"
                    placeholder="หมายเหตุเพิ่มเติม (ถ้ามี)"></textarea>
            </div>
        </div>

        <!-- Summary -->
        <div class="bg-white rounded-2xl border border-paw-200/50 p-6 h-fit sticky top-24 shadow-sm">
            <h3 class="font-bold text-lg text-paw-700 mb-4">สรุปคำสั่งซื้อ</h3>
            <?php foreach ($cart as $item): ?>
                <div class="flex items-center gap-3 py-3 border-b border-paw-100">
                    <img src="<?= getProductImageUrl($item['image']) ?>" class="w-12 h-12 rounded-lg object-cover"
                        onerror="this.src='https://via.placeholder.com/50'">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold truncate"><?= $item['name'] ?></div>
                        <div class="text-xs text-paw-700/40">x<?= $item['qty'] ?></div>
                    </div>
                    <div class="font-semibold text-sm"><?= formatPrice($item['price'] * $item['qty']) ?></div>
                </div>
            <?php endforeach; ?>
            <div class="space-y-2 text-sm py-4 border-b border-paw-100">
                <div class="flex justify-between"><span class="text-paw-700/60">ยอดรวมสินค้า</span><span
                        class="font-semibold"><?= formatPrice($cartTotal) ?></span></div>
                <?php if ($discount > 0): ?>
                    <div class="flex justify-between text-accent-500">
                        <span>ส่วนลด</span><span>-<?= formatPrice($discount) ?></span></div><?php endif; ?>
                <div class="flex justify-between"><span class="text-paw-700/60">ค่าจัดส่ง</span><span
                        class="font-semibold"><?= $shipping > 0 ? formatPrice($shipping) : '<span class="text-accent-500">ฟรี!</span>' ?></span>
                </div>
            </div>
            <div class="flex justify-between py-4 text-lg font-bold"><span>ยอดรวมทั้งหมด</span><span
                    class="text-paw-500"><?= formatPrice($grandTotal) ?></span></div>
            <button type="submit"
                class="w-full py-3.5 bg-accent-500 text-white rounded-xl font-semibold hover:bg-accent-400 transition-colors shadow-lg flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                ดำเนินการชำระเงิน
            </button>
            <div class="text-center mt-3 text-xs text-paw-700/40 flex items-center justify-center gap-1">🔒
                ข้อมูลของคุณปลอดภัย 100%</div>
        </div>
    </form>
</div>

<script>
    function selectPay(el) {
        document.querySelectorAll('.payment-opt').forEach(e => { e.classList.remove('border-paw-500', 'bg-paw-500/5'); e.classList.add('border-paw-200'); e.querySelector('.pay-check').classList.remove('border-paw-500'); e.querySelector('.pay-check div').className = 'w-2.5 h-2.5 rounded-full'; });
        el.classList.remove('border-paw-200'); el.classList.add('border-paw-500', 'bg-paw-500/5');
        el.querySelector('input').checked = true; el.querySelector('.pay-check').classList.add('border-paw-500'); el.querySelector('.pay-check div').className = 'w-2.5 h-2.5 rounded-full bg-paw-500';
    }
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>