<?php
    require_once '../includes/auth.php';
    require_once '../includes/db.php';

    // เฉพาะ admin เท่านั้น
    if ($_SESSION['user']['user_role'] !== 'admin') {
        header("Location: dashboard.php");
        exit;
    }

    // เมื่อกดอนุมัติ / ไม่อนุมัติ
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $borrow_id = $_POST['borrow_id'];
        $action    = $_POST['action'];

        $status = $action === 'approve' ? 'อนุมัติแล้ว' : 'ไม่อนุมัติ';

        $stmt = $conn->prepare("UPDATE asset_borrowing SET status=? WHERE borrow_id=?");
        $stmt->bind_param("si", $status, $borrow_id);
        $stmt->execute();

        // ถ้าไม่อนุมัติ → คืนสถานะ item ให้เป็น 'ว่าง'
        if ($status === 'ไม่อนุมัติ') {
            $item_stmt = $conn->prepare("UPDATE asset_item SET status='ว่าง' WHERE item_id=(SELECT item_id FROM asset_borrowing WHERE borrow_id=?)");
            $item_stmt->bind_param("i", $borrow_id);
            $item_stmt->execute();
        }

        header("Location: borrow_approve.php");
        exit;
    }

    // ดึงรายการที่รออนุมัติ
    $sql = "
SELECT b.borrow_id, b.borrow_date, b.usage_location, b.borrow_reason, p.full_name, ai.asset_number
FROM asset_borrowing b
JOIN asset_item ai ON b.item_id = ai.item_id
JOIN personnel p ON b.personnel_id = p.personnel_id
WHERE b.status = 'รออนุมัติ'
ORDER BY b.borrow_date DESC
";
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>อนุมัติคำขอเบิก</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex">

  <?php include '../partials/sidebar.php'; ?>

  <div class="flex-1 ml-64 p-6">
    <h2 class="text-xl font-bold text-gray-700 mb-4">อนุมัติคำขอเบิกครุภัณฑ์</h2>

    <?php if ($result->num_rows === 0): ?>
      <div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-4">
        ไม่มีรายการรออนุมัติในขณะนี้
      </div>
    <?php else: ?>
    <div class="bg-white shadow rounded-lg overflow-x-auto">
      <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
          <tr>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">หมายเลขครุภัณฑ์</th>
            <th class="px-4 py-3">ผู้ขอ</th>
            <th class="px-4 py-3">วันที่</th>
            <th class="px-4 py-3">สถานที่ใช้งาน</th>
            <th class="px-4 py-3">เหตุผล</th>
            <th class="px-4 py-3 text-center">การดำเนินการ</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1;while ($row = $result->fetch_assoc()): ?>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-4 py-2"><?php echo $i++?></td>
            <td class="px-4 py-2"><?php echo htmlspecialchars($row['asset_number'])?></td>
            <td class="px-4 py-2"><?php echo htmlspecialchars($row['full_name'])?></td>
            <td class="px-4 py-2"><?php echo date("d/m/Y", strtotime($row['borrow_date']))?></td>
            <td class="px-4 py-2"><?php echo htmlspecialchars($row['usage_location'])?></td>
            <td class="px-4 py-2"><?php echo htmlspecialchars($row['borrow_reason'])?></td>
            <td class="px-4 py-2 text-center space-x-1">
              <form method="POST" class="inline">
                <input type="hidden" name="borrow_id" value="<?php echo $row['borrow_id']?>">
                <input type="hidden" name="action" value="approve">
                <button class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700">อนุมัติ</button>
              </form>
              <form method="POST" class="inline">
                <input type="hidden" name="borrow_id" value="<?php echo $row['borrow_id']?>">
                <input type="hidden" name="action" value="reject">
                <button class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700">ไม่อนุมัติ</button>
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
