<!-- /pages/login.php -->
<?php
    session_start();
    include '../includes/db.php';

    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $stmt = $conn->prepare("SELECT * FROM personnel WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'personnel_id' => $user['personnel_id'],
                'full_name'    => $user['full_name'],
                'user_role'    => $user['user_role'],
            ];

            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
        }
    }
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เข้าสู่ระบบ</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Kanit', sans-serif; }
  </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-700">ระบบบริหารจัดการครุภัณฑ์</h2>

    <?php if ($error): ?>
      <div class="mb-4 text-red-500 text-center"><?php echo $error?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-4">
        <label class="block text-gray-600 mb-1">ชื่อผู้ใช้</label>
        <input type="text" name="username" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
      </div>
      <div class="mb-6">
        <label class="block text-gray-600 mb-1">รหัสผ่าน</label>
        <input type="password" name="password" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
      </div>
      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">เข้าสู่ระบบ</button>
    </form>
  </div>
</body>
</html>
