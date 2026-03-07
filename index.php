<?php
require_once __DIR__ . '/init.php';
$pageTitle = 'หน้าแรก';
$currentPage = 'home';
$categories = getCategories();
if (!isset($GLOBALS['_allSettings'])) $GLOBALS['_allSettings'] = getAllSettings();
$s = $GLOBALS['_allSettings'];
$g = function($key, $default = '') use ($s) { return !empty($s[$key]) ? $s[$key] : $default; };
include __DIR__ . '/includes/header.php';
?>

<!-- HERO SECTION - Full width background -->
<section class="relative min-h-[70vh] lg:min-h-[85vh] flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    <?php
    $heroImg = $g('hero_image_1', '');
    $heroSrc = $heroImg ? $heroImg : BASE_URL . 'assets/img/hero1.png';
    ?>
    <div class="absolute inset-0">
        <img src="<?= $heroSrc ?>" alt="Elite Pet Design Hero"
            class="w-full h-full object-cover"
            onerror="this.style.display='none'">
        <div class="absolute inset-0 bg-elite-100/20"></div>
        <div class="absolute inset-0" style="background: radial-gradient(ellipse at center, rgba(250,248,244,0.45) 0%, rgba(250,248,244,0.1) 70%, transparent 100%)"></div>
    </div>
    <!-- Content Overlay -->
    <div class="relative z-10 text-center px-4 max-w-3xl mx-auto">
        <p class="text-xs tracking-[0.4em] uppercase text-elite-800 font-semibold mb-4 font-en" style="text-shadow: 0 0 8px rgba(250,248,244,0.9), 0 0 20px rgba(250,248,244,0.6)">Elite Pet Design</p>
        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl font-bold text-elite-900 italic leading-tight mb-3" style="text-shadow: 0 0 12px rgba(250,248,244,0.95), 0 0 30px rgba(250,248,244,0.7), 0 2px 4px rgba(250,248,244,0.8)">
            <?= $g('hero_title', 'Crafted for Every Pet,') ?>
        </h1>
        <p class="text-elite-800 text-base md:text-lg mb-2 font-th font-semibold" style="text-shadow: 0 0 10px rgba(250,248,244,0.9), 0 0 25px rgba(250,248,244,0.6)">
            <?= $g('hero_subtitle', 'เฟอร์นิเจอร์สัตว์เลี้ยง ออกแบบสั่งทำพิเศษ') ?>
        </p>
        <p class="text-elite-700 text-sm md:text-base italic mb-8 font-serif" style="text-shadow: 0 0 8px rgba(250,248,244,0.9), 0 0 20px rgba(250,248,244,0.5)">
            Made-to-order furniture designed specifically<br>for the lifestyle of each beloved pet.
        </p>
        <a href="<?= BASE_URL ?>products.php"
            class="inline-block px-8 py-3 border-2 border-elite-700 text-elite-700 text-sm font-semibold tracking-wider uppercase hover:bg-elite-700 hover:text-white transition-all duration-300">
            Explore Our Work
        </a>
    </div>
</section>

<!-- WELCOME TEXT -->
<section class="py-8 bg-elite-50 reveal">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <p class="text-sm md:text-base text-elite-500 leading-relaxed font-serif italic">
            <?= $g('intro_text', 'Welcome to ELITE PET DESIGN, a workshop where custom pet furniture is hand-crafted to perfectly suit the lives of your furry, feathered, or exotic pets.') ?>
        </p>
    </div>
</section>

<hr class="section-divider">

