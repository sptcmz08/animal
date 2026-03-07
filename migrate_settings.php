<?php
/**
 * Migration script: Add homepage content settings
 * Run once via browser: http://localhost/animal/migrate_settings.php
 *
 * NOTE: To upload large videos, update php.ini:
 *   upload_max_filesize = 200M
 *   post_max_size = 210M
 *   max_execution_time = 300
 * Or create .htaccess in project root with:
 *   php_value upload_max_filesize 200M
 *   php_value post_max_size 210M
 */
require_once __DIR__ . '/config.php';

$settings = [
    // Branding
    ['site_name', 'Elite Pet Design'],
    ['site_tagline', 'Custom Pet Furniture'],

    // Hero Section
    ['hero_title', 'Crafted for Every Pet'],
    ['hero_subtitle', 'เฟอร์นิเจอร์สัตว์เลี้ยง ออกแบบสั่งทำพิเศษ เพื่อเพื่อนรักสี่ขาของคุณ'],
    ['hero_image_1', 'assets/img/hero1.png'],
    ['hero_image_2', 'assets/img/hero2.png'],
    ['hero_image_3', 'assets/img/hero3.png'],

    // Intro Section
    ['intro_text', 'ยินดีต้อนรับสู่ Elite Pet Design สตูดิโอออกแบบเฟอร์นิเจอร์สัตว์เลี้ยงแบบสั่งทำพิเศษ เราให้ความสำคัญกับทุกรายละเอียดในการสร้างสรรค์ผลงานที่เหมาะสมกับสัตว์เลี้ยงและไลฟ์สไตล์ของคุณ'],

    // Products Section
    ['products_video_url', ''],
    ['products_video_file', ''],
    ['products_video_loop', '1'],
    ['products_section_title', 'Our Products'],
    ['products_section_subtitle', 'ผลงานการออกแบบเฟอร์นิเจอร์สัตว์เลี้ยงจากสตูดิโอของเรา'],

    // Services Section
    ['services_video_url', ''],
    ['services_video_file', ''],
    ['services_video_loop', '1'],
    ['services_section_title', 'Our Services'],
    ['service_1_title', 'Tailored Design'],
    ['service_1_desc', 'ออกแบบเฉพาะตามความต้องการ เลือกขนาด สี วัสดุได้ตามใจ'],
    ['service_1_image', 'assets/img/service1.png'],
    ['service_2_title', 'Handcrafted Quality'],
    ['service_2_desc', 'ผลิตด้วยมือจากช่างฝีมือ ใส่ใจทุกรายละเอียด คุณภาพระดับพรีเมียม'],
    ['service_2_image', 'assets/img/service2.png'],
    ['service_3_title', 'Pets Welcome'],
    ['service_3_desc', 'ทดสอบกับสัตว์เลี้ยงจริง มั่นใจในความปลอดภัยและความสะดวกสบาย'],
    ['service_3_image', 'assets/img/service3.png'],

    // Materials Section
    ['materials_title', 'Materials'],
    ['materials_text', 'เราคัดสรรวัสดุคุณภาพสูงที่ปลอดภัยสำหรับสัตว์เลี้ยง ไม้ยางพาราแท้ ผ้ากำมะหยี่เกรดพรีเมียม เชือกศิลาธรธรรมชาติ และอะคริลิคใสคุณภาพสูง ทุกชิ้นผ่านการทดสอบความปลอดภัยก่อนส่งมอบ'],

    // Reviews Section
    ['review_1_name', 'คุณสมศรี'],
    ['review_1_text', 'คอนโดแมวสวยมากค่ะ น้องเหมียวชอบมาก ดีไซน์เข้ากับบ้านเลย คุณภาพดีมาก คุ้มค่ามากๆ'],
    ['review_1_rating', '5'],
    ['review_2_name', 'คุณวิชัย'],
    ['review_2_text', 'สั่งทำเตียงสุนัขให้น้องหมา ได้ตามที่ออกแบบเลยครับ ขนาดพอดี วัสดุดี น้องหมาชอบมาก'],
    ['review_2_rating', '5'],

    // Social Links
    ['social_line_url', 'https://lin.ee/sbutzgh'],
    ['social_line_id', '@elitepetdesign'],
    ['social_facebook_url', 'https://www.facebook.com/share/1G1sLYGRhR/?mibextid=wwXIfr'],
    ['social_instagram_url', 'https://www.instagram.com/elite_petdesign/'],

    // Contact
    ['contact_phone', '080-123-4567'],
    ['contact_email', 'info@elitepetdesign.com'],
    ['contact_address', 'กรุงเทพมหานคร ประเทศไทย'],
    ['contact_map_embed', 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15496.123!2d100.523186!3d13.736717!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2z!5e0!3m2!1sth!2sth'],
    ['contact_working_hours', 'จ-ศ 9:00-18:00'],
];

$stmt = $pdo->prepare("INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES (?, ?)");
$count = 0;
foreach ($settings as [$key, $val]) {
    $stmt->execute([$key, $val]);
    if ($stmt->rowCount() > 0) $count++;
}

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Migration</title><style>body{font-family:system-ui;max-width:600px;margin:40px auto;padding:20px}h2{color:#5c4a2a}.ok{color:#16a34a;font-weight:bold}</style></head><body>";
echo "<h2>🔧 Settings Migration</h2>";
echo "<p class='ok'>✅ เพิ่ม $count settings ใหม่เรียบร้อย!</p>";
echo "<p><a href='admin/settings.php'>→ ไปหน้าจัดการ Settings</a></p>";
echo "</body></html>";
