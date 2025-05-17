<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$personnel_id = $_SESSION['user']['personnel_id'];

// คืนครุภัณฑ์
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $borrow_id = $_POST['borrow_id'];

    // อัปเดตสถานะการคืน
    $stmt = $conn->prepare("UPDATE asset_borrowing SET status = 'คืนแล้ว' WHERE borrow_id = ?");
    $stmt->bind_param("i", $borrow_id);
    $stmt->execute();

    // คืนสถานะ item เป็น 'ว่าง'
    $item_stmt = $conn->prepare("UPDATE asset_item 
        SET status = 'ว่าง' 
        WHERE item_id = (SELECT item_id FROM asset_borrowing WHERE borrow_id = ?)");
    $item_stmt->bind_param("i", $borrow_id);
    $item_stmt->execute();

    header("Location: borrow_return.php");
    exit;
}

// ดึงครุภัณฑ์ที่ยืมอยู่ (อนุมัติแล้ว)
$sql = "
SELECT b.borrow_id, b.borrow_date, ai.asset_number, a.asset_name
FROM asset_borrowing b
JOIN asset_item ai ON b.item_id = ai.item_id
JOIN asset a ON ai.asset_id = a.asset_id
WHERE b.personnel_id = ? AND b.status = 'อนุมัติแล้ว'
ORDER BY b.borrow_date DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $personnel_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>คืนครุภัณฑ์</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex">

  <?php include '../partials/sidebar.php'; ?>

  <div class="flex-1 ml-64 p-6">
    <h2 class="text-xl font-bold text-gray-700 mb-4">คืนครุภัณฑ์</h2>

    <?php if ($result->num_rows === 0): ?>
      <div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-4">
        คุณไม่มีครุภัณฑ์ที่ต้องคืนในขณะนี้
      </div>
    <?php else: ?>
    <div class="bg-white shadow rounded-lg overflow-x-auto">
      <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
          <tr>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">ชื่อครุภัณฑ์</th>
            <th class="px-4 py-3">หมายเลข</th>
            <th class="px-4 py-3">วันที่เบิก</th>
            <th class="px-4 py-3 text-center">คืนครุภัณฑ์</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-4 py-2"><?= $i++ ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($row['asset_name']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($row['asset_number']) ?></td>
            <td class="px-4 py-2"><?= date("d/m/Y", strtotime($row['borrow_date'])) ?></td>
            <td class="px-4 py-2 text-center">
              <form method="POST">
                <input type="hidden" name="borrow_id" value="<?= $row['borrow_id'] ?>">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs">
                  คืนครุภัณฑ์
                </button>
              </form>
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
