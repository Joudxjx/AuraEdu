<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    session_start();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $userType = $_POST['user_type'] ?? 'customer';

    if ($email === '' || $password === '') {
        $message = 'Please fill in all fields.';
    } else {
        if ($userType === 'admin') {
            $stmt = mysqli_prepare($conn, "SELECT admin_id, full_name, password_hash FROM admins WHERE email = ?");
        } else {
            $stmt = mysqli_prepare($conn, "SELECT customer_id, full_name, password_hash FROM customers WHERE email = ?");
        }

        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user) {
            // Milestone 2 simplified auth: accept typed password, hash-verify, or default 'admin123'
            $hash = (string) $user['password_hash'];
            $isPasswordValid = ($password === $hash) || password_verify($password, $hash) || ($password === 'admin123' && $hash === '$2y$10$examplehash');

            if ($isPasswordValid) {
                if ($userType === 'admin') {
                    $_SESSION['role'] = 'admin';
                    $_SESSION['admin_id'] = (int) $user['admin_id'];
                    $_SESSION['name'] = $user['full_name'];
                    header('Location: admin_dashboard.php');
                    exit;
                }

                $_SESSION['role'] = 'customer';
                $_SESSION['customer_id'] = (int) $user['customer_id'];
                $_SESSION['name'] = $user['full_name'];
                header('Location: customer_dashboard.php');
                exit;
            }
        }

        $message = 'Invalid credentials.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="auth-wrapper">
  <div class="auth-card">
    <h2>Welcome Back</h2>
    <p class="auth-subtitle">Sign in to your AuraEdu account</p>

    <?php if ($message !== ''): ?>
      <div class="auth-error"><?php echo h($message); ?></div>
    <?php endif; ?>

    <form method="post" action="signin.php" class="auth-form">
      <label for="user_type">I am a...</label>
      <select id="user_type" name="user_type" required>
        <option value="customer">Customer / Student</option>
        <option value="admin">Administrator</option>
      </select>

      <label for="email">Email Address</label>
      <input id="email" type="email" name="email" placeholder="email@example.com" required>

      <label for="password">Password</label>
      <input id="password" type="password" name="password" placeholder="••••••••" required>

      <button class="auth-btn" type="submit">Sign In</button>
    </form>

    <div class="auth-footer">
      Don't have an account? <a href="signup.php">Create one now</a>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
