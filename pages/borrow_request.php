<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$personnel_id = $_SESSION['user']['personnel_id'];
$success = false;

// ดึงรายการครุภัณฑ์ที่ว่าง
$items = $conn->query("
    SELECT ai.item_id, a.asset_name, ai.asset_number, ai.status
    FROM asset_item ai
    JOIN asset a ON ai.asset_id = a.asset_id
    WHERE ai.status IN ('ว่าง', 'ใช้งานได้')
");

// บันทึกเมื่อส่ง POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $location = $_POST['location'];
    $reason = $_POST['reason'];
    $quantity = 1;
    $date = date('Y-m-d');

   $stmt = $conn->prepare("INSERT INTO asset_borrowing (item_id, personnel_id, quantity, borrow_date, usage_location, borrow_reason) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisss", $item_id, $personnel_id, $quantity, $date, $location, $reason);
    $stmt->execute();

    // เปลี่ยนสถานะใน asset_item
    $conn->query("UPDATE asset_item SET status = 'ยืม' WHERE item_id = $item_id");

    $success = true;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แบบฟอร์มขอเบิกครุภัณฑ์</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
   <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
  <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <form method="POST" class="bg-white shadow-xl rounded-lg w-full max-w-xl p-8">
    <h2 class="text-xl font-bold mb-6 text-gray-700">แบบฟอร์มขอเบิกครุภัณฑ์</h2>

    <div class="mb-4">
      <label class="block text-gray-600 mb-1">เลือกครุภัณฑ์</label>
      <select name="item_id" required class="w-full px-4 py-2 border rounded">
        <option value="">-- เลือกรายการ --</option>
        <?php while ($row = $items->fetch_assoc()): ?>
        <option value="<?= $row['item_id'] ?>">
          <?= $row['asset_name'] ?> (<?= $row['asset_number'] ?>) [<?= $row['status'] ?>]
        </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-4">
      <label class="block text-gray-600 mb-1">สถานที่ใช้งาน</label>
      <input type="text" name="location" required class="w-full px-4 py-2 border rounded">
    </div>

    <div class="mb-6">
      <label class="block text-gray-600 mb-1">เหตุผลในการเบิก</label>
      <textarea name="reason" rows="3" required class="w-full px-4 py-2 border rounded"></textarea>
    </div>

    <div class="text-right">
      <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">ส่งคำขอ</button>
    </div>
  </form>

  <?php if ($success): ?>
  <script>
    Swal.fire({
      title: 'ส่งคำขอเบิกสำเร็จ',
      icon: 'success',
      timer: 1500,
      showConfirmButton: false
    }).then(() => {
      window.location.href = 'dashboard.php';
    });
  </script>
  <?php endif; ?>

</body>
</html>
