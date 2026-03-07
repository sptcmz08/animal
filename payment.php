<?php
require_once __DIR__ . '/init.php';
$orderNum = $_GET['order'] ?? '';
$order = getOrderByNumber($orderNum);
if (!$order) {
    header('Location: ' . BASE_URL);
    exit;
}
$pageTitle = 'ชำระเงิน - ' . $order['order_number'];
$items = getOrderItems($order['id']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'upload_slip') {
        $slipPath = '';
        if (!empty($_FILES['payment_slip']['tmp_name'])) {
            $slipPath = uploadImage($_FILES['payment_slip'], 'slips');
        }
        if ($slipPath) {
            $pdo->prepare("UPDATE orders SET payment_proof = ?, payment_status = 'pending', status = 'pending' WHERE id = ?")->execute([$slipPath, $order['id']]);
            header('Location: ' . BASE_URL . 'payment.php?order=' . $orderNum . '&uploaded=1');
            exit;
        }
    }
    if ($action === 'mock_pay_card') {
        $pdo->prepare("UPDATE orders SET payment_status = 'paid', status = 'confirmed', payment_proof = 'CARD_MOCK_TXN_" . strtoupper(bin2hex(random_bytes(6))) . "' WHERE id = ?")->execute([$order['id']]);
        header('Location: ' . BASE_URL . 'order-complete.php?order=' . $orderNum);
        exit;
    }
    if ($action === 'mock_pay_qr') {
        $pdo->prepare("UPDATE orders SET payment_status = 'paid', status = 'confirmed', payment_proof = 'QR_PROMPTPAY_" . strtoupper(bin2hex(random_bytes(6))) . "' WHERE id = ?")->execute([$order['id']]);
        header('Location: ' . BASE_URL . 'order-complete.php?order=' . $orderNum);
        exit;
    }
}
$uploaded = isset($_GET['uploaded']);
$currentPage = 'payment';
include __DIR__ . '/includes/header.php';
?>

