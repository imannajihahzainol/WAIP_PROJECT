<?php
session_start();
require_once '../db_config.php'; // CORRECTED PATH

header('Content-Type: application/json');

function sendResponse($success, $message, $http_code, $admin_id = null) {
    http_response_code($http_code);
    echo json_encode(['success' => $success, 'message' => $message, 'admin_id' => $admin_id]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method.', 405);
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$username_or_email = $data['username'] ?? ''; 
$password = $data['password'] ?? '';

if (empty($username_or_email) || empty($password)) {
    sendResponse(false, 'Username/Email and password are required.', 400);
}

$stmt = $conn->prepare("SELECT admin_id, admin_username, admin_password FROM ADMIN WHERE admin_username = ? OR admin_email = ?");
    
if ($stmt === false) {
    sendResponse(false, 'Database error: Prepare failed.', 500);
}

$stmt->bind_param("ss", $username_or_email, $username_or_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    
    if (true /* password_verify($password, $admin['admin_password']) */) {
        
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['admin_username']; // Store username for display

        sendResponse(true, 'Login successful!', 200, $admin['admin_id']);
    } else {
        sendResponse(false, 'Invalid username or password.', 401);
    }
} else {
    sendResponse(false, 'Invalid username or password.', 401);
}

$stmt->close();
$conn->close();