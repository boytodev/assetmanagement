<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// ตรวจสอบสิทธิ์เฉพาะ admin
if ($_SESSION['user']['user_role'] !== 'admin') {
  header("Location: dashboard.php");
  exit;
}

$success = false;
$error = '';

// ดึงรายการบุคลากรที่ยังไม่ถูกลบ
$users = $conn->query("
  SELECT personnel_id, full_name 
  FROM personnel 
  WHERE is_deleted = 0 
  ORDER BY full_name ASC
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // รับค่าจากฟอร์ม
  $personnel_id = intval($_POST['personnel_id']);
  $new_password = $_POST['new_password'];

  // ตรวจความยาวรหัสผ่าน
  if (strlen($new_password) < 4) {
    $error = "รหัสผ่านควรมีความยาวอย่างน้อย 4 ตัวอักษร";
  } else {
    // แฮชรหัสผ่านใหม่
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);

    // อัปเดตรหัสผ่าน
    $stmt = $conn->prepare("
      UPDATE personnel 
      SET password = ? 
      WHERE personnel_id = ?
    ");
    $stmt->bind_param("si", $hashed, $personnel_id);
    if ($stmt->execute()) {
      $success = true;
    } else {
      $error = "ไม่สามารถรีเซ็ตรหัสผ่านได้: " . $stmt->error;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>รีเซ็ตรหัสผ่านบุคลากร</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex">

  <?php include '../partials/sidebar.php'; ?>

  <div class="flex-1 ml-64 p-6">
    <?php include '../partials/header.php'; ?>

    <h2 class="text-xl font-bold text-gray-700 mb-6">รีเซ็ตรหัสผ่านบุคลากร</h2>

    <form method="POST" class="bg-white shadow p-6 max-w-xl rounded-lg space-y-4">
      <div>
        <label class="block text-gray-700 mb-1">เลือกบุคลากร</label>
        <select name="personnel_id" required class="w-full border rounded px-4 py-2">
          <option value="">-- เลือก --</option>
          <?php while ($row = $users->fetch_assoc()): ?>
            <option value="<?= $row['personnel_id'] ?>"><?= htmlspecialchars($row['full_name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div>
        <label class="block text-gray-700 mb-1">รหัสผ่านใหม่</label>
        <input 
          type="password" 
          name="new_password" 
          required 
          minlength="4" 
          class="w-full border rounded px-4 py-2" 
          placeholder="รหัสผ่านใหม่">
      </div>

      <div class="text-right">
        <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded hover:bg-yellow-700">
          รีเซ็ตรหัสผ่าน
        </button>
      </div>
    </form>
  </div>

  <?php if ($success): ?>
  <script>
    Swal.fire({
      title: 'รีเซ็ตรหัสผ่านสำเร็จ',
      icon: 'success',
      timer: 1500,
      showConfirmButton: false
    });
  </script>
  <?php elseif ($error): ?>
  <script>
    Swal.fire({
      title: 'เกิดข้อผิดพลาด',
      text: '<?= htmlspecialchars($error) ?>',
      icon: 'error'
    });
  </script>
  <?php endif; ?>

</body>
</html>
