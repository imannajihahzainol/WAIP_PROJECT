<?php
session_start();
require_once '../db_config.php'; 
header('Content-Type: application/json');
function sendResponse($success, $message, $http_code, $bookings = []) {
    http_response_code($http_code);
    echo json_encode(['success' => $success, 'message' => $message, 'bookings' => $bookings]);
    exit;
}

//customer is logged in
if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
    sendResponse(false, 'Authorization required.', 401);
}

$customer_id = $_SESSION['customer_id'];
$status_filter = $_GET['status'] ?? null; 
$status_filter = strtoupper(trim($status_filter));

//sql  query
$sql = "SELECT 
            B.booking_id,
            B.seat_num,
            B.booking_status,
            B.total_price,
            S.depart_date,
            S.depart_time,
            R.route_name
        FROM BOOKINGS B
        JOIN SCHEDULES S ON B.schedule_id = S.schedule_id
        JOIN ROUTES R ON S.route_id = R.route_id
        WHERE B.customer_id = ?";

//status filter
$params = [$customer_id];
$types = "i";

if ($status_filter === 'CONFIRMED' || $status_filter === 'UPCOMING') {
    $sql .= " AND B.booking_status = 'CONFIRMED' AND S.depart_date >= CURDATE()";
}
else if ($status_filter === 'COMPLETED') {
    $sql .= " AND B.booking_status = 'CONFIRMED' AND S.depart_date < CURDATE()";
}
else if ($status_filter === 'CANCELLED') {
    $sql .= " AND B.booking_status = ?";
    $params[] = 'CANCELLED';
    $types .= "s";
}
//order results by date
$sql .= " ORDER BY S.depart_date DESC, S.depart_time DESC";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    sendResponse(false, 'Database preparation failed: ' . $conn->error, 500);
}
$stmt->bind_param($types, ...$params);

if (!$stmt->execute()) {
    $stmt->close();
    sendResponse(false, 'Failed to execute query: ' . $stmt->error, 500);
}

$result = $stmt->get_result();
$bookings = [];

while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

$stmt->close();
$conn->close();

sendResponse(true, 'Bookings fetched successfully.', 200, $bookings);
?>