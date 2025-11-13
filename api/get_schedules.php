<?php
// No session_start() is required as this is a public endpoint used by the booking page.
require_once '../db_config.php'; 

header('Content-Type: application/json');

// Function to safely output JSON response and set HTTP status code
function sendResponse($success, $message, $http_code, $schedules = [], $route_name = null) {
    http_response_code($http_code);
    echo json_encode([
        'success' => $success, 
        'message' => $message, 
        'route_name' => $route_name,
        'schedules' => $schedules
    ]);
    exit;
}

// 1. Retrieve and validate Route ID
$route_id = $_GET['route_id'] ?? null;

if (!$route_id || !is_numeric($route_id)) {
    sendResponse(false, 'Invalid or missing Route ID.', 400);
}

try {
    // 2. Get Route Name and Details (for the header display)
    $stmt_route = $conn->prepare("SELECT route_name FROM ROUTES WHERE route_id = ?");
    $stmt_route->bind_param("i", $route_id);
    $stmt_route->execute();
    $route_result = $stmt_route->get_result();
    $route = $route_result->fetch_assoc();
    $stmt_route->close();

    if (!$route) {
        sendResponse(false, 'Route not found.', 404);
    }
    $route_name = $route['route_name'];

    // 3. Get all active schedules for the route
    // Only show schedules whose departure date is today or in the future
    $sql = "SELECT 
                schedule_id, 
                depart_date, 
                depart_time, 
                price, 
                available_seats
            FROM SCHEDULES
            WHERE route_id = ? AND depart_date >= CURDATE()
            ORDER BY depart_date ASC, depart_time ASC";

    $stmt_schedule = $conn->prepare($sql);
    $stmt_schedule->bind_param("i", $route_id);
    
    if (!$stmt_schedule->execute()) {
        $stmt_schedule->close();
        throw new Exception("Failed to execute schedule query: " . $conn->error);
    }
    
    $schedules_result = $stmt_schedule->get_result();

    $schedules = [];
    while ($row = $schedules_result->fetch_assoc()) {
        // Format price to two decimal places
        $row['price'] = number_format((float)$row['price'], 2, '.', '');
        $schedules[] = $row;
    }
    $stmt_schedule->close();
    $conn->close();

    // 4. Final Success Response
    sendResponse(true, 'Schedules fetched successfully.', 200, $schedules, $route_name);

} catch (Exception $e) {
    sendResponse(false, "Server error: " . $e->getMessage(), 500);
}

$conn->close();
?>