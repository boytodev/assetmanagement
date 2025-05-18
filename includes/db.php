<!-- /includes/db.php -->
<?php
$host = 'sql103.infinityfree.com';
$user = 'if0_39011339';
$pass = 'boy9876543';
$dbname = 'if0_39011339_db_equipment';

$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}
?>