<!-- OUR PRODUCTS -->
<section class="py-16 bg-elite-50 reveal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-serif font-bold text-elite-800 text-center mb-10 italic">
            <?= $g('products_section_title', 'Our Products') ?>
        </h2>
        <hr class="section-divider mb-10">
        <div class="grid lg:grid-cols-[200px_1fr] gap-6 items-stretch">
            <!-- Category Sidebar with Thumbnails -->
            <div class="flex lg:flex-col gap-3 overflow-x-auto lg:overflow-x-visible pb-2 lg:pb-0 justify-between">
                <?php foreach (array_slice($categories, 0, 6) as $cat):
                    $catImg = $cat['image'] ?? ''; ?>
                    <a href="<?= BASE_URL ?>products.php?category=<?= $cat['slug'] ?>"
                        class="flex-shrink-0 w-[140px] lg:w-full group flex-1">
                        <div class="rounded-xl overflow-hidden border-2 border-elite-200 hover:border-elite-400 transition-colors shadow-sm">
                            <?php if ($catImg): ?>
                                <img src="<?= UPLOAD_URL . $catImg ?>" alt="<?= $cat['name'] ?>"
                                    class="w-full h-24 lg:h-20 object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy"
                                    onerror="this.parentElement.innerHTML='<div class=\'w-full h-24 lg:h-20 bg-elite-200 flex items-center justify-center text-elite-400 text-2xl\'>🐾</div>'">
                            <?php else: ?>
                                <div class="w-full h-24 lg:h-20 bg-elite-200 flex items-center justify-center text-elite-400 text-2xl">🐾</div>
                            <?php endif; ?>
                        </div>
                        <p class="text-center text-xs font-serif italic text-elite-600 mt-1.5 group-hover:text-elite-800 transition-colors"><?= $cat['name'] ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
            <!-- Video -->
            <div class="rounded-2xl overflow-hidden shadow-lg relative">
                <?php
                $pVideoFile = $g('products_video_file');
                $pVideoUrl = $g('products_video_url');
                $pVideoLoop = $g('products_video_loop', '0') === '1';
                if ($pVideoFile): ?>
                    <video class="absolute inset-0 w-full h-full object-cover" autoplay muted playsinline
                        <?= $pVideoLoop ? 'loop' : '' ?>>
                        <source src="<?= $pVideoFile ?>" type="video/mp4">
                    </video>
                <?php elseif ($pVideoUrl): ?>
                    <iframe src="<?= $pVideoUrl ?>" class="w-full aspect-video" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
                <?php else: ?>
                    <div class="video-placeholder aspect-video">
                        <div class="play-btn">
                            <svg viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21" /></svg>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<hr class="section-divider">

<!-- OUR SERVICES -->
<section id="services" class="py-16 bg-white reveal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-serif font-bold text-elite-800 text-center mb-3 italic">
            <?= $g('services_section_title', 'Our Services') ?>
        </h2>
        <hr class="section-divider mb-10">
        <div class="grid lg:grid-cols-[1fr_300px] gap-8 items-stretch">
            <!-- Video -->
            <div class="rounded-2xl overflow-hidden shadow-lg relative">
                <?php
                $sVideoFile = $g('services_video_file');
                $sVideoUrl = $g('services_video_url');
                $sVideoLoop = $g('services_video_loop', '0') === '1';
                if ($sVideoFile): ?>
                    <video class="absolute inset-0 w-full h-full object-cover" autoplay muted playsinline
                        <?= $sVideoLoop ? 'loop' : '' ?>>
                        <source src="<?= $sVideoFile ?>" type="video/mp4">
                    </video>
                <?php elseif ($sVideoUrl): ?>
                    <iframe src="<?= $sVideoUrl ?>" class="absolute inset-0 w-full h-full" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
                <?php else: ?>
                    <div class="absolute inset-0 video-placeholder flex items-center justify-center">
                        <div class="play-btn">
                            <svg viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21" /></svg>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <!-- Service Cards -->
            <?php
            $defaultServices = [
                1 => ['title' => 'ออกแบบเฟอร์นิเจอร์', 'desc' => 'ออกแบบเฟอร์นิเจอร์สัตว์เลี้ยงตามสั่ง ตอบโจทย์ทุกความต้องการ'],
                2 => ['title' => 'ผลิตงานคุณภาพ', 'desc' => 'ผลิตด้วยวัสดุคุณภาพ ทนทาน ปลอดภัยสำหรับสัตว์เลี้ยง'],
                3 => ['title' => 'จัดส่งทั่วประเทศ', 'desc' => 'บริการจัดส่งถึงบ้านทั่วประเทศ พร้อมรับประกันสินค้า'],
            ];
            ?>
            <div class="flex flex-col gap-4 justify-between">
                <?php for ($i = 1; $i <= 3; $i++):
                    $sTitle = $g("service_{$i}_title", $defaultServices[$i]['title']);
                    $sDesc = $g("service_{$i}_desc", $defaultServices[$i]['desc']);
                    $sImgRaw = $g("service_{$i}_image", '');
                    $sImg = $sImgRaw ? $sImgRaw : BASE_URL . "assets/img/service{$i}.png";
                ?>
                    <div class="group flex-1">
                        <div class="rounded-xl overflow-hidden mb-2 shadow-sm">
                            <img src="<?= $sImg ?>" alt="<?= $sTitle ?>"
                                class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy"
                                onerror="this.parentElement.innerHTML='<div class=\'w-full h-28 bg-elite-200 flex items-center justify-center text-elite-400\'>🔧</div>'">
                        </div>
                        <h3 class="font-serif text-sm font-bold text-elite-700 italic"><?= $sTitle ?></h3>
                        <p class="text-xs text-elite-400 leading-relaxed"><?= $sDesc ?></p>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</section>

