<?php
require_once 'db.php';
require_once 'auth.php';
require_once 'helpers.php';
require_login(['student']);

$user = current_user();
$type = $_POST['type'] ?? 'complaint';
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');
$attachment = handle_upload($_FILES['attachment']);

$status = $type === 'compliment' ? 'noted' : 'open';

$stmt = $conn->prepare(
    'INSERT INTO complaints (student_id, subject, message, attachment, status)
     VALUES (?, ?, ?, ?, ?)'
);
$stmt->bind_param('issss', $user['id'], $subject, $message, $attachment, $status);
$stmt->execute();

header('Location: student-dashboard.php?submitted=complaint');