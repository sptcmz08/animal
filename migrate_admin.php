<?php
/**
 * Migration: Reset admin credentials
 * Run once via browser then delete this file
 */
require_once __DIR__ . '/init.php';

$email = 'admin';
$password = password_hash('password', PASSWORD_DEFAULT);

// Update existing admin or create one
$stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
$stmt->execute();
$admin = $stmt->fetch();

if ($admin) {
    $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?")->execute([$email, $password, $admin['id']]);
    echo "✅ อัปเดต admin สำเร็จ<br>";
} else {
    $pdo->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'admin', 1)")->execute(['Admin', $email, $password]);
    echo "✅ สร้าง admin ใหม่สำเร็จ<br>";
}

echo "<br>📧 Username: <b>admin</b><br>";
echo "🔑 Password: <b>password</b><br>";
echo "<br>⚠️ กรุณาลบไฟล์ migrate_admin.php หลังรัน";
