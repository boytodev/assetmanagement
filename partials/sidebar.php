<?php
    $user = $_SESSION['user'] ?? ['full_name' => 'ไม่ทราบชื่อ', 'user_role' => '-'];
?>

<div class="w-64 h-screen bg-gray-800 text-white flex flex-col fixed top-0 left-0">
  <div class="p-6 border-b border-gray-700">
    <div class="text-lg font-bold mb-1"><?php echo htmlspecialchars($user['full_name']) ?></div>
    <div class="text-sm text-blue-300"><?php echo htmlspecialchars($user['user_role']) ?></div>
  </div>

  <nav class="flex-1 px-4 py-4 space-y-2">
    <!-- ทุกคนเห็นได้ -->
    <a href="dashboard.php" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700">
      <i class="bi bi-house-door"></i>
      <span>หน้าแรก</span>
    </a>

    <!-- เฉพาะ admin -->
    <?php if ($user['user_role'] === 'admin'): ?>
      <a href="personnel_list.php" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700">
        <i class="bi bi-person-lines-fill"></i>
        <span>จัดการบุคลากร</span>
      </a>
      <a href="group_list.php" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700">
        <i class="bi bi-diagram-3"></i>
        <span>จัดการกลุ่มงาน</span>
      </a>
      
      <a href="asset_list.php" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700">
        <i class="bi bi-hdd-stack"></i>
        <span>จัดการข้อมูลทรัพย์สิน</span>
      </a>
    <?php endif; ?>

    <!-- ทุกคนเห็น -->
<?php if ($user['user_role'] === 'user'): ?>
    <a href="borrow_request.php" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700">
  <i class="bi bi-clipboard-plus"></i>
  <span>ขอเบิกครุภัณฑ์</span>
</a>
<?php endif; ?>

<a href="borrow_history.php" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700">
  <i class="bi bi-clock-history"></i>
  <span>ประวัติการเบิก</span>
</a>

<?php if ($user['user_role'] === 'admin'): ?>
<a href="borrow_return.php" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700">
  <i class="bi bi-arrow-return-left"></i>
  <span>คืนครุภัณฑ์</span>
</a>

<a href="report_damage.php" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700">
  <i class="bi bi-exclamation-triangle"></i>
  <span>แจ้งชำรุด / สูญหาย</span>
</a>
<?php endif; ?>

<?php if ($user['user_role'] === 'admin'): ?>
<a href="borrow_approve.php" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700">
  <i class="bi bi-check2-square"></i>
  <span>อนุมัติคำขอเบิก</span>
</a>

<a href="borrow_returned_list.php" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700">
  <i class="bi bi-archive"></i>
  <span>รายการที่คืนแล้ว</span>
</a>

<a href="report_list.php" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700">
  <i class="bi bi-exclamation-octagon"></i>
  <span>ประวัติการแจ้งปัญหา</span>
</a>

<a href="personnel_reset_password.php" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-700">
  <i class="bi bi-key"></i>
  <span>รีเซ็ตรหัสผ่าน</span>
</a>
<?php endif; ?>


  </nav>

  <div class="p-4 border-t border-gray-700">
    <a href="/assetmanagement/logout.php" class="text-red-300 hover:text-red-500 flex items-center space-x-2">
      <i class="bi bi-box-arrow-right"></i>
      <span>ออกจากระบบ</span>
    </a>
  </div>
</div>
