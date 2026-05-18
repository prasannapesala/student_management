<?php
require_once 'db.php';
require_once 'auth.php';
require_once 'helpers.php';
require_login(['student']);
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Complaint / Compliment | Student Connect</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page">
  <header>
    <h1>Complaint & Compliment</h1>
    <nav>
      <a href="student-dashboard.php">Dashboard</a>
      <a href="leaveform.php">Leave</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main>
    <section class="card" style="max-width:720px;margin:auto;">
      <h2>Send a Message</h2>
      <form class="form-grid" method="post" action="handle-complaint.php" enctype="multipart/form-data">
        <label>
          Type
          <select name="type" required>
            <option value="">Select one</option>
            <option value="complaint">Complaint</option>
            <option value="compliment">Compliment</option>
          </select>
        </label>

        <label>
          Subject
          <input type="text" name="subject" placeholder="Short headline" required>
        </label>

        <label>
          Message
          <textarea name="message" placeholder="Describe the issue or praise..." required></textarea>
        </label>

        <label>
          Attachment (optional)
          <input type="file" name="attachment" accept=".pdf,.jpg,.png">
        </label>

        <button type="submit">Submit</button>
      </form>
    </section>
  </main>

  <footer>© 2025 Student Connect</footer>
</div>
</body>
</html>

