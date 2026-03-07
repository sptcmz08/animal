<?php
require_once __DIR__ . '/init.php';
$pageTitle = 'ตะกร้าสินค้า';
$currentPage = 'cart';
$cartItems = getCartItems();
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += ($item['sale_price'] ?: $item['price']) * $item['qty'];
}
$couponDiscount = $_SESSION['coupon_discount'] ?? 0;
$shipping = $subtotal >= 2000 ? 0 : 150;
$total = $subtotal - $couponDiscount + $shipping;
include __DIR__ . '/includes/header.php';
?>

<div class="bg-gradient-to-r from-paw-100 to-paw-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl lg:text-3xl font-extrabold text-paw-700">🛒 ตะกร้าสินค้า</h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <?php if (empty($cartItems)): ?>
        <div class="text-center py-20">
            <div class="text-6xl mb-4">🛒</div>
            <h3 class="text-xl font-bold text-paw-700 mb-2">ตะกร้าว่างเปล่า</h3>
            <p class="text-paw-700/50 mb-6">ยังไม่มีสินค้าในตะกร้า ไปช้อปกันเลย!</p>
            <a href="<?= BASE_URL ?>products.php"
                class="inline-flex items-center gap-2 px-6 py-3 bg-paw-500 text-white rounded-xl font-semibold hover:bg-paw-600 transition-colors">ช้อปเลย</a>
        </div>
    <?php else: ?>
        <div class="grid lg:grid-cols-[1fr_380px] gap-8">
            <div class="space-y-3">
                <?php foreach ($cartItems as $item):
                    $itemPrice = $item['sale_price'] ?: $item['price']; ?>
                    <div
                        class="bg-white rounded-2xl border border-paw-200/50 p-4 flex items-center gap-4 hover:shadow-md transition-shadow">
                        <img src="<?= getProductImageUrl($item['primary_image']) ?>" alt="<?= $item['name'] ?>"
                            class="w-20 h-20 rounded-xl object-cover border border-paw-200/50"
                            onerror="this.src='https://via.placeholder.com/80'">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-paw-700 truncate"><a
                                    href="<?= BASE_URL ?>product.php?slug=<?= $item['slug'] ?>"
                                    class="hover:text-paw-500"><?= $item['name'] ?></a></h3>
                            <div class="text-sm text-paw-500 font-bold mt-1"><?= formatPrice($itemPrice) ?></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="updateCart(<?= $item['id'] ?>, <?= $item['qty'] - 1 ?>)"
                                class="w-8 h-8 rounded-lg border border-paw-200 flex items-center justify-center text-paw-700 hover:bg-paw-50 transition-colors">−</button>
                            <span class="w-8 text-center font-semibold text-sm"><?= $item['qty'] ?></span>
                            <button onclick="updateCart(<?= $item['id'] ?>, <?= $item['qty'] + 1 ?>)"
                                class="w-8 h-8 rounded-lg border border-paw-200 flex items-center justify-center text-paw-700 hover:bg-paw-50 transition-colors">+</button>
                        </div>
                        <div class="font-bold text-paw-700 w-24 text-right"><?= formatPrice($itemPrice * $item['qty']) ?></div>
                        <button onclick="removeFromCart(<?= $item['id'] ?>)"
                            class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Summary -->
            <div class="bg-white rounded-2xl border border-paw-200/50 p-6 h-fit sticky top-24 shadow-sm">
                <h3 class="font-bold text-lg text-paw-700 mb-4">สรุปคำสั่งซื้อ</h3>
                <div class="space-y-3 text-sm pb-4 border-b border-paw-200/50">
                    <div class="flex justify-between"><span class="text-paw-700/60">ราคาสินค้า</span><span
                            class="font-semibold"><?= formatPrice($subtotal) ?></span></div>
                    <div class="flex justify-between"><span class="text-paw-700/60">ค่าจัดส่ง</span><span
                            class="font-semibold"><?= $shipping > 0 ? formatPrice($shipping) : '<span class="text-accent-500">ฟรี!</span>' ?></span>
                    </div>
                    <?php if ($couponDiscount > 0): ?>
                        <div class="flex justify-between text-accent-500">
                            <span>ส่วนลดคูปอง</span><span>-<?= formatPrice($couponDiscount) ?></span></div><?php endif; ?>
                </div>
                <div class="flex justify-between py-4 text-lg font-bold text-paw-700"><span>ยอดรวม</span><span
                        class="text-paw-500"><?= formatPrice($total) ?></span></div>

                <div class="flex gap-2 mb-4">
                    <input type="text" id="couponInput" placeholder="รหัสคูปอง"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500">
                    <button onclick="applyCoupon()"
                        class="px-4 py-2.5 bg-paw-100 text-paw-500 rounded-xl text-sm font-semibold hover:bg-paw-200 transition-colors">ใช้คูปอง</button>
                </div>
                <a href="<?= BASE_URL ?>checkout.php"
                    class="block w-full py-3.5 bg-paw-500 text-white rounded-xl font-semibold text-center hover:bg-paw-600 transition-colors shadow-lg shadow-paw-500/20">ดำเนินการสั่งซื้อ</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>