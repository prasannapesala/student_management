<?php
require_once 'auth.php';

if (!empty($_SESSION['user'])) {
    $role = $_SESSION['user']['role'];
    $redirect = match ($role) {
        'student' => 'student-dashboard.php',
        'teacher' => 'teacherdashboard.php',
        'hod' => 'hoddashboard.php',
        default => 'student-dashboard.php',
    };
    header("Location: {$redirect}");
    exit;
}

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Connect | Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page">
  <main>
    <section class="card" style="max-width: 420px; margin: 5rem auto;">
      <h2>Welcome Back</h2>
      <p class="text-muted">Login as Student, Teacher, or HOD.</p>

      <?php if ($error): ?>
        <div style="background:#ffe1e1;color:#c53030;padding:0.8rem 1rem;border-radius:12px;margin-bottom:1rem;">
          <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <form class="form-grid" method="post" action="handle-login.php">
        <label>
          Email or Roll Number
          <input type="text" name="identifier" placeholder="name@college.edu" required>
        </label>
        <label>
          Password
          <input type="password" name="password" placeholder="••••••••" required>
        </label>
        <label>
          Role
          <select name="role" required>
            <option value="">Select role</option>
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
            <option value="hod">HOD</option>
          </select>
        </label>
        <button type="submit">Login</button>
      </form>
    </section>
  </main>
  <footer>© 2025 Student Connect</footer>
</div>
</body>
</html>

