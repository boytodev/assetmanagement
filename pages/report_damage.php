<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$personnel_id = $_SESSION['user']['personnel_id'];
$success = false;

// รายการที่ผู้ใช้เคยยืมหรือมีสิทธิ์แจ้ง
$items = $conn->query("
  SELECT ai.item_id, a.asset_name, ai.asset_number
  FROM asset_item ai
  JOIN asset a ON ai.asset_id = a.asset_id
  WHERE ai.status IN ('ใช้งานได้', 'ยืม')
");

// เมื่อแจ้งชำรุดหรือสูญหาย
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $type = $_POST['report_type'];
    $detail = $_POST['report_detail'];
    $date = date('Y-m-d');

    // 1. เพิ่มข้อมูลการแจ้ง
    $stmt = $conn->prepare("INSERT INTO asset_report (item_id, personnel_id, report_type, report_detail, report_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $item_id, $personnel_id, $type, $detail, $date);
    $stmt->execute();

    // 2. อัปเดตสถานะของครุภัณฑ์
    $conn->query("UPDATE asset_item SET status = '$type' WHERE item_id = $item_id");

    $success = true;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แจ้งชำรุด / สูญหาย</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
  <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex">

  <?php include '../partials/sidebar.php'; ?>

  <div class="flex-1 ml-64 p-6">
    <h2 class="text-xl font-bold text-gray-700 mb-4">แจ้งชำรุด / สูญหาย</h2>

    <form method="POST" class="bg-white shadow rounded-lg p-6 max-w-xl">
      <div class="mb-4">
        <label class="block text-gray-600 mb-1">เลือกรายการครุภัณฑ์</label>
        <select name="item_id" required class="w-full px-4 py-2 border rounded">
          <option value="">-- เลือก --</option>
          <?php while ($row = $items->fetch_assoc()): ?>
            <option value="<?= $row['item_id'] ?>">
              <?= $row['asset_name'] ?> (<?= $row['asset_number'] ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-4">
        <label class="block text-gray-600 mb-1">ประเภทการแจ้ง</label>
        <select name="report_type" required class="w-full px-4 py-2 border rounded">
          <option value="ชำรุด">ชำรุด</option>
          <option value="สูญหาย">สูญหาย</option>
        </select>
      </div>

      <div class="mb-6">
        <label class="block text-gray-600 mb-1">รายละเอียดเพิ่มเติม</label>
        <textarea name="report_detail" rows="4" class="w-full px-4 py-2 border rounded" placeholder="เช่น แตกหัก เสียหายระหว่างใช้งาน ฯลฯ"></textarea>
      </div>

      <div class="text-right">
        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">
          แจ้งปัญหา
        </button>
      </div>
    </form>

    <?php if ($success): ?>
    <script>
      Swal.fire({
        title: 'แจ้งปัญหาเรียบร้อยแล้ว',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
      }).then(() => {
        window.location.href = 'dashboard.php';
      });
    </script>
    <?php endif; ?>
  </div>
</body>
</html>
