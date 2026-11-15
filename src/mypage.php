<?php
require __DIR__ . '/config.php';
session_start();

// 로그인 안 돼 있으면 로그인 페이지로 (세션만 확인함)
if (empty($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$sid  = session_id();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>마이페이지</title>
    <style>
        body { font-family: sans-serif; background: #eef2f7; margin: 0; }
        .bar { background: #2a5e9e; color: #fff; padding: 16px 28px; display: flex; justify-content: space-between; align-items: center; }
        .bar a { color: #cfe0f1; font-size: 14px; text-decoration: none; }
        .wrap { max-width: 640px; margin: 40px auto; padding: 0 20px; }
        .box { background: #fff; padding: 32px; border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,.08); }
        h1 { margin: 0 0 6px; font-size: 24px; color: #1d2b3a; }
        .user { font-size: 18px; color: #2a5e9e; font-weight: 700; }
        .sid { margin-top: 24px; padding: 14px 16px; background: #f4f1ec; border: 1px dashed #cbb896; border-radius: 8px; font-family: monospace; font-size: 13px; color: #6a5a3a; word-break: break-all; }
        .desc { margin-top: 20px; font-size: 13px; color: #8a94a0; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="bar">
        <span>Demo Bank</span>
        <a href="logout.php">로그아웃</a>
    </div>
    <div class="wrap">
        <div class="box">
            <h1>마이페이지</h1>
            <p>현재 로그인한 계정: <span class="user"><?php echo htmlspecialchars($user); ?></span> 님</p>
            <div class="sid">PHPSESSID = <?php echo htmlspecialchars($sid); ?></div>
            <p class="desc">
                이 세션 쿠키 값만 있으면 비밀번호 없이도 이 페이지에 들어올 수 있습니다.<br>
                공격자가 이 쿠키를 훔쳐 자기 브라우저에 넣으면 위 계정명이 그대로 보입니다.
            </p>
        </div>
    </div>
</body>
</html>
