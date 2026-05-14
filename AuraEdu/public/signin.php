<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

// The same page also handles logout.
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    session_start();
}

$message = '';

// Check admin login details and start the admin session.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $message = 'Please fill in all fields.';
    } else {
        $stmt = mysqli_prepare(db(), 'SELECT id, name, password FROM Admin WHERE email = ?');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $admin = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($admin && password_verify($password, (string) $admin['password'])) {
            $_SESSION['role'] = 'admin';
            $_SESSION['admin_id'] = (int) $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            header('Location: admin_dashboard.php');
            exit;
        }

        $message = 'Invalid admin credentials.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="auth-wrapper">
  <div class="auth-card">
    <h1>Admin Login</h1>
    <p class="auth-subtitle">Use the admin account to add, modify, delete, and search products.</p>

    <?php if ($message !== ''): ?>
      <p class="notice error"><?php echo h($message); ?></p>
    <?php endif; ?>

    <form method="post" action="signin.php" id="signin-form" class="auth-form">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" required>

      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>

      <button class="auth-btn" type="submit">Login</button>
    </form>

    <p class="auth-subtitle">Default admin email: `admin@auraedu.edu`</p>
    <p class="auth-subtitle">Default admin password: `admin123`</p>
  </div>
</section>

<script>
document.getElementById('signin-form').addEventListener('submit', function (event) {
  // Stop the form if the admin leaves a field empty.
  if (document.getElementById('email').value.trim() === '' || document.getElementById('password').value.trim() === '') {
    event.preventDefault();
    alert('Please enter email and password.');
  }
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
