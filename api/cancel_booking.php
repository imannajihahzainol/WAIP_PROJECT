<?php
session_start();
require_once '../db_config.php'; 
header('Content-Type: application/json');

//check if user is logged in
if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$customer_id = $_SESSION['customer_id'];
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); 
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$booking_id = $data['booking_id'] ?? null;

if (empty($booking_id)) {
    http_response_code(400); 
    echo json_encode(['success' => false, 'message' => 'Booking ID is required.']);
    exit;
}
$safe_booking_id = $conn->real_escape_string($booking_id);
$sql = "UPDATE BOOKINGS 
        SET booking_status = 'CANCELLED'
        WHERE booking_id = '{$safe_booking_id}' 
        AND customer_id = {$customer_id}
        AND booking_status = 'CONFIRMED'"; // only allow cancellation for confirmed bookings

if ($conn->query($sql) === TRUE) {
    if ($conn->affected_rows > 0) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully.']);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Booking could not be cancelled. It may be past the travel date or already cancelled.']);
    }
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$conn->close();
?>