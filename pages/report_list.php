<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// ตรวจสอบเฉพาะ admin
if ($_SESSION['user']['user_role'] !== 'admin') {
  header("Location: dashboard.php");
  exit;
}

// ดึงรายการแจ้งทั้งหมด
$sql = "
SELECT r.*, p.full_name, a.asset_name, ai.asset_number
FROM asset_report r
JOIN personnel p ON r.personnel_id = p.personnel_id
JOIN asset_item ai ON r.item_id = ai.item_id
JOIN asset a ON ai.asset_id = a.asset_id
ORDER BY r.report_date DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ประวัติการแจ้งปัญหา</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex">

<?php include '../partials/sidebar.php'; ?>

<div class="flex-1 ml-64 p-6">
  <h2 class="text-xl font-bold text-gray-700 mb-4">ประวัติการแจ้งชำรุด / สูญหาย</h2>

  <?php if ($result->num_rows === 0): ?>
    <div class="bg-yellow-100 text-yellow-800 p-4 rounded">
      ไม่มีรายการแจ้งปัญหาในระบบ
    </div>
  <?php else: ?>
  <div class="bg-white shadow rounded-lg overflow-x-auto">
    <table class="min-w-full text-sm text-left text-gray-700">
      <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
        <tr>
          <th class="px-4 py-3">#</th>
          <th class="px-4 py-3">ชื่อครุภัณฑ์</th>
          <th class="px-4 py-3">หมายเลข</th>
          <th class="px-4 py-3">ผู้แจ้ง</th>
          <th class="px-4 py-3">ประเภท</th>
          <th class="px-4 py-3">รายละเอียด</th>
          <th class="px-4 py-3">วันที่แจ้ง</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
        <tr class="border-b hover:bg-gray-50">
          <td class="px-4 py-2"><?= $i++ ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($row['asset_name']) ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($row['asset_number']) ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($row['full_name']) ?></td>
          <td class="px-4 py-2">
            <span class="px-2 py-1 text-xs rounded 
              <?= $row['report_type'] === 'ชำรุด' ? 'bg-yellow-200 text-yellow-800' : 'bg-red-200 text-red-800' ?>">
              <?= $row['report_type'] ?>
            </span>
          </td>
          <td class="px-4 py-2"><?= nl2br(htmlspecialchars($row['report_detail'])) ?></td>
          <td class="px-4 py-2"><?= date("d/m/Y", strtotime($row['report_date'])) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
