<!-- /partials/header.php -->
<div class="w-full bg-cyan-600 text-white shadow flex justify-between items-center px-6 py-3">
  <!-- ชื่อระบบ -->
  <div class="text-lg font-semibold tracking-wide">
    ระบบบริหารจัดการครุภัณฑ์และการเบิกใช้โดยบุคลากร
  </div>

  <!-- ข้อมูลผู้ใช้ -->
  <div class="flex items-center space-x-4">
    <div class="text-right">
      <div class="font-semibold"><?= $_SESSION['user']['full_name'] ?? 'ไม่ระบุ' ?></div>
      <div class="text-xs text-cyan-100"><?= $_SESSION['user']['user_role'] ?? '-' ?></div>
    </div>
  </div>
</div>
