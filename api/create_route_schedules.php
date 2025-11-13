<?php
session_start();
// CORRECTED PATH: Step up one directory to find db_config.php
require_once '../db_config.php'; 

header('Content-Type: application/json');

// Function to safely output JSON response and set HTTP status code
function sendResponse($success, $message, $http_code, $route_id = null) {
    http_response_code($http_code);
    echo json_encode(['success' => $success, 'message' => $message, 'route_id' => $route_id]);
    exit;
}

// 1. Security Check: Ensure an admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    sendResponse(false, 'Authorization required.', 401);
}

$admin_id = $_SESSION['admin_id'] ?? 1; // Fallback ID if session didn't set it cleanly

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method.', 405);
}

// Read the JSON data sent from the frontend (required for dynamic schedule array)
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// 2. Initial Data Validation
if (!isset($data['routeName'], $data['routeDesc'], $data['schedules']) || !is_array($data['schedules']) || count($data['schedules']) === 0) {
    sendResponse(false, 'Missing required route or schedule data.', 400);
}

// Start transaction to ensure both Route and all Schedules are saved, or none are.
$conn->begin_transaction();
$new_route_id = null;

try {
    // A. Insert into ROUTES Table
    $route_name = $conn->real_escape_string($data['routeName']);
    $route_desc = $conn->real_escape_string($data['routeDesc']);

    $stmt_route = $conn->prepare("INSERT INTO ROUTES (route_name, route_desc, created_by) VALUES (?, ?, ?)");
    $stmt_route->bind_param("ssi", $route_name, $route_desc, $admin_id);

    if (!$stmt_route->execute()) {
        throw new Exception("Failed to create route: " . $stmt_route->error);
    }
    $new_route_id = $conn->insert_id;
    $stmt_route->close();

    // B. Insert into SCHEDULES Table (Multiple Inserts)
    // Values: route_id, depart_date, depart_time, max_seats, available_seats, price
    $stmt_schedule = $conn->prepare("INSERT INTO SCHEDULES (route_id, depart_date, depart_time, max_seats, available_seats, price) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($data['schedules'] as $schedule) {
        // Data sanitization and casting
        $depart_date = $conn->real_escape_string($schedule['depart_date'] ?? date('Y-m-d')); 
        $depart_time = $conn->real_escape_string($schedule['depart_time']);
        $max_seats = (int)$schedule['max_seats'];
        $price = (float)$schedule['price'];
        
        // available_seats is set equal to max_seats initially
        $available_seats = $max_seats; 

        // Bind parameters: i (int), s (string), s (string), i (int), i (int), d (double/decimal)
        $stmt_schedule->bind_param("issidi", 
            $new_route_id, 
            $depart_date, 
            $depart_time, 
            $max_seats, 
            $available_seats,
            $price
        ); 

        if (!$stmt_schedule->execute()) {
            throw new Exception("Failed to create schedule: " . $stmt_schedule->error);
        }
    }
    $stmt_schedule->close();

    // C. Commit Transaction
    $conn->commit();
    sendResponse(true, 'Route and schedules created successfully!', 201, $new_route_id);

} catch (Exception $e) {
    // Rollback transaction on any error
    $conn->rollback();
    sendResponse(false, $e->getMessage(), 500); 
}

$conn->close();
?>