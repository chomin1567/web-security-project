<?php
require __DIR__ . '/config.php';
session_start();
guard_session(); 

if (empty($_SESSION['login'])) { header('Location: login.php'); exit; } // 세션만 확인 (취약 구조 유지)
$user = $_SESSION['user'];

// 글 작성
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title !== '' && $content !== '') { add_post($user, $title, $content); }
    header('Location: board.php'); exit; // 새로고침 재전송 방지
}
$posts = array_reverse(load_posts()); // 최신 글 위로
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>상품 후기 · Daily Mall</title>
    <style>
        body { font-family: sans-serif; background: #eef2f7; margin: 0; }
        .bar { background: #2a5e9e; color: #fff; padding: 16px 28px; display: flex; justify-content: space-between; align-items: center; }
        .bar .nav a { color: #cfe0f1; font-size: 14px; text-decoration: none; margin-left: 18px; }
        .hero { background: linear-gradient(120deg, #2a5e9e, #3d7bc4); color: #fff; padding: 26px 28px; }
        .hero .inner { max-width: 720px; margin: 0 auto; }
        .hero h2 { margin: 0 0 4px; font-size: 20px; }
        .hero p { margin: 0; font-size: 14px; color: #d4e3f4; }
        .wrap { max-width: 720px; margin: 28px auto; padding: 0 20px; }
        .box { background: #fff; padding: 28px; border-radius: 14px; box-shadow: 0 8px 30px rgba(0,0,0,.06); margin-bottom: 20px; }
        h1 { margin: 0 0 16px; font-size: 20px; color: #1d2b3a; }
        h2 { margin: 0 0 14px; font-size: 15px; color: #44505e; }
        label { display: block; font-size: 13px; color: #44505e; margin: 12px 0 6px; }
        input, textarea { width: 100%; box-sizing: border-box; padding: 11px 12px; border: 1px solid #d5dbe3; border-radius: 8px; font-size: 15px; font-family: inherit; }
        input:focus, textarea:focus { outline: none; border-color: #2a5e9e; box-shadow: 0 0 0 3px rgba(42,94,158,.12); }
        textarea { min-height: 90px; resize: vertical; }
        button { margin-top: 16px; padding: 11px 22px; background: #2a5e9e; color: #fff; border: 0; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .post { border-top: 1px solid #eceff3; padding: 18px 0; }
        .post:first-of-type { border-top: 0; }
        .ptitle { font-size: 16px; font-weight: 700; color: #1d2b3a; margin: 0 0 6px; }
        .meta { font-size: 12px; color: #9aa4af; margin: 0 0 10px; }
        .pcontent { font-size: 14px; color: #44505e; line-height: 1.6; white-space: pre-wrap; margin: 0; }
        .empty { color: #9aa4af; font-size: 14px; text-align: center; padding: 20px 0; }
        .empty .ic { font-size: 40px; display: block; margin-bottom: 10px; }
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
            <h2>💬 상품 후기</h2>
            <p>구매하신 상품의 후기를 남겨주세요</p>
        </div>
    </div>

    <div class="wrap">
        <div class="box">
            <h1>후기 작성</h1>
            <h2><?php echo htmlspecialchars($user); ?> 님</h2>
            <form method="post" action="board.php">
                <label>제목</label>
                <input type="text" name="title" autocomplete="off">
                <label>내용</label>
                <textarea name="content"></textarea>
                <button type="submit">등록</button>
            </form>
        </div>

        <div class="box">
            <h1>후기 목록</h1>
            <?php if (empty($posts)) { ?>
                <p class="empty"><span class="ic">💬</span>아직 작성된 후기가 없습니다.</p>
            <?php } else { ?>
                <?php foreach ($posts as $p) { ?>
                    <div class="post">
                        <p class="ptitle"><?php echo htmlspecialchars($p['title']); ?></p>
                        <p class="meta"><?php echo htmlspecialchars($p['author']); ?> · <?php echo htmlspecialchars($p['time']); ?></p>
                        <p class="pcontent"><?php echo htmlspecialchars($p['content']); ?></p>
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