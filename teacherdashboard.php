<?php
require_once 'db.php';
require_once 'auth.php';
require_once 'helpers.php';
require_login(['teacher']);
$user = current_user();

// Get teacher's class to filter requests
$teacherClass = $user['class'] ?? '';

// Count pending requests only for teacher's class
$pendingCountStmt = $conn->prepare(
    'SELECT COUNT(*) AS total 
     FROM leave_requests lr
     JOIN users u ON lr.student_id = u.id
     WHERE lr.status = "pending" AND u.class = ?'
);
$pendingCountStmt->bind_param('s', $teacherClass);
$pendingCountStmt->execute();
$pendingCount = $pendingCountStmt->get_result()->fetch_assoc()['total'] ?? 0;

// Count forwarded requests only for teacher's class
$forwardedCountStmt = $conn->prepare(
    'SELECT COUNT(*) AS total 
     FROM leave_requests lr
     JOIN users u ON lr.student_id = u.id
     WHERE lr.status = "forwarded" AND u.class = ?'
);
$forwardedCountStmt->bind_param('s', $teacherClass);
$forwardedCountStmt->execute();
$forwardedCount = $forwardedCountStmt->get_result()->fetch_assoc()['total'] ?? 0;

// Count complaints from teacher's class only
$complaintCountStmt = $conn->prepare(
    'SELECT COUNT(*) AS total 
     FROM complaints c
     JOIN users u ON c.student_id = u.id
     WHERE c.status = "open" AND u.class = ?'
);
$complaintCountStmt->bind_param('s', $teacherClass);
$complaintCountStmt->execute();
$complaintCount = $complaintCountStmt->get_result()->fetch_assoc()['total'] ?? 0;

// Get pending and teacher-approved requests that teacher can act on - only from their class
$pendingRequestsStmt = $conn->prepare(
    'SELECT lr.*, u.name AS student_name, u.class
     FROM leave_requests lr
     JOIN users u ON lr.student_id = u.id
     WHERE lr.status IN ("pending", "teacher_approved") AND u.class = ?
     ORDER BY 
       CASE WHEN lr.status = "pending" THEN 0 ELSE 1 END,
       lr.created_at DESC
     LIMIT 15'
);
$pendingRequestsStmt->bind_param('s', $teacherClass);
$pendingRequestsStmt->execute();
$pendingRequests = $pendingRequestsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get forwarded requests for reference - only from their class
$forwardedRequestsStmt = $conn->prepare(
    'SELECT lr.*, u.name AS student_name, u.class
     FROM leave_requests lr
     JOIN users u ON lr.student_id = u.id
     WHERE lr.status = "forwarded" AND u.class = ?
     ORDER BY lr.created_at DESC
     LIMIT 10'
);
$forwardedRequestsStmt->bind_param('s', $teacherClass);
$forwardedRequestsStmt->execute();
$forwardedRequests = $forwardedRequestsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get teacher approved requests awaiting HOD approval - only from their class
$teacherApprovedRequestsStmt = $conn->prepare(
    'SELECT lr.*, u.name AS student_name, u.class
     FROM leave_requests lr
     JOIN users u ON lr.student_id = u.id
     WHERE lr.status = "teacher_approved" AND u.class = ?
     ORDER BY lr.created_at DESC
     LIMIT 10'
);
$teacherApprovedRequestsStmt->bind_param('s', $teacherClass);
$teacherApprovedRequestsStmt->execute();
$teacherApprovedRequests = $teacherApprovedRequestsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$requests = $pendingRequests;

