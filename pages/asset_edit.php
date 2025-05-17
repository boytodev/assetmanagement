<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$id = $_GET['id'] ?? 0;
$success = false;

// โหลดข้อมูลครุภัณฑ์
$stmt = $conn->prepare("SELECT * FROM asset WHERE asset_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

// บันทึกเมื่อมี POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['asset_name'];
    $type = $_POST['asset_type'];
    $unit_price = $_POST['unit_price'];
    $quantity = $_POST['quantity'];
    $receive_date = $_POST['receive_date'];
    $feature = $_POST['special_feature'];
    $life = $_POST['useful_life'];
    $remain = $_POST['remaining_value'];
    $source = $_POST['source'];

    $update = $conn->prepare("UPDATE asset SET asset_name=?, unit_price=?, quantity=?, receive_date=?, asset_type=?, special_feature=?, useful_life=?, remaining_value=?, source=? WHERE asset_id=?");
    $update->bind_param("sdisssidsi", $name, $unit_price, $quantity, $receive_date, $type, $feature, $life, $remain, $source, $id);
    $update->execute();

    $success = true;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แก้ไขครุภัณฑ์</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
  <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

  <form method="POST" class="bg-white shadow-xl rounded-lg w-full max-w-2xl p-8">
    <h2 class="text-xl font-bold mb-6 text-gray-700">แก้ไขครุภัณฑ์</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-gray-600 mb-1">ชื่อครุภัณฑ์</label>
        <input type="text" name="asset_name" value="<?= htmlspecialchars($data['asset_name']) ?>" required class="w-full px-4 py-2 border rounded">
      </div>
      <div>
        <label class="block text-gray-600 mb-1">ประเภท</label>
        <input type="text" name="asset_type" value="<?= htmlspecialchars($data['asset_type']) ?>" required class="w-full px-4 py-2 border rounded">
      </div>

      <div>
        <label class="block text-gray-600 mb-1">ราคาต่อหน่วย</label>
        <input type="number" step="0.01" name="unit_price" value="<?= $data['unit_price'] ?>" class="w-full px-4 py-2 border rounded">
      </div>
      <div>
        <label class="block text-gray-600 mb-1">จำนวน</label>
        <input type="number" name="quantity" value="<?= $data['quantity'] ?>" class="w-full px-4 py-2 border rounded">
      </div>

      <div>
        <label class="block text-gray-600 mb-1">วันที่ได้รับ</label>
        <input type="date" name="receive_date" value="<?= $data['receive_date'] ?>" class="w-full px-4 py-2 border rounded">
      </div>
      <div>
        <label class="block text-gray-600 mb-1">ลักษณะเฉพาะ</label>
        <input type="text" name="special_feature" value="<?= htmlspecialchars($data['special_feature']) ?>" class="w-full px-4 py-2 border rounded">
      </div>

      <div>
        <label class="block text-gray-600 mb-1">อายุการใช้งาน</label>
        <input type="number" name="useful_life" value="<?= $data['useful_life'] ?>" class="w-full px-4 py-2 border rounded">
      </div>
      <div>
        <label class="block text-gray-600 mb-1">มูลค่าคงเหลือ</label>
        <input type="number" step="0.01" name="remaining_value" value="<?= $data['remaining_value'] ?>" class="w-full px-4 py-2 border rounded">
      </div>

      <div class="col-span-1 md:col-span-2">
        <label class="block text-gray-600 mb-1">แหล่งที่มา</label>
        <input type="text" name="source" value="<?= htmlspecialchars($data['source']) ?>" class="w-full px-4 py-2 border rounded">
      </div>
    </div>

    <div class="mt-6 text-right">
      <button type="submit" class="bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600">
        บันทึกการเปลี่ยนแปลง
      </button>
    </div>
  </form>

  <?php if ($success): ?>
  <script>
    Swal.fire({
      title: 'บันทึกข้อมูลแล้ว',
      icon: 'success',
      timer: 1500,
      showConfirmButton: false
    }).then(() => {
      window.location.href = 'asset_list.php';
    });
  </script>
  <?php endif; ?>

</body>
</html>
