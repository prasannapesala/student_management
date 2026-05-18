<?php
require_once 'db.php';
require_once 'auth.php';
require_once 'helpers.php';
require_login(['student']);

$user = current_user();
$reason = trim($_POST['reason'] ?? '');
$from = $_POST['from_date'] ?? null;
$to = $_POST['to_date'] ?? null;
$attachment = null;
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    $attachment = handle_upload($_FILES['attachment']);
}

$stmt = $conn->prepare(
    'INSERT INTO leave_requests (student_id, reason, from_date, to_date, status, attachment)
     VALUES (?, ?, ?, ?, "pending", ?)'
);
$stmt->bind_param('issss', $user['id'], $reason, $from, $to, $attachment);
$stmt->execute();

header('Location: student-dashboard.php?submitted=leave');