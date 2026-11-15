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

// 로그인 계정
$LOGIN_ID = 'victim';
$LOGIN_PW = 'password123';
