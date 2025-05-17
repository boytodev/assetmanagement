<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE personnel SET is_deleted = 1 WHERE personnel_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $deleted = true;
} else {
    $deleted = false;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ลบบุคลากร</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
Swal.fire({
  title: <?= $deleted ? "'ลบข้อมูลเรียบร้อย'" : "'ไม่สามารถลบได้'" ?>,
  icon: <?= $deleted ? "'success'" : "'error'" ?>,
  timer: 1500,
  showConfirmButton: false
}).then(() => {
  window.location.href = 'personnel_list.php';
});
</script>
</body>
</html>
