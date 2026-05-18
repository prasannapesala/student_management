<?php
require_once 'db.php';
require_once 'auth.php';
require_once 'helpers.php';
require_login(['student']);
$user = current_user();

$pendingStmt = $conn->prepare('SELECT COUNT(*) AS total FROM leave_requests WHERE student_id = ? AND status IN ("pending","forwarded")');
$pendingStmt->bind_param('i', $user['id']);
$pendingStmt->execute();
$pendingLeaves = $pendingStmt->get_result()->fetch_assoc()['total'] ?? 0;

$approvedStmt = $conn->prepare('SELECT COUNT(*) AS total FROM leave_requests WHERE student_id = ? AND status = "approved"');
$approvedStmt->bind_param('i', $user['id']);
$approvedStmt->execute();
$approvedLeaves = $approvedStmt->get_result()->fetch_assoc()['total'] ?? 0;

$complaintStmt = $conn->prepare('SELECT COUNT(*) AS total FROM complaints WHERE student_id = ? AND status IN ("open","noted")');
$complaintStmt->bind_param('i', $user['id']);
$complaintStmt->execute();
$openComplaints = $complaintStmt->get_result()->fetch_assoc()['total'] ?? 0;

$leaves = $conn->prepare('SELECT * FROM leave_requests WHERE student_id = ? ORDER BY created_at DESC LIMIT 5');
$leaves->bind_param('i', $user['id']);
$leaves->execute();
$leaveRows = $leaves->get_result()->fetch_all(MYSQLI_ASSOC);

$complaints = $conn->prepare('SELECT * FROM complaints WHERE student_id = ? ORDER BY created_at DESC LIMIT 5');
$complaints->bind_param('i', $user['id']);
$complaints->execute();
$complaintRows = $complaints->get_result()->fetch_all(MYSQLI_ASSOC);

$successMessage = null;
if (isset($_GET['submitted'])) {
    $successMessage = $_GET['submitted'] === 'leave'
        ? 'Leave request submitted.'
        : 'Complaint submitted.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Dashboard | Student Connect</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page">
  <header>
    <h1>Student Dashboard</h1>
    <nav>
      <a href="leaveform.php">Apply Leave</a>
      <a href="complaintform.php">Send Complaint</a>
      <a href="student-dashboard.php">History</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main>
    <?php if ($successMessage): ?>
      <div style="background:#dcfce7;color:#166534;padding:0.8rem 1rem;border-radius:12px;margin-bottom:1rem;">
        <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <section class="stat-grid">
      <div class="stat-card">
        <span>Pending Leaves</span>
        <strong><?php echo (int) $pendingLeaves; ?></strong>
      </div>
      <div class="stat-card">
        <span>Approved Leaves</span>
        <strong><?php echo (int) $approvedLeaves; ?></strong>
      </div>
      <div class="stat-card">
        <span>Complaints Open</span>
        <strong><?php echo (int) $openComplaints; ?></strong>
      </div>
    </section>

    <section class="card">
      <h2>Quick Actions</h2>
      <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:1rem;">
        <a class="input-btn" href="leaveform.php">Apply Leave</a>
        <a class="input-btn" href="complaintform.php">Send Complaint</a>
        <a class="input-btn secondary" href="student-dashboard.php">View History</a>
      </div>
    </section>

    <section class="card">
      <h2>Recent Leave Requests</h2>
      <table class="table">
        <thead>
        <tr>
          <th>Reason</th>
          <th>Dates</th>
          <th>Teacher Remarks</th>
          <th>HOD Remarks</th>
          <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!$leaveRows): ?>
          <tr><td colspan="5">No leave requests yet.</td></tr>
        <?php else: ?>
          <?php foreach ($leaveRows as $row): ?>
            <tr>
              <td><?php echo sanitize($row['reason']); ?></td>
              <td><?php echo sanitize($row['from_date']); ?> → <?php echo sanitize($row['to_date']); ?></td>
              <td><?php echo $row['teacher_remarks'] ? sanitize($row['teacher_remarks']) : '—'; ?></td>
              <td><?php echo $row['hod_remarks'] ? sanitize($row['hod_remarks']) : '—'; ?></td>
              <td>
                <?php
                $statusClass = match ($row['status']) {
                    'approved' => 'status-approved',
                    'rejected' => 'status-rejected',
                    'forwarded' => 'status-open',
                    default => 'status-pending',
                };
                ?>
                <span class="status-chip <?php echo $statusClass; ?>">
                  <?php echo ucfirst($row['status']); ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </section>

    <section class="card">
      <h2>Recent Complaints / Compliments</h2>
      <table class="table">
        <thead>
        <tr>
          <th>Subject</th>
          <th>Message</th>
          <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!$complaintRows): ?>
          <tr><td colspan="3">No complaints yet.</td></tr>
        <?php else: ?>
          <?php foreach ($complaintRows as $row): ?>
            <tr>
              <td><?php echo sanitize($row['subject']); ?></td>
              <td><?php echo sanitize($row['message']); ?></td>
              <td>
                <span class="status-chip <?php echo $row['status'] === 'open' ? 'status-open' : 'status-approved'; ?>">
                  <?php echo ucfirst($row['status']); ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </section>
  </main>

  <footer>© 2025 Student Connect</footer>
</div>
</body>
</html>