<hr class="section-divider">

<!-- MATERIALS -->
<?php $matTitle = $g('materials_title'); $matText = $g('materials_text'); if ($matTitle || $matText): ?>
<section id="materials" class="relative py-20 overflow-hidden reveal">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="<?= BASE_URL ?>assets/img/materials-bg.jpg" alt="Materials"
            class="w-full h-full object-cover"
            onerror="this.style.display='none'">
        <div class="absolute inset-0 bg-elite-100/70 backdrop-blur-[2px]"></div>
    </div>
    <div class="relative z-10 max-w-4xl mx-auto px-4 text-center">
        <?php if ($matTitle): ?>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-elite-800 mb-4 italic"><?= $matTitle ?></h2>
        <?php endif; ?>
        <?php if ($matText): ?>
            <p class="text-elite-600 leading-relaxed text-base"><?= $matText ?></p>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<hr class="section-divider">

<!-- CUSTOMER REVIEWS - Paper/parchment style -->
<section id="reviews" class="py-16 bg-elite-50 reveal">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-serif font-bold text-elite-800 text-center mb-10 italic">Customer Reviews</h2>
        <hr class="section-divider mb-10">
        <div class="grid md:grid-cols-2 gap-8">
            <?php for ($i = 1; $i <= 2; $i++):
                $rName = $g("review_{$i}_name");
                $rText = $g("review_{$i}_text");
                if ($rName && $rText): ?>
                <div class="relative bg-[#f5efe3] rounded-sm p-8 shadow-sm"
                    style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22><filter id=%22n%22><feTurbulence baseFrequency=%220.06%22 type=%22fractalNoise%22/></filter><rect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23n)%22 opacity=%220.04%22/></svg>'); background-size: cover;">
                    <p class="text-elite-700 leading-relaxed text-base italic font-serif mb-6">"<?= $rText ?>"</p>
                    <p class="text-sm text-elite-500">— <span class="font-semibold"><?= $rName ?></span></p>
                </div>
            <?php endif; endfor; ?>
        </div>
    </div>
</section>

<hr class="section-divider">

