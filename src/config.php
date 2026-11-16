<?php
// 세션 쿠키 설정 (실습용으로 보안 옵션은 꺼둠)
ini_set('session.use_strict_mode', '0');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => false,
    'httponly' => false,
    'samesite' => '',
]);

// ===== 파일 기반 저장소 (DB 없이 동작) =====
define('DATA_DIR', __DIR__ . '/data');

function ensure_data_dir() {
    if (!is_dir(DATA_DIR)) { @mkdir(DATA_DIR, 0777, true); }
}
function read_json($name, $default = []) {
    ensure_data_dir();
    $f = DATA_DIR . '/' . $name;
    if (!file_exists($f)) return $default;
    $data = json_decode(file_get_contents($f), true);
    return is_array($data) ? $data : $default;
}
function write_json($name, $data) {
    ensure_data_dir();
    file_put_contents(DATA_DIR . '/' . $name,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// ----- 사용자 -----
function load_users() {
    return read_json('users.json', []);
}
function save_users($users) { write_json('users.json', $users); }
function find_user($username) {
    foreach (load_users() as $u) { if ($u['username'] === $username) return $u; }
    return null;
}
function check_login($username, $password) { // 평문 비교 (실습용)
    $u = find_user($username);
    return $u && $u['password'] === $password;
}

// ----- 게시판 -----
function load_posts() { return read_json('posts.json', []); }
function save_posts($posts) { write_json('posts.json', $posts); }
function add_post($author, $title, $content) {
    $posts = load_posts();
    $posts[] = ['id'=>count($posts)+1, 'author'=>$author, 'title'=>$title,
                'content'=>$content, 'time'=>date('Y-m-d H:i')];
    save_posts($posts);
}

// ----- 상품 (고정 목록, 최초 1회 seed) -----
// sale = 할인가 (없으면 null), emoji/color = 외부 이미지 없이 VM에서도 보이게
function load_products() {
    $f = DATA_DIR . '/products.json';
    if (!file_exists($f)) {
        $seed = [
            ['id'=>1, 'name'=>'무지 티셔츠',     'price'=>12900, 'sale'=>9900,  'desc'=>'베이식 코튼 반팔 티셔츠',   'emoji'=>'👕', 'color'=>'#dfe7f1'],
            ['id'=>2, 'name'=>'러닝 스니커즈',   'price'=>59000, 'sale'=>49000, 'desc'=>'가벼운 데일리 운동화',       'emoji'=>'👟', 'color'=>'#e6e1f3'],
            ['id'=>3, 'name'=>'캔버스 백팩',     'price'=>34000, 'sale'=>null,  'desc'=>'수납 넉넉한 데일리 백팩',     'emoji'=>'🎒', 'color'=>'#f1e6df'],
            ['id'=>4, 'name'=>'보온 텀블러',     'price'=>18500, 'sale'=>14900, 'desc'=>'500ml 스테인리스 텀블러',    'emoji'=>'☕', 'color'=>'#dff1ea'],
            ['id'=>5, 'name'=>'무선 마우스',     'price'=>22000, 'sale'=>null,  'desc'=>'정숙한 무선 광 마우스',       'emoji'=>'🖱️', 'color'=>'#eef0f2'],
            ['id'=>6, 'name'=>'블루투스 이어폰', 'price'=>89000, 'sale'=>69000, 'desc'=>'노이즈 캔슬링 무선 이어폰',   'emoji'=>'🎧', 'color'=>'#e1ecf3'],
            ['id'=>7, 'name'=>'기계식 키보드',   'price'=>78000, 'sale'=>null,  'desc'=>'타건감 좋은 적축 키보드',     'emoji'=>'⌨️', 'color'=>'#f3eadf'],
            ['id'=>8, 'name'=>'데스크 램프',     'price'=>25000, 'sale'=>19900, 'desc'=>'눈이 편한 LED 스탠드',       'emoji'=>'💡', 'color'=>'#f3f0df'],
        ];
        write_json('products.json', $seed);
        return $seed;
    }
    return read_json('products.json', []);
}

function find_product($id) {
    foreach (load_products() as $p) { if ((int)$p['id'] === (int)$id) return $p; }
    return null;
}

// ----- 장바구니 (세션에 저장: id => qty) -----
function cart_add($pid, $qty = 1) {
    if (empty($_SESSION['cart'])) $_SESSION['cart'] = [];
    $pid = (int)$pid;
    $_SESSION['cart'][$pid] = ($_SESSION['cart'][$pid] ?? 0) + (int)$qty;
}
function cart_remove($pid) {
    unset($_SESSION['cart'][(int)$pid]);
}
function cart_clear() { $_SESSION['cart'] = []; }
function cart_items() { // [{id,name,price,qty,subtotal}]
    $out = [];
    foreach (($_SESSION['cart'] ?? []) as $pid => $qty) {
        $p = find_product($pid);
        if (!$p) continue;
        $unit = (isset($p['sale']) && $p['sale'] !== null) ? $p['sale'] : $p['price']; // 할인가 우선
        $out[] = ['id'=>$p['id'], 'name'=>$p['name'], 'price'=>$unit,
                  'qty'=>$qty, 'subtotal'=>$unit * $qty];
    }
    return $out;
}
function cart_total() {
    $t = 0;
    foreach (cart_items() as $it) $t += $it['subtotal'];
    return $t;
}
function cart_count() {
    $n = 0;
    foreach (($_SESSION['cart'] ?? []) as $qty) $n += $qty;
    return $n;
}

// ----- 주문 -----
function load_orders() { return read_json('orders.json', []); }
function save_orders($orders) { write_json('orders.json', $orders); }
function add_order($user, $items, $total) {
    $orders = load_orders();
    $orders[] = ['id'=>count($orders)+1, 'user'=>$user, 'items'=>$items,
                 'total'=>$total, 'time'=>date('Y-m-d H:i')];
    save_orders($orders);
    return count($orders); // 주문번호
}
function orders_of($user) {
    $out = [];
    foreach (load_orders() as $o) { if ($o['user'] === $user) $out[] = $o; }
    return array_reverse($out); // 최신순
}