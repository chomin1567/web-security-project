<?php
require __DIR__ . '/config.php';
session_start();

if (empty($_SESSION['login'])) { header('Location: login.php'); exit; }
$user = $_SESSION['user'];

// 장바구니 담기
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    cart_add($_POST['add'], 1);
    header('Location: products.php?added=1'); exit;
}

$products = load_products();
$added = isset($_GET['added']);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>상품 목록 · Daily Mall</title>
    <style>
        body { font-family: sans-serif; background: #eef2f7; margin: 0; }
        .bar { background: #2a5e9e; color: #fff; padding: 16px 28px; display: flex; justify-content: space-between; align-items: center; }
        .bar .nav a { color: #cfe0f1; font-size: 14px; text-decoration: none; margin-left: 18px; }
        .hero { background: linear-gradient(120deg, #2a5e9e, #3d7bc4); color: #fff; padding: 30px 28px; }
        .hero .inner { max-width: 980px; margin: 0 auto; }
        .hero h2 { margin: 0 0 6px; font-size: 22px; }
        .hero p { margin: 0; font-size: 14px; color: #d4e3f4; }
        .wrap { max-width: 980px; margin: 28px auto; padding: 0 20px; }
        h1 { font-size: 20px; color: #1d2b3a; margin: 0 0 18px; }
        .ok { background: #e4f3ea; color: #1e824c; padding: 10px 14px; border-radius: 8px; font-size: 14px; margin-bottom: 18px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 18px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,.06); padding: 16px; display: flex; flex-direction: column; position: relative; }
        .thumb { height: 130px; border-radius: 10px; margin-bottom: 14px; display: flex; align-items: center; justify-content: center; font-size: 54px; }
        .badge { position: absolute; top: 22px; left: 22px; background: #e23b3b; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; }
        .name { font-size: 15px; font-weight: 700; color: #1d2b3a; margin: 0 0 4px; }
        .desc { font-size: 12px; color: #8a94a0; margin: 0 0 12px; min-height: 30px; }
        .priceline { margin-bottom: 14px; }
        .was { font-size: 12px; color: #b4bcc6; text-decoration: line-through; margin-right: 6px; }
        .now { font-size: 17px; font-weight: 700; color: #2a5e9e; }
        .plain { font-size: 17px; font-weight: 700; color: #1d2b3a; }
        button { margin-top: auto; padding: 10px; background: #2a5e9e; color: #fff; border: 0; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .footer { background: #1d2b3a; color: #9aa6b2; margin-top: 40px; padding: 24px 28px; font-size: 12px; line-height: 1.7; }
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
            <a href="logout.php">로그아웃</a>
        </span>
    </div>

    <div class="hero">
        <div class="inner">
            <h2>가을 시즌 특가 🍂</h2>
            <p>데일리 아이템 최대 25% 할인 · 오늘의 추천 상품을 만나보세요</p>
        </div>
    </div>

    <div class="wrap">
        <h1>상품 목록</h1>
        <?php if ($added) { ?><div class="ok">장바구니에 담았습니다.</div><?php } ?>
        <div class="grid">
            <?php foreach ($products as $p) {
                $onSale = isset($p['sale']) && $p['sale'] !== null;
                $color  = $p['color'] ?? '#dfe7f1';
                $emoji  = $p['emoji'] ?? '🛍️';
                if ($onSale) { $rate = round((1 - $p['sale'] / $p['price']) * 100); }
            ?>
                <div class="card">
                    <?php if ($onSale) { ?><span class="badge"><?php echo $rate; ?>% SALE</span><?php } ?>
                    <div class="thumb" style="background: <?php echo htmlspecialchars($color); ?>;"><?php echo $emoji; ?></div>
                    <p class="name"><?php echo htmlspecialchars($p['name']); ?></p>
                    <p class="desc"><?php echo htmlspecialchars($p['desc']); ?></p>
                    <div class="priceline">
                        <?php if ($onSale) { ?>
                            <span class="was"><?php echo number_format($p['price']); ?>원</span>
                            <span class="now"><?php echo number_format($p['sale']); ?>원</span>
                        <?php } else { ?>
                            <span class="plain"><?php echo number_format($p['price']); ?>원</span>
                        <?php } ?>
                    </div>
                    <form method="post" action="products.php">
                        <input type="hidden" name="add" value="<?php echo (int)$p['id']; ?>">
                        <button type="submit">장바구니 담기</button>
                    </form>
                </div>
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