<!-- CONTACT US -->
<section class="py-16 bg-white reveal">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-serif font-bold text-elite-800 text-center mb-10 italic">Contact Us</h2>
        <hr class="section-divider mb-10">
        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Map -->
            <div class="rounded-lg overflow-hidden shadow-sm" style="min-height: 280px;">
                <?php $mapUrl = $g('contact_map_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d402.3!2d102.8175765!3d16.4061353!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31228baeeaff5de7%3A0x3a1cf5d69159511a!2sPorPhayakCattery!5e0!3m2!1sth!2sth!4v1709654400000'); if ($mapUrl): ?>
                    <iframe src="<?= $mapUrl ?>" width="100%" height="100%" style="border:0; min-height:280px;"
                        allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <?php else: ?>
                    <div class="w-full h-full bg-elite-200 flex items-center justify-center text-elite-400 min-h-[280px]">
                        <span>📍 Google Maps</span>
                    </div>
                <?php endif; ?>
            </div>
            <!-- Contact Info -->
            <div class="flex flex-col justify-center gap-4">
                <h3 class="font-serif text-xl font-bold text-elite-800 italic">Visit Our Studio</h3>
                <?php
                $addr = $g('contact_address');
                $lineId = $g('social_line_id');
                $phone = $g('contact_phone');
                $email = $g('contact_email');
                $hours = $g('contact_working_hours');
                if ($addr): ?><p class="text-sm text-elite-600"><?= $addr ?></p><?php endif;
                if ($lineId): ?><p class="text-sm text-elite-600">LINE: <a href="<?= $g('social_line_url', '#') ?>" class="text-elite-700 font-semibold hover:underline"><?= $lineId ?></a></p><?php endif;
                if ($phone): ?><p class="text-sm text-elite-600">TEL: <span class="font-semibold"><?= $phone ?></span></p><?php endif;
                if ($email): ?><p class="text-sm text-elite-600">Email: <span class="font-semibold"><?= $email ?></span></p><?php endif;
                if ($hours): ?><p class="text-sm text-elite-500"><?= $hours ?></p><?php endif; ?>
                <div class="flex gap-4 mt-3">
                    <?php if ($fbUrl = $g('social_facebook_url')): ?>
                        <a href="<?= $fbUrl ?>" target="_blank" class="group flex flex-col items-center gap-1.5">
                            <div class="w-11 h-11 rounded-full bg-[#1877F2] text-white flex items-center justify-center shadow-lg shadow-[#1877F2]/25 group-hover:scale-110 group-hover:shadow-[#1877F2]/40 transition-all duration-300">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </div>
                            <span class="text-[10px] text-elite-400 group-hover:text-elite-600 transition-colors">Facebook</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($igUrl = $g('social_instagram_url')): ?>
                        <a href="<?= $igUrl ?>" target="_blank" class="group flex flex-col items-center gap-1.5">
                            <div class="w-11 h-11 rounded-full text-white flex items-center justify-center shadow-lg shadow-pink-500/25 group-hover:scale-110 group-hover:shadow-pink-500/40 transition-all duration-300"
                                style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            </div>
                            <span class="text-[10px] text-elite-400 group-hover:text-elite-600 transition-colors">Instagram</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($lineUrl = $g('social_line_url')): ?>
                        <a href="<?= $lineUrl ?>" target="_blank" class="group flex flex-col items-center gap-1.5">
                            <div class="w-11 h-11 rounded-full bg-[#06C755] text-white flex items-center justify-center shadow-lg shadow-[#06C755]/25 group-hover:scale-110 group-hover:shadow-[#06C755]/40 transition-all duration-300">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 10.304c0-5.369-5.383-9.738-12-9.738-6.616 0-12 4.369-12 9.738 0 4.814 4.269 8.846 10.036 9.608.391.084.922.258 1.057.592.121.303.079.778.039 1.085l-.171 1.027c-.053.303-.242 1.186 1.039.647 1.281-.54 6.911-4.069 9.428-6.967C23.309 14.254 24 12.382 24 10.304z"/></svg>
                            </div>
                            <span class="text-[10px] text-elite-400 group-hover:text-elite-600 transition-colors">LINE</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>