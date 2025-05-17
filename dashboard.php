<?php
session_start();
include 'connect.php';

$user = $_SESSION['user']['full_name'] ?? 'ไม่ระบุ';

// สรุปสถานะ (ดัดแปลงตามฐานข้อมูลจริง)
$total    = 100;
$usable   = 50;
$borrowed = 50;
$broken   = 50;
$lost     = 50;

// ตัวอย่างข้อมูลตาราง (คุณสามารถใช้ SELECT จริงได้)
$items = [
    ['id' => 1, 'name' => 'Computer Asus book 15', 'year' => '2550', 'price' => 12900, 'status' => 'ว่าง'],
    ['id' => 2, 'name' => 'Computer Asus book 15', 'year' => '2560', 'price' => 10000, 'status' => 'ยืม'],
    ['id' => 3, 'name' => 'Computer Asus book 15', 'year' => '2565', 'price' => 9000,  'status' => 'ใช้งาน'],
    ['id' => 4, 'name' => 'Computer Asus book 15', 'year' => '2567', 'price' => 9000,  'status' => 'ชำรุด'],
    ['id' => 5, 'name' => 'Computer Asus book 15', 'year' => '2568', 'price' => 9000,  'status' => 'สูญหาย'],
    ['id' => 6, 'name' => 'Computer Asus book 15', 'year' => '2568', 'price' => 9000,  'status' => 'สูญหาย'],
    ['id' => 7, 'name' => 'Computer Asus book 15', 'year' => '2568', 'price' => 9000,  'status' => 'สูญหาย'],
    ['id' => 8, 'name' => 'Computer Asus book 15', 'year' => '2568', 'price' => 9000,  'status' => 'สูญหาย'],
    ['id' => 9, 'name' => 'Computer Asus book 15', 'year' => '2568', 'price' => 9000,  'status' => 'สูญหาย'],
    ['id' => 10, 'name' => 'Computer Asus book 15', 'year' => '2568', 'price' => 9000,  'status' => 'สูญหาย'],
    ['id' => 11, 'name' => 'Computer Asus book 15', 'year' => '2568', 'price' => 9000,  'status' => 'สูญหาย'],
    ['id' => 12, 'name' => 'Computer Asus book 15', 'year' => '2568', 'price' => 9000,  'status' => 'สูญหาย'],
    ['id' => 13, 'name' => 'Computer Asus book 15', 'year' => '2568', 'price' => 9000,  'status' => 'สูญหาย'],
    ['id' => 14, 'name' => 'Computer Asus book 15', 'year' => '2568', 'price' => 9000,  'status' => 'สูญหาย'],
    ['id' => 15, 'name' => 'Computer Asus book 15', 'year' => '2568', 'price' => 9000,  'status' => 'สูญหาย'],
];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
</head>
<body class="bg-gray-100 font-sans">

<!-- Sidebar + Header -->
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

    <!-- Main -->
    <div class="flex-1">
        <!-- Top bar -->
        <header class="bg-cyan-500 text-white px-6 py-4 flex justify-between items-center">
            <h1 class="text-xl font-semibold">ระบบบริหารจัดการครุภัณฑ์และการเบิกใช้โดยบุคลากร</h1>
            <div class="text-right">
                <div class="font-bold"><?= $user ?></div>
                <div class="text-sm">สำนักบริหาร</div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4">Dashboard</h2>

            <!-- Summary Boxes -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8 text-white text-center">
                <div class="bg-blue-600 p-4 rounded"><p>รายการทรัพย์สิน</p><h3 class="text-2xl font-bold"><?= $total ?></h3></div>
                <div class="bg-green-600 p-4 rounded"><p>ใช้งานได้</p><h3 class="text-2xl font-bold"><?= $usable ?></h3></div>
                <div class="bg-indigo-600 p-4 rounded"><p>ยืม</p><h3 class="text-2xl font-bold"><?= $borrowed ?></h3></div>
                <div class="bg-yellow-500 p-4 rounded"><p>ชำรุด</p><h3 class="text-2xl font-bold"><?= $broken ?></h3></div>
                <div class="bg-red-600 p-4 rounded"><p>สูญหาย</p><h3 class="text-2xl font-bold"><?= $lost ?></h3></div>
            </div>

            <!-- Table -->
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">รายการทรัพย์สินทั้งหมด</h3>
                <div class="overflow-x-auto">
                    <table id="assetTable" class="display text-sm w-full">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ชื่อข้อมูลทรัพย์สิน</th>
                                <th>ปีที่ซื้อ</th>
                                <th>ราคา (บาท)</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $row): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td>ชื่อ <?= $row['name'] ?><br><small>เลขครุภัณฑ์: 1440-001-0001-68-001, S/N: SR400CP221</small></td>
                                <td><?= $row['year'] ?></td>
                                <td><?= number_format($row['price']) ?></td>
                                <td><?= $row['status'] ?></td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
    $('#assetTable').DataTable({
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
</script>

</body>
</html>
