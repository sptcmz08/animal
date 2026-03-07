-- ============================================
-- PawHaven - Pet Furniture E-commerce Database
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `contact_messages`;
DROP TABLE IF EXISTS `coupons`;
DROP TABLE IF EXISTS `wishlists`;
DROP TABLE IF EXISTS `blog_posts`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `settings`;
SET FOREIGN_KEY_CHECKS = 1;

-- Site Settings
CREATE TABLE `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Categories
CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT,
    `image` VARCHAR(500),
    `parent_id` INT DEFAULT NULL,
    `sort_order` INT DEFAULT 0,
    `status` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Products
CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT,
    `name` VARCHAR(500) NOT NULL,
    `slug` VARCHAR(500) NOT NULL UNIQUE,
    `sku` VARCHAR(100),
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `sale_price` DECIMAL(10,2) DEFAULT NULL,
    `short_description` TEXT,
    `description` LONGTEXT,
    `stock` INT DEFAULT 0,
    `featured` TINYINT(1) DEFAULT 0,
    `status` ENUM('active','inactive','draft') DEFAULT 'active',
    `views` INT DEFAULT 0,
    `sold_count` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    INDEX `idx_featured` (`featured`),
    INDEX `idx_status` (`status`),
    INDEX `idx_category` (`category_id`)
) ENGINE=InnoDB;

-- Product Images
CREATE TABLE `product_images` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `image_path` VARCHAR(500) NOT NULL,
    `is_primary` TINYINT(1) DEFAULT 0,
    `sort_order` INT DEFAULT 0,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Users
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `address` TEXT,
    `province` VARCHAR(100),
    `district` VARCHAR(100),
    `zipcode` VARCHAR(10),
    `role` ENUM('customer','admin') DEFAULT 'customer',
    `avatar` VARCHAR(500),
    `status` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Orders
CREATE TABLE `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `order_number` VARCHAR(50) NOT NULL UNIQUE,
    `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `shipping_cost` DECIMAL(10,2) DEFAULT 0,
    `discount` DECIMAL(10,2) DEFAULT 0,
    `total` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `coupon_id` INT DEFAULT NULL,
    `status` ENUM('pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    `payment_method` ENUM('transfer','cod','qr_promptpay','credit_card') DEFAULT 'transfer',
    `payment_status` ENUM('pending','paid','refunded') DEFAULT 'pending',
    `payment_proof` VARCHAR(500),
    `shipping_name` VARCHAR(255),
    `shipping_phone` VARCHAR(20),
    `shipping_address` TEXT,
    `shipping_province` VARCHAR(100),
    `shipping_district` VARCHAR(100),
    `shipping_zipcode` VARCHAR(10),
    `tracking_number` VARCHAR(100),
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_status` (`status`),
    INDEX `idx_order_number` (`order_number`)
) ENGINE=InnoDB;

-- Order Items
CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT,
    `product_name` VARCHAR(500),
    `quantity` INT NOT NULL DEFAULT 1,
    `price` DECIMAL(10,2) NOT NULL,
    `total` DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Reviews
CREATE TABLE `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `user_id` INT,
    `rating` TINYINT NOT NULL DEFAULT 5,
    `comment` TEXT,
    `status` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Blog Posts
CREATE TABLE `blog_posts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(500) NOT NULL,
    `slug` VARCHAR(500) NOT NULL UNIQUE,
    `excerpt` TEXT,
    `content` LONGTEXT,
    `image` VARCHAR(500),
    `author_id` INT,
    `status` ENUM('published','draft') DEFAULT 'published',
    `views` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Wishlists
CREATE TABLE `wishlists` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_wishlist` (`user_id`, `product_id`)
) ENGINE=InnoDB;

-- Coupons
CREATE TABLE `coupons` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `discount_type` ENUM('percent','fixed') DEFAULT 'percent',
    `discount_value` DECIMAL(10,2) NOT NULL,
    `min_order` DECIMAL(10,2) DEFAULT 0,
    `max_uses` INT DEFAULT NULL,
    `used_count` INT DEFAULT 0,
    `expires_at` DATETIME DEFAULT NULL,
    `status` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Contact Messages
CREATE TABLE `contact_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255),
    `phone` VARCHAR(20),
    `subject` VARCHAR(500),
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- SEED DATA
-- ============================================

