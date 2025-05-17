<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$groups = $conn->query("SELECT group_id, group_name FROM work_group");
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $group_id = $_POST['group_id'];
    $phone = $_POST['phone_number'];
    $created_at = date('Y-m-d');
    $username = $_POST['username'];
    $password = password_hash('123456', PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO personnel (full_name, phone_number, username, password, user_role, group_id) VALUES (?, ?, ?, ?, 'user', ?)");
    $stmt->bind_param("ssssi", $full_name, $phone, $username, $password, $group_id);
    $stmt->execute();

    $success = true;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เพิ่มบุคลากร</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
  <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

  <form method="POST" class="bg-white shadow-xl rounded-lg w-full max-w-lg p-8">
    <h2 class="text-xl font-bold mb-6 text-gray-700">เพิ่มบุคลากร</h2>

    <div class="mb-4">
      <label class="block text-gray-600 mb-1">ชื่อ-นามสกุล</label>
      <input type="text" name="full_name" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring">
    </div>
    <div class="mb-4">
      <label class="block text-gray-600 mb-1">ชื่อผู้ใช้</label>
      <input type="text" name="username" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring">
    </div>

    <div class="mb-4">
      <label class="block text-gray-600 mb-1">กลุ่มงาน</label>
      <select name="group_id" required class="w-full px-4 py-2 border rounded">
        <option value="">-- เลือกกลุ่มงาน --</option>
        <?php while($g = $groups->fetch_assoc()): ?>
          <option value="<?= $g['group_id'] ?>"><?= $g['group_name'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-4">
      <label class="block text-gray-600 mb-1">เบอร์โทร</label>
      <input type="text" name="phone_number" class="w-full px-4 py-2 border rounded">
    </div>

    <div class="mb-6">
      <label class="block text-gray-600 mb-1">วันที่เพิ่ม</label>
      <input type="text" value="<?= date('d/m/Y') ?>" class="w-full px-4 py-2 border rounded bg-gray-100" disabled>
    </div>

    <div class="text-right">
      <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2 rounded">
        บันทึก
      </button>
    </div>
  </form>

  <?php if ($success): ?>
  <script>
    Swal.fire({
      title: 'เพิ่มข้อมูลสำเร็จ',
      icon: 'success',
      showConfirmButton: false,
      timer: 1500
    }).then(() => {
      window.location.href = 'personnel_list.php';
    });
  </script>
  <?php endif; ?>

</body>
</html>
