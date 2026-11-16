<?php
require __DIR__ . '/config.php';
session_start();

if (empty($_SESSION['login'])) { header('Location: login.php'); exit; }
$user = $_SESSION['user'];

$items = cart_items();
$total = cart_total();

// 장바구니가 비어 있으면 상품으로
if (empty($items)) { header('Location: products.php'); exit; }

// 주문 확정 (가짜 결제): orders.json에 저장 후 장바구니 비움
$orderNo = add_order($user, $items, $total);
cart_clear();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>주문 완료 · Daily Mall</title>
    <style>
        body { font-family: sans-serif; background: #eef2f7; margin: 0; }
        .bar { background: #2a5e9e; color: #fff; padding: 16px 28px; display: flex; justify-content: space-between; align-items: center; }
        .bar .nav a { color: #cfe0f1; font-size: 14px; text-decoration: none; margin-left: 18px; }
        .hero { background: linear-gradient(120deg, #2a5e9e, #3d7bc4); color: #fff; padding: 26px 28px; }
        .hero .inner { max-width: 980px; margin: 0 auto; }
        .hero h2 { margin: 0 0 4px; font-size: 20px; }
        .hero p { margin: 0; font-size: 14px; color: #d4e3f4; }
        .wrap { max-width: 560px; margin: 40px auto; padding: 0 20px; text-align: center; }
        .box { background: #fff; border-radius: 14px; box-shadow: 0 8px 30px rgba(0,0,0,.06); padding: 40px 32px; }
        .check { width: 64px; height: 64px; border-radius: 50%; background: #e4f3ea; color: #1e824c; font-size: 34px; line-height: 64px; margin: 0 auto 18px; }
        h1 { font-size: 22px; color: #1d2b3a; margin: 0 0 8px; }
        .box p { color: #6a7482; font-size: 14px; margin: 4px 0; }
        .no { font-weight: 700; color: #2a5e9e; }
        .amt { font-size: 18px; font-weight: 700; color: #1d2b3a; margin-top: 10px; }
        .btns { margin-top: 24px; }
        .btns a { display: inline-block; margin: 0 6px; padding: 11px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; }
        .primary { background: #2a5e9e; color: #fff; }
        .ghost { background: #eef2f7; color: #44505e; }
        .footer { background: #1d2b3a; color: #9aa6b2; margin-top: 50px; padding: 24px 28px; font-size: 12px; line-height: 1.7; }
        .footer .inner { max-width: 980px; margin: 0 auto; }
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
            <h2>주문 완료</h2>
            <p>이용해 주셔서 감사합니다 🎉</p>
        </div>
    </div>

    <div class="wrap">
        <div class="box">
            <div class="check">&#10003;</div>
            <h1>주문이 완료되었습니다</h1>
            <p>주문번호 <span class="no">#<?php echo (int)$orderNo; ?></span></p>
            <p>주문자 <?php echo htmlspecialchars($user); ?> 님</p>
            <p class="amt">결제 금액 <?php echo number_format($total); ?>원</p>
            <div class="btns">
                <a class="primary" href="mypage.php">주문 내역 보기</a>
                <a class="ghost" href="products.php">쇼핑 계속하기</a>
            </div>
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