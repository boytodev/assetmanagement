<?php
    require_once '../includes/auth.php';
    require_once '../includes/db.php';

    $id      = $_GET['id'] ?? 0;
    $success = false;

    // ดึงข้อมูลบุคลากร
    $stmt = $conn->prepare("SELECT * FROM personnel WHERE personnel_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    // โหลดกลุ่มงาน
    $groups = $conn->query("SELECT group_id, group_name FROM work_group");

    // เมื่อแก้ไขและกดบันทึก
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $full_name = $_POST['full_name'];
        $username  = $_POST['username'];
        $group_id  = $_POST['group_id'];
        $phone     = $_POST['phone_number'];
        $role      = $_POST['user_role'];

        $update = $conn->prepare("
  UPDATE personnel
  SET full_name=?, phone_number=?, username=?, group_id=?, user_role=?
  WHERE personnel_id=?"
        );
        $update->bind_param("sssisi", $full_name, $phone, $username, $group_id, $role, $id);
        $update->execute();

        $success = true;
    }
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แก้ไขข้อมูลบุคลากร</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
  <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

  <form method="POST" class="bg-white shadow-xl rounded-lg w-full max-w-lg p-8">
    <h2 class="text-xl font-bold mb-6 text-gray-700">แก้ไขข้อมูลบุคลากร</h2>

    <div class="mb-4">
      <label class="block text-gray-600 mb-1">ชื่อ - นามสกุล</label>
      <input type="text" name="full_name" value="<?php echo htmlspecialchars($data['full_name'])?>" required class="w-full px-4 py-2 border rounded">
    </div>
    <div class="mb-4">
      <label class="block text-gray-600 mb-1">ชื่อผู้ใช้</label>
      <input type="text" name="username" value="<?php echo htmlspecialchars($data['username'])?>" required class="w-full px-4 py-2 border rounded">
    </div>

    <div class="mb-4">
      <label class="block text-gray-600 mb-1">กลุ่มงาน</label>
      <select name="group_id" required class="w-full px-4 py-2 border rounded">
        <?php while ($g = $groups->fetch_assoc()): ?>
          <option value="<?php echo $g['group_id']?>" <?php echo $g['group_id'] == $data['group_id'] ? 'selected' : ''?>>
            <?php echo $g['group_name']?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-4">
      <label class="block text-gray-600 mb-1">เบอร์โทร</label>
      <input type="text" name="phone_number" value="<?php echo htmlspecialchars($data['phone_number'])?>" class="w-full px-4 py-2 border rounded">
    </div>

    <div class="mb-6">
      <label class="block text-gray-600 mb-1">สิทธิ์ผู้ใช้</label>
      <select name="user_role" class="w-full px-4 py-2 border rounded">
        <option value="user" <?php echo $data['user_role'] == 'user' ? 'selected' : ''?>>user</option>
        <option value="admin" <?php echo $data['user_role'] == 'admin' ? 'selected' : ''?>>admin</option>
      </select>
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
      title: 'บันทึกข้อมูลสำเร็จ',
      icon: 'success',
      timer: 1500,
      showConfirmButton: false
    }).then(() => {
      window.location.href = 'personnel_list.php';
    });
  </script>
  <?php endif; ?>

</body>
</html>
