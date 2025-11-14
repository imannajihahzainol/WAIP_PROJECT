<?php
session_start();
require_once '../db_config.php'; // Assuming db_config.php contains your $conn variable

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Check for POST request and booking_id
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Read JSON data from request body
$data = json_decode(file_get_contents("php://input"), true);
$booking_id = $data['booking_id'] ?? null;

if (empty($booking_id)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Booking ID is required.']);
    exit;
}

// Sanitize input
$safe_booking_id = $conn->real_escape_string($booking_id);

// Prepare the update statement: 
// 1. Set status to CANCELLED.
// 2. Ensure the booking belongs to the current logged-in customer.
$sql = "UPDATE BOOKINGS 
        SET booking_status = 'CANCELLED'
        WHERE booking_id = '{$safe_booking_id}' 
        AND customer_id = {$customer_id}
        AND booking_status = 'CONFIRMED'"; // Only allow cancellation for confirmed bookings

if ($conn->query($sql) === TRUE) {
    if ($conn->affected_rows > 0) {
        // Success: The booking was cancelled
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully.']);
    } else {
        // Failure: Booking not found, or already cancelled/completed, or doesn't belong to user
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Booking could not be cancelled. It may be past the travel date or already cancelled.']);
    }
} else {
    // Database error
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$conn->close();
?>