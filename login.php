<?php
require 'config.php';
$errors = '';

try {
    $pdo = connect_db();
    $countStmt = $pdo->query('SELECT COUNT(*) FROM admin_users');
    $adminCount = (int) $countStmt->fetchColumn();
    if ($adminCount === 0) {
        $defaultPassword = 'Password123';
        $passwordHash = password_hash($defaultPassword, PASSWORD_DEFAULT);
        $insert = $pdo->prepare('INSERT INTO admin_users (email, password_hash, created_at) VALUES (:email, :hash, NOW())');
        $insert->execute([':email' => 'admin@portfolio.local', ':hash' => $passwordHash]);
        $errors = 'Default admin user created: admin@portfolio.local / Password123. Please change the password after login.';
    }
} catch (Exception $exception) {
    $errors = 'Database connection failed. Please ensure MySQL is configured and the schema is installed.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors = 'Please enter both email and password.';
    } else {
        try {
            $stmt = connect_db()->prepare('SELECT password_hash FROM admin_users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['admin'] = $email;
                header('Location: dashboard.php');
                exit;
            }
            $errors = 'Invalid login credentials.';
        } catch (Exception $exception) {
            $errors = 'Unable to validate login. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | Masud Portfolio</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <main class="contact-inner" style="max-width:600px;margin:6rem auto;">
    <div class="contact-header">
      <h2 class="section-title">Admin Login</h2>
      <p class="contact-sub">Use your admin credentials to manage projects and view messages.</p>
    </div>
    <form method="post" class="contact-form" novalidate>
      <?php if ($errors): ?>
        <div class="form-status" style="color:#ff6b6b;"><?= htmlspecialchars($errors) ?></div>
      <?php endif; ?>
      <label>
        Email
        <input type="email" name="email" required placeholder="admin@portfolio.local">
      </label>
      <label class="full-width">
        Password
        <input type="password" name="password" required placeholder="Enter password">
      </label>
      <div class="form-actions">
        <button type="submit" class="btn-primary">Login</button>
      </div>
    </form>
  </main>
</body>
</html>
