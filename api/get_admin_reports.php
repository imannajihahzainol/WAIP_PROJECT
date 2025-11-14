<?php
session_start();
require_once '../db_config.php'; 
header('Content-Type: application/json');

function sendResponse($success, $message, $http_code, $data = null) {
    http_response_code($http_code);
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

//security check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    sendResponse(false, 'Authorization required.', 401);
}

$today = date('Y-m-d');
$last_month_start = date('Y-m-d', strtotime('first day of last month'));
$last_week_start = date('Y-m-d', strtotime('-7 days'));
$report_data = [];
$stmt1 = $stmt2 = $stmt3 = $stmt4 = null; 

try {
    // query for total bookings
    $query_total_bookings = "SELECT COUNT(booking_id) AS total FROM BOOKINGS WHERE booking_status = 'CONFIRMED' AND booking_time >= ?";
    $stmt1 = $conn->prepare($query_total_bookings);
    $stmt1->bind_param("s", $last_month_start);
    $stmt1->execute();
    $result = $stmt1->get_result()->fetch_assoc();
    $report_data['totalBookingsCount'] = (int)$result['total'];
    $stmt1->close(); 

    //query active routes 
    $query_active_routes = "SELECT COUNT(DISTINCT R.route_id) AS active FROM ROUTES R JOIN SCHEDULES S ON R.route_id = S.route_id WHERE S.depart_date >= ?";
    $stmt2 = $conn->prepare($query_active_routes);
    $stmt2->bind_param("s", $today);
    $stmt2->execute();
    $result = $stmt2->get_result()->fetch_assoc();
    $report_data['activeRoutesCount'] = (int)$result['active'];
    $stmt2->close(); 

    //query new users 
    $query_new_users = "SELECT COUNT(customer_id) AS new_users FROM customer WHERE created_at >= ?";
    $stmt3 = $conn->prepare($query_new_users);
    $stmt3->bind_param("s", $last_week_start);
    $stmt3->execute();
    $result = $stmt3->get_result()->fetch_assoc();
    $report_data['newUsersCount'] = (int)$result['new_users'];
    $stmt3->close(); 
    
    //query future tickets 
    $query_pending = "
        SELECT SUM(SUBSTRING_INDEX(B.seat_num, ' ', 1)) AS pending_seats 
        FROM BOOKINGS B 
        JOIN SCHEDULES S ON B.schedule_id = S.schedule_id 
        WHERE B.booking_status = 'CONFIRMED' AND S.depart_date >= ?";
    $stmt4 = $conn->prepare($query_pending);
    $stmt4->bind_param("s", $today);
    $stmt4->execute();
    $result = $stmt4->get_result()->fetch_assoc();
    $report_data['pendingTicketsCount'] = (int)$result['pending_seats'];
    $stmt4->close();


    //query route booking summary
    $query_route_summary = "
        SELECT 
            R.route_name, 
            SUM(SUBSTRING_INDEX(B.seat_num, ' ', 1)) AS total_seats_booked
        FROM ROUTES R
        JOIN SCHEDULES S ON R.route_id = S.route_id
        JOIN BOOKINGS B ON S.schedule_id = B.schedule_id
        WHERE B.booking_status = 'CONFIRMED'
        GROUP BY R.route_name
        ORDER BY total_seats_booked DESC
        LIMIT 5";
        
    $route_summary_result = $conn->query($query_route_summary);
    $route_data = [];
    if ($route_summary_result) {
        while ($row = $route_summary_result->fetch_assoc()) {
            $route_data[] = [
                'route_name' => $row['route_name'],
                'total_seats_booked' => (int)$row['total_seats_booked']
            ];
        }
    }
    $report_data['routeSummary'] = $route_data;

    sendResponse(true, 'Report data fetched successfully.', 200, $report_data);

} catch (Exception $e) {
    if (isset($stmt1) && $stmt1 instanceof mysqli_stmt) { $stmt1->close(); }
    if (isset($stmt2) && $stmt2 instanceof mysqli_stmt) { $stmt2->close(); }
    if (isset($stmt3) && $stmt3 instanceof mysqli_stmt) { $stmt3->close(); }
    if (isset($stmt4) && $stmt4 instanceof mysqli_stmt) { $stmt4->close(); }
    
    sendResponse(false, "Database error: " . $e->getMessage(), 500); 
}

$conn->close();