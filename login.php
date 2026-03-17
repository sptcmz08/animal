<?php
require_once __DIR__ . '/init.php';

// Already logged in? Go to admin
if (isLoggedIn() && isAdmin()) {
    header('Location: ' . BASE_URL . 'admin/');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'กรุณากรอกอีเมลและรหัสผ่าน';
    } else {
        $result = loginUser($email, $password);
        if ($result['success']) {
            if (isAdmin()) {
                header('Location: ' . BASE_URL . 'admin/');
            } else {
                header('Location: ' . BASE_URL);
            }
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ | Elite Pet Design</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Prompt', sans-serif;
            background: #faf8f4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(107,85,64,0.1);
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            border: 1px solid #e8dfc9;
        }
        .logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: #4a3a2c;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        .logo p {
            font-size: 0.7rem;
            color: #b8a07a;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 28px;
        }
        .divider span:first-child, .divider span:last-child {
            width: 40px; height: 1px; background: #d4c4a0;
        }
        .divider span:nth-child(2) { color: #b8a07a; font-size: 14px; }
        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: #6b5540;
            margin-bottom: 8px;
            letter-spacing: 0.05em;
        }
        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #e8dfc9;
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: 'Prompt', sans-serif;
            color: #4a3a2c;
            background: #faf8f4;
            transition: all 0.3s;
            outline: none;
        }
        .form-group input:focus {
            border-color: #a08860;
            box-shadow: 0 0 0 3px rgba(160,136,96,0.15);
            background: white;
        }
        .form-group input::placeholder { color: #b8a07a; }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #6b5540, #8a7050);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Prompt', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            letter-spacing: 0.05em;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #4a3a2c, #6b5540);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(107,85,64,0.3);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 24px;
            font-size: 0.8rem;
            color: #b8a07a;
            text-decoration: none;
            transition: color 0.3s;
        }
        .back-link:hover { color: #6b5540; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <h1>Elite Pet Design</h1>
            <p>Admin Panel</p>
        </div>
        <div class="divider">
            <span></span><span>✦</span><span></span>
        </div>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>ชื่อผู้ใช้</label>
                <input type="text" name="email" placeholder="admin"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
            </div>
            <div class="form-group">
                <label>รหัสผ่าน</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">เข้าสู่ระบบ</button>
        </form>

        <a href="<?= BASE_URL ?>" class="back-link">← กลับไปหน้าเว็บไซต์</a>
    </div>
</body>
</html>
