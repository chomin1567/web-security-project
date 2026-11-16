<?php
require __DIR__ . '/config.php';
session_start();

// 로그인 안 돼 있으면 로그인 페이지로 (세션만 확인함)
if (empty($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

$user   = $_SESSION['user'];
$orders = orders_of($user);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>마이페이지 · Daily Mall</title>
    <style>
        body { font-family: sans-serif; background: #eef2f7; margin: 0; }
        .bar { background: #2a5e9e; color: #fff; padding: 16px 28px; display: flex; justify-content: space-between; align-items: center; }
        .bar .nav a { color: #cfe0f1; font-size: 14px; text-decoration: none; margin-left: 18px; }
        .hero { background: linear-gradient(120deg, #2a5e9e, #3d7bc4); color: #fff; padding: 26px 28px; }
        .hero .inner { max-width: 720px; margin: 0 auto; }
        .hero h2 { margin: 0 0 4px; font-size: 20px; }
        .hero p { margin: 0; font-size: 14px; color: #d4e3f4; }
        .wrap { max-width: 720px; margin: 28px auto; padding: 0 20px; }
        .box { background: #fff; padding: 28px 32px; border-radius: 14px; box-shadow: 0 8px 30px rgba(0,0,0,.06); margin-bottom: 20px; }
        h1 { margin: 0 0 6px; font-size: 22px; color: #1d2b3a; }
        h2 { margin: 0 0 16px; font-size: 18px; color: #1d2b3a; }
        .userline { display: flex; align-items: center; gap: 14px; }
        .avatar { width: 52px; height: 52px; border-radius: 50%; background: #e1ecf8; display: flex; align-items: center; justify-content: center; font-size: 26px; flex: none; }
        .user { font-size: 18px; color: #2a5e9e; font-weight: 700; }
        .order { border-top: 1px solid #eceff3; padding: 16px 0; }
        .order:first-of-type { border-top: 0; }
        .ohead { display: flex; justify-content: space-between; font-size: 13px; color: #8a94a0; margin-bottom: 8px; }
        .ono { font-weight: 700; color: #2a5e9e; }
        .oitem { font-size: 14px; color: #44505e; }
        .ototal { text-align: right; font-weight: 700; color: #1d2b3a; margin-top: 8px; }
        .empty { color: #9aa4af; font-size: 14px; text-align: center; padding: 20px 0; }
        .empty .ic { font-size: 40px; display: block; margin-bottom: 10px; }
        .link { color: #2a5e9e; text-decoration: none; font-weight: 600; }
        .footer { background: #1d2b3a; color: #9aa6b2; margin-top: 40px; padding: 24px 28px; font-size: 12px; line-height: 1.7; }
        .footer .inner { max-width: 720px; margin: 0 auto; }
        .footer b { color: #cfe0f1; font-size: 14px; }
    </style>
</head>
<body>
    <div class="bar">
        <span>Daily Mall</span>
        <span class="nav">
            <a href="products.php">상품</a>
            <a href="cart.php">장바구니(<?php echo cart_count(); ?>)</a>
            <a href="mypage.php">마이페이지</a>
            <a href="board.php">게시판</a>
            <a href="logout.php">로그아웃</a>
        </span>
    </div>

    <div class="hero">
        <div class="inner">
            <h2>마이페이지</h2>
            <p>나의 주문 내역과 계정 정보를 확인하세요</p>
        </div>
    </div>

    <div class="wrap">
        <div class="box">
            <div class="userline">
                <div class="avatar">🙂</div>
                <div>
                    <div><span class="user"><?php echo htmlspecialchars($user); ?></span> 님</div>
                    <div style="font-size:13px;color:#8a94a0;">Daily Mall 회원</div>
                </div>
            </div>
        </div>

        <div class="box">
            <h2>내 주문 내역</h2>
            <?php if (empty($orders)) { ?>
                <p class="empty"><span class="ic">📦</span>주문 내역이 없습니다.<br><a class="link" href="products.php">상품 보러 가기</a></p>
            <?php } else { ?>
                <?php foreach ($orders as $o) { ?>
                    <div class="order">
                        <div class="ohead">
                            <span class="ono">주문 #<?php echo (int)$o['id']; ?></span>
                            <span><?php echo htmlspecialchars($o['time']); ?></span>
                        </div>
                        <?php foreach ($o['items'] as $it) { ?>
                            <div class="oitem"><?php echo htmlspecialchars($it['name']); ?> × <?php echo (int)$it['qty']; ?></div>
                        <?php } ?>
                        <div class="ototal"><?php echo number_format($o['total']); ?>원</div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>

    <div class="footer">
        <div class="inner">
            <b>Daily Mall</b><br>
            고객센터 1234-5678 · 평일 10:00~18:00 (주말·공휴일 휴무)<br>
            서울특별시 어딘가 123 · 사업자등록번호 000-00-00000<br>
        </div>
    </div>
</body>
</html>