-- Default Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'PawHaven'),
('site_tagline', 'เฟอร์นิเจอร์สัตว์เลี้ยงดีไซน์พรีเมียม'),
('site_description', 'PawHaven แหล่งรวมเฟอร์นิเจอร์สัตว์เลี้ยงคุณภาพ ดีไซน์สวยงาม ปลอดภัย เพื่อความสุขของสัตว์เลี้ยงและเจ้าของ'),
('contact_email', 'hello@pawhaven.com'),
('contact_phone', '02-XXX-XXXX'),
('contact_line', '@pawhaven'),
('contact_address', 'กรุงเทพมหานคร ประเทศไทย'),
('facebook_url', 'https://facebook.com/pawhaven'),
('instagram_url', 'https://instagram.com/pawhaven'),
('line_url', 'https://line.me/pawhaven'),
('tiktok_url', 'https://tiktok.com/@pawhaven'),
('shipping_fee', '100'),
('free_shipping_min', '2000');

-- Admin User (password: admin123)
INSERT INTO `users` (`name`, `email`, `password`, `phone`, `role`) VALUES
('Admin', 'admin@pawhaven.com', '$2y$10$65Z6OX7mDpfH.8.4d5h6peRsOMAyCTt5LLNpQGPtiMMXVGGFfgsb2', '0812345678', 'admin');

-- Test Customer (password: admin123)
INSERT INTO `users` (`name`, `email`, `password`, `phone`, `address`, `province`, `district`, `zipcode`, `role`) VALUES
('สมชาย รักแมว', 'customer@test.com', '$2y$10$65Z6OX7mDpfH.8.4d5h6peRsOMAyCTt5LLNpQGPtiMMXVGGFfgsb2', '0898765432', '123/45 ถ.สุขุมวิท', 'กรุงเทพมหานคร', 'วัฒนา', '10110', 'customer');

-- Categories
INSERT INTO `categories` (`name`, `slug`, `description`, `image`, `sort_order`) VALUES
('คอนโดแมว', 'cat-condo', 'คอนโดแมวดีไซน์สวย แข็งแรง ให้น้องแมวปีนเล่นได้อย่างปลอดภัย', 'categories/cat-condo.jpg', 1),
('เตียง & ที่นอน', 'pet-bed', 'เตียงและที่นอนสัตว์เลี้ยง นุ่มสบาย ดีไซน์น่ารัก', 'categories/pet-bed.jpg', 2),
('โต๊ะอาหาร & ชาม', 'food-bowl', 'โต๊ะอาหารและชามสัตว์เลี้ยง ใช้งานสะดวก ดีไซน์มินิมอล', 'categories/food-bowl.jpg', 3),
('ชั้นลอย & ที่ปีนเล่น', 'wall-shelf', 'ชั้นลอยติดผนัง ที่ปีนเล่นสำหรับแมว ประหยัดพื้นที่', 'categories/wall-shelf.jpg', 4),
('บ้านสัตว์เลี้ยง', 'pet-house', 'บ้านสัตว์เลี้ยงไม้แท้ ดีไซน์เก๋ กลมกลืนกับบ้าน', 'categories/pet-house.jpg', 5),
('ของเล่น & อุปกรณ์', 'toys-accessories', 'ของเล่นและอุปกรณ์สัตว์เลี้ยง หลากหลายรูปแบบ', 'categories/toys.jpg', 6);

