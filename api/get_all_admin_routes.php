<?php
session_start();
require_once '../db_config.php'; 

header('Content-Type: application/json');

function sendResponse($success, $message, $http_code, $data = []) {
    http_response_code($http_code);
    echo json_encode(['success' => $success, 'message' => $message, 'routes' => $data]);
    exit;
}

// 1. Security Check: Only admins should access the full list
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    sendResponse(false, 'Unauthorized access.', 401);
}

// 2. SQL Query to fetch ALL routes and aggregate schedule count
$sql = "SELECT 
            R.route_id, 
            R.route_name, 
            R.route_desc,
            -- CRITICAL FIX: Count distinct schedule IDs for the total number of slots
            COUNT(DISTINCT S.schedule_id) AS schedule_count 
        FROM ROUTES R
        -- Use LEFT JOIN to include routes even if they have 0 schedules
        LEFT JOIN SCHEDULES S ON R.route_id = S.route_id
        -- CRITICAL FIX: Group by route_id (and non-aggregated route fields) to avoid redundancy
        GROUP BY R.route_id, R.route_name, R.route_desc 
        ORDER BY R.route_id DESC";

$result = $conn->query($sql);
$routes = [];

if ($result === false) {
    sendResponse(false, 'Database query failed: ' . $conn->error, 500);
}

while ($row = $result->fetch_assoc()) {
    $routes[] = $row;
}

$conn->close();
sendResponse(true, 'All routes fetched successfully for admin.', 200, $routes);
?>