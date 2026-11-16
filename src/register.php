<?php
require __DIR__ . '/config.php';
session_start();
if (!empty($_SESSION['login'])) { header('Location: products.php'); exit; }

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id  = trim($_POST['username'] ?? '');
    $pw  = $_POST['password'] ?? '';
    $pw2 = $_POST['password2'] ?? '';
    if ($id === '' || $pw === '')      { $msg = '아이디와 비밀번호를 입력하세요.'; }
    elseif ($pw !== $pw2)              { $msg = '비밀번호가 일치하지 않습니다.'; }
    elseif (find_user($id))           { $msg = '이미 존재하는 아이디입니다.'; }
    else {
        $users = load_users();
        $users[] = ['username' => $id, 'password' => $pw]; // 평문 저장
        save_users($users);
        header('Location: login.php?registered=1'); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입 · Daily Mall</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; margin: 0; min-height: 100vh; display: flex; }
        .split { display: flex; width: 100%; min-height: 100vh; }
        /* ===== 왼쪽 브랜드 패널 ===== */
        .brand { flex: 1; position: relative; overflow: hidden; color: #fff;
                 padding: 60px 64px; display: flex; flex-direction: column; justify-content: center;
                 background: linear-gradient(140deg, #24528a 0%, #2a5e9e 45%, #3d7bc4 100%); }
        .brand .deco { position: absolute; opacity: .10; pointer-events: none; line-height: 1; }
        .brand .d1 { font-size: 200px; top: -30px; right: -20px; transform: rotate(12deg); }
        .brand .d2 { font-size: 130px; bottom: 30px; right: 90px; transform: rotate(-10deg); }
        .brand .d3 { font-size: 110px; bottom: -20px; left: 40px; transform: rotate(8deg); }
        .brand .content { position: relative; z-index: 1; max-width: 460px; }
        .brand .logo { font-size: 32px; font-weight: 800; margin: 0 0 14px; letter-spacing: -.5px; }
        .brand .tag { font-size: 16px; color: #ddeafe; line-height: 1.7; margin: 0 0 30px; }
        .benefit { display: flex; align-items: center; gap: 14px; background: rgba(255,255,255,.12);
                   border: 1px solid rgba(255,255,255,.2); border-radius: 12px; padding: 14px 16px; margin: 0 0 12px; }
        .benefit .bic { font-size: 26px; width: 34px; text-align: center; flex: none; }
        .benefit .btxt { line-height: 1.4; }
        .benefit .bt { font-size: 15px; font-weight: 700; }
        .benefit .bd { font-size: 13px; color: #cfe0f1; }
        /* ===== 오른쪽 폼 ===== */
        .panel { width: 460px; background: #eef2f7; display: flex; align-items: center; justify-content: center; padding: 30px; }
        .box { background: #fff; padding: 40px; border-radius: 14px; box-shadow: 0 10px 40px rgba(0,0,0,.12); width: 330px; }
        h1 { margin: 0 0 4px; font-size: 22px; color: #1d2b3a; }
        .sub { margin: 0 0 24px; color: #6a7482; font-size: 14px; }
        label { display: block; font-size: 13px; color: #44505e; margin: 14px 0 6px; }
        input { width: 100%; padding: 12px; border: 1px solid #d5dbe3; border-radius: 8px; font-size: 15px; }
        input:focus { outline: none; border-color: #2a5e9e; box-shadow: 0 0 0 3px rgba(42,94,158,.12); }
        button { width: 100%; margin-top: 24px; padding: 13px; background: #2a5e9e; color: #fff; border: 0; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; }
        button:hover { background: #24528a; }
        .msg { margin-top: 16px; color: #c0392b; font-size: 13px; }
        .foot { margin-top: 22px; font-size: 13px; color: #9aa4af; text-align: center; }
        .foot a { color: #2a5e9e; text-decoration: none; font-weight: 600; }
        @media (max-width: 860px) {
            .brand { display: none; }
            .panel { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="split">
        <div class="brand">
            <span class="deco d1">🎁</span>
            <span class="deco d2">🛍️</span>
            <span class="deco d3">🛒</span>
            <div class="content">
                <p class="logo">🛍️ Daily Mall</p>
                <p class="tag">지금 가입하고 데일리 특가를<br>가장 먼저 만나보세요.</p>
                <div class="benefit">
                    <span class="bic">🎁</span>
                    <span class="btxt"><span class="bt">신규 가입 10% 쿠폰</span><br><span class="bd">첫 주문에 바로 사용 가능</span></span>
                </div>
                <div class="benefit">
                    <span class="bic">🚚</span>
                    <span class="btxt"><span class="bt">2만원 이상 무료배송</span><br><span class="bd">부담 없는 데일리 쇼핑</span></span>
                </div>
                <div class="benefit">
                    <span class="bic">🔁</span>
                    <span class="btxt"><span class="bt">7일 이내 무료 반품</span><br><span class="bd">마음에 안 들면 바로 반품</span></span>
                </div>
            </div>
        </div>

        <div class="panel">
            <form class="box" method="post" action="register.php">
                <h1>회원가입</h1>
                <p class="sub">간단하게 가입하고 쇼핑을 시작하세요</p>
                <label>아이디</label>
                <input type="text" name="username" autocomplete="off" autofocus>
                <label>비밀번호</label>
                <input type="password" name="password" autocomplete="off">
                <label>비밀번호 확인</label>
                <input type="password" name="password2" autocomplete="off">
                <button type="submit">가입하기</button>
                <?php if ($msg !== '') { ?>
                    <div class="msg"><?php echo htmlspecialchars($msg); ?></div>
                <?php } ?>
                <div class="foot">이미 계정이 있으신가요? <a href="login.php">로그인</a></div>
            </form>
        </div>
    </div>
</body>
</html>