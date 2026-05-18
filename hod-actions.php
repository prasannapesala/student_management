<?php
require_once 'db.php';
require_once 'auth.php';
require_login(['hod']);

$requestId = (int)($_POST['request_id'] ?? 0);
$remarks = trim($_POST['remarks'] ?? '');
$decision = $_POST['decision'] ?? '';

if (!in_array($decision, ['approve', 'reject'], true)) {
    header('Location: hoddashboard.php?error=unknown');
    exit;
}

$status = $decision === 'approve' ? 'approved' : 'rejected';

$stmt = $conn->prepare(
    'UPDATE leave_requests SET status = ?, hod_remarks = ? WHERE id = ?'
);
$stmt->bind_param('ssi', $status, $remarks, $requestId);
$stmt->execute();

header('Location: hoddashboard.php?updated=1');