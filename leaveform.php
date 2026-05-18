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
  <title>Leave Application | Student Connect</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page">
  <header>
    <h1>Leave Request</h1>
    <nav>
      <a href="student-dashboard.php">Dashboard</a>
      <a href="complaintform.php">Complaints</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main>
    <section class="card" style="max-width:720px;margin:auto;">
      <h2>Submit Leave Request</h2>
      <form class="form-grid" method="post" action="handle-leave.php" enctype="multipart/form-data">
        <label>
          Reason
          <textarea name="reason" placeholder="Explain your reason..." required></textarea>
        </label>

        <div style="display:grid; gap:1rem; grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
          <label>
            From Date
            <input type="date" name="from_date" required>
          </label>
          <label>
            To Date
            <input type="date" name="to_date" required>
          </label>
        </div>

        <label>
          Attachment (Medical certificate if any)
          <input type="file" id="attachment-input" name="attachment" accept=".pdf,.jpg,.png">
          <span id="attachment-status" style="display:block;margin-top:0.5rem;color:#166534;font-size:0.9rem;"></span>
        </label>

        <button type="submit">Submit Request</button>
      </form>
    </section>
  </main>

  <footer>© 2025 Student Connect</footer>
</div>
<script>
  const attachmentInput = document.getElementById('attachment-input');
  const attachmentStatus = document.getElementById('attachment-status');

  attachmentInput.addEventListener('change', (event) => {
    const files = event.target.files;
    if (files.length > 1) {
      event.target.value = '';
      alert('Please select only one file.');
      return;
    }
    if (files.length === 1) {
      alert('Your file uploaded successfully.');
      attachmentInput.style.display = 'none';
      attachmentStatus.textContent = `Uploaded: ${files[0].name}`;
    }
  });
</script>
</body>
</html>