<div class="min-h-[70vh] py-10 bg-gradient-to-br from-paw-100 via-paw-50 to-white">
    <div class="max-w-2xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl lg:text-3xl font-extrabold text-paw-700">💳 ชำระเงิน</h1>
            <p class="text-paw-700/50 mt-1">คำสั่งซื้อ #<?= $order['order_number'] ?></p>
            <div
                class="inline-flex items-center gap-2 mt-3 px-5 py-2 rounded-full bg-yellow-50 text-yellow-700 text-sm font-semibold">
                ⏱️ กรุณาชำระภายใน <span id="countdown" class="font-bold">30:00</span> นาที</div>
        </div>

        <!-- Steps -->
        <div class="flex items-center justify-center gap-0 mb-8">
            <?php
            $steps = [['label' => 'ตะกร้า', 'done' => true], ['label' => 'ข้อมูล', 'done' => true], ['label' => 'ชำระเงิน', 'active' => true], ['label' => 'สำเร็จ', 'pending' => true]];
            foreach ($steps as $i => $s):
                if ($i > 0): ?>
                    <div class="w-8 h-0.5 bg-paw-300"></div><?php endif;
                $cls = isset($s['done']) ? 'bg-accent-500 text-white' : (isset($s['active']) ? 'bg-paw-500 text-white animate-pulse' : 'bg-paw-200 text-paw-700/40');
                ?>
                <div class="flex flex-col items-center gap-1">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold <?= $cls ?>">
                        <?= isset($s['done']) ? '✓' : ($i + 1) ?></div>
                    <span class="text-[10px] text-paw-700/40 font-medium"><?= $s['label'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($uploaded): ?>
            <!-- Success -->
            <div class="bg-white rounded-2xl border border-paw-200/50 p-8 text-center shadow-md">
                <div class="w-16 h-16 rounded-full bg-accent-500 text-white flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-paw-700 mb-2">อัพโหลดหลักฐานสำเร็จ!</h2>
                <p class="text-paw-700/50 mb-6">ทีมงาน PawHaven จะตรวจสอบและยืนยันภายใน 15 นาที</p>
                <div class="flex justify-center gap-3">
                    <a href="<?= BASE_URL ?>order-complete.php?order=<?= $orderNum ?>"
                        class="px-6 py-3 bg-paw-500 text-white rounded-xl font-semibold hover:bg-paw-600 transition-colors">ดูรายละเอียดคำสั่งซื้อ</a>
                    <a href="<?= BASE_URL ?>products.php"
                        class="px-6 py-3 border-2 border-paw-300 text-paw-700 rounded-xl font-semibold hover:bg-paw-50 transition-colors">ช้อปต่อ</a>
                </div>
            </div>

        <?php else: ?>

            <?php if ($order['payment_method'] === 'qr_promptpay'): ?>
                <!-- QR PromptPay -->
                <div class="bg-white rounded-2xl border border-paw-200/50 overflow-hidden shadow-md mb-6">
                    <div class="flex items-center gap-4 p-5 border-b border-paw-100">
                        <div
                            class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-700 to-blue-900 flex items-center justify-center text-white text-lg flex-shrink-0">
                            📱</div>
                        <div>
                            <h3 class="font-bold text-paw-700">QR PromptPay / พร้อมเพย์</h3>
                            <p class="text-xs text-paw-700/40">สแกน QR Code เพื่อชำระเงิน</p>
                        </div>
                    </div>
                    <div class="p-6 text-center">
                        <div class="inline-block bg-white border-2 border-paw-200 rounded-2xl p-5 mb-4">
                            <canvas id="qrCanvas" width="200" height="200"></canvas>
                        </div>
                        <div class="text-3xl font-extrabold text-paw-500 mb-1 font-en"><?= formatPrice($order['total']) ?></div>
                        <div class="text-sm text-paw-700/50 mb-4">PawHaven Co., Ltd.</div>
                        <div class="bg-paw-50 rounded-xl p-4 text-left space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-paw-700/50">PromptPay ID</span><span
                                    class="font-semibold">0-XXXX-XXXXX-XX-X</span></div>
                            <div class="flex justify-between"><span class="text-paw-700/50">ชื่อบัญชี</span><span
                                    class="font-semibold">บจก. พอว์เฮเวน</span></div>
                            <div class="flex justify-between"><span class="text-paw-700/50">จำนวนเงิน</span><span
                                    class="font-bold text-paw-500"><?= formatPrice($order['total']) ?></span></div>
                            <div class="flex justify-between"><span class="text-paw-700/50">Ref. No.</span><span
                                    class="font-semibold"><?= $order['order_number'] ?></span></div>
                        </div>
                        <form method="POST" class="mt-5">
                            <input type="hidden" name="action" value="mock_pay_qr">
                            <button type="submit"
                                class="w-full py-3.5 bg-gradient-to-r from-blue-700 to-blue-600 text-white rounded-xl font-semibold hover:opacity-90 transition-opacity">✅
                                จำลองการชำระเงินสำเร็จ (Mock)</button>
                            <p class="text-[11px] text-paw-700/30 mt-2">*ปุ่มนี้สำหรับ Demo เท่านั้น</p>
                        </form>
                    </div>
                </div>

            <?php elseif ($order['payment_method'] === 'transfer'): ?>
                <!-- Bank Transfer -->
                <div class="bg-white rounded-2xl border border-paw-200/50 overflow-hidden shadow-md mb-6">
                    <div class="flex items-center gap-4 p-5 border-b border-paw-100">
                        <div
                            class="w-12 h-12 rounded-xl bg-gradient-to-br from-accent-500 to-accent-400 flex items-center justify-center text-white text-lg flex-shrink-0">
                            🏦</div>
                        <div>
                            <h3 class="font-bold text-paw-700">โอนเงินผ่านธนาคาร</h3>
                            <p class="text-xs text-paw-700/40">โอนเงินไปยังบัญชีด้านล่าง แล้วอัพโหลดสลิป</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-center mb-6">
                            <div class="text-3xl font-extrabold text-paw-500 font-en"><?= formatPrice($order['total']) ?></div>
                            <div class="text-sm text-paw-700/50">ยอดที่ต้องชำระ</div>
                        </div>
                        <div class="space-y-3 mb-6">
                            <?php
                            $banks = [
                                ['name' => 'ธนาคารกสิกรไทย', 'acc' => '123-4-56789-0', 'holder' => 'บจก. พอว์เฮเวน', 'abbr' => 'KBank', 'color' => 'from-green-600 to-green-700'],
                                ['name' => 'ธนาคารกรุงเทพ', 'acc' => '098-7-65432-1', 'holder' => 'บจก. พอว์เฮเวน', 'abbr' => 'BBL', 'color' => 'from-blue-700 to-blue-800'],
                                ['name' => 'ธนาคารไทยพาณิชย์', 'acc' => '456-7-89012-3', 'holder' => 'บจก. พอว์เฮเวน', 'abbr' => 'SCB', 'color' => 'from-purple-700 to-purple-800'],
                            ];
                            foreach ($banks as $b): ?>
                                <div
                                    class="flex items-center gap-4 p-4 border-2 border-paw-200 rounded-xl hover:border-paw-400 transition-colors">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-gradient-to-br <?= $b['color'] ?> flex items-center justify-center text-white text-[10px] font-extrabold flex-shrink-0">
                                        <?= $b['abbr'] ?></div>
                                    <div class="flex-1">
                                        <div class="font-bold text-sm text-paw-700"><?= $b['name'] ?></div>
                                        <div class="text-paw-500 font-bold font-en tracking-wider"><?= $b['acc'] ?></div>
                                        <div class="text-xs text-paw-700/40"><?= $b['holder'] ?></div>
                                    </div>
                                    <button onclick="copyText('<?= str_replace('-', '', $b['acc']) ?>',this)"
                                        class="px-3 py-1.5 border border-paw-200 rounded-lg text-xs text-paw-700/50 hover:bg-paw-500 hover:text-white hover:border-paw-500 transition-colors">คัดลอก</button>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <h4 class="font-bold text-paw-700 mb-3">📎 อัพโหลดหลักฐานการโอน</h4>
                        <form method="POST" enctype="multipart/form-data" id="slipForm">
                            <input type="hidden" name="action" value="upload_slip">
                            <div onclick="document.getElementById('slipFile').click()"
                                class="border-2 border-dashed border-paw-300 rounded-xl p-8 text-center cursor-pointer hover:border-paw-500 hover:bg-paw-50/50 transition-all relative">
                                <input type="file" name="payment_slip" id="slipFile" accept="image/*"
                                    onchange="previewSlip(this)" required class="absolute inset-0 opacity-0 cursor-pointer">
                                <div class="text-4xl mb-2">📤</div>
                                <h4 class="font-semibold text-paw-700">คลิกเพื่ออัพโหลดสลิป</h4>
                                <p class="text-xs text-paw-700/40">รองรับ JPG, PNG ขนาดไม่เกิน 5MB</p>
                                <img id="slipPreview" class="max-w-[200px] mx-auto mt-3 rounded-lg hidden" alt="">
                            </div>
                            <button type="submit" id="submitSlip" disabled
                                class="w-full mt-4 py-3.5 bg-accent-500 text-white rounded-xl font-semibold hover:bg-accent-400 transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">✅
                                ยืนยันการชำระเงิน</button>
                        </form>
                    </div>
                </div>

            <?php elseif ($order['payment_method'] === 'credit_card'): ?>
                <!-- Credit Card -->
                <div class="bg-white rounded-2xl border border-paw-200/50 overflow-hidden shadow-md mb-6">
                    <div class="flex items-center gap-4 p-5 border-b border-paw-100">
                        <div
                            class="w-12 h-12 rounded-xl bg-gradient-to-br from-paw-500 to-paw-400 flex items-center justify-center text-white text-lg flex-shrink-0">
                            💎</div>
                        <div>
                            <h3 class="font-bold text-paw-700">บัตรเครดิต / เดบิต</h3>
                            <p class="text-xs text-paw-700/40">กรอกข้อมูลบัตรเพื่อชำระเงิน</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-center mb-5">
                            <div class="text-3xl font-extrabold text-paw-500 font-en"><?= formatPrice($order['total']) ?></div>
                            <div class="text-sm text-paw-700/50">ยอดที่ต้องชำระ</div>
                        </div>
                        <form method="POST" class="max-w-md mx-auto" id="ccForm">
                            <input type="hidden" name="action" value="mock_pay_card">
                            <!-- Card Preview -->
                            <div
                                class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 rounded-2xl p-6 text-white mb-6 aspect-[1.586/1] flex flex-col justify-between relative overflow-hidden">
                                <div class="absolute -top-1/2 -right-1/3 w-48 h-48 rounded-full bg-white/5"></div>
                                <div class="absolute -bottom-1/3 -left-1/4 w-56 h-56 rounded-full bg-paw-500/10"></div>
                                <div class="w-11 h-8 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-md relative z-10">
                                </div>
                                <div class="font-en text-lg tracking-[3px] relative z-10" id="ccDisplay">•••• •••• •••• ••••
                                </div>
                                <div class="flex justify-between items-end relative z-10">
                                    <div>
                                        <div class="text-[10px] text-white/40 mb-0.5">CARDHOLDER</div>
                                        <div class="text-xs uppercase tracking-wider" id="ccNameDisplay">YOUR NAME</div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] text-white/40 mb-0.5">EXPIRES</div>
                                        <div class="text-xs" id="ccExpDisplay">MM/YY</div>
                                    </div>
                                    <div class="text-xl font-extrabold italic text-white/60" id="ccBrand">VISA</div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div><label class="block text-sm font-semibold text-paw-700 mb-1.5">หมายเลขบัตร</label><input
                                        type="text" id="ccNumber" maxlength="19" oninput="formatCC(this)" required
                                        placeholder="1234 5678 9012 3456"
                                        class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 font-en tracking-widest">
                                </div>
                                <div><label class="block text-sm font-semibold text-paw-700 mb-1.5">ชื่อบนบัตร</label><input
                                        type="text" id="ccName" required placeholder="SOMCHAI RAKHMAEW"
                                        oninput="document.getElementById('ccNameDisplay').textContent=this.value||'YOUR NAME'"
                                        class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 uppercase">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div><label class="block text-sm font-semibold text-paw-700 mb-1.5">วันหมดอายุ</label><input
                                            type="text" id="ccExp" maxlength="5" oninput="formatExp(this)" required
                                            placeholder="MM/YY"
                                            class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 font-en">
                                    </div>
                                    <div><label class="block text-sm font-semibold text-paw-700 mb-1.5">CVV</label><input
                                            type="password" maxlength="4" required placeholder="•••"
                                            class="w-full px-4 py-3 rounded-xl border border-paw-200 text-sm outline-none focus:border-paw-500 font-en tracking-[4px]">
                                    </div>
                                </div>
                            </div>
                            <div
                                class="flex items-center gap-2 mt-4 p-3 bg-green-50 rounded-xl border border-green-200 text-xs text-green-700">
                                🔒 ข้อมูลบัตรถูกเข้ารหัส SSL 256-bit ปลอดภัย 100%</div>
                            <button type="submit"
                                class="w-full mt-4 py-3.5 bg-paw-500 text-white rounded-xl font-semibold hover:bg-paw-600 transition-colors shadow-lg flex items-center justify-center gap-2">🔒
                                ชำระเงิน <?= formatPrice($order['total']) ?></button>
                            <p class="text-[11px] text-paw-700/30 text-center mt-2">*Mock — จำลองการชำระผ่านบัตรเครดิตสำหรับ
                                Demo</p>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Order Summary -->
            <div class="bg-white rounded-2xl border border-paw-200/50 overflow-hidden shadow-md">
                <div class="p-5 border-b border-paw-100">
                    <h3 class="font-bold text-paw-700">📦 สรุปคำสั่งซื้อ</h3>
                </div>
                <div class="p-5">
                    <?php foreach ($items as $item): ?>
                        <div class="flex items-center gap-3 py-2.5 border-b border-paw-100 last:border-0">
                            <img src="<?= getProductImageUrl($item['product_image'] ?? '') ?>"
                                class="w-12 h-12 rounded-lg object-cover" onerror="this.src='https://via.placeholder.com/50'">
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold truncate"><?= $item['product_name'] ?></div>
                                <div class="text-xs text-paw-700/40">x<?= $item['quantity'] ?></div>
                            </div>
                            <div class="font-semibold text-sm"><?= formatPrice($item['total']) ?></div>
                        </div>
                    <?php endforeach; ?>
                    <div class="flex justify-between pt-3 mt-2 text-lg font-bold"><span>ยอดรวมทั้งหมด</span><span
                            class="text-paw-500"><?= formatPrice($order['total']) ?></span></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    let timeLeft = 30 * 60;
    const timerEl = document.getElementById('countdown');
    if (timerEl) { setInterval(() => { if (timeLeft <= 0) return; timeLeft--; const m = Math.floor(timeLeft / 60).toString().padStart(2, '0'); const s = (timeLeft % 60).toString().padStart(2, '0'); timerEl.textContent = m + ':' + s; if (timeLeft < 300) timerEl.parentElement.classList.add('bg-red-50', 'text-red-600'); }, 1000); }
    const canvas = document.getElementById('qrCanvas');
    if (canvas) { const ctx = canvas.getContext('2d'); const s = 200; const g = 8; const cs = Math.floor(s / 25); for (let i = 0; i < 25; i++)for (let j = 0; j < 25; j++) { ctx.fillStyle = Math.random() > 0.5 || ((i < 7 && j < 7) || (i < 7 && j > 17) || (i > 17 && j < 7)) ? '#1A3C7B' : 'white'; ctx.fillRect(i * cs, j * cs, cs, cs); } for (let p of [[0, 0], [18, 0], [0, 18]]) { for (let i = 0; i < 7; i++)for (let j = 0; j < 7; j++) { ctx.fillStyle = (i === 0 || i === 6 || j === 0 || j === 6 || (i >= 2 && i <= 4 && j >= 2 && j <= 4)) ? '#1A3C7B' : 'white'; ctx.fillRect((p[0] + i) * cs, (p[1] + j) * cs, cs, cs); } } }
    function copyText(t, btn) { navigator.clipboard.writeText(t); btn.textContent = '✓ คัดลอกแล้ว'; btn.classList.add('bg-paw-500', 'text-white', 'border-paw-500'); setTimeout(() => { btn.textContent = 'คัดลอก'; btn.classList.remove('bg-paw-500', 'text-white', 'border-paw-500'); }, 2000); }
    function previewSlip(input) { if (input.files && input.files[0]) { const r = new FileReader(); r.onload = e => { const p = document.getElementById('slipPreview'); p.src = e.target.result; p.classList.remove('hidden'); document.getElementById('submitSlip').disabled = false; }; r.readAsDataURL(input.files[0]); } }
    function formatCC(el) { let v = el.value.replace(/\D/g, ''); v = v.replace(/(\d{4})/g, '$1 ').trim(); el.value = v; document.getElementById('ccDisplay').textContent = v || '•••• •••• •••• ••••'; const n = v.replace(/\s/g, ''); document.getElementById('ccBrand').textContent = n.startsWith('4') ? 'VISA' : n.startsWith('5') ? 'MASTERCARD' : n.startsWith('3') ? 'AMEX' : 'VISA'; }
    function formatExp(el) { let v = el.value.replace(/\D/g, ''); if (v.length >= 2) v = v.substring(0, 2) + '/' + v.substring(2); el.value = v; document.getElementById('ccExpDisplay').textContent = v || 'MM/YY'; }
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>