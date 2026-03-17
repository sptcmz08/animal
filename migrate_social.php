<?php
/**
 * Migration: Update social links + default reviews
 * Run once then delete this file
 */
require_once __DIR__ . '/init.php';

$updates = [
    // Social Links
    'social_facebook_url' => 'https://www.facebook.com/share/1G1sLYGRhR/?mibextid=wwXIfr',
    'social_line_url' => 'https://lin.ee/sbutzgh',
    'social_instagram_url' => 'https://www.instagram.com/elite.petdesign?igsh=N2R2ZWVucmI4Z2Nk',

    // Default Reviews (if not set)
    'review_1_name' => 'คุณนภา',
    'review_1_text' => 'คอนโดแมวสวยมากค่ะ ออกแบบมาพอดีกับห้อง น้องแมวชอบมาก ขอบคุณที่ทำงานอย่างดีค่ะ',
    'review_1_rating' => '5',
    'review_2_name' => 'คุณสมชาย',
    'review_2_text' => 'สั่งเตียงสุนัขตามขนาดที่ต้องการ ได้งานตรงสเปคมาก วัสดุดีทนทาน น้องหมานอนสบายครับ',
    'review_2_rating' => '5',
];

$count = 0;
foreach ($updates as $key => $value) {
    saveSetting($key, $value);
    $count++;
    echo "✅ {$key} = {$value}\n";
}

echo "\n🎉 Updated {$count} settings successfully!\n";
echo "⚠️ Please delete this file (migrate_social.php) after running.\n";
