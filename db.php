<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'studentdb';

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    $errorMessage = $e->getMessage();
    
    if (strpos($errorMessage, 'actively refused') !== false || strpos($errorMessage, 'Can\'t connect') !== false) {
        die('
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Database Connection Error</title>
            <style>
                body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
                .error-box { background: #fee2e2; border-left: 4px solid #dc2626; padding: 20px; border-radius: 4px; }
                h1 { color: #dc2626; margin-top: 0; }
                .steps { background: #fef3c7; padding: 15px; border-radius: 4px; margin-top: 20px; }
                .steps ol { margin: 10px 0; }
                .steps li { margin: 8px 0; }
            </style>
        </head>
        <body>
            <div class="error-box">
                <h1>⚠️ MySQL Server Not Running</h1>
                <p><strong>Error:</strong> The application cannot connect to the MySQL database server.</p>
                <p>This usually means that the MySQL/MariaDB service in XAMPP is not started.</p>
                
                <div class="steps">
                    <h3>To fix this, please follow these steps:</h3>
                    <ol>
                        <li>Open <strong>XAMPP Control Panel</strong></li>
                        <li>Click the <strong>"Start"</strong> button next to <strong>MySQL</strong></li>
                        <li>Wait for MySQL to start (the status should turn green)</li>
                        <li>Refresh this page</li>
                    </ol>
                    <p><strong>Note:</strong> If MySQL fails to start, check the error logs in XAMPP Control Panel.</p>
                </div>
            </div>
        </body>
        </html>
        ');
    } else {
        die('
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Database Error</title>
            <style>
                body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
                .error-box { background: #fee2e2; border-left: 4px solid #dc2626; padding: 20px; border-radius: 4px; }
                h1 { color: #dc2626; margin-top: 0; }
            </style>
        </head>
        <body>
            <div class="error-box">
                <h1>Database Connection Error</h1>
                <p><strong>Error:</strong> ' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</p>
                <p>Please check your database configuration in <code>db.php</code> or contact your system administrator.</p>
            </div>
        </body>
        </html>
        ');
    }
} catch (Exception $e) {
    die('
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Database Error</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
            .error-box { background: #fee2e2; border-left: 4px solid #dc2626; padding: 20px; border-radius: 4px; }
            h1 { color: #dc2626; margin-top: 0; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h1>Database Connection Error</h1>
            <p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>
        </div>
    </body>
    </html>
    ');
}