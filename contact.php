<?php
require_once __DIR__ . '/init.php';
$pageTitle = 'ติดต่อเรา';
$currentPage = 'contact';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)")->execute([$_POST['name'], $_POST['email'], $_POST['subject'], $_POST['message']]);
    $msg = '✅ ส่งข้อความเรียบร้อย เราจะใช้ติดต่อกลับโดยเร็ว';
}
include __DIR__ . '/includes/header.php';
?>
<div class="bg-gradient-to-r from-elite-150 to-elite-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl lg:text-3xl font-serif font-bold text-elite-800 italic">📞 ติดต่อเรา</h1>
        <p class="text-elite-500 mt-1">มีคำถามหรือข้อสงสัย? ยินดีให้บริการค่ะ</p>
    </div>
</div>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid md:grid-cols-[1fr_340px] lg:grid-cols-[1fr_400px] gap-6 md:gap-8">
        <div class="bg-white rounded-2xl border border-elite-200/50 p-6 lg:p-8 shadow-sm">
            <h2 class="text-xl font-serif font-bold text-elite-800 mb-6 italic">ส่งข้อความถึงเรา</h2>
            <?php if ($msg): ?>
                <div
                    class="mb-4 px-4 py-3 rounded-xl bg-green-50 text-green-700 border border-green-200 text-sm font-semibold">
                    <?= $msg ?>
                </div><?php endif; ?>
            <form method="POST" class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-semibold text-elite-700 mb-1.5">ชื่อ-นามสกุล *</label><input
                            type="text" name="name" required
                            class="w-full px-4 py-3 rounded-xl border border-elite-200 text-sm outline-none focus:border-elite-500 focus:ring-2 focus:ring-elite-500/10 transition">
                    </div>
                    <div><label class="block text-sm font-semibold text-elite-700 mb-1.5">อีเมล *</label><input
                            type="email" name="email" required
                            class="w-full px-4 py-3 rounded-xl border border-elite-200 text-sm outline-none focus:border-elite-500 focus:ring-2 focus:ring-elite-500/10 transition">
                    </div>
                </div>
                <div><label class="block text-sm font-semibold text-elite-700 mb-1.5">หัวข้อ</label><input type="text"
                        name="subject"
                        class="w-full px-4 py-3 rounded-xl border border-elite-200 text-sm outline-none focus:border-elite-500 focus:ring-2 focus:ring-elite-500/10 transition">
                </div>
                <div><label class="block text-sm font-semibold text-elite-700 mb-1.5">ข้อความ *</label><textarea
                        name="message" required rows="5"
                        class="w-full px-4 py-3 rounded-xl border border-elite-200 text-sm outline-none focus:border-elite-500 focus:ring-2 focus:ring-elite-500/10 transition resize-none"></textarea>
                </div>
                <button type="submit"
                    class="px-8 py-3.5 bg-elite-600 text-white rounded-xl font-semibold hover:bg-elite-700 transition-colors shadow-lg shadow-elite-600/20">ส่งข้อความ</button>
            </form>
        </div>
        <div class="space-y-4">
            <?php
            $infos = [
                ['icon' => '📞', 'title' => 'โทรศัพท์', 'desc' => '063-653-5151', 'sub' => 'จ-ศ 9:00-18:00'],
                ['icon' => '✉️', 'title' => 'อีเมล', 'desc' => 'info@elitepetdesign.com', 'sub' => 'ตอบกลับภายใน 24 ชม.'],
                ['icon' => '📍', 'title' => 'ที่อยู่', 'desc' => '117 ข้างศูนย์ฝึก AIA 103 ม.17 Thanon Mittraphap Frontage, ในเมือง Mueang Khon Kaen District, Khon Kaen 40000', 'sub' => 'Thailand'],
                ['icon' => '💬', 'title' => 'LINE', 'desc' => '@elitepetdesign', 'sub' => 'ตอบกลับเร็วที่สุด'],
            ];
            foreach ($infos as $i): ?>
                <div
                    class="bg-white rounded-2xl border border-elite-200/50 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="text-2xl"><?= $i['icon'] ?></div>
                    <div>
                        <div class="font-bold text-elite-700 text-sm"><?= $i['title'] ?></div>
                        <div class="text-elite-600 font-semibold"><?= $i['desc'] ?></div>
                        <div class="text-sm text-elite-400"><?= $i['sub'] ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="mt-8">
        <h2 class="text-xl font-serif font-bold text-elite-800 mb-4 italic">📍 แผนที่</h2>
        <div class="bg-white rounded-2xl border border-elite-200/50 overflow-hidden shadow-sm">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d402.3!2d102.8175765!3d16.4061353!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31228baeeaff5de7%3A0x3a1cf5d69159511a!2sPorPhayakCattery!5e0!3m2!1sth!2sth!4v1709654400000"
                width="100%" height="100%" style="border:0; min-height:250px;" class="md:min-h-[400px]"
                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>