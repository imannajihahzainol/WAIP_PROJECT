<?php
session_start();
require_once '../db_config.php'; 
header('Content-Type: application/json');
function sendResponse($success, $message, $http_code, $booking_id = null) {
    http_response_code($http_code);
    echo json_encode(['success' => $success, 'message' => $message, 'booking_id' => $booking_id]);
    exit;
}

//customer is logged in
if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
    sendResponse(false, 'You must be logged in to book a ticket.', 401);
}

$customer_id = $_SESSION['customer_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method.', 405);
}

//retrieve data
$schedule_id = $_POST['schedule_id'] ?? null;
$seat_count = (int)($_POST['seatCount'] ?? 0);
$total_price = (float)($_POST['total_price'] ?? 0.00); 

if (!$schedule_id || $seat_count <= 0) {
    sendResponse(false, 'Missing schedule ID or number of seats.', 400);
}
$conn->begin_transaction();

try {
    //lock and check seats availability
    $stmt_check = $conn->prepare("SELECT available_seats, price FROM SCHEDULES WHERE schedule_id = ? FOR UPDATE");
    $stmt_check->bind_param("i", $schedule_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    $schedule = $result->fetch_assoc();
    $stmt_check->close();

    if (!$schedule) {
        throw new Exception("Schedule not found.");
    }
    
    $available_seats = $schedule['available_seats'];

    if ($available_seats < $seat_count) {
        throw new Exception("Booking failed. Only " . $available_seats . " seats remaining.");
    }
    //insert into bookings table
    $seat_num_str = $seat_count . " seat(s)"; 
    
    $stmt_booking = $conn->prepare("INSERT INTO BOOKINGS (customer_id, schedule_id, seat_num, total_price, booking_time) VALUES (?, ?, ?, ?, NOW())");
    $stmt_booking->bind_param("iisd", $customer_id, $schedule_id, $seat_num_str, $total_price); // i=int, s=string, d=double/decimal

    if (!$stmt_booking->execute()) {
        throw new Exception("Booking creation failed: " . $stmt_booking->error);
    }
    $new_booking_id = $conn->insert_id;
    $stmt_booking->close();
    
    //update schedules table
    $new_available_seats = $available_seats - $seat_count;
    
    $stmt_update = $conn->prepare("UPDATE SCHEDULES SET available_seats = ? WHERE schedule_id = ?");
    $stmt_update->bind_param("ii", $new_available_seats, $schedule_id);

    if (!$stmt_update->execute()) {
        throw new Exception("Failed to update seat count: " . $stmt_update->error);
    }
    $stmt_update->close();
    $conn->commit();
    sendResponse(true, 'Booking confirmed!', 200, $new_booking_id);

} catch (Exception $e) {
    $conn->rollback();
    sendResponse(false, "Booking failed: " . $e->getMessage(), 500);
}

$conn->close();
?>