-- Products
INSERT INTO `products` (`category_id`, `name`, `slug`, `sku`, `price`, `sale_price`, `short_description`, `description`, `stock`, `featured`, `sold_count`) VALUES
-- Cat Condos
(1, 'Tower Classic คอนโดแมว 4 ชั้น สไตล์มินิมอล', 'tower-classic-cat-condo', 'CC001', 4990.00, 3990.00,
 'คอนโดแมว 4 ชั้น ไม้แท้ แข็งแรง ดีไซน์มินิมอลสไตล์ญี่ปุ่น',
 '<h3>Tower Classic คอนโดแมว 4 ชั้น</h3><p>คอนโดแมวดีไซน์มินิมอลสไตล์ญี่ปุ่น ผลิตจากไม้ยางพาราแท้ พร้อมเสาลับเล็บ ถ้ำนอน และจุดชมวิว ขนาดกะทัดรัดเหมาะสำหรับทุกมุมบ้าน</p><ul><li>วัสดุ: ไม้ยางพาราแท้</li><li>ขนาด: 50 x 50 x 130 ซม.</li><li>รองรับน้ำหนัก: 15 กก.</li><li>เสาลับเล็บ: เชือกศิลาธร</li></ul>',
 25, 1, 142),

(1, 'Sky Dome คอนโดแมว พร้อมจุดชมวิวอะคริลิค', 'sky-dome-cat-condo', 'CC002', 6990.00, NULL,
 'คอนโดแมวพรีเมียม 5 ชั้น พร้อมโดมอะคริลิคใส',
 '<h3>Sky Dome คอนโดแมว</h3><p>คอนโดแมวระดับพรีเมียม ไฮไลท์คือโดมอะคริลิคใสชั้นบน ให้น้องแมวเพลิดเพลินกับวิวจากที่สูง พร้อมเสาลับเล็บ ถ้ำนอน และแท่นนั่งเล่นหลายจุด</p><ul><li>วัสดุ: ไม้ยางพารา + อะคริลิคใส</li><li>ขนาด: 60 x 50 x 160 ซม.</li><li>รองรับน้ำหนัก: 20 กก.</li></ul>',
 15, 1, 89),

(1, 'Dino Land คอนโดแมว ดีไซน์ไดโนเสาร์', 'dino-land-cat-condo', 'CC003', 5490.00, 4790.00,
 'คอนโดแมวดีไซน์ไดโนเสาร์สุดคิ้วท์ แข็งแรง',
 '<h3>Dino Land คอนโดแมว</h3><p>คอนโดแมวดีไซน์ไดโนเสาร์สุดน่ารัก เหมาะสำหรับตกแต่งห้องเด็กหรือมุมน่ารักในบ้าน เล่นสนุกได้ทั้งปีนป่ายและซ่อนตัว</p>',
 18, 1, 67),

-- Pet Beds
(2, 'Cloud Bed เตียงแมวรูปก้อนเมฆ', 'cloud-bed-cat', 'PB001', 1990.00, 1590.00,
 'เตียงแมวรูปก้อนเมฆ นุ่มสบาย ดีไซน์น่ารัก',
 '<h3>Cloud Bed เตียงแมว</h3><p>เตียงแมวทรงก้อนเมฆน่ารักๆ ผ้าซุปเปอร์ซอฟท์ นุ่มสบาย น้องแมวชอบมาก ติดตั้งได้ทั้งวางพื้นและแขวนผนัง</p><ul><li>วัสดุ: ผ้ากำมะหยี่เกรดพรีเมียม</li><li>ขนาด: 45 x 40 x 15 ซม.</li><li>ซักทำความสะอาดได้</li></ul>',
 50, 1, 234),

(2, 'Donut Bed เตียงสุนัขทรงโดนัท', 'donut-bed-dog', 'PB002', 1490.00, NULL,
 'เตียงสุนัขทรงโดนัท นุ่มลึก อบอุ่น',
 '<h3>Donut Bed เตียงสุนัข</h3><p>เตียงสุนัขทรงโดนัทขอบสูง ให้ความรู้สึกอบอุ่นและปลอดภัย เหมาะสำหรับน้องหมาตัวเล็กถึงกลาง ผ้ากำมะหยี่นุ่มพิเศษ</p>',
 40, 0, 156),

