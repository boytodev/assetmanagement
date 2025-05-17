<?php
    session_start();
    include 'connect.php';

    $result = $conn->query("SELECT personnel_id, full_name, username FROM personnel");

    // Handle delete request
    if (isset($_GET['delete_id'])) {
        $delete_id = intval($_GET['delete_id']);
        $stmt      = $conn->prepare("DELETE FROM personnel WHERE personnel_id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
        // Redirect to avoid resubmission
        header("Location: manage_personnel.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ข้อมูลบุคลากร</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">

<div class="flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white min-h-screen p-4 space-y-4">
        <div class="text-xl font-bold mb-6">สมคิด คำดี</div>
        <nav class="space-y-2">
            <a href="dashboard.php" class="block hover:bg-gray-700 rounded px-3 py-2"><i class="fa fa-home mr-2"></i> หน้าแรก</a>
            <a href="manage_personnel.php" class="block hover:bg-gray-700 rounded px-3 py-2"><i class="fa fa-users mr-2"></i> จัดการเจ้าหน้าที่</a>
            <a href="#" class="block hover:bg-gray-700 rounded px-3 py-2"><i class="fa fa-box mr-2"></i> จัดการข้อมูลทรัพย์สิน</a>
        </nav>
    </aside>

    <!-- Main content -->
    <div class="flex-1">
        <!-- Header -->
        <header class="bg-cyan-500 text-white px-6 py-4 flex justify-between items-center">
            <h1 class="text-xl font-semibold">ระบบบริหารจัดการครุภัณฑ์และการเบิกใช้โดยบุคลากร</h1>
            <div class="text-right">
                <div class="font-bold">สมคิด คำดี</div>
                <div class="text-sm">สำนักบริหาร</div>
            </div>
        </header>

        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">ข้อมูลบุคลากร</h2>
                <a href="add_personnel.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    เพิ่มบุคลากร
                </a>
            </div>

            <div class="bg-white p-6 rounded shadow">
                <table id="personnelTable" class="display text-sm w-full text-center">
                    <thead class="bg-gray-100">
                        <tr>
                            <th>ID</th>
                            <th>ชื่อบุคลากร</th>
                            <th>Username</th>
                            <th>แก้รหัสผ่าน</th>
                            <th>แก้ไข</th>
                            <th>ลบ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['personnel_id'] ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']) ?></td>
                            <td><?php echo htmlspecialchars($row['username']) ?></td>
                            <td>
                                <a href="reset_password.php?id=<?php echo $row['personnel_id'] ?>" class="bg-sky-500 hover:bg-sky-600 text-white px-3 py-1 rounded">แก้รหัสผ่าน</a>
                            </td>
                            <td>
                                <a href="edit_personnel.php?id=<?php echo $row['personnel_id'] ?>" class="bg-orange-400 hover:bg-orange-500 text-white px-4 py-1 rounded">แก้ไข</a>
                            </td>
                            <td>
                                <button onclick="confirmDelete(<?php echo $row['personnel_id']?>)" class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded">
                                    ลบ
                                </button>
                            </td>
                        </tr>
                        <?php endwhile?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    $('#personnelTable').DataTable({
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
        }
    });
});

function confirmDelete(id) {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "คุณต้องการลบข้อมูลบุคลากรนี้ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            // ลบโดย redirect พร้อมส่ง id
            window.location.href = "manage_personnel.php?delete_id=" + id;
        }
    });
}
</script>

</body>
</html>
