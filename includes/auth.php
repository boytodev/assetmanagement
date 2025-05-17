<?php
// ต้องเรียกใช้ก่อนหน้าอื่นเสมอ
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
