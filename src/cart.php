<?php
require __DIR__ . '/config.php';
session_start();

if (empty($_SESSION['login'])) { header('Location: login.php'); exit; }
$user = $_SESSION['user'];

// 장바구니에서 삭제
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove'])) {
    cart_remove($_POST['remove']);
    header('Location: cart.php'); exit;
}

$items = cart_items();
$total = cart_total();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>장바구니 · Daily Mall</title>
    <style>
        body { font-family: sans-serif; background: #eef2f7; margin: 0; }
        .bar { background: #2a5e9e; color: #fff; padding: 16px 28px; display: flex; justify-content: space-between; align-items: center; }
        .bar .nav a { color: #cfe0f1; font-size: 14px; text-decoration: none; margin-left: 18px; }
        .hero { background: linear-gradient(120deg, #2a5e9e, #3d7bc4); color: #fff; padding: 26px 28px; }
        .hero .inner { max-width: 720px; margin: 0 auto; }
        .hero h2 { margin: 0 0 4px; font-size: 20px; }
        .hero p { margin: 0; font-size: 14px; color: #d4e3f4; }
        .wrap { max-width: 720px; margin: 28px auto; padding: 0 20px; }
        .box { background: #fff; border-radius: 14px; box-shadow: 0 8px 30px rgba(0,0,0,.06); padding: 24px; }
        .row { display: flex; align-items: center; justify-content: space-between; padding: 14px 0; border-top: 1px solid #eceff3; }
        .row:first-child { border-top: 0; }
        .iname { font-weight: 700; color: #1d2b3a; }
        .iqty { color: #8a94a0; font-size: 13px; margin-left: 8px; }
        .isub { color: #2a5e9e; font-weight: 700; margin-right: 14px; }
        .rm { padding: 6px 10px; background: #eef2f7; color: #6a7482; border: 0; border-radius: 6px; font-size: 12px; cursor: pointer; }
        .total { display: flex; justify-content: space-between; align-items: center; margin-top: 18px; padding-top: 18px; border-top: 2px solid #1d2b3a; font-size: 18px; font-weight: 700; color: #1d2b3a; }
        .order { display: block; width: 100%; margin-top: 20px; padding: 13px; background: #2a5e9e; color: #fff; border: 0; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; text-align: center; text-decoration: none; box-sizing: border-box; }
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
            <h2>🛒 장바구니</h2>
            <p>담아둔 상품을 확인하고 주문하세요</p>
        </div>
    </div>

    <div class="wrap">
        <div class="box">
            <?php if (empty($items)) { ?>
                <p class="empty"><span class="ic">🛒</span>장바구니가 비어 있습니다.<br><a class="link" href="products.php">상품 보러 가기</a></p>
            <?php } else { ?>
                <?php foreach ($items as $it) { ?>
                    <div class="row">
                        <div>
                            <span class="iname"><?php echo htmlspecialchars($it['name']); ?></span>
                            <span class="iqty"><?php echo number_format($it['price']); ?>원 × <?php echo (int)$it['qty']; ?></span>
                        </div>
                        <div>
                            <span class="isub"><?php echo number_format($it['subtotal']); ?>원</span>
                            <form method="post" action="cart.php" style="display:inline">
                                <input type="hidden" name="remove" value="<?php echo (int)$it['id']; ?>">
                                <button type="submit" class="rm">삭제</button>
                            </form>
                        </div>
                    </div>
                <?php } ?>
                <div class="total">
                    <span>합계</span>
                    <span><?php echo number_format($total); ?>원</span>
                </div>
                <a class="order" href="order.php">주문하기</a>
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