<?php
session_start();           // เริ่ม session
session_unset();           // ลบตัวแปร session ทั้งหมด
session_destroy();         // ทำลาย session

header("Location: pages/login.php"); // กลับไปยังหน้าล็อกอิน
exit;
