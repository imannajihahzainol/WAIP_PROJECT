<?php
session_start();
require_once '../db_config.php'; 

header('Content-Type: application/json');

// Check if ADMIN is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Admin Authorization required.']);
    exit;
}

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

// Admin Action: Update status to CANCELLED for any booking ID
$sql = "UPDATE BOOKINGS 
        SET booking_status = 'CANCELLED'
        WHERE booking_id = '{$safe_booking_id}' 
        AND booking_status <> 'CANCELLED'"; // Prevent re-cancelling

if ($conn->query($sql) === TRUE) {
    if ($conn->affected_rows > 0) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully by Admin.']);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Booking not found or already cancelled.']);
    }
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$conn->close();
?>