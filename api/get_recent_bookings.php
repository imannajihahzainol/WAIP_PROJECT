<?php
session_start();
require_once '../db_config.php'; 

header('Content-Type: application/json');

function sendResponse($success, $message, $http_code, $data = []) {
    http_response_code($http_code);
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

// 1. Security Check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    sendResponse(false, 'Unauthorized access.', 401);
}

// 2. SQL Query to fetch recent bookings
// REVISION: JOINed with 'customer' (singular) and selected 'customer_username'
$sql = "SELECT 
            B.booking_id,
            B.booking_status,
            S.depart_date,
            R.route_name,
            C.customer_username AS customer_name 
        FROM BOOKINGS B
        JOIN SCHEDULES S ON B.schedule_id = S.schedule_id
        JOIN ROUTES R ON S.route_id = R.route_id
        JOIN customer C ON B.customer_id = C.customer_id
        ORDER BY B.booking_id DESC
        LIMIT 10";

$result = $conn->query($sql);
$recent_bookings = [];

if ($result === false) {
    sendResponse(false, 'Database query failed: ' . $conn->error, 500);
}

while ($row = $result->fetch_assoc()) {
    $row['display_id'] = 'BK' . $row['booking_id'];
    $recent_bookings[] = $row;
}

$conn->close();
sendResponse(true, 'Recent bookings fetched successfully.', 200, $recent_bookings);
?>