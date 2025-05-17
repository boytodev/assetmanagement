<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$id = $_GET['id'] ?? 0;
$deleted = false;

// ตรวจสอบก่อนลบ
if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM work_group WHERE group_id = ?");
    $stmt->bind_param("i", $id);

    try {
        $stmt->execute();
        $deleted = true;
    } catch (Exception $e) {
        $deleted = false; // เช่น ลบไม่ได้เพราะมีบุคลากรใช้กลุ่มนี้อยู่
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ลบกลุ่มงาน</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
Swal.fire({
  title: <?= $deleted ? "'ลบข้อมูลเรียบร้อย'" : "'ไม่สามารถลบกลุ่มงานได้'" ?>,
  icon: <?= $deleted ? "'success'" : "'error'" ?>,
  timer: 1500,
  showConfirmButton: false
}).then(() => {
  window.location.href = 'group_list.php';
});
</script>
</body>
</html>
