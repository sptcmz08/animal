<?php
/**
 * PawHaven - Core Functions
 */

// ==================== CATEGORIES ====================
function getCategories($activeOnly = true) {
    global $pdo;
    $sql = "SELECT * FROM categories" . ($activeOnly ? " WHERE status = 1" : "") . " ORDER BY sort_order ASC";
    return $pdo->query($sql)->fetchAll();
}

function getCategory($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getCategoryBySlug($slug) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

// ==================== PRODUCTS ====================
function getProducts($options = []) {
    global $pdo;
    $where = ["p.status = 'active'"];
    $params = [];
    $orderBy = "p.created_at DESC";
    $limit = isset($options['limit']) ? (int)$options['limit'] : 12;
    $offset = isset($options['offset']) ? (int)$options['offset'] : 0;

    if (!empty($options['category_id'])) {
        $where[] = "p.category_id = ?";
        $params[] = $options['category_id'];
    }
    if (!empty($options['featured'])) {
        $where[] = "p.featured = 1";
    }
    if (!empty($options['search'])) {
        $where[] = "(p.name LIKE ? OR p.short_description LIKE ?)";
        $params[] = "%{$options['search']}%";
        $params[] = "%{$options['search']}%";
    }
    if (!empty($options['min_price'])) {
        $where[] = "COALESCE(p.sale_price, p.price) >= ?";
        $params[] = $options['min_price'];
    }
    if (!empty($options['max_price'])) {
        $where[] = "COALESCE(p.sale_price, p.price) <= ?";
        $params[] = $options['max_price'];
    }
    if (!empty($options['sort'])) {
        switch ($options['sort']) {
            case 'price_asc': $orderBy = "COALESCE(p.sale_price, p.price) ASC"; break;
            case 'price_desc': $orderBy = "COALESCE(p.sale_price, p.price) DESC"; break;
            case 'popular': $orderBy = "p.sold_count DESC"; break;
            case 'name': $orderBy = "p.name ASC"; break;
            default: $orderBy = "p.created_at DESC";
        }
    }

    $whereStr = implode(" AND ", $where);
    $sql = "SELECT p.*, 
                   pi.image_path as primary_image,
                   c.name as category_name, c.slug as category_slug
            FROM products p
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE {$whereStr}
            ORDER BY {$orderBy}
            LIMIT {$limit} OFFSET {$offset}";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function countProducts($options = []) {
    global $pdo;
    $where = ["p.status = 'active'"];
    $params = [];

    if (!empty($options['category_id'])) {
        $where[] = "p.category_id = ?";
        $params[] = $options['category_id'];
    }
    if (!empty($options['search'])) {
        $where[] = "(p.name LIKE ? OR p.short_description LIKE ?)";
        $params[] = "%{$options['search']}%";
        $params[] = "%{$options['search']}%";
    }
    if (!empty($options['min_price'])) {
        $where[] = "COALESCE(p.sale_price, p.price) >= ?";
        $params[] = $options['min_price'];
    }
    if (!empty($options['max_price'])) {
        $where[] = "COALESCE(p.sale_price, p.price) <= ?";
        $params[] = $options['max_price'];
    }

    $whereStr = implode(" AND ", $where);
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products p WHERE {$whereStr}");
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

function getProduct($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id
                           WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getProductBySlug($slug) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id
                           WHERE p.slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

function getProductImages($productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

function getRelatedProducts($productId, $categoryId, $limit = 4) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, pi.image_path as primary_image 
                           FROM products p
                           LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                           WHERE p.category_id = ? AND p.id != ? AND p.status = 'active'
                           ORDER BY RAND() LIMIT ?");
    $stmt->execute([$categoryId, $productId, $limit]);
    return $stmt->fetchAll();
}





// ==================== AUTH ====================

function loginUser($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง'];
    }
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
    
    return ['success' => true, 'user' => $user];
}

function logoutUser() {
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_role']);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// ==================== REVIEWS ====================
function getProductReviews($productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT r.*, u.name as user_name 
                           FROM reviews r 
                           LEFT JOIN users u ON r.user_id = u.id 
                           WHERE r.product_id = ? AND r.status = 1 
                           ORDER BY r.created_at DESC");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

function getProductRating($productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM reviews WHERE product_id = ? AND status = 1");
    $stmt->execute([$productId]);
    return $stmt->fetch();
}

// ==================== BLOG ====================
function getBlogPosts($limit = 10, $offset = 0) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT bp.*, u.name as author_name 
                           FROM blog_posts bp 
                           LEFT JOIN users u ON bp.author_id = u.id 
                           WHERE bp.status = 'published' 
                           ORDER BY bp.created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll();
}

function getBlogPost($slug) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT bp.*, u.name as author_name 
                           FROM blog_posts bp 
                           LEFT JOIN users u ON bp.author_id = u.id 
                           WHERE bp.slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}



// ==================== HELPERS ====================
function formatPrice($price) {
    return '฿' . number_format($price, 0);
}

function getProductPrimaryImage($productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ? AND is_primary = 1 LIMIT 1");
    $stmt->execute([$productId]);
    $img = $stmt->fetch();
    return $img ? $img['image_path'] : 'placeholder.jpg';
}

function getProductImageUrl($path) {
    if (empty($path) || $path === 'placeholder.jpg') {
        return BASE_URL . 'assets/img/placeholder.jpg';
    }
    return UPLOAD_URL . $path;
}

function generateSlug($str) {
    $slug = preg_replace('/[^a-zA-Z0-9\-\s]/', '', $str);
    $slug = preg_replace('/[\s]+/', '-', trim(strtolower($slug)));
    return $slug ?: 'product-' . time();
}

function uploadImage($file, $dir = 'products') {
    $uploadDir = UPLOAD_DIR . $dir . '/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) return false;
    
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $dir . '/' . $filename;
    }
    return false;
}

function uploadVideo($file, $dir = 'videos') {
    $uploadDir = UPLOAD_DIR . $dir . '/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['mp4', 'mov', 'webm', 'avi'];
    if (!in_array($ext, $allowed)) return false;
    if ($file['size'] > 200 * 1024 * 1024) return false; // 200MB max
    
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $dir . '/' . $filename;
    }
    return false;
}

