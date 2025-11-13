<?php
session_start();
// CORRECTED PATH: Step up one directory to find db_config.php
require_once '../db_config.php'; 

header('Content-Type: application/json');

// Function to safely output JSON response and set HTTP status code
function sendResponse($success, $message, $http_code, $data = []) {
    http_response_code($http_code);
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

// 1. Security Check: Ensure an admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    sendResponse(false, 'Authorization required.', 401);
}

$today = date('Y-m-d');
// Define reporting periods
$last_month_start = date('Y-m-d', strtotime('first day of last month'));
$last_week_start = date('Y-m-d', strtotime('-7 days'));

$report_data = [];

try {
    // --- Query 1: Total Bookings (Since last month) ---
    // Counts all confirmed bookings since the start of last month
    $query_total_bookings = "SELECT COUNT(booking_id) AS total FROM BOOKINGS WHERE booking_status = 'CONFIRMED' AND booking_time >= ?";
    $stmt = $conn->prepare($query_total_bookings);
    $stmt->bind_param("s", $last_month_start);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $report_data['totalBookingsCount'] = (int)$result['total'];
    $stmt->close();

    // --- Query 2: Active Routes (With schedules today or later) ---
    $query_active_routes = "SELECT COUNT(DISTINCT R.route_id) AS active FROM ROUTES R JOIN SCHEDULES S ON R.route_id = S.route_id WHERE S.depart_date >= ?";
    $stmt = $conn->prepare($query_active_routes);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $report_data['activeRoutesCount'] = (int)$result['active'];
    $stmt->close();

    // --- Query 3: New Users (Registered this week) ---
    $query_new_users = "SELECT COUNT(customer_id) AS new_users FROM CUSTOMER WHERE created_at >= ?";
    $stmt = $conn->prepare($query_new_users);
    $stmt->bind_param("s", $last_week_start);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $report_data['newUsersCount'] = (int)$result['new_users'];
    $stmt->close();
    
    // --- Query 4: Future Tickets (Confirmed bookings with a future departure date) ---
    $query_pending = "
        SELECT COUNT(B.booking_id) AS pending 
        FROM BOOKINGS B 
        JOIN SCHEDULES S ON B.schedule_id = S.schedule_id 
        WHERE B.booking_status = 'CONFIRMED' AND S.depart_date >= ?";
    $stmt = $conn->prepare($query_pending);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $report_data['pendingTicketsCount'] = (int)$result['pending'];
    $stmt->close();


    // --- Query 5: Route Booking Summary (for chart data visualization) ---
    // Calculates total seats booked per route for the chart.
    // NOTE: For simplicity, the `seat_num` column in BOOKINGS is assumed to be a string 
    // indicating the seat count (e.g., '2 seats'). We rely on the total number of bookings or 
    // total price for aggregation if true seat count is not available. 
    // Since we need seats, we'll COUNT total bookings per route.
    $query_route_summary = "
        SELECT 
            R.route_name, 
            COUNT(B.booking_id) AS total_bookings
        FROM ROUTES R
        JOIN SCHEDULES S ON R.route_id = S.route_id
        JOIN BOOKINGS B ON S.schedule_id = B.schedule_id
        WHERE B.booking_status = 'CONFIRMED'
        GROUP BY R.route_name
        ORDER BY total_bookings DESC
        LIMIT 5";
        
    $route_summary_result = $conn->query($query_route_summary);
    $route_data = [];
    while ($row = $route_summary_result->fetch_assoc()) {
        $route_data[] = [
            'route_name' => $row['route_name'],
            'total_seats_booked' => $row['total_bookings'] // Using bookings count as proxy for seats booked
        ];
    }
    $report_data['routeSummary'] = $route_data;

    // Final Success Response
    sendResponse(true, 'Report data fetched successfully.', 200, $report_data);

} catch (Exception $e) {
    sendResponse(false, "Database error: " . $e->getMessage(), 500); 
}

$conn->close();
?>