(2, 'Nordic Hammock เปลแมวแขวนผนัง', 'nordic-hammock-cat', 'PB003', 1290.00, NULL,
 'เปลแมวแบบแขวน สไตล์นอร์ดิก',
 '<h3>Nordic Hammock เปลแมว</h3><p>เปลแมวแบบแขวนผนัง สไตล์นอร์ดิกมินิมอล ผ้าฝ้ายธรรมชาติ กรอบไม้บีชแข็งแรง น้องแมวชอบนอนแกว่ง</p>',
 35, 1, 98),

-- Food Bowls
(3, 'Duo Bowl โต๊ะอาหารไม้ดัดโค้ง 2 หลุม', 'duo-bowl-curved', 'FB001', 890.00, 690.00,
 'โต๊ะอาหารสัตว์เลี้ยงไม้ดัดโค้ง 2 หลุม สไตล์มินิมอล',
 '<h3>Duo Bowl โต๊ะอาหาร</h3><p>โต๊ะอาหารสัตว์เลี้ยงดีไซน์ไม้ดัดโค้งสวยงาม ชามเซรามิค 2 หลุม ปลอดสารพิษ ความสูงเหมาะสมกับสรีระสัตว์เลี้ยง</p>',
 60, 1, 312),

(3, 'Solo Bowl ชามอาหารเซรามิคพร้อมฐานไม้', 'solo-bowl-ceramic', 'FB002', 590.00, NULL,
 'ชามอาหารเซรามิค 1 หลุม พร้อมฐานไม้',
 '<h3>Solo Bowl ชามอาหาร</h3><p>ชามอาหารเซรามิคเกรดอาหารปลอดภัย พร้อมฐานไม้ยกสูง ช่วยลดอาการปวดคอขณะทานอาหาร</p>',
 80, 0, 187),

(3, 'Triple Bowl ชุดชามอาหาร 3 หลุม สแตนเลส', 'triple-bowl-stainless', 'FB003', 1290.00, NULL,
 'ชุดโต๊ะอาหาร 3 หลุม สแตนเลส+ไม้',
 '<h3>Triple Bowl ชุดโต๊ะอาหาร</h3><p>ชุดโต๊ะอาหาร 3 หลุม เหมาะสำหรับบ้านที่มีสัตว์เลี้ยงหลายตัว ชามสแตนเลสถอดล้างง่าย ฐานไม้มีแผ่นกันลื่น</p>',
 30, 0, 73),

-- Wall Shelves
(4, 'Bubble Step ชั้นปีนแมว ทรงกลม', 'bubble-step-wall', 'WS001', 790.00, NULL,
 'ชั้นปีนแมวติดผนัง ทรงกลม หลากสี',
 '<h3>Bubble Step ชั้นปีนแมว</h3><p>ชั้นปีนสำหรับแมวติดผนัง ทรงกลมน่ารัก มีหลายสีให้เลือก ติดตั้งง่าย แข็งแรงรับน้ำหนัก 10 กก.</p>',
 100, 1, 445),

(4, 'Cloud Step ชั้นลอยแมว ทรงก้อนเมฆ', 'cloud-step-wall', 'WS002', 890.00, 690.00,
 'ชั้นลอยแมว ทรงก้อนเมฆ ไม้แท้',
 '<h3>Cloud Step ชั้นลอยแมว</h3><p>ชั้นลอยแมวติดผนังทรงก้อนเมฆ ผลิตจากไม้ยางพาราแท้ พร้อมแผ่นกันลื่น ดีไซน์น่ารักตกแต่งบ้านได้</p>',
 70, 0, 198),

