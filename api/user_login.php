<?php
session_start();
require_once '../db_config.php'; 

header('Content-Type: application/json');
function sendResponse($success, $message, $http_code, $customer_id = null) {
    http_response_code($http_code);
    echo json_encode(['success' => $success, 'message' => $message, 'customer_id' => $customer_id]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method.', 405);
}

//retrieve data from the JSON payload
$input = file_get_contents("php://input");
$data = json_decode($input, true);
$username_or_email = $data['username'] ?? ''; 
$password = $data['password'] ?? '';

if (empty($username_or_email) || empty($password)) {
    sendResponse(false, 'Username/Email and password are required.', 400);
}

// SQL query to find the customer (by username OR email)
$stmt = $conn->prepare("SELECT customer_id, customer_password FROM CUSTOMER WHERE customer_username = ? OR customer_email = ?");
if ($stmt === false) {
    sendResponse(false, 'Database error: Prepare failed.', 500);
}

$stmt->bind_param("ss", $username_or_email, $username_or_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $customer = $result->fetch_assoc();
    
    //verify the hashed password
    if (password_verify($password, $customer['customer_password'])) {
        
        //successful login
        $_SESSION['customer_logged_in'] = true;
        $_SESSION['customer_id'] = $customer['customer_id'];
        sendResponse(true, 'Login successful!', 200, $customer['customer_id']);
    } else {
        sendResponse(false, 'Invalid username or password.', 401); // Unauthorized
    }
} else {
    //user not found
    sendResponse(false, 'Invalid username or password.', 401); // Unauthorized
}

$stmt->close();
$conn->close();
?>