<?php
require_once '../db_config.php'; 

header('Content-Type: application/json');

function sendResponse($success, $message, $http_code, $routes = [], $total_pages = 0, $current_page = 0) {
    http_response_code($http_code);
    echo json_encode([
        'success' => $success, 
        'message' => $message, 
        'routes' => $routes,
        'total_pages' => $total_pages, 
        'current_page' => $current_page 
    ]);
    exit;
}

// PAGINATION AND FILTER SETUP
$limit = 4; //4 routes per page 
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
$fromCity = isset($_GET['from']) ? trim($_GET['from']) : '';
$toCity = isset($_GET['to']) ? trim($_GET['to']) : '';
$where_clauses = []; 

if (!empty($fromCity) && $fromCity !== 'Select City') {
    $safe_from = $conn->real_escape_string($fromCity);
    $where_clauses[] = "R.route_name LIKE '{$safe_from} to %'";
}

if (!empty($toCity) && $toCity !== 'Select City') {
    $safe_to = $conn->real_escape_string($toCity);
    $where_clauses[] = "R.route_name LIKE '% to {$safe_to}%'";
}

$where_sql = count($where_clauses) > 0 ? " WHERE " . implode(" AND ", $where_clauses) : "";

//total results
$count_sql = "SELECT 
                COUNT(DISTINCT R.route_id) AS total_routes
              FROM ROUTES R
              LEFT JOIN SCHEDULES S ON R.route_id = S.route_id AND S.depart_date >= CURDATE()"
              . $where_sql;

$count_result = $conn->query($count_sql);

$total_routes = 0;
if ($count_result && $count_row = $count_result->fetch_assoc()) {
    $total_routes = (int)$count_row['total_routes'];
}

$total_pages = ceil($total_routes / $limit);
if ($page > $total_pages && $total_pages > 0) {
    $page = $total_pages;
    $offset = ($page - 1) * $limit;
} else if ($total_routes === 0) {
    $page = 1;
}

//paginated data with filters
$sql = "SELECT 
            R.route_id, 
            R.route_name, 
            R.route_desc, 
            MIN(S.price) AS min_price,
            COUNT(S.schedule_id) AS schedule_count
        FROM ROUTES R
        LEFT JOIN SCHEDULES S ON R.route_id = S.route_id AND S.depart_date >= CURDATE()"
        . $where_sql . " 
        GROUP BY R.route_id, R.route_name, R.route_desc
        ORDER BY R.route_id DESC
        LIMIT $limit OFFSET $offset";

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
            'min_price' => $row['min_price'] ? number_format((float)$row['min_price'], 2, '.', '') : '0.00',
            'schedule_count' => (int)$row['schedule_count']
        ];
    }
    
    sendResponse(true, 'Routes fetched successfully.', 200, $routes, $total_pages, $page);
} else {
    sendResponse(true, 'No routes found.', 200, [], $total_pages, $page);
}

$conn->close();