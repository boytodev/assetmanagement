<?php
session_start();
include 'connect.php';

if (!isset($_GET['id'])) {
    header("Location: manage_personnel.php");
    exit;
}
$id = intval($_GET['id']);

// โหลดข้อมูลบุคลากรเดิม
$stmt = $conn->prepare("SELECT * FROM personnel WHERE personnel_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

// โหลดกลุ่ม
$groups = $conn->query("SELECT group_id, group_name FROM `groups`")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone     = trim($_POST['phone_number']);
    $role      = $_POST['user_role'];
    $group_id  = $_POST['group_id'];

    $stmt = $conn->prepare("UPDATE personnel SET full_name=?, phone_number=?, user_role=?, group_id=? WHERE personnel_id=?");
    $stmt->bind_param("sssii", $full_name, $phone, $role, $group_id, $id);
    $stmt->execute();

    header("Location: manage_personnel.php?updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขบุคลากร</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-6 text-center">แก้ไขข้อมูลบุคลากร</h2>

    <form method="post" class="grid grid-cols-1 gap-4">
        <div>
            <label>ชื่อ-นามสกุล</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($data['full_name']) ?>" required class="w-full border px-3 py-2 rounded">
        </div>
        <div>
            <label>เบอร์โทรศัพท์</label>
            <input type="text" name="phone_number" value="<?= htmlspecialchars($data['phone_number']) ?>" required class="w-full border px-3 py-2 rounded">
        </div>
        <div>
            <label>บทบาท</label>
            <select name="user_role" class="w-full border px-3 py-2 rounded" required>
                <option value="user" <?= $data['user_role'] === 'user' ? 'selected' : '' ?>>ผู้ใช้งานทั่วไป</option>
                <option value="staff" <?= $data['user_role'] === 'staff' ? 'selected' : '' ?>>เจ้าหน้าที่</option>
                <option value="admin" <?= $data['user_role'] === 'admin' ? 'selected' : '' ?>>ผู้ดูแลระบบ</option>
            </select>
        </div>
        <div>
            <label>กลุ่ม/หน่วยงาน</label>
            <select name="group_id" required class="w-full border px-3 py-2 rounded">
                <?php foreach ($groups as $group): ?>
                    <option value="<?= $group['group_id'] ?>" <?= $data['group_id'] == $group['group_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($group['group_name']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="mt-4">
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded">บันทึกการเปลี่ยนแปลง</button>
        </div>
    </form>
</div>

</body>
</html>
