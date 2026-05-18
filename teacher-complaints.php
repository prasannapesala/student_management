<?php
require_once 'db.php';
require_once 'auth.php';
require_once 'helpers.php';
require_login(['teacher']);
$user = current_user();

// Get teacher's class to filter complaints
$teacherClass = $user['class'] ?? '';

// Count complaints from teacher's class
$openComplaintsStmt = $conn->prepare(
    'SELECT COUNT(*) AS total 
     FROM complaints c
     JOIN users u ON c.student_id = u.id
     WHERE c.status = "open" AND u.class = ?'
);
$openComplaintsStmt->bind_param('s', $teacherClass);
$openComplaintsStmt->execute();
$openComplaintsCount = $openComplaintsStmt->get_result()->fetch_assoc()['total'] ?? 0;

// Get all complaints from teacher's class
$complaintsStmt = $conn->prepare(
    'SELECT c.*, u.name AS student_name, u.class, u.roll_no, u.email
     FROM complaints c
     JOIN users u ON c.student_id = u.id
     WHERE u.class = ?
     ORDER BY c.created_at DESC
     LIMIT 50'
);
$complaintsStmt->bind_param('s', $teacherClass);
$complaintsStmt->execute();
$complaints = $complaintsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$flash = null;
if (isset($_GET['updated'])) {
    $flash = 'Complaint status updated successfully.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Complaints & Compliments | Student Connect</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page">
  <header>
    <h1>Complaints & Compliments<?php if ($teacherClass): ?> - <?php echo htmlspecialchars($teacherClass, ENT_QUOTES, 'UTF-8'); ?><?php endif; ?></h1>
    <nav>
      <a href="teacherdashboard.php">Requests</a>
      <a href="teacher-complaints.php">Complaints</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main>
    <?php if (empty($teacherClass)): ?>
      <div style="background:#fef3c7;color:#92400e;padding:0.8rem 1rem;border-radius:12px;margin-bottom:1rem;">
        <strong>⚠ Warning:</strong> No class assigned. Please contact administrator to assign a class.
      </div>
    <?php endif; ?>

    <?php if ($flash): ?>
      <div style="background:#dcfce7;color:#166534;padding:0.8rem 1rem;border-radius:12px;margin-bottom:1rem;">
        <strong>✓ Success!</strong> <?php echo htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <section class="stat-grid">
      <div class="stat-card">
        <span>Open Complaints</span>
        <strong><?php echo (int) $openComplaintsCount; ?></strong>
      </div>
      <div class="stat-card">
        <span>Total Complaints</span>
        <strong><?php echo count($complaints); ?></strong>
      </div>
    </section>

    <section class="card">
      <h2>Complaints & Compliments from Your Students</h2>
      <table class="table">
        <thead>
        <tr>
          <th>Student</th>
          <th>Roll No</th>
          <th>Type</th>
          <th>Subject</th>
          <th>Message</th>
          <th>Attachment</th>
          <th>Status</th>
          <th>Date</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!$complaints): ?>
          <tr><td colspan="8">No complaints or compliments from your students yet.</td></tr>
        <?php else: ?>
          <?php foreach ($complaints as $complaint): ?>
            <tr>
              <td><?php echo sanitize($complaint['student_name']); ?></td>
              <td><?php echo sanitize($complaint['roll_no'] ?? ''); ?></td>
              <td>
                <?php
                // Determine type based on status: 'noted' = Compliment, others = Complaint
                $type = $complaint['status'] === 'noted' ? 'Compliment' : 'Complaint';
                $typeClass = $type === 'Compliment' ? 'status-approved' : 'status-open';
                ?>
                <span class="status-chip <?php echo $typeClass; ?>" style="font-size:0.875rem;">
                  <?php echo $type; ?>
                </span>
              </td>
              <td><?php echo sanitize($complaint['subject']); ?></td>
              <td style="max-width:300px;word-wrap:break-word;"><?php echo sanitize($complaint['message']); ?></td>
              <td>
                <?php if ($complaint['attachment']): ?>
                  <a href="<?php echo htmlspecialchars($complaint['attachment'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" style="color:#3b82f6;">View</a>
                <?php else: ?>
                  <em style="color:#666;">None</em>
                <?php endif; ?>
              </td>
              <td>
                <?php
                $statusClass = match($complaint['status']) {
                    'open' => 'status-open',
                    'noted' => 'status-approved',
                    default => 'status-pending',
                };
                ?>
                <span class="status-chip <?php echo $statusClass; ?>">
                  <?php echo ucfirst($complaint['status']); ?>
                </span>
              </td>
              <td><?php echo date('Y-m-d H:i', strtotime($complaint['created_at'])); ?></td>
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

