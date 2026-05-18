<?php
require_once 'db.php';
require_once 'auth.php';
require_once 'helpers.php';

// Allow access for teachers and HOD only
require_login(['teacher', 'hod']);

$filename = $_GET['file'] ?? '';

if (empty($filename)) {
    http_response_code(400);
    die('File name required');
}

// Security: Only allow alphanumeric, dots, underscores, and hyphens
if (!preg_match('/^[a-zA-Z0-9._-]+$/', $filename)) {
    http_response_code(400);
    die('Invalid file name');
}

// Verify the file belongs to a leave request that the user can access
$user = current_user();
$userRole = $user['role'];

$filePath = __DIR__ . '/../uploads/' . $filename;

if (!file_exists($filePath)) {
    http_response_code(404);
    die('File not found');
}

// For teachers, verify the file belongs to a student in their class
if ($userRole === 'teacher') {
    $teacherClass = $user['class'] ?? '';
    if (empty($teacherClass)) {
        http_response_code(403);
        die('Access denied: No class assigned');
    }
    
    $stmt = $conn->prepare(
        'SELECT lr.id 
         FROM leave_requests lr
         JOIN users u ON lr.student_id = u.id
         WHERE lr.attachment = ? AND u.class = ?'
    );
    $stmt->bind_param('ss', $filename, $teacherClass);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(403);
        die('Access denied: File does not belong to your class');
    }
}

// For HOD, verify the file belongs to a leave request
if ($userRole === 'hod') {
    $stmt = $conn->prepare('SELECT id FROM leave_requests WHERE attachment = ?');
    $stmt->bind_param('s', $filename);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(403);
        die('Access denied: File not found in leave requests');
    }
}

// Determine MIME type based on file extension
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
$mimeTypes = [
    'pdf' => 'application/pdf',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
];

$mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';

// Set headers and output file
header('Content-Type: ' . $mimeType);
header('Content-Disposition: inline; filename="' . basename($filename) . '"');
header('Content-Length: ' . filesize($filePath));

readfile($filePath);
exit;

