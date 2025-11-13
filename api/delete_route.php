<?php
session_start();
// CORRECTED PATH: Step up one directory to find db_config.php
require_once '../db_config.php'; 

header('Content-Type: application/json');

// Function to safely output JSON response and set HTTP status code
function sendResponse($success, $message, $http_code) {
    http_response_code($http_code);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

// 1. Security Check: Ensure an admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    sendResponse(false, 'Authorization required.', 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    sendResponse(false, 'Invalid request method.', 405);
}

// 2. Extract route ID from the GET parameter
$route_id = $_GET['id'] ?? null;

if (!$route_id || !is_numeric($route_id)) {
    sendResponse(false, 'Missing or invalid Route ID.', 400);
}

// Start Transaction: Ensures all related data is deleted, or nothing is.
$conn->begin_transaction();

try {
    // A. Find all Schedules associated with the Route to correctly delete bookings
    $stmt_schedules = $conn->prepare("SELECT schedule_id FROM SCHEDULES WHERE route_id = ?");
    $stmt_schedules->bind_param("i", $route_id);
    $stmt_schedules->execute();
    $schedule_ids_result = $stmt_schedules->get_result();
    $stmt_schedules->close();
    
    $schedule_ids = [];
    while ($row = $schedule_ids_result->fetch_assoc()) {
        $schedule_ids[] = $row['schedule_id'];
    }

    // B. Delete related BOOKINGS (Must happen before deleting schedules)
    if (!empty($schedule_ids)) {
        // Create parameter binding string and array for the IN clause
        $in_clause = implode(',', array_fill(0, count($schedule_ids), '?'));
        $types = str_repeat('i', count($schedule_ids));

        $stmt_bookings = $conn->prepare("DELETE FROM BOOKINGS WHERE schedule_id IN ($in_clause)");
        $stmt_bookings->bind_param($types, ...$schedule_ids);

        if (!$stmt_bookings->execute()) {
            throw new Exception("Failed to delete associated bookings: " . $stmt_bookings->error);
        }
        $stmt_bookings->close();
    }

    // C. Delete SCHEDULES
    $stmt_schedule_del = $conn->prepare("DELETE FROM SCHEDULES WHERE route_id = ?");
    $stmt_schedule_del->bind_param("i", $route_id);
    if (!$stmt_schedule_del->execute()) {
        throw new Exception("Failed to delete schedules: " . $stmt_schedule_del->error);
    }
    $stmt_schedule_del->close();

    // D. Delete the ROUTE itself
    $stmt_route_del = $conn->prepare("DELETE FROM ROUTES WHERE route_id = ?");
    $stmt_route_del->bind_param("i", $route_id);
    if (!$stmt_route_del->execute()) {
        throw new Exception("Failed to delete route: " . $stmt_route_del->error);
    }
    // Check if any row was actually deleted (i.e., the route existed)
    if ($conn->affected_rows === 0) {
        throw new Exception("Route not found or already deleted.");
    }
    $stmt_route_del->close();

    // E. Commit Transaction
    $conn->commit();
    sendResponse(true, "Route $route_id and all related data deleted successfully.", 200);

} catch (Exception $e) {
    // Rollback transaction on any error
    $conn->rollback();
    sendResponse(false, "Deletion failed: " . $e->getMessage(), 500); 
}

$conn->close();
?>