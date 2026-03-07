<?php
require_once __DIR__ . '/../init.php';
header('Content-Type: application/json');
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

$productId = intval($_POST['product_id'] ?? 0);
$rating = max(1, min(5, intval($_POST['rating'] ?? 5)));
$comment = trim($_POST['comment'] ?? '');

if (!$productId || !$comment) {
    echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบ']);
    exit;
}

$pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)")
    ->execute([$productId, $_SESSION['user_id'], $rating, $comment]);

echo json_encode(['success' => true, 'message' => 'ขอบคุณสำหรับรีวิว!']);
