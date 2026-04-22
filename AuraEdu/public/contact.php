<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senderName = trim($_POST['sender_name'] ?? '');
    $senderEmail = trim($_POST['sender_email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $body = trim($_POST['message_body'] ?? '');
    $customerId = is_customer_logged_in() ? (int) $_SESSION['customer_id'] : null;

    if ($senderName === '' || $senderEmail === '' || $subject === '' || $body === '') {
        $message = 'Please fill all fields.';
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO contact_messages (customer_id, sender_name, sender_email, subject, message_body)
             VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, 'issss', $customerId, $senderName, $senderEmail, $subject, $body);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $message = 'Message sent successfully.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="contact-page-wrapper">
  <div class="container">
    <div class="contact-header">
      <h2>Contact Us</h2>
      <p>Have questions? We're here to help. Send us a message or visit our campus.</p>
    </div>

    <div class="contact-grid">
      <!-- Contact Form -->
      <div class="contact-form-section">
        <div class="card auth-card" style="max-width: 100%; margin: 0;">
          <?php if ($message !== ''): ?>
            <div class="<?php echo (strpos($message, 'successfully') !== false) ? 'auth-success' : 'auth-error'; ?>">
              <?php echo h($message); ?>
            </div>
          <?php endif; ?>

          <form method="post" action="contact.php" class="auth-form">
            <label for="sender_name">Your Name</label>
            <input id="sender_name" name="sender_name" type="text" placeholder="John Doe" required>

            <label for="sender_email">Email Address</label>
            <input id="sender_email" name="sender_email" type="email" placeholder="email@example.com" required>

            <label for="subject">Subject</label>
            <input id="subject" name="subject" type="text" placeholder="How can we help?" required>

            <label for="message_body">Message</label>
            <textarea id="message_body" name="message_body" rows="5" placeholder="Write your message here..." required style="width: 100%; padding: 12px; border: 1px solid var(--pd-border); border-radius: 8px; margin-bottom: 20px; font-family: inherit;"></textarea>

            <button class="auth-btn" type="submit">Send Message</button>
          </form>
        </div>
      </div>

      <!-- Map & Info -->
      <div class="contact-info-section">
        <div class="info-card">
          <div class="info-item">
            <i class="fas fa-map-marker-alt"></i>
            <div>
              <h4>Our Location</h4>
              <p>Imam Abdulrahman Al Faisal University<br>College of Computer Science & IT</p>
            </div>
          </div>
          <div class="info-item">
            <i class="fas fa-envelope"></i>
            <div>
              <h4>Email Us</h4>
              <p>support@auraedu.edu</p>
            </div>
          </div>
          <div class="info-item">
            <i class="fas fa-phone"></i>
            <div>
              <h4>Call Us</h4>
              <p>+966 13 333 3333</p>
            </div>
          </div>
        </div>

        <div class="map-container">
          <!-- Embedded Google Map for IAU CCSIT -->
          <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3575.441864115904!2d50.19159997542036!3d26.344583177002026!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e49ef97f374345f%3A0x67347a468641505c!2z2YPZhNmK2Kkg2KfZhNit2KfYs9io2Kkg2YjYqtmC2YbZitipINmE2YXYudmE2YjZhdin2Kog2YjYqtmC2YbZitipINmB2YrYt9mK!5e0!3m2!1sen!2ssa!4v1713100000000!5m2!1sen!2ssa" 
            width="100%" 
            height="350" 
            style="border:0; border-radius: 12px;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
          <div style="margin-top: 12px; text-align: center;">
            <a href="https://maps.app.goo.gl/UhbsBDJf9m8yK26QA" target="_blank" class="nav-link" style="color: var(--pd-primary); font-weight: bold;">
              <i class="fas fa-external-link-alt"></i> Open in Google Maps
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
