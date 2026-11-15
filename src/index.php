<?php
require __DIR__ . '/config.php';
session_start();

if (!empty($_SESSION['login'])) {
    header('Location: mypage.php');
} else {
    header('Location: login.php');
}
exit;
