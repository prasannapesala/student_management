<?php
function sanitize($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function handle_upload(array $file): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = uniqid('doc_', true) . '.' . $ext;
    $uploadDir = __DIR__ . '/../uploads/';
    
    // Create uploads directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $dest = $uploadDir . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }
    return $name;
}