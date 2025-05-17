<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$id = $_GET['id'] ?? 0;
$success = false;

// โหลดข้อมูลกลุ่มงาน
$stmt = $conn->prepare("SELECT * FROM work_group WHERE group_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$group = $stmt->get_result()->fetch_assoc();

// บันทึกการแก้ไข
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_name = $_POST['group_name'];
    $contact_number = $_POST['contact_number'];

    $update = $conn->prepare("UPDATE work_group SET group_name = ?, contact_number = ? WHERE group_id = ?");
    $update->bind_param("ssi", $group_name, $contact_number, $id);
    $update->execute();

    $success = true;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แก้ไขกลุ่มงาน</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
  <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

  <form method="POST" class="bg-white shadow-xl rounded-lg w-full max-w-md p-8">
    <h2 class="text-xl font-bold mb-6 text-gray-700">แก้ไขกลุ่มงาน</h2>

    <div class="mb-4">
      <label class="block text-gray-600 mb-1">ชื่อกลุ่มงาน</label>
      <input type="text" name="group_name" value="<?= htmlspecialchars($group['group_name']) ?>" required class="w-full px-4 py-2 border rounded">
    </div>

    <div class="mb-6">
      <label class="block text-gray-600 mb-1">เบอร์ติดต่อ</label>
      <input type="text" name="contact_number" value="<?= htmlspecialchars($group['contact_number']) ?>" class="w-full px-4 py-2 border rounded">
    </div>

    <div class="text-right">
      <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded">
        บันทึกการเปลี่ยนแปลง
      </button>
    </div>
  </form>

  <?php if ($success): ?>
  <script>
    Swal.fire({
      title: 'แก้ไขสำเร็จ',
      icon: 'success',
      timer: 1500,
      showConfirmButton: false
    }).then(() => {
      window.location.href = 'group_list.php';
    });
  </script>
  <?php endif; ?>

</body>
</html>
