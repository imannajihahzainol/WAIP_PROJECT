<?php
// We do NOT require session_start() here as this is a public API used by routes.html/php
// and a private API used by manage_routes.php (which already has session control).
require_once '../db_config.php'; 

header('Content-Type: application/json');

// Function to safely output JSON response and set HTTP status code
function sendResponse($success, $message, $http_code, $routes = []) {
    http_response_code($http_code);
    echo json_encode(['success' => $success, 'message' => $message, 'routes' => $routes]);
    exit;
}

// SQL Query: Joins ROUTES (R) with SCHEDULES (S) to find the minimum price and schedule count per route.
$sql = "SELECT 
            R.route_id, 
            R.route_name, 
            R.route_desc, 
            MIN(S.price) AS min_price,
            COUNT(S.schedule_id) AS schedule_count
        FROM ROUTES R
        -- Use LEFT JOIN to include routes even if they don't have a schedule yet
        LEFT JOIN SCHEDULES S ON R.route_id = S.route_id AND S.depart_date >= CURDATE()
        GROUP BY R.route_id, R.route_name, R.route_desc
        ORDER BY R.route_id DESC";

$result = $conn->query($sql);

$routes = [];

if ($result === false) {
    sendResponse(false, 'Database query failed: ' . $conn->error, 500);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $routes[] = [
            'route_id' => $row['route_id'],
            'route_name' => $row['route_name'],
            'route_desc' => $row['route_desc'],
            // Format price to two decimal places, or set to 0.00 if min_price is NULL (no schedules)
            'min_price' => $row['min_price'] ? number_format((float)$row['min_price'], 2, '.', '') : '0.00',
            'schedule_count' => (int)$row['schedule_count']
        ];
    }
    
    // Success response with data
    sendResponse(true, 'Routes fetched successfully.', 200, $routes);
} else {
    // Success response but no routes found
    sendResponse(true, 'No routes found.', 200, []);
}

$conn->close();
?>