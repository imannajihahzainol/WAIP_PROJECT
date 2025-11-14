<?php
require_once '../db_config.php'; 
header('Content-Type: application/json');
function sendResponse($success, $message, $http_code) {
    http_response_code($http_code);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method.', 405);
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$username = $data['username'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($email) || empty($password)) {
    sendResponse(false, 'All fields are required.', 400);
}

//hash the password for security
$hashed_password = password_hash($password, PASSWORD_DEFAULT); 
$stmt = $conn->prepare("INSERT INTO customer (customer_username, customer_email, customer_password) VALUES (?, ?, ?)");
if ($stmt === false) {
    sendResponse(false, 'Database preparation failed: ' . $conn->error, 500);
}

$stmt->bind_param("sss", $username, $email, $hashed_password);

if ($stmt->execute()) {
    sendResponse(true, 'Registration successful!', 201); 
} else {
    if ($conn->errno == 1062) { 
        sendResponse(false, 'Username or Email already exists.', 409); 
    } else {
        sendResponse(false, 'Registration failed: ' . $stmt->error, 500);
    }
}

$stmt->close();
$conn->close();