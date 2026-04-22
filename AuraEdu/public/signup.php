<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');

    if ($fullName === '' || $email === '' || $password === '') {
        $message = 'Please fill required fields.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO customers (full_name, email, password_hash, phone, city) VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, 'sssss', $fullName, $email, $hash, $phone, $city);

        if (mysqli_stmt_execute($stmt)) {
            $message = 'Account created successfully. You can now sign in.';
        } else {
            $message = 'Could not create account. Email may already exist.';
        }
        mysqli_stmt_close($stmt);
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="auth-wrapper">
  <div class="auth-card">
    <h2>Create Account</h2>
    <p class="auth-subtitle">Join AuraEdu and start learning today</p>

    <?php if ($message !== ''): ?>
      <div class="<?php echo (strpos($message, 'successfully') !== false) ? 'auth-success' : 'auth-error'; ?>">
        <?php echo h($message); ?>
      </div>
    <?php endif; ?>

    <form method="post" action="signup.php" class="auth-form">
      <label for="full_name">Full Name</label>
      <input id="full_name" name="full_name" type="text" placeholder="John Doe" required>

      <label for="email">Email Address</label>
      <input id="email" name="email" type="email" placeholder="email@example.com" required>

      <label for="password">Password</label>
      <input id="password" name="password" type="password" placeholder="Min. 8 characters" required>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div>
          <label for="phone">Phone (Optional)</label>
          <input id="phone" name="phone" type="text" placeholder="050...">
        </div>
        <div>
          <label for="city">City (Optional)</label>
          <input id="city" name="city" type="text" placeholder="Dammam">
        </div>
      </div>

      <button class="auth-btn" type="submit">Create Account</button>
    </form>

    <div class="auth-footer">
      Already have an account? <a href="signin.php">Sign in instead</a>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
