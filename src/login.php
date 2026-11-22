<?php
require __DIR__ . '/config.php';
session_start();

if (!empty($_SESSION['login'])) {
    header('Location: products.php');
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['username'] ?? '';
    $pw = $_POST['password'] ?? '';

    if (check_login($id, $pw)) {
        if (DEF_SESSION_REGEN) {
            session_regenerate_id(true);           // 방어: 세션 재발급
        }
        $_SESSION['login'] = true;
        $_SESSION['user']  = $id;
        $_SESSION['ua']    = $_SERVER['HTTP_USER_AGENT'] ?? '';  // UA 저장 (UA 이상탐지용)
        header('Location: products.php');
        exit;
    } else {
        $msg = '아이디 또는 비밀번호가 틀렸습니다.';
    }
}

$registered = isset($_GET['registered']);
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인 · Daily Mall</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
        }

        .split {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ===== 왼쪽 브랜드 패널 (채워진 버전) ===== */
        .brand {
            flex: 1;
            position: relative;
            overflow: hidden;
            color: #fff;
            padding: 60px 64px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: linear-gradient(140deg, #24528a 0%, #2a5e9e 45%, #3d7bc4 100%);
        }

        /* 배경 장식 이모지 (은은하게) */
        .brand .deco {
            position: absolute;
            opacity: .10;
            pointer-events: none;
            line-height: 1;
        }

        .brand .d1 {
            font-size: 200px;
            top: -30px;
            right: -20px;
            transform: rotate(12deg);
        }

        .brand .d2 {
            font-size: 130px;
            bottom: 30px;
            right: 90px;
            transform: rotate(-10deg);
        }

        .brand .d3 {
            font-size: 110px;
            bottom: -20px;
            left: 40px;
            transform: rotate(8deg);
        }

        .brand .content {
            position: relative;
            z-index: 1;
            max-width: 460px;
        }

        .brand .logo {
            font-size: 32px;
            font-weight: 800;
            margin: 0 0 14px;
            letter-spacing: -.5px;
        }

        .brand .tag {
            font-size: 16px;
            color: #ddeafe;
            line-height: 1.7;
            margin: 0 0 26px;
        }

        /* 카테고리 칩 */
        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 0 0 26px;
        }

        .chip {
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .25);
            padding: 7px 13px;
            border-radius: 999px;
            font-size: 13px;
        }

        /* 오늘의 특가 미니카드 */
        .promo {
            display: flex;
            align-items: center;
            gap: 14px;
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 14px;
            padding: 14px 16px;
            margin: 0 0 26px;
        }

        .promo .ic {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            background: #dfe7f1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            flex: none;
        }

        .promo .ptxt {
            line-height: 1.4;
        }

        .promo .plabel {
            font-size: 11px;
            color: #ffd84d;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .promo .pname {
            font-size: 15px;
            font-weight: 700;
        }

        .promo .pprice {
            font-size: 14px;
        }

        .promo .pprice b {
            font-size: 16px;
        }

        .promo .was {
            color: #bcd3ef;
            text-decoration: line-through;
            font-size: 12px;
            margin-right: 6px;
        }

        /* 혜택 */
        .feat {
            font-size: 14px;
            color: #eaf2fd;
            margin: 9px 0;
        }

        .feat span {
            display: inline-block;
            width: 24px;
        }

        /* ===== 오른쪽 로그인 ===== */
        .panel {
            width: 460px;
            background: #eef2f7;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        .box {
            background: #fff;
            padding: 40px;
            border-radius: 14px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .12);
            width: 330px;
        }

        h1 {
            margin: 0 0 4px;
            font-size: 22px;
            color: #1d2b3a;
        }

        .sub {
            margin: 0 0 24px;
            color: #6a7482;
            font-size: 14px;
        }

        label {
            display: block;
            font-size: 13px;
            color: #44505e;
            margin: 14px 0 6px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d5dbe3;
            border-radius: 8px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #2a5e9e;
            box-shadow: 0 0 0 3px rgba(42, 94, 158, .12);
        }

        button {
            width: 100%;
            margin-top: 24px;
            padding: 13px;
            background: #2a5e9e;
            color: #fff;
            border: 0;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover {
            background: #24528a;
        }

        .msg {
            margin-top: 16px;
            color: #c0392b;
            font-size: 13px;
        }

        .ok {
            margin-top: 16px;
            color: #1e824c;
            font-size: 13px;
        }

        .hint {
            margin-top: 22px;
            font-size: 13px;
            color: #9aa4af;
            text-align: center;
        }

        .hint a {
            color: #2a5e9e;
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 860px) {
            .brand {
                display: none;
            }

            .panel {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="split">
        <div class="brand">
            <span class="deco d1">🛍️</span>
            <span class="deco d2">🎁</span>
            <span class="deco d3">🛒</span>
            <div class="content">
                <p class="logo">🛍️ Daily Mall</p>
                <p class="tag">매일이 특가인 데일리 쇼핑몰<br>오늘의 추천 상품을 만나보세요.</p>

                <div class="chips">
                    <span class="chip">👕 의류</span>
                    <span class="chip">👟 신발</span>
                    <span class="chip">🎒 가방</span>
                    <span class="chip">🎧 전자</span>
                    <span class="chip">☕ 리빙</span>
                </div>

                <div class="promo">
                    <div class="ic">👕</div>
                    <div class="ptxt">
                        <div class="plabel">🔥 오늘의 특가</div>
                        <div class="pname">무지 티셔츠</div>
                        <div class="pprice"><span class="was">12,900원</span><b>9,900원</b></div>
                    </div>
                </div>

                <p class="feat"><span>🚚</span> 2만원 이상 무료배송</p>
                <p class="feat"><span>🔁</span> 7일 이내 무료 반품</p>
                <p class="feat"><span>🎁</span> 신규 가입 10% 쿠폰</p>
            </div>
        </div>

        <div class="panel">
            <form class="box" method="post" action="login.php">
                <h1>로그인</h1>
                <p class="sub">Daily Mall에 오신 것을 환영합니다</p>
                <label>아이디</label>
                <input type="text" name="username" autocomplete="off" autofocus>
                <label>비밀번호</label>
                <input type="password" name="password" autocomplete="off">
                <button type="submit">로그인</button>
                <?php if (!empty($registered)) { ?>
                    <div class="ok">회원가입이 완료되었습니다. 로그인해 주세요.</div>
                <?php } ?>
                <?php if ($msg !== '') { ?>
                    <div class="msg"><?php echo htmlspecialchars($msg); ?></div>
                <?php } ?>
                <div class="hint">계정이 없으신가요? <a href="register.php">회원가입</a></div>
            </form>
        </div>
    </div>
</body>

</html>