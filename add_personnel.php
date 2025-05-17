<?php
    session_start();
    include 'connect.php';

    $user = $_SESSION['user']['full_name'] ?? 'ไม่ระบุ';
    // โหลดกลุ่ม (หน่วยงาน) สำหรับ dropdown
    $groups       = [];
    $group_result = $conn->query("SELECT group_id, group_name FROM `groups`");
    while ($row = $group_result->fetch_assoc()) {
        $groups[] = $row;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $full_name = trim($_POST['full_name']);
        $phone     = trim($_POST['phone_number']);
        $username  = trim($_POST['username']);
        $password  = trim($_POST['password']);
        $role      = $_POST['user_role'];
        $group_id  = $_POST['group_id'];

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // ตรวจสอบ username ซ้ำ
        $stmt = $conn->prepare("SELECT * FROM personnel WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        if ($existing) {
            $error = "ชื่อผู้ใช้นี้ถูกใช้งานแล้ว";
        } else {
            $stmt = $conn->prepare("INSERT INTO personnel (full_name, phone_number, username, password, user_role, group_id)
                                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $full_name, $phone, $username, $hashed_password, $role, $group_id);
            $stmt->execute();
            header("Location: manage_personnel.php?success=1");
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มบุคลากร</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

<div class="flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white min-h-screen p-4 space-y-4">
        <div class="text-xl font-bold mb-6">สมคิด คำดี</div>
        <nav class="space-y-2">
            <a href="dashboard.php" class="block hover:bg-gray-700 rounded px-3 py-2"><i class="fa fa-home mr-2"></i> หน้าแรก</a>
            <a href="manage_personnel.php" class="block hover:bg-gray-700 rounded px-3 py-2"><i class="fa fa-users mr-2"></i> จัดการเจ้าหน้าที่</a>
            <a href="manage_equipment.php" class="block hover:bg-gray-700 rounded px-3 py-2"><i class="fa fa-box mr-2"></i> จัดการข้อมูลทรัพย์สิน</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1">
        <header class="bg-cyan-500 text-white px-6 py-4 flex justify-between items-center">
            <h1 class="text-xl font-semibold">ระบบบริหารจัดการครุภัณฑ์และการเบิกใช้โดยบุคลากร</h1>
            <div class="text-right">
                <div class="font-bold"><?php echo $user ?></div>
                <div class="text-sm">สำนักบริหาร</div>
            </div>
        </header>

        <div class="p-6">
        <h2 class="text-xl font-semibold mb-4">เพิ่มบุคลากร</h2>

            <?php if (! empty($error)): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <?php echo htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-6 rounded shadow">
            <div>
                <label class="block mb-1">ชื่อ-นามสกุล</label>
                <input type="text" name="full_name" required class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label class="block mb-1">เบอร์โทรศัพท์</label>
                <input type="text" name="phone_number" required class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label class="block mb-1">ชื่อผู้ใช้</label>
                <input type="text" name="username" required class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label class="block mb-1">รหัสผ่าน</label>
                <input type="password" name="password" required class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label class="block mb-1">บทบาท</label>
                <select name="user_role" required class="w-full border px-3 py-2 rounded">
                    <option value="user">ผู้ใช้งานทั่วไป</option>
                    <option value="staff">เจ้าหน้าที่</option>
                    <option value="admin">ผู้ดูแลระบบ</option>
                </select>
            </div>

            <div>
                <label class="block mb-1">กลุ่ม/หน่วยงาน</label>
                <select name="group_id" required class="w-full border px-3 py-2 rounded">
                    <option value="">-- กรุณาเลือก --</option>
                    <?php foreach ($groups as $group): ?>
                        <option value="<?php echo $group['group_id'] ?>"><?php echo htmlspecialchars($group['group_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="md:col-span-2 mt-4">
                <button type="submit" class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded">
                    บันทึกข้อมูล
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

</body>
</html>
