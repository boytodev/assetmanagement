<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// ตรวจสอบสิทธิ์ admin หรือไม่
$is_admin = ($_SESSION['user']['user_role'] === 'admin');

// ถ้าเป็นผู้ใช้งานทั่วไป ใช้ personnel_id เฉพาะของตัวเอง
if (!$is_admin) {
    $personnel_id = $_SESSION['user']['personnel_id'];

    $sql = "
    SELECT b.*, ai.asset_number, p.full_name
    FROM asset_borrowing b
    JOIN asset_item ai ON b.item_id = ai.item_id
    JOIN personnel p ON b.personnel_id = p.personnel_id
    WHERE b.personnel_id = ?
    ORDER BY b.borrow_date DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $personnel_id);
} else {
    // ถ้าเป็น admin ให้ดึงทุกคำขอ
    $sql = "
    SELECT b.*, ai.asset_number, p.full_name
    FROM asset_borrowing b
    JOIN asset_item ai ON b.item_id = ai.item_id
    JOIN personnel p ON b.personnel_id = p.personnel_id
    ORDER BY b.borrow_date DESC
    ";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ประวัติการเบิกครุภัณฑ์</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex">

  <?php include '../partials/sidebar.php'; ?>

  <div class="flex-1 ml-64 p-6">
    <h2 class="text-xl font-bold text-gray-700 mb-4">ประวัติการเบิกครุภัณฑ์</h2>

    <?php if ($result->num_rows === 0): ?>
      <div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-4">
        ไม่มีประวัติการเบิกของคุณในระบบ
      </div>
    <?php else: ?>

    <div class="bg-white shadow rounded-lg overflow-x-auto">
      <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
          <tr>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">หมายเลขครุภัณฑ์</th>
            <th class="px-4 py-3">วันที่เบิก</th>
            <th class="px-4 py-3">สถานที่ใช้งาน</th>
            <th class="px-4 py-3">เหตุผล</th>
            <th class="px-4 py-3">สถานะ</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-4 py-2"><?= $i++ ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($row['asset_number']) ?></td>
            <td class="px-4 py-2"><?= date("d/m/Y", strtotime($row['borrow_date'])) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($row['usage_location']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($row['borrow_reason']) ?></td>
            <td class="px-4 py-2">
              <span class="text-xs px-2 py-1 rounded 
                <?= $row['status'] === 'อนุมัติแล้ว' ? 'bg-green-100 text-green-700' :
                    ($row['status'] === 'ไม่อนุมัติ' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') ?>">
                <?= $row['status'] ?>
              </span>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <?php endif; ?>
  </div>
</body>
</html>
