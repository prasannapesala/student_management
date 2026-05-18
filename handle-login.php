<?php
require_once 'db.php';
require_once 'auth.php';

$identifier = trim($_POST['identifier'] ?? '');
$password = $_POST['password'] ?? '';
$role = trim($_POST['role'] ?? '');

// Validate input
if (empty($identifier) || empty($password) || empty($role)) {
    $_SESSION['error'] = 'Please fill in all fields';
    header('Location: login.php');
    exit;
}

// Validate role
if (!in_array($role, ['student', 'teacher', 'hod'], true)) {
    $_SESSION['error'] = 'Invalid role selected';
    header('Location: login.php');
    exit;
}

$stmt = $conn->prepare(
    'SELECT id, name, email, roll_no, password, role, class, dept
     FROM users
     WHERE (email = ? OR roll_no = ?) AND role = ? LIMIT 1'
);

if (!$stmt) {
    $_SESSION['error'] = 'Database error. Please try again later.';
    header('Location: login.php');
    exit;
}

$stmt->bind_param('sss', $identifier, $identifier, $role);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Verify password - support both MD5 (legacy) and bcrypt (modern)
$passwordValid = false;
if ($user) {
    $storedPassword = $user['password'];
    // Check if it's a bcrypt hash (starts with $2y$ or $2a$)
    if (preg_match('/^\$2[ay]\$\d{2}\$/', $storedPassword)) {
        // Modern bcrypt hash
        $passwordValid = password_verify($password, $storedPassword);
    } else {
        // Legacy MD5 hash (32 character hex string)
        $passwordValid = (md5($password) === $storedPassword);
    }
}

if ($user && $passwordValid) {
    // Remove password from session for security
    unset($user['password']);
    $_SESSION['user'] = $user;
    switch ($user['role']) {
        case 'student':
            header('Location: student-dashboard.php');
            break;
        case 'teacher':
            header('Location: teacherdashboard.php');
            break;
        case 'hod':
            header('Location: hoddashboard.php');
            break;
    }
    exit;
}

$_SESSION['error'] = 'Invalid credentials';
header('Location: login.php');
exit;