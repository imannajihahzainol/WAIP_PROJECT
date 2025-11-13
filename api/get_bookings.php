<?php
session_start();
// CORRECTED PATH: Step up one directory to find db_config.php
require_once '../db_config.php'; 

header('Content-Type: application/json');

// Function to safely output JSON response and set HTTP status code
function sendResponse($success, $message, $http_code, $bookings = []) {
    http_response_code($http_code);
    echo json_encode(['success' => $success, 'message' => $message, 'bookings' => $bookings]);
    exit;
}

// 1. Security Check: Ensure a customer is logged in
if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
    sendResponse(false, 'Authorization required.', 401);
}

$customer_id = $_SESSION['customer_id'];
$status_filter = $_GET['status'] ?? null; // Optional filter (CONFIRMED, COMPLETED, CANCELLED, or null for all)

// 2. Base SQL Query: Join Bookings (B) with Schedules (S) and Routes (R)
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
        WHERE B.customer_id = ? ";

// 3. Apply status filter if provided
$params = [$customer_id];
$types = "i";

if (!empty($status_filter)) {
    // Basic sanitization on the status filter
    $status_filter = strtoupper($status_filter);
    $sql .= " AND B.booking_status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

// 4. Order results by date
$sql .= " ORDER BY S.depart_date DESC, S.depart_time DESC";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    sendResponse(false, 'Database preparation failed: ' . $conn->error, 500);
}

// 5. Dynamic binding of parameters
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

// 6. Final Success Response
sendResponse(true, 'Bookings fetched successfully.', 200, $bookings);
?>