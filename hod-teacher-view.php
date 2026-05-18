<?php
require_once 'db.php';
require_once 'auth.php';
require_once 'helpers.php';
require_login(['hod']);
$user = current_user();

// Get all pending requests from all classes (what teachers see)
$pendingCount = $conn->query('SELECT COUNT(*) AS total FROM leave_requests WHERE status = "pending"')->fetch_assoc()['total'] ?? 0;
$forwardedCount = $conn->query('SELECT COUNT(*) AS total FROM leave_requests WHERE status = "forwarded"')->fetch_assoc()['total'] ?? 0;
$complaintCount = $conn->query('SELECT COUNT(*) AS total FROM complaints WHERE status = "open"')->fetch_assoc()['total'] ?? 0;

// Get all pending requests grouped by class
$pendingRequests = $conn->query(
    'SELECT lr.*, u.name AS student_name, u.class, u.roll_no
     FROM leave_requests lr
     JOIN users u ON lr.student_id = u.id
     WHERE lr.status = "pending"
     ORDER BY u.class, lr.created_at DESC
     LIMIT 50'
)->fetch_all(MYSQLI_ASSOC);

// Get forwarded requests grouped by class
$forwardedRequests = $conn->query(
    'SELECT lr.*, u.name AS student_name, u.class, u.roll_no
     FROM leave_requests lr
     JOIN users u ON lr.student_id = u.id
     WHERE lr.status = "forwarded"
     ORDER BY u.class, lr.created_at DESC
     LIMIT 30'
)->fetch_all(MYSQLI_ASSOC);

$flash = null;
if (isset($_GET['info'])) {
    $flash = 'This view shows all pending and forwarded leave requests from all classes, similar to what individual teachers see for their own classes.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Teacher View - All Classes | Student Connect</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page">
  <header>
    <h1>Teacher View - All Classes</h1>
    <nav>
      <a href="hoddashboard.php">HOD Dashboard</a>
      <a href="hod-teacher-view.php">Teacher View</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main>
    <?php if ($flash): ?>
      <div style="background:#e0f2fe;color:#1d4ed8;padding:0.8rem 1rem;border-radius:12px;margin-bottom:1rem;">
        ℹ️ <?php echo htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <section class="stat-grid">
      <div class="stat-card">
        <span>Pending Requests (All Classes)</span>
        <strong><?php echo (int) $pendingCount; ?></strong>
      </div>
      <div class="stat-card">
        <span>Forwarded to HOD</span>
        <strong><?php echo (int) $forwardedCount; ?></strong>
      </div>
      <div class="stat-card">
        <span>Open Complaints</span>
        <strong><?php echo (int) $complaintCount; ?></strong>
      </div>
    </section>

    <section class="card">
      <h2>Pending Leave Requests (All Classes)</h2>
      <p style="color:#666;margin-bottom:1rem;">These are requests that teachers are currently reviewing across all classes.</p>
      <table class="table">
        <thead>
        <tr>
          <th>Student</th>
          <th>Class</th>
          <th>Reason</th>
          <th>Dates</th>
          <th>Status</th>
          <th>Teacher Remarks</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!$pendingRequests): ?>
          <tr><td colspan="6">No pending requests across all classes.</td></tr>
        <?php else: ?>
          <?php
          $currentClass = '';
          foreach ($pendingRequests as $request):
            // Show class header when class changes
            if ($currentClass !== $request['class']):
              $currentClass = $request['class'];
          ?>
            <tr style="background:#f3f4f6;">
              <td colspan="6" style="font-weight:bold;padding:0.75rem;">
                📚 Class: <?php echo htmlspecialchars($currentClass, ENT_QUOTES, 'UTF-8'); ?>
              </td>
            </tr>
          <?php endif; ?>
            <tr>
              <td>
                <?php echo sanitize($request['student_name']); ?><br>
                <small style="color:#666;"><?php echo sanitize($request['roll_no'] ?? ''); ?></small>
              </td>
              <td><?php echo sanitize($request['class'] ?? ''); ?></td>
              <td><?php echo sanitize($request['reason'] ?? '—'); ?></td>
              <td><?php echo sanitize($request['from_date']); ?> → <?php echo sanitize($request['to_date']); ?></td>
              <td>
                <span class="status-chip status-pending">
                  Pending
                </span>
              </td>
              <td><?php echo $request['teacher_remarks'] ? sanitize($request['teacher_remarks']) : '<em style="color:#666;">No remarks yet</em>'; ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </section>

    <?php if ($forwardedRequests): ?>
    <section class="card">
      <h2>Forwarded to HOD (All Classes)</h2>
      <p style="color:#666;margin-bottom:1rem;">These requests have been forwarded by teachers and are awaiting your final approval.</p>
      <table class="table">
        <thead>
        <tr>
          <th>Student</th>
          <th>Class</th>
          <th>Reason</th>
          <th>Dates</th>
          <th>Status</th>
          <th>Teacher Remarks</th>
        </tr>
        </thead>
        <tbody>
          <?php
          $currentClass = '';
          foreach ($forwardedRequests as $request):
            // Show class header when class changes
            if ($currentClass !== $request['class']):
              $currentClass = $request['class'];
          ?>
            <tr style="background:#f3f4f6;">
              <td colspan="6" style="font-weight:bold;padding:0.75rem;">
                📚 Class: <?php echo htmlspecialchars($currentClass, ENT_QUOTES, 'UTF-8'); ?>
              </td>
            </tr>
          <?php endif; ?>
            <tr>
              <td>
                <?php echo sanitize($request['student_name']); ?><br>
                <small style="color:#666;"><?php echo sanitize($request['roll_no'] ?? ''); ?></small>
              </td>
              <td><?php echo sanitize($request['class'] ?? ''); ?></td>
              <td><?php echo sanitize($request['reason'] ?? '—'); ?></td>
              <td><?php echo sanitize($request['from_date']); ?> → <?php echo sanitize($request['to_date']); ?></td>
              <td>
                <span class="status-chip status-open" style="background:#3b82f6;color:white;">
                  ✓ Forwarded
                </span>
              </td>
              <td><?php echo $request['teacher_remarks'] ? sanitize($request['teacher_remarks']) : '<em style="color:#666;">No remarks</em>'; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
    <?php endif; ?>
  </main>

  <footer>© 2025 Student Connect</footer>
</div>
</body>
</html>


