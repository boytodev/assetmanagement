<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$id = $_GET['id'] ?? 0;
$deleted = false;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM asset WHERE asset_id = ?");
    $stmt->bind_param("i", $id);

    try {
        $stmt->execute();
        $deleted = true;
    } catch (Exception $e) {
        $deleted = false;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ลบครุภัณฑ์</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
Swal.fire({
  title: <?= $deleted ? "'ลบข้อมูลสำเร็จ'" : "'ไม่สามารถลบได้'" ?>,
  icon: <?= $deleted ? "'success'" : "'error'" ?>,
  timer: 1500,
  showConfirmButton: false
}).then(() => {
  window.location.href = 'asset_list.php';
});
</script>
</body>
</html>
