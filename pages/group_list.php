<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// ดึงกลุ่มงานทั้งหมด
$result = $conn->query("SELECT * FROM work_group ORDER BY group_id ASC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>จัดการกลุ่มงาน</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex">

  <?php include '../partials/sidebar.php'; ?>

  <div class="flex-1 ml-64">
    <?php include '../partials/header.php'; ?>

    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-700">ข้อมูลกลุ่มงาน</h2>
        <a href="group_add.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
          <i class="bi bi-plus-circle"></i> เพิ่มกลุ่มงาน
        </a>
      </div>

      <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full text-sm text-left text-gray-700">
          <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
            <tr>
              <th class="px-4 py-3">#</th>
              <th class="px-4 py-3">ชื่อกลุ่มงาน</th>
              <th class="px-4 py-3">เบอร์ติดต่อ</th>
              <th class="px-4 py-3 text-center">จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="px-4 py-2"><?= $i++ ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['group_name']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['contact_number']) ?></td>
              <td class="px-4 py-2 text-center space-x-1">
                <a href="group_edit.php?id=<?= $row['group_id'] ?>" class="bg-yellow-400 text-white px-2 py-1 rounded text-xs hover:bg-yellow-500">แก้ไข</a>
                <a href="#" onclick="confirmDelete(<?= $row['group_id'] ?>)" class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600">ลบ</a>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
  function confirmDelete(id) {
    Swal.fire({
      title: 'คุณแน่ใจหรือไม่?',
      text: "ข้อมูลกลุ่มงานจะถูกลบ!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'ลบ',
      cancelButtonText: 'ยกเลิก'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'group_delete.php?id=' + id;
      }
    });
  }
  </script>

</body>
</html>
