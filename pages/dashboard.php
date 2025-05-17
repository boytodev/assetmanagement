<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// สถิติ
$total_assets   = $conn->query("SELECT COUNT(*) AS total FROM asset_item")->fetch_assoc()['total'];
$usable         = $conn->query("SELECT COUNT(*) AS usable FROM asset_item WHERE status = 'ใช้งานได้'")->fetch_assoc()['usable'];
$borrowed       = $conn->query("SELECT COUNT(*) AS borrowed FROM asset_item WHERE status = 'ยืม'")->fetch_assoc()['borrowed'];
$broken         = $conn->query("SELECT COUNT(*) AS broken FROM asset_item WHERE status = 'ชำรุด'")->fetch_assoc()['broken'];
$lost           = $conn->query("SELECT COUNT(*) AS lost FROM asset_item WHERE status = 'สูญหาย'")->fetch_assoc()['lost'];
$returned       = $conn->query("SELECT COUNT(*) AS returned FROM asset_borrowing WHERE status = 'คืนแล้ว'")->fetch_assoc()['returned'];
$total_personnel = $conn->query("SELECT COUNT(*) AS count FROM personnel")->fetch_assoc()['count'];
$total_groups    = $conn->query("SELECT COUNT(*) AS count FROM work_group")->fetch_assoc()['count'];

// เบิกล่าสุด
$latest_borrow = $conn->query("
  SELECT b.borrow_date, p.full_name, ai.asset_number
  FROM asset_borrowing b
  JOIN personnel p ON b.personnel_id = p.personnel_id
  JOIN asset_item ai ON b.item_id = ai.item_id
  WHERE b.status = 'อนุมัติแล้ว'
  ORDER BY b.borrow_date DESC
  LIMIT 5
");

// คืนล่าสุด
$latest_return = $conn->query("
  SELECT b.borrow_date, p.full_name, ai.asset_number
  FROM asset_borrowing b
  JOIN personnel p ON b.personnel_id = p.personnel_id
  JOIN asset_item ai ON b.item_id = ai.item_id
  WHERE b.status = 'คืนแล้ว'
  ORDER BY b.borrow_date DESC
  LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
  <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex">

  <?php include '../partials/sidebar.php'; ?>

  <div class="flex-1 ml-64">
    <?php include '../partials/header.php'; ?>

    <!-- สถิติ -->
    <div class="py-6 px-3">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
      <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm mb-1">ครุภัณฑ์ทั้งหมด</div>
        <div class="text-3xl font-bold text-blue-600"><?= $total_assets ?></div>
      </div>
      <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm mb-1">ใช้งานได้</div>
        <div class="text-3xl font-bold text-green-600"><?= $usable ?></div>
      </div>
      <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm mb-1">ยืมอยู่</div>
        <div class="text-3xl font-bold text-yellow-600"><?= $borrowed ?></div>
      </div>
      <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm mb-1">คืนแล้ว</div>
        <div class="text-3xl font-bold text-blue-400"><?= $returned ?></div>
      </div>
      <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm mb-1">ชำรุด</div>
        <div class="text-3xl font-bold text-red-500"><?= $broken ?></div>
      </div>
      <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm mb-1">สูญหาย</div>
        <div class="text-3xl font-bold text-red-700"><?= $lost ?></div>
      </div>
    </div>

    <!-- บุคลากร / กลุ่ม -->
    <div class="grid grid-cols-2 gap-6 mt-6">
      <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm mb-2">จำนวนบุคลากร</div>
        <div class="text-3xl font-bold text-indigo-600"><?= $total_personnel ?></div>
      </div>
      <div class="bg-white shadow p-4 rounded text-center">
        <div class="text-gray-500 text-sm mb-2">กลุ่มงานทั้งหมด</div>
        <div class="text-3xl font-bold text-purple-600"><?= $total_groups ?></div>
      </div>
    </div>

    <!-- รายการล่าสุด -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
      <!-- เบิกล่าสุด -->
      <div class="bg-white shadow rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">
          <i class="bi bi-clipboard-plus"></i> การเบิกล่าสุด
        </h3>
        <ul class="space-y-2 text-sm">
          <?php while ($row = $latest_borrow->fetch_assoc()): ?>
            <li class="flex justify-between border-b pb-1">
              <span><?= htmlspecialchars($row['full_name']) ?> เบิก <?= htmlspecialchars($row['asset_number']) ?></span>
              <span class="text-gray-500"><?= date("d/m/Y", strtotime($row['borrow_date'])) ?></span>
            </li>
          <?php endwhile; ?>
        </ul>
      </div>

      <!-- คืนล่าสุด -->
      <div class="bg-white shadow rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">
          <i class="bi bi-arrow-return-left"></i> การคืนล่าสุด
        </h3>
        <ul class="space-y-2 text-sm">
          <?php while ($row = $latest_return->fetch_assoc()): ?>
            <li class="flex justify-between border-b pb-1">
              <span><?= htmlspecialchars($row['full_name']) ?> คืน <?= htmlspecialchars($row['asset_number']) ?></span>
              <span class="text-gray-500"><?= date("d/m/Y", strtotime($row['borrow_date'])) ?></span>
            </li>
          <?php endwhile; ?>
        </ul>
      </div>
    </div>
    </div>
  </div>
</body>
</html>
