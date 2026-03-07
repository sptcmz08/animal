<?php
require_once __DIR__ . '/../init.php';
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
$adminPage = 'settings';
$msg = '';
$section = $_GET['section'] ?? 'branding';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = $_POST['_section'] ?? $section;

    // Text fields per section
    $sectionFields = [
        'branding' => ['site_name', 'site_tagline'],
        'hero' => ['hero_title', 'hero_subtitle'],
        'intro' => ['intro_text'],
        'products' => ['products_section_title', 'products_section_subtitle', 'products_video_url'],
        'services' => ['services_section_title', 'services_video_url',
            'service_1_title', 'service_1_desc', 'service_2_title', 'service_2_desc', 'service_3_title', 'service_3_desc'],
        'materials' => ['materials_title', 'materials_text'],
        'reviews' => ['review_1_name', 'review_1_text', 'review_1_rating', 'review_2_name', 'review_2_text', 'review_2_rating'],
        'social' => ['social_line_url', 'social_line_id', 'social_facebook_url', 'social_instagram_url'],
        'contact' => ['contact_phone', 'contact_email', 'contact_address', 'contact_map_embed', 'contact_working_hours'],
    ];

    $fields = $sectionFields[$section] ?? [];
    foreach ($fields as $f) {
        if (isset($_POST[$f])) {
            saveSetting($f, $_POST[$f]);
        }
    }

    // Handle checkboxes
    if ($section === 'products') {
        saveSetting('products_video_loop', isset($_POST['products_video_loop']) ? '1' : '0');
    }
    if ($section === 'services') {
        saveSetting('services_video_loop', isset($_POST['services_video_loop']) ? '1' : '0');
    }

    // Handle image uploads
    $sectionImages = [
        'branding' => ['site_logo'],
        'hero' => ['hero_image_1', 'hero_image_2', 'hero_image_3'],
        'services' => ['service_1_image', 'service_2_image', 'service_3_image'],
    ];
    foreach ($sectionImages[$section] ?? [] as $imgKey) {
        if (!empty($_FILES[$imgKey]['tmp_name']) && $_FILES[$imgKey]['error'] === 0) {
            $path = uploadImage($_FILES[$imgKey], 'settings');
            if ($path) saveSetting($imgKey, UPLOAD_URL . $path);
        }
    }

    // Handle video uploads
    $sectionVideos = [
        'products' => ['products_video_file'],
        'services' => ['services_video_file'],
    ];
    foreach ($sectionVideos[$section] ?? [] as $vidKey) {
        if (!empty($_FILES[$vidKey]['tmp_name']) && $_FILES[$vidKey]['error'] === 0) {
            $path = uploadVideo($_FILES[$vidKey], 'videos');
            if ($path) saveSetting($vidKey, UPLOAD_URL . $path);
        }
    }

    $msg = '✅ บันทึกข้อมูลเรียบร้อยแล้ว';
}

$s = getAllSettings();
$v = function($key, $default = '') use ($s) {
    return htmlspecialchars($s[$key] ?? $default, ENT_QUOTES, 'UTF-8');
};

