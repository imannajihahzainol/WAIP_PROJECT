<?php
// Start a session before using session functions
session_start();

// CORRECTED PATH: Step up one directory to find db_config.php
require_once '../db_config.php'; 

header('Content-Type: application/json');

// Function to safely output JSON response and set HTTP status code
function sendResponse($success, $message, $http_code, $customer_id = null) {
    http_response_code($http_code);
    echo json_encode(['success' => $success, 'message' => $message, 'customer_id' => $customer_id]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method.', 405);
}

// 1. Retrieve data from the JSON payload (as frontend sends JSON)
$input = file_get_contents("php://input");
$data = json_decode($input, true);

$username_or_email = $data['username'] ?? ''; // Can be username or email
$password = $data['password'] ?? '';

if (empty($username_or_email) || empty($password)) {
    sendResponse(false, 'Username/Email and password are required.', 400);
}

// 2. Prepare and execute SQL query to find the customer (by username OR email)
// Note: We use the same input for both checks
$stmt = $conn->prepare("SELECT customer_id, customer_password FROM CUSTOMER WHERE customer_username = ? OR customer_email = ?");
    
if ($stmt === false) {
    sendResponse(false, 'Database error: Prepare failed.', 500);
}

$stmt->bind_param("ss", $username_or_email, $username_or_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $customer = $result->fetch_assoc();
    
    // 3. Verify the hashed password
    if (password_verify($password, $customer['customer_password'])) {
        
        // 4. Successful Login: Set session variables
        $_SESSION['customer_logged_in'] = true;
        $_SESSION['customer_id'] = $customer['customer_id'];

        // Success response
        sendResponse(true, 'Login successful!', 200, $customer['customer_id']);
    } else {
        // Failed Password Verification
        sendResponse(false, 'Invalid username or password.', 401); // Unauthorized
    }
} else {
    // User not found
    sendResponse(false, 'Invalid username or password.', 401); // Unauthorized
}

$stmt->close();
$conn->close();
?>