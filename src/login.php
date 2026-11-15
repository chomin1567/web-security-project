<?php
require __DIR__ . '/config.php';
session_start();

if (!empty($_SESSION['login'])) {
    header('Location: mypage.php');
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['username'] ?? '';
    $pw = $_POST['password'] ?? '';

    if ($id === $LOGIN_ID && $pw === $LOGIN_PW) {
        // 로그인 성공 -> 세션 만들고 마이페이지로
        $_SESSION['login'] = true;
        $_SESSION['user']  = $id;
        header('Location: mypage.php');
        exit;
    } else {
        $msg = '아이디 또는 비밀번호가 틀렸습니다.';
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>
    <style>
        body { font-family: sans-serif; background: #eef2f7; display: flex; min-height: 100vh; align-items: center; justify-content: center; margin: 0; }
        .box { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,.08); width: 320px; }
        h1 { margin: 0 0 4px; font-size: 22px; color: #1d2b3a; }
        .sub { margin: 0 0 24px; color: #6a7482; font-size: 14px; }
        label { display: block; font-size: 13px; color: #44505e; margin: 14px 0 6px; }
        input { width: 100%; box-sizing: border-box; padding: 11px 12px; border: 1px solid #d5dbe3; border-radius: 8px; font-size: 15px; }
        button { width: 100%; margin-top: 22px; padding: 12px; background: #2a5e9e; color: #fff; border: 0; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; }
        .msg { margin-top: 16px; color: #c0392b; font-size: 13px; }
        .hint { margin-top: 20px; font-size: 12px; color: #9aa4af; text-align: center; }
    </style>
</head>
<body>
    <form class="box" method="post" action="login.php">
        <h1>Demo Bank</h1>
        <p class="sub">로그인</p>
        <label>아이디</label>
        <input type="text" name="username" autocomplete="off" autofocus>
        <label>비밀번호</label>
        <input type="password" name="password" autocomplete="off">
        <button type="submit">로그인</button>
        <?php if ($msg !== '') { ?>
            <div class="msg"><?php echo htmlspecialchars($msg); ?></div>
        <?php } ?>
        <div class="hint">victim / password123</div>
    </form>
</body>
</html>
