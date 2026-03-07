<?php
require_once __DIR__ . '/../init.php';
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
$adminPage = 'settings';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'site_name', 'site_tagline',
        'hero_title', 'hero_subtitle',
        'intro_text',
        'products_video_url', 'products_section_title', 'products_section_subtitle',
        'services_video_url', 'services_section_title',
        'service_1_title', 'service_1_desc',
        'service_2_title', 'service_2_desc',
        'service_3_title', 'service_3_desc',
        'materials_title', 'materials_text',
        'review_1_name', 'review_1_text', 'review_1_rating',
        'review_2_name', 'review_2_text', 'review_2_rating',
        'social_line_url', 'social_line_id',
        'social_facebook_url', 'social_instagram_url',
        'contact_phone', 'contact_email', 'contact_address',
        'contact_map_embed', 'contact_working_hours',
    ];
    // Handle loop checkboxes (unchecked = not sent, so default to 0)
    saveSetting('products_video_loop', isset($_POST['products_video_loop']) ? '1' : '0');
    saveSetting('services_video_loop', isset($_POST['services_video_loop']) ? '1' : '0');
    foreach ($fields as $f) {
        if (isset($_POST[$f])) {
            saveSetting($f, $_POST[$f]);
        }
    }

    // Handle image uploads
    $imageFields = ['hero_image_1', 'hero_image_2', 'hero_image_3', 'service_1_image', 'service_2_image', 'service_3_image'];
    foreach ($imageFields as $imgKey) {
        if (!empty($_FILES[$imgKey]['tmp_name']) && $_FILES[$imgKey]['error'] === 0) {
            $path = uploadImage($_FILES[$imgKey], 'settings');
            if ($path) saveSetting($imgKey, UPLOAD_URL . $path);
        }
    }

    // Handle video uploads
    $videoFields = ['products_video_file', 'services_video_file'];
    foreach ($videoFields as $vidKey) {
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
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการหน้าเว็บ | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
    <style>
        .section-card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 20px; }
        .section-card .card-header { background: #f8f6f0; border-bottom: 1px solid #e8e0d0; border-radius: 12px 12px 0 0 !important; font-weight: 700; padding: 14px 20px; }
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
            <h4 class="fw-bold mb-0"><i class="bi bi-gear me-2"></i>จัดการหน้าเว็บ</h4>
        </div>

        <?php if ($msg): ?>
            <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i><?= $msg ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <!-- Branding -->
            <div class="card section-card">
                <div class="card-header"><i class="bi bi-building me-2"></i>Branding</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อเว็บไซต์</label>
                            <input type="text" name="site_name" class="form-control" value="<?= $v('site_name') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tagline</label>
                            <input type="text" name="site_tagline" class="form-control"
                                value="<?= $v('site_tagline') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero Section -->
            <div class="card section-card">
                <div class="card-header"><i class="bi bi-image me-2"></i>Hero Section (หน้าแรก)</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">หัวข้อ Hero</label>
                            <input type="text" name="hero_title" class="form-control" value="<?= $v('hero_title') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">คำอธิบาย Hero</label>
                            <textarea name="hero_subtitle" class="form-control"
                                rows="2"><?= $v('hero_subtitle') ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">รูป Hero 1</label>
                            <?php if (!empty($s['hero_image_1'])): ?><br><img src="<?= BASE_URL . $s['hero_image_1'] ?>"
                                    class="img-preview mb-2"
                                    onerror="this.style.display='none'"><?php endif; ?>
                            <input type="file" name="hero_image_1" class="form-control form-control-sm"
                                accept="image/*">
                            <div class="form-text">ปัจจุบัน: <?= $v('hero_image_1', 'ไม่มี') ?></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">รูป Hero 2</label>
                            <?php if (!empty($s['hero_image_2'])): ?><br><img src="<?= BASE_URL . $s['hero_image_2'] ?>"
                                    class="img-preview mb-2"
                                    onerror="this.style.display='none'"><?php endif; ?>
                            <input type="file" name="hero_image_2" class="form-control form-control-sm"
                                accept="image/*">
                            <div class="form-text">ปัจจุบัน: <?= $v('hero_image_2', 'ไม่มี') ?></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">รูป Hero 3</label>
                            <?php if (!empty($s['hero_image_3'])): ?><br><img src="<?= BASE_URL . $s['hero_image_3'] ?>"
                                    class="img-preview mb-2"
                                    onerror="this.style.display='none'"><?php endif; ?>
                            <input type="file" name="hero_image_3" class="form-control form-control-sm"
                                accept="image/*">
                            <div class="form-text">ปัจจุบัน: <?= $v('hero_image_3', 'ไม่มี') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Intro -->
            <div class="card section-card">
                <div class="card-header"><i class="bi bi-text-paragraph me-2"></i>Intro Text</div>
                <div class="card-body">
                    <label class="form-label">ข้อความต้อนรับ</label>
                    <textarea name="intro_text" class="form-control" rows="3"><?= $v('intro_text') ?></textarea>
                </div>
            </div>

            <!-- Products Section -->
            <div class="card section-card">
                <div class="card-header"><i class="bi bi-collection-play me-2"></i>Products Section</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">หัวข้อ</label>
                            <input type="text" name="products_section_title" class="form-control"
                                value="<?= $v('products_section_title') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">คำอธิบาย</label>
                            <textarea name="products_section_subtitle" class="form-control"
                                rows="2"><?= $v('products_section_subtitle') ?></textarea>
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
                            <input type="text" name="products_video_url" class="form-control"
                                value="<?= $v('products_video_url') ?>" placeholder="https://www.youtube.com/embed/xxxxx">
                            <div class="form-text">⚠️ ถ้าอัปโหลดไฟล์ MP4 ไว้ จะใช้ไฟล์อัปโหลดก่อน YouTube</div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="products_video_loop" id="productsLoop" value="1"
                                    <?= ($s['products_video_loop'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="productsLoop">
                                    🔁 เล่นวนซ้ำ (Loop) — วีดีโอจะเล่นซ้ำอัตโนมัติเมื่อจบ
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Section -->
            <div class="card section-card">
                <div class="card-header"><i class="bi bi-tools me-2"></i>Services Section</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">หัวข้อ</label>
                            <input type="text" name="services_section_title" class="form-control"
                                value="<?= $v('services_section_title') ?>">
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
                            <div class="form-text">รองรับ MP4, MOV, WEBM, AVI (สูงสุด 200MB)</div>
                        </div>
                        <div class="col-md-6">
                            <div class="or-divider">หรือ ใช้ YouTube URL</div>
                            <label class="form-label">YouTube Embed URL</label>
                            <input type="text" name="services_video_url" class="form-control"
                                value="<?= $v('services_video_url') ?>" placeholder="https://www.youtube.com/embed/xxxxx">
                            <div class="form-text">⚠️ ถ้าอัปโหลดไฟล์ MP4 ไว้ จะใช้ไฟล์อัปโหลดก่อน YouTube</div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="services_video_loop" id="servicesLoop" value="1"
                                    <?= ($s['services_video_loop'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="servicesLoop">
                                    🔁 เล่นวนซ้ำ (Loop) — วีดีโอจะเล่นซ้ำอัตโนมัติเมื่อจบ
                                </label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="row g-3 mb-3">
                            <div class="col-12"><h6 class="fw-bold text-muted">Service <?= $i ?></h6></div>
                            <div class="col-md-4">
                                <label class="form-label">ชื่อ</label>
                                <input type="text" name="service_<?= $i ?>_title" class="form-control"
                                    value="<?= $v("service_{$i}_title") ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">คำอธิบาย</label>
                                <textarea name="service_<?= $i ?>_desc" class="form-control"
                                    rows="2"><?= $v("service_{$i}_desc") ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">รูปภาพ</label>
                                <?php $simg = $s["service_{$i}_image"] ?? ''; if ($simg): ?><br><img
                                        src="<?= BASE_URL . $simg ?>" class="img-preview mb-2"
                                        onerror="this.style.display='none'"><?php endif; ?>
                                <input type="file" name="service_<?= $i ?>_image" class="form-control form-control-sm"
                                    accept="image/*">
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Materials -->
            <div class="card section-card">
                <div class="card-header"><i class="bi bi-palette me-2"></i>Materials Section</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">หัวข้อ</label>
                            <input type="text" name="materials_title" class="form-control"
                                value="<?= $v('materials_title') ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">รายละเอียดวัสดุ</label>
                            <textarea name="materials_text" class="form-control"
                                rows="3"><?= $v('materials_text') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews -->
            <div class="card section-card">
                <div class="card-header"><i class="bi bi-star me-2"></i>Customer Reviews (หน้าแรก)</div>
                <div class="card-body">
                    <?php for ($i = 1; $i <= 2; $i++): ?>
                        <div class="row g-3 mb-3">
                            <div class="col-12"><h6 class="fw-bold text-muted">Review <?= $i ?></h6></div>
                            <div class="col-md-3">
                                <label class="form-label">ชื่อลูกค้า</label>
                                <input type="text" name="review_<?= $i ?>_name" class="form-control"
                                    value="<?= $v("review_{$i}_name") ?>">
                            </div>
                            <div class="col-md-7">
                                <label class="form-label">ข้อความรีวิว</label>
                                <textarea name="review_<?= $i ?>_text" class="form-control"
                                    rows="2"><?= $v("review_{$i}_text") ?></textarea>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">คะแนน (1-5)</label>
                                <select name="review_<?= $i ?>_rating" class="form-select">
                                    <?php for ($r = 5; $r >= 1; $r--): ?>
                                        <option value="<?= $r ?>"
                                            <?= ($s["review_{$i}_rating"] ?? 5) == $r ? 'selected' : '' ?>>
                                            <?= str_repeat('★', $r) . str_repeat('☆', 5 - $r) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Social Links -->
            <div class="card section-card">
                <div class="card-header"><i class="bi bi-share me-2"></i>Social Media Links</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">LINE URL</label>
                            <input type="text" name="social_line_url" class="form-control"
                                value="<?= $v('social_line_url') ?>" placeholder="https://line.me/R/ti/p/@xxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">LINE ID</label>
                            <input type="text" name="social_line_id" class="form-control"
                                value="<?= $v('social_line_id') ?>" placeholder="@elitepetdesign">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Facebook URL</label>
                            <input type="text" name="social_facebook_url" class="form-control"
                                value="<?= $v('social_facebook_url') ?>" placeholder="https://www.facebook.com/xxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Instagram URL</label>
                            <input type="text" name="social_instagram_url" class="form-control"
                                value="<?= $v('social_instagram_url') ?>" placeholder="https://www.instagram.com/xxx">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact -->
            <div class="card section-card">
                <div class="card-header"><i class="bi bi-telephone me-2"></i>Contact Info</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">เบอร์โทร</label>
                            <input type="text" name="contact_phone" class="form-control"
                                value="<?= $v('contact_phone') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">อีเมล</label>
                            <input type="text" name="contact_email" class="form-control"
                                value="<?= $v('contact_email') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">เวลาทำการ</label>
                            <input type="text" name="contact_working_hours" class="form-control"
                                value="<?= $v('contact_working_hours') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">ที่อยู่</label>
                            <textarea name="contact_address" class="form-control"
                                rows="2"><?= $v('contact_address') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Google Maps Embed URL</label>
                            <input type="text" name="contact_map_embed" class="form-control"
                                value="<?= $v('contact_map_embed') ?>"
                                placeholder="https://www.google.com/maps/embed?pb=...">
                            <div class="form-text">ไปที่ Google Maps → แชร์ → ฝังแผนที่ → คัดลอก URL ที่อยู่ใน src="..."</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mt-2 mb-5">
                <button type="submit" class="btn btn-paw btn-lg px-5"><i
                        class="bi bi-check-lg me-2"></i>บันทึกทั้งหมด</button>
                <a href="<?= BASE_URL ?>admin/" class="btn btn-outline-secondary btn-lg">ยกเลิก</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