(4, 'Bridge Walk สะพานแมวติดผนัง', 'bridge-walk-wall', 'WS003', 1490.00, NULL,
 'สะพานแมวติดผนัง ไม้+อะคริลิค',
 '<h3>Bridge Walk สะพานแมว</h3><p>สะพานแมวติดผนัง ผสมผสานไม้แท้กับอะคริลิคใส ให้น้องแมวเดินเล่นข้ามผนัง เสริมพื้นที่เล่นในแนวตั้ง</p>',
 25, 1, 56),

-- Pet Houses
(5, 'Cube Home บ้านแมว 2-in-1 โต๊ะข้างเตียง', 'cube-home-2in1', 'PH001', 3490.00, 2990.00,
 'บ้านแมวที่เป็นทั้งโต๊ะข้างเตียง เฟอร์นิเจอร์ 2-in-1',
 '<h3>Cube Home บ้านแมว 2-in-1</h3><p>เฟอร์นิเจอร์ 2-in-1 ที่เป็นทั้งโต๊ะข้างเตียงสำหรับคนและบ้านน้องแมว ดีไซน์โมเดิร์น กลมกลืนกับทุกห้อง</p>',
 20, 1, 83),

(5, 'Nordic Curve บ้านแมวสไตล์นอร์ดิก', 'nordic-curve-cat-house', 'PH002', 2990.00, NULL,
 'บ้านแมวทรงโค้ง เป็นทั้งที่วางของ สไตล์นอร์ดิก',
 '<h3>Nordic Curve บ้านแมว</h3><p>บ้านแมวดีไซน์นอร์ดิก ทรงโค้งเรียบหรู เป็นได้ทั้งบ้านแมวและชั้นวางของ ผลิตจากไม้อัดเกรด E1 ปลอดภัย</p>',
 15, 0, 45),

(5, 'Clean Box ตู้ห้องน้ำแมว เก็บกลิ่น', 'clean-box-litter', 'PH003', 2490.00, 1990.00,
 'ตู้ครอบห้องน้ำแมว เก็บกลิ่น ลดทรายกระเด็น',
 '<h3>Clean Box ตู้ห้องน้ำแมว</h3><p>ตู้ครอบห้องน้ำแมวดีไซน์สวย เก็บกลิ่นได้ดี ลดปัญหาทรายกระเด็น เปิดทำความสะอาดง่าย มองจากภายนอกเหมือนตู้เฟอร์นิเจอร์ปกติ</p>',
 22, 1, 167),

-- Toys & Accessories
(6, 'Scratch Post เสาลับเล็บแมว เชือกศิลาธร', 'scratch-post-sisal', 'TA001', 690.00, 490.00,
 'เสาลับเล็บแมว เชือกศิลาธรแท้ ฐานไม้แข็งแรง',
 '<h3>Scratch Post เสาลับเล็บ</h3><p>เสาลับเล็บแมวคุณภาพสูง เชือกศิลาธรแท้ทนทาน ฐานไม้กว้างไม่ล้มง่าย ช่วยป้องกันแมวลับเล็บกับเฟอร์นิเจอร์ในบ้าน</p>',
 100, 0, 523),

(6, 'Feather Wand ไม้ล่อแมว ขนนกธรรมชาติ', 'feather-wand-toy', 'TA002', 290.00, NULL,
 'ไม้ล่อแมว ขนนกธรรมชาติ ด้ามไม้',
 '<h3>Feather Wand ไม้ล่อแมว</h3><p>ไม้ล่อแมวขนนกธรรมชาติ ด้ามไม้ยาว 50 ซม. กระตุ้นสัญชาตญาณล่าเหยื่อ ชวนน้องแมวออกกำลังกาย</p>',
 200, 0, 678);

