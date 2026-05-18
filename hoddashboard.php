<?php
require_once 'db.php';
require_once 'auth.php';
require_once 'helpers.php';
require_login(['hod']);
$user = current_user();

// Get all requests that need HOD approval (only forwarded requests - not teacher_approved until forwarded)
$pendingCount = $conn->query('SELECT COUNT(*) AS total FROM leave_requests WHERE status = "forwarded"')->fetch_assoc()['total'] ?? 0;
$approvedWeek = $conn->query('SELECT COUNT(*) AS total FROM leave_requests WHERE status = "approved" AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)')->fetch_assoc()['total'] ?? 0;
$rejectedCount = $conn->query('SELECT COUNT(*) AS total FROM leave_requests WHERE status = "rejected"')->fetch_assoc()['total'] ?? 0;

// Get all requests awaiting HOD approval with student details
$requests = $conn->query(
    'SELECT lr.*, u.name AS student_name, u.class, u.roll_no, u.attendance_percentage
     FROM leave_requests lr
     JOIN users u ON lr.student_id = u.id
     WHERE lr.status = "forwarded"
     ORDER BY lr.created_at DESC
     LIMIT 50'
)->fetch_all(MYSQLI_ASSOC);

// Calculate leave days for each request
foreach ($requests as &$request) {
    $fromDate = new DateTime($request['from_date']);
    $toDate = new DateTime($request['to_date']);
    $request['leave_days'] = $fromDate->diff($toDate)->days + 1;
}
unset($request);

$flash = isset($_GET['updated']) ? 'Request updated.' : (isset($_GET['error']) ? 'Unknown action.' : null);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>HOD Dashboard | Student Connect</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page">
  <header>
    <h1>HOD Dashboard</h1>
    <nav>
      <a href="hoddashboard.php">All Requests</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main>
    <?php if ($flash): ?>
      <div style="background:#fef9c3;color:#92400e;padding:0.8rem 1rem;border-radius:12px;margin-bottom:1rem;">
        <?php echo htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <section class="stat-grid">
      <div class="stat-card">
        <span>Awaiting Approval</span>
        <strong><?php echo (int) $pendingCount; ?></strong>
      </div>
      <div class="stat-card">
        <span>Approved (7 days)</span>
        <strong><?php echo (int) $approvedWeek; ?></strong>
      </div>
      <div class="stat-card">
        <span>Rejected</span>
        <strong><?php echo (int) $rejectedCount; ?></strong>
      </div>
    </section>

    <section class="card">
      <h2>Leave Requests Awaiting Final Approval</h2>
      <p style="color:#666;margin-bottom:1rem;font-size:0.9rem;">
        <strong>Note:</strong> Only requests forwarded by teachers are shown here. Teachers must forward approved requests for your final approval. Attendance percentages are pulled from the student profile.
      </p>
      <table class="table">
        <thead>
        <tr>
          <th>ID</th>
          <th>Student</th>
          <th>Reason</th>
          <th>Leave Period</th>
          <th>Days</th>
          <th>Attendance</th>
          <th>Attachment</th>
          <th>Status</th>
          <th>Teacher Remarks</th>
          <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!$requests): ?>
          <tr><td colspan="10">No requests awaiting action.</td></tr>
        <?php else: ?>
          <?php foreach ($requests as $request): ?>
            <tr>
              <td>#<?php echo (int) $request['id']; ?></td>
              <td>
                <strong><?php echo sanitize($request['student_name']); ?></strong><br>
                <small style="color:#666;">
                  <?php echo sanitize($request['class'] ?? ''); ?> | <?php echo sanitize($request['roll_no'] ?? ''); ?>
                </small>
              </td>
              <td><?php echo sanitize($request['reason'] ?? '—'); ?></td>
              <td>
                <?php echo sanitize($request['from_date']); ?><br>
                <small style="color:#666;">to <?php echo sanitize($request['to_date']); ?></small>
              </td>
              <td>
                <strong><?php echo $request['leave_days']; ?></strong> day<?php echo $request['leave_days'] != 1 ? 's' : ''; ?>
              </td>
              <td>
                <?php if (!is_null($request['attendance_percentage'])): ?>
                  <strong><?php echo sanitize($request['attendance_percentage']); ?>%</strong>
                <?php else: ?>
                  <em style="color:#666;">Not set</em>
                <?php endif; ?>
              </td>
              <td>
                <?php if (isset($request['attachment']) && !empty(trim($request['attachment']))): ?>
                  <a href="view-attachment.php?file=<?php echo urlencode($request['attachment']); ?>" target="_blank" style="color:#3b82f6;text-decoration:underline;">
                    📎 View File
                  </a>
                <?php else: ?>
                  <em style="color:#666;">No attachment</em>
                <?php endif; ?>
              </td>
              <td>
                <span class="status-chip status-open" style="background:#3b82f6;color:white;">
                  ✓ Forwarded by Teacher
                </span>
              </td>
              <td style="max-width:200px;word-wrap:break-word;">
                <?php if ($request['teacher_remarks']): ?>
                  <?php echo sanitize($request['teacher_remarks']); ?>
                <?php else: ?>
                  <em style="color:#666;">No remarks</em>
                <?php endif; ?>
              </td>
              <td>
                <form method="post" action="hod-actions.php" style="display:flex;flex-direction:column;gap:0.25rem;">
                  <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                  <textarea name="remarks" placeholder="HOD Remarks" style="min-height:60px;font-size:0.875rem;"></textarea>
                  <button type="submit" name="decision" value="approve" style="background:#10b981;">Approve</button>
                  <button type="submit" name="decision" value="reject" class="secondary">Reject</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </section>

    <section class="card">
      <h2>Quick Status Update</h2>
      <form class="form-grid" method="post" action="hod-actions.php">
        <label>
          Request ID
          <input type="number" name="request_id" placeholder="Enter request ID" required>
        </label>
        <label>
          HOD Remarks
          <textarea name="remarks" placeholder="Provide final approval remarks"></textarea>
        </label>
        <div style="display:flex; gap:1rem; flex-wrap:wrap;">
          <button type="submit" name="decision" value="approve">Approve</button>
          <button type="submit" name="decision" value="reject" class="secondary">Reject</button>
        </div>
      </form>
    </section>
  </main>

  <footer>© 2025 Student Connect</footer>
</div>
</body>
</html>