function getSetting($key, $default = '') {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : $default;
}

function getAllSettings() {
    global $pdo;
    $rows = $pdo->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
    $map = [];
    foreach ($rows as $r) $map[$r['setting_key']] = $r['setting_value'];
    return $map;
}

function saveSetting($key, $value) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    $stmt->execute([$key, $value]);
}

function getStatusBadge($status) {
    $badges = [
        'pending' => ['รอดำเนินการ', '#f59e0b'],
        'confirmed' => ['ยืนยันแล้ว', '#3b82f6'],
        'processing' => ['กำลังจัดส่ง', '#8b5cf6'],
        'shipped' => ['จัดส่งแล้ว', '#06b6d4'],
        'delivered' => ['ส่งสำเร็จ', '#10b981'],
        'cancelled' => ['ยกเลิก', '#ef4444'],
    ];
    $b = $badges[$status] ?? ['ไม่ทราบ', '#6b7280'];
    return '<span class="status-badge" style="background:' . $b[1] . '20;color:' . $b[1] . ';border:1px solid ' . $b[1] . '40">' . $b[0] . '</span>';
}

function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    if ($diff->y > 0) return $diff->y . ' ปีที่แล้ว';
    if ($diff->m > 0) return $diff->m . ' เดือนที่แล้ว';
    if ($diff->d > 0) return $diff->d . ' วันที่แล้ว';
    if ($diff->h > 0) return $diff->h . ' ชั่วโมงที่แล้ว';
    if ($diff->i > 0) return $diff->i . ' นาทีที่แล้ว';
    return 'เมื่อสักครู่';
}

function sanitize($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function flash($key, $value = null) {
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
    } else {
        $val = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $val;
    }
}

function redirect($url) {
    header("Location: " . BASE_URL . ltrim($url, '/'));
    exit;
}

// ==================== GMAIL SMTP ====================
function sendGmailSMTP($to, $subject, $htmlBody) {
    $smtpHost = MAIL_HOST;
    $smtpPort = MAIL_PORT;
    $user = MAIL_USERNAME;
    $pass = MAIL_PASSWORD;

    $socket = @fsockopen($smtpHost, $smtpPort, $errno, $errstr, 10);
    if (!$socket) return "Connection failed: $errstr ($errno)";

    $read = function() use ($socket) {
        $r = '';
        while ($line = fgets($socket, 515)) {
            $r .= $line;
            if (substr($line, 3, 1) === ' ') break;
        }
        return $r;
    };

    $send = function($cmd) use ($socket, $read) {
        fwrite($socket, $cmd . "\r\n");
        return $read();
    };

    $read(); // greeting
    $send("EHLO localhost");
    $send("STARTTLS");

    if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
        fclose($socket);
        return "TLS handshake failed";
    }

    $send("EHLO localhost");

    // AUTH LOGIN
    $send("AUTH LOGIN");
    $send(base64_encode($user));
    $authResp = $send(base64_encode($pass));
    if (strpos($authResp, '235') === false) {
        fclose($socket);
        return "Auth failed – ตรวจสอบ SMTP_USER / SMTP_PASS (App Password)";
    }

    $send("MAIL FROM:<{$user}>");
    $send("RCPT TO:<{$to}>");
    $send("DATA");

    $headers  = "From: " . SITE_NAME . " <{$user}>\r\n";
    $headers .= "To: <{$to}>\r\n";
    $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "Date: " . date('r') . "\r\n";

    fwrite($socket, $headers . "\r\n" . $htmlBody . "\r\n.\r\n");
    $dataResp = $read();

    $send("QUIT");
    fclose($socket);

    return (strpos($dataResp, '250') !== false) ? true : "Send failed: $dataResp";
}