$sectionTitles = [
    'branding' => ['icon' => 'bi-building', 'title' => 'Branding'],
    'hero' => ['icon' => 'bi-image', 'title' => 'Hero Section (หน้าแรก)'],
    'intro' => ['icon' => 'bi-text-paragraph', 'title' => 'Intro Text'],
    'products' => ['icon' => 'bi-collection-play', 'title' => 'Products Section'],
    'services' => ['icon' => 'bi-tools', 'title' => 'Services Section'],
    'materials' => ['icon' => 'bi-palette', 'title' => 'Materials Section'],
    'reviews' => ['icon' => 'bi-star', 'title' => 'Customer Reviews'],
    'social' => ['icon' => 'bi-share', 'title' => 'Social Media Links'],
    'contact' => ['icon' => 'bi-telephone', 'title' => 'Contact Info'],
];
$st = $sectionTitles[$section] ?? $sectionTitles['branding'];
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $st['title'] ?> | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
    <style>
        .section-card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 20px; }
        .section-card .card-body { padding: 20px; }
        .img-preview { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #e8e0d0; }
        .vid-preview { max-width: 280px; border-radius: 8px; border: 2px solid #e8e0d0; }
        .form-label { font-weight: 600; font-size: 0.875rem; color: #5c4a2a; }
        .form-text { font-size: 0.75rem; }
        .or-divider { display: flex; align-items: center; gap: 10px; color: #999; font-size: 0.8rem; margin: 12px 0; }
        .or-divider::before, .or-divider::after { content: ''; flex: 1; height: 1px; background: #ddd; }
    </style>
</head>

<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="admin-main">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-bold mb-0"><i class="bi <?= $st['icon'] ?> me-2"></i><?= $st['title'] ?></h4>
        </div>

        <?php if ($msg): ?>
            <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i><?= $msg ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_section" value="<?= $section ?>">
            <div class="card section-card">
                <div class="card-body">

<?php if ($section === 'branding'): ?>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">โลโก้เว็บไซต์</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <?php $curLogo = $s['site_logo'] ?? ''; if ($curLogo): ?>
                                    <img src="<?= $curLogo ?>" class="img-preview" style="width:120px;height:auto;max-height:60px" onerror="this.style.display='none'">
                                    <span class="text-success small"><i class="bi bi-check-circle"></i> มีโลโก้อยู่แล้ว</span>
                                <?php else: ?>
                                    <span class="text-muted small">ยังไม่ได้อัปโหลดโลโก้ (จะแสดงชื่อเว็บแทน)</span>
                                <?php endif; ?>
                            </div>
                            <input type="file" name="site_logo" class="form-control form-control-sm" accept="image/*">
                            <div class="form-text">แนะนำ PNG โปร่งใส ขนาด 200x60px หรือใหญ่กว่า</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ชื่อเว็บไซต์</label>
                            <input type="text" name="site_name" class="form-control" value="<?= $v('site_name') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tagline</label>
                            <input type="text" name="site_tagline" class="form-control" value="<?= $v('site_tagline') ?>">
                        </div>
                    </div>

<?php elseif ($section === 'hero'): ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">หัวข้อ Hero</label>
                            <input type="text" name="hero_title" class="form-control" value="<?= $v('hero_title') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">คำอธิบาย Hero</label>
                            <textarea name="hero_subtitle" class="form-control" rows="2"><?= $v('hero_subtitle') ?></textarea>
                        </div>
                        <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="col-md-4">
                            <label class="form-label">รูป Hero <?= $i ?></label>
                            <?php if (!empty($s["hero_image_{$i}"])): ?><br><img src="<?= $s["hero_image_{$i}"] ?>"
                                    class="img-preview mb-2" onerror="this.style.display='none'"><?php endif; ?>
                            <input type="file" name="hero_image_<?= $i ?>" class="form-control form-control-sm" accept="image/*">
                            <div class="form-text">ปัจจุบัน: <?= $v("hero_image_{$i}", 'ไม่มี') ?></div>
                        </div>
                        <?php endfor; ?>
                    </div>

<?php elseif ($section === 'intro'): ?>
                    <label class="form-label">ข้อความต้อนรับ</label>
                    <textarea name="intro_text" class="form-control" rows="4"><?= $v('intro_text') ?></textarea>
                    <div class="form-text mt-1">แสดงใต้ Hero Section ในหน้าแรก</div>

<?php elseif ($section === 'products'): ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">หัวข้อ</label>
                            <input type="text" name="products_section_title" class="form-control" value="<?= $v('products_section_title') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">คำอธิบาย</label>
                            <textarea name="products_section_subtitle" class="form-control" rows="2"><?= $v('products_section_subtitle') ?></textarea>
                        </div>
                    </div>
                    <hr class="my-3">
                    <h6 class="fw-bold text-muted mb-3"><i class="bi bi-camera-video me-1"></i> วีดีโอ Products</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">อัปโหลดไฟล์วีดีโอ (MP4, MOV, WEBM)</label>
                            <?php $pvFile = $s['products_video_file'] ?? ''; if ($pvFile): ?>
                                <div class="mb-2">
                                    <video src="<?= $pvFile ?>" class="vid-preview" muted controls></video>
                                    <div class="form-text text-success">✅ มีวีดีโออัปโหลดอยู่แล้ว</div>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="products_video_file" class="form-control form-control-sm" accept="video/mp4,video/quicktime,video/webm,video/avi">
                            <div class="form-text">รองรับ MP4, MOV, WEBM, AVI (สูงสุด 200MB)</div>
                        </div>
                        <div class="col-md-6">
                            <div class="or-divider">หรือ ใช้ YouTube URL</div>
                            <label class="form-label">YouTube Embed URL</label>
                            <input type="text" name="products_video_url" class="form-control" value="<?= $v('products_video_url') ?>" placeholder="https://www.youtube.com/embed/xxxxx">
                            <div class="form-text">⚠️ ถ้าอัปโหลดไฟล์ MP4 ไว้ จะใช้ไฟล์อัปโหลดก่อน YouTube</div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="products_video_loop" id="productsLoop" value="1"
                                    <?= ($s['products_video_loop'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="productsLoop">🔁 เล่นวนซ้ำ (Loop)</label>
                            </div>
                        </div>
                    </div>

<?php elseif ($section === 'services'): ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">หัวข้อ</label>
                            <input type="text" name="services_section_title" class="form-control" value="<?= $v('services_section_title') ?>">
                        </div>
                    </div>
                    <hr class="my-3">
                    <h6 class="fw-bold text-muted mb-3"><i class="bi bi-camera-video me-1"></i> วีดีโอ Services</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">อัปโหลดไฟล์วีดีโอ (MP4, MOV, WEBM)</label>
                            <?php $svFile = $s['services_video_file'] ?? ''; if ($svFile): ?>
                                <div class="mb-2">
                                    <video src="<?= $svFile ?>" class="vid-preview" muted controls></video>
                                    <div class="form-text text-success">✅ มีวีดีโออัปโหลดอยู่แล้ว</div>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="services_video_file" class="form-control form-control-sm" accept="video/mp4,video/quicktime,video/webm,video/avi">
                        </div>
                        <div class="col-md-6">
                            <div class="or-divider">หรือ ใช้ YouTube URL</div>
                            <label class="form-label">YouTube Embed URL</label>
                            <input type="text" name="services_video_url" class="form-control" value="<?= $v('services_video_url') ?>" placeholder="https://www.youtube.com/embed/xxxxx">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="services_video_loop" id="servicesLoop" value="1"
                                    <?= ($s['services_video_loop'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="servicesLoop">🔁 เล่นวนซ้ำ (Loop)</label>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="row g-3 mb-3">
                            <div class="col-12"><h6 class="fw-bold text-muted">Service <?= $i ?></h6></div>
                            <div class="col-md-4">
                                <label class="form-label">ชื่อ</label>
                                <input type="text" name="service_<?= $i ?>_title" class="form-control" value="<?= $v("service_{$i}_title") ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">คำอธิบาย</label>
                                <textarea name="service_<?= $i ?>_desc" class="form-control" rows="2"><?= $v("service_{$i}_desc") ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">รูปภาพ</label>
                                <?php $simg = $s["service_{$i}_image"] ?? ''; if ($simg): ?><br><img
                                        src="<?= $simg ?>" class="img-preview mb-2"
                                        onerror="this.style.display='none'"><?php endif; ?>
                                <input type="file" name="service_<?= $i ?>_image" class="form-control form-control-sm" accept="image/*">
                            </div>
                        </div>
                    <?php endfor; ?>

<?php elseif ($section === 'materials'): ?>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">หัวข้อ</label>
                            <input type="text" name="materials_title" class="form-control" value="<?= $v('materials_title') ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">รายละเอียดวัสดุ</label>
                            <textarea name="materials_text" class="form-control" rows="4"><?= $v('materials_text') ?></textarea>
                        </div>
                    </div>

<?php elseif ($section === 'reviews'): ?>
                    <?php for ($i = 1; $i <= 2; $i++): ?>
                        <div class="row g-3 mb-3">
                            <div class="col-12"><h6 class="fw-bold text-muted">Review <?= $i ?></h6></div>
                            <div class="col-md-3">
                                <label class="form-label">ชื่อลูกค้า</label>
                                <input type="text" name="review_<?= $i ?>_name" class="form-control" value="<?= $v("review_{$i}_name") ?>">
                            </div>
                            <div class="col-md-7">
                                <label class="form-label">ข้อความรีวิว</label>
                                <textarea name="review_<?= $i ?>_text" class="form-control" rows="2"><?= $v("review_{$i}_text") ?></textarea>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">คะแนน (1-5)</label>
                                <select name="review_<?= $i ?>_rating" class="form-select">
                                    <?php for ($r = 5; $r >= 1; $r--): ?>
                                        <option value="<?= $r ?>" <?= ($s["review_{$i}_rating"] ?? 5) == $r ? 'selected' : '' ?>>
                                            <?= str_repeat('★', $r) . str_repeat('☆', 5 - $r) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    <?php endfor; ?>

<?php elseif ($section === 'social'): ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">LINE URL</label>
                            <input type="text" name="social_line_url" class="form-control" value="<?= $v('social_line_url') ?>" placeholder="https://line.me/R/ti/p/@xxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">LINE ID</label>
                            <input type="text" name="social_line_id" class="form-control" value="<?= $v('social_line_id') ?>" placeholder="@elitepetdesign">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Facebook URL</label>
                            <input type="text" name="social_facebook_url" class="form-control" value="<?= $v('social_facebook_url') ?>" placeholder="https://www.facebook.com/xxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Instagram URL</label>
                            <input type="text" name="social_instagram_url" class="form-control" value="<?= $v('social_instagram_url') ?>" placeholder="https://www.instagram.com/xxx">
                        </div>
                    </div>

<?php elseif ($section === 'contact'): ?>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">เบอร์โทร</label>
                            <input type="text" name="contact_phone" class="form-control" value="<?= $v('contact_phone') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">อีเมล</label>
                            <input type="text" name="contact_email" class="form-control" value="<?= $v('contact_email') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">เวลาทำการ</label>
                            <input type="text" name="contact_working_hours" class="form-control" value="<?= $v('contact_working_hours') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">ที่อยู่</label>
                            <textarea name="contact_address" class="form-control" rows="2"><?= $v('contact_address') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Google Maps Embed URL</label>
                            <input type="text" name="contact_map_embed" class="form-control" value="<?= $v('contact_map_embed') ?>"
                                placeholder="https://www.google.com/maps/embed?pb=...">
                            <div class="form-text">ไปที่ Google Maps → แชร์ → ฝังแผนที่ → คัดลอก URL ที่อยู่ใน src="..."</div>
                        </div>
                    </div>

<?php endif; ?>

                </div>
            </div>

            <div class="d-flex gap-3 mt-2 mb-5">
                <button type="submit" class="btn btn-paw btn-lg px-5"><i class="bi bi-check-lg me-2"></i>บันทึก</button>
                <a href="<?= BASE_URL ?>" class="btn btn-outline-secondary btn-lg">ดูหน้าเว็บ</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