-- Product Images (using placeholder paths)
INSERT INTO `product_images` (`product_id`, `image_path`, `is_primary`, `sort_order`) VALUES
(1, 'products/tower-classic-1.jpg', 1, 1),
(1, 'products/tower-classic-2.jpg', 0, 2),
(2, 'products/sky-dome-1.jpg', 1, 1),
(2, 'products/sky-dome-2.jpg', 0, 2),
(3, 'products/dino-land-1.jpg', 1, 1),
(4, 'products/cloud-bed-1.jpg', 1, 1),
(5, 'products/donut-bed-1.jpg', 1, 1),
(6, 'products/nordic-hammock-1.jpg', 1, 1),
(7, 'products/duo-bowl-1.jpg', 1, 1),
(8, 'products/solo-bowl-1.jpg', 1, 1),
(9, 'products/triple-bowl-1.jpg', 1, 1),
(10, 'products/bubble-step-1.jpg', 1, 1),
(11, 'products/cloud-step-1.jpg', 1, 1),
(12, 'products/bridge-walk-1.jpg', 1, 1),
(13, 'products/cube-home-1.jpg', 1, 1),
(14, 'products/nordic-curve-1.jpg', 1, 1),
(15, 'products/clean-box-1.jpg', 1, 1),
(16, 'products/scratch-post-1.jpg', 1, 1),
(17, 'products/feather-wand-1.jpg', 1, 1);

-- Sample Reviews
INSERT INTO `reviews` (`product_id`, `user_id`, `rating`, `comment`) VALUES
(1, 2, 5, 'คอนโดสวยมากครับ น้องแมวชอบมาก ปีนเล่นทั้งวัน!'),
(1, 2, 4, 'ดีไซน์สวย แข็งแรง แต่ประกอบค่อนข้างยากหน่อย'),
(4, 2, 5, 'เตียงนุ่มมากค่ะ น้องเหมียวนอนไม่ยอมลุก'),
(7, 2, 5, 'โต๊ะอาหารสวยมากค่ะ ดูดีในบ้านเลย'),
(10, 2, 5, 'ชั้นปีนแข็งแรงดีค่ะ ติดตั้งง่าย'),
(13, 2, 4, 'บ้านแมว 2-in-1 ดีไซน์สวย ใช้เป็นโต๊ะข้างเตียงได้จริง');

-- Sample Blog Posts
INSERT INTO `blog_posts` (`title`, `slug`, `excerpt`, `content`, `image`, `author_id`, `status`) VALUES
('5 เคล็ดลับเลือกคอนโดแมวให้เหมาะกับบ้าน', '5-tips-choose-cat-condo',
 'การเลือกคอนโดแมวไม่ใช่แค่เลือกแบบสวย แต่ต้องเหมาะกับพื้นที่และพฤติกรรมน้องแมวด้วย',
 '<p>การเลือกคอนโดแมวที่ดีต้องคำนึงถึงหลายปัจจัย...</p>', 'blog/cat-condo-tips.jpg', 1, 'published'),
('ทำไมแมวชอบที่สูง? เข้าใจพฤติกรรมน้องเหมียว', 'why-cats-love-heights',
 'แมวชอบอยู่ที่สูงเป็นสัญชาตญาณตามธรรมชาติ มาทำความเข้าใจกันว่าทำไม',
 '<p>แมวเป็นสัตว์ที่ชอบอยู่ที่สูงโดยธรรมชาติ...</p>', 'blog/cat-heights.jpg', 1, 'published'),
('วิธีดูแลเฟอร์นิเจอร์ไม้สัตว์เลี้ยงให้อยู่นาน', 'wood-furniture-care-guide',
 'เฟอร์นิเจอร์ไม้สำหรับสัตว์เลี้ยงต้องการการดูแลพิเศษ เรามีเคล็ดลับดีๆ มาฝาก',
 '<p>เฟอร์นิเจอร์ไม้เป็นวัสดุที่สวยงามและทนทาน...</p>', 'blog/wood-care.jpg', 1, 'published');

-- Sample Coupons
INSERT INTO `coupons` (`code`, `discount_type`, `discount_value`, `min_order`, `max_uses`, `expires_at`, `status`) VALUES
('WELCOME10', 'percent', 10.00, 1000.00, 100, '2026-12-31 23:59:59', 1),
('SAVE500', 'fixed', 500.00, 3000.00, 50, '2026-06-30 23:59:59', 1);
