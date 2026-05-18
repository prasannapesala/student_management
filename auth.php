<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login(array $roles = []) {
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
    if ($roles && !in_array($_SESSION['user']['role'], $roles, true)) {
        http_response_code(403);
        echo 'Access denied';
        exit;
    }
}

function current_user() {
    return $_SESSION['user'] ?? null;
}