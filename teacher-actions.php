<?php
require_once 'db.php';
require_once 'auth.php';
require_login(['teacher']);

$user = current_user();
$action = $_POST['action'] ?? '';
$rawIds = trim($_POST['request_id'] ?? '');
$requestIds = array_values(array_filter(array_map('intval', preg_split('/\s*,\s*/', $rawIds)), fn ($id) => $id > 0));
$remarks = trim($_POST['remarks'] ?? '');
$teacherClass = $user['class'] ?? '';

if (!$requestIds) {
    header('Location: teacherdashboard.php?error=invalid');
    exit;
}

$message = '';
switch ($action) {
    case 'approve':
        // Teacher approval still requires HOD final approval
        $stmt = $conn->prepare(
            'UPDATE leave_requests SET status = "teacher_approved", teacher_remarks = ? WHERE id = ?'
        );
        $message = 'approved';
        break;
    case 'reject':
        $stmt = $conn->prepare(
            'UPDATE leave_requests SET status = "rejected", teacher_remarks = ? WHERE id = ?'
        );
        $message = 'rejected';
        break;
    case 'forward':
        $stmt = $conn->prepare(
            'UPDATE leave_requests SET status = "forwarded", teacher_remarks = ? WHERE id = ?'
        );
        $message = 'forwarded';
        break;
    default:
        header('Location: teacherdashboard.php?error=unknown');
        exit;
}

if (!$stmt) {
    header('Location: teacherdashboard.php?error=database');
    exit;
}

// Verify each request belongs to the teacher's class and apply action
$verifyStmt = $conn->prepare(
    'SELECT lr.id 
     FROM leave_requests lr
     JOIN users u ON lr.student_id = u.id
     WHERE lr.id = ? AND u.class = ?'
);

foreach ($requestIds as $requestId) {
    $verifyStmt->bind_param('is', $requestId, $teacherClass);
    $verifyStmt->execute();
    $validRequest = $verifyStmt->get_result()->fetch_assoc();

    if (!$validRequest) {
        header('Location: teacherdashboard.php?error=unauthorized');
        exit;
    }

    $stmt->bind_param('si', $remarks, $requestId);
    $stmt->execute();
}

if ($message === 'forwarded') {
    header('Location: teacherdashboard.php?forwarded=1&id=' . urlencode(implode(',', $requestIds)));
} elseif ($message === 'approved') {
    header('Location: teacherdashboard.php?approved=1&id=' . urlencode(implode(',', $requestIds)));
} else {
    header('Location: teacherdashboard.php?updated=1&action=' . $message);
}
exit;