$flash = null;
if (isset($_GET['forwarded'])) {
    $flash = 'Request #' . ($_GET['id'] ?? '') . ' has been successfully forwarded to HOD for final approval.';
} elseif (isset($_GET['approved'])) {
    $flash = 'Request #' . ($_GET['id'] ?? '') . ' has been approved by you. It is now pending HOD final approval.';
} elseif (isset($_GET['updated'])) {
    $action = $_GET['action'] ?? 'updated';
    $flash = 'Request has been ' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8') . ' successfully.';
} elseif (isset($_GET['error'])) {
    $errorMsg = $_GET['error'];
    $flash = match($errorMsg) {
        'unauthorized' => 'Error: You are not authorized to perform this action. This request does not belong to your class.',
        'invalid' => 'Error: Invalid request ID provided.',
        default => 'Error: ' . htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8'),
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Teacher Dashboard | Student Connect</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page">
  <header>
    <h1>Teacher Dashboard<?php if ($teacherClass): ?> - <?php echo htmlspecialchars($teacherClass, ENT_QUOTES, 'UTF-8'); ?><?php endif; ?></h1>
    <nav>
      <a href="teacherdashboard.php">Requests</a>
      <a href="teacher-complaints.php">Complaints</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main>
    <?php if (empty($teacherClass)): ?>
      <div style="background:#fef3c7;color:#92400e;padding:0.8rem 1rem;border-radius:12px;margin-bottom:1rem;">
        <strong>⚠ Warning:</strong> No class assigned. Please contact administrator to assign a class. You will not see any leave requests until a class is assigned.
      </div>
    <?php endif; ?>
    <?php if ($flash): ?>
      <div style="background:<?php echo isset($_GET['forwarded']) ? '#dcfce7;color:#166534' : (isset($_GET['error']) ? '#fee2e2;color:#991b1b' : '#e0f2fe;color:#1d4ed8'); ?>;padding:0.8rem 1rem;border-radius:12px;margin-bottom:1rem;">
        <?php if (isset($_GET['forwarded'])): ?>
          <strong>✓ Success!</strong> <?php echo htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
        <?php else: ?>
          <?php echo htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <section class="stat-grid">
      <div class="stat-card">
        <span>Pending Approvals</span>
        <strong><?php echo (int) $pendingCount; ?></strong>
      </div>
      <div class="stat-card">
        <span>Forwarded to HOD</span>
        <strong><?php echo (int) $forwardedCount; ?></strong>
      </div>
      <div class="stat-card">
        <span>Complaints Open</span>
        <strong><?php echo (int) $complaintCount; ?></strong>
      </div>
    </section>

    <section class="card">
      <h2>Leave Requests</h2>
      <p style="color:#666;margin-bottom:1rem;font-size:0.9rem;">
        <strong>Note:</strong> After approving a request, you can forward it to HOD using the "Forward to HOD" button.
      </p>
      <table class="table">
        <thead>
        <tr>
          <th>Student</th>
          <th>Class</th>
          <th>Reason</th>
          <th>Dates</th>
          <th>Attachment</th>
          <th>Status</th>
          <th>Remarks</th>
          <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!$requests): ?>
          <tr><td colspan="8">No requests awaiting action.</td></tr>
        <?php else: ?>
          <?php foreach ($requests as $request): ?>
            <tr>
              <td><?php echo sanitize($request['student_name']); ?></td>
              <td><?php echo sanitize($request['class'] ?? ''); ?></td>
              <td><?php echo sanitize($request['reason']); ?></td>
              <td><?php echo sanitize($request['from_date']); ?> → <?php echo sanitize($request['to_date']); ?></td>
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
                <?php if ($request['status'] === 'forwarded'): ?>
                  <span class="status-chip status-open" style="background:#3b82f6;color:white;">
                    ✓ Forwarded to HOD
                  </span>
                <?php elseif ($request['status'] === 'teacher_approved'): ?>
                  <span class="status-chip status-open" style="background:#10b981;color:white;">
                    ✓ Approved (Pending HOD)
                  </span>
                <?php else: ?>
                  <span class="status-chip status-pending">
                    Pending
                  </span>
                <?php endif; ?>
              </td>
              <td><?php echo $request['teacher_remarks'] ? sanitize($request['teacher_remarks']) : '—'; ?></td>
              <td>
                <form method="post" action="teacher-actions.php" style="display:flex;flex-direction:column;gap:0.25rem;">
                  <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                  <?php if ($request['status'] === 'pending'): ?>
                    <button type="submit" name="action" value="approve">Approve</button>
                  <?php elseif ($request['status'] === 'teacher_approved'): ?>
                    <div style="background:#dcfce7;padding:0.5rem;border-radius:4px;margin-bottom:0.25rem;font-size:0.875rem;color:#166534;">
                      ✓ Approved. Click below to forward to HOD.
                    </div>
                  <?php elseif ($request['status'] === 'forwarded'): ?>
                    <em style="color:#666;font-size:0.875rem;">Already forwarded to HOD</em>
                  <?php endif; ?>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </section>

    <?php if ($forwardedRequests): ?>
    <section class="card">
      <h2>Forwarded to HOD (Awaiting Final Approval)</h2>
      <table class="table">
        <thead>
        <tr>
          <th>Student</th>
          <th>Class</th>
          <th>Reason</th>
          <th>Dates</th>
          <th>Attachment</th>
          <th>Status</th>
          <th>Your Remarks</th>
        </tr>
        </thead>
        <tbody>
          <?php foreach ($forwardedRequests as $request): ?>
            <tr>
              <td><?php echo sanitize($request['student_name']); ?></td>
              <td><?php echo sanitize($request['class'] ?? ''); ?></td>
              <td><?php echo sanitize($request['reason']); ?></td>
              <td><?php echo sanitize($request['from_date']); ?> → <?php echo sanitize($request['to_date']); ?></td>
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
                  ✓ Forwarded to HOD
                </span>
              </td>
              <td><?php echo $request['teacher_remarks'] ? sanitize($request['teacher_remarks']) : '<em style="color:#666;">No remarks</em>'; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
    <?php endif; ?>

    <section class="card">
      <h2>Add Remarks / Update Status</h2>
      <form class="form-grid" method="post" action="teacher-actions.php">
        <label>
          Request ID(s)
          <input type="text" name="request_id" placeholder="Enter one or more IDs, separated by commas" required>
        </label>
        <label>
          Remarks
          <textarea name="remarks" placeholder="Add remarks before approving or forwarding"></textarea>
        </label>
        <div style="display:flex; gap:1rem; flex-wrap:wrap;">
          
          <button type="submit" name="action" value="forward">Forward to HOD</button>
        </div>
      </form>
    </section>
  </main>

  <footer>© 2025 Student Connect</footer>
</div>
</body>
</html>

