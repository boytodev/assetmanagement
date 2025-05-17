<?php
session_start();

// ถ้ายังไม่ได้ล็อกอิน
if (!isset($_SESSION['user'])) {
    header("Location: pages/login.php");
    exit;
}

// ถ้าล็อกอินแล้ว ให้ redirect ไปหน้าหลัก
header("Location: pages/dashboard.php");
exit;
