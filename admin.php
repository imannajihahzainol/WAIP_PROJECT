<?php
// PHP Security Check - MUST be at the top of the file
session_start();

// Check if the admin is logged in (using the session variable set during admin_login)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.html'); // Redirect to login page if unauthorized
    exit; 
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin User'; // Assuming you set username in session during login
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BTB | Admin Dashboard</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="bg-light">

    <div class="d-flex" id="wrapper"> 
        <div class="admin-sidebar" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom p-3 text-center" style="background-color: #ebd9ff;">
                <img src="assets/img/btb_logo.png" alt="BTB Logo" style="height: 40px; filter: drop-shadow(0 0 1px #fff);"> 
            </div>
            
            <div class="list-group list-group-flush admin-sidebar-nav">
                <a class="list-group-item list-group-item-action p-3 active" href="admin.php">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                <a class="list-group-item list-group-item-action p-3" href="manage_routes.php">
                    <i class="bi bi-geo-alt me-2"></i>Manage Routes</a>
                <a class="list-group-item list-group-item-action p-3" href="admin.php#reports">
                    <i class="bi bi-clipboard-data me-2"></i>Reports</a>
            </div>
        </div>
      
        <div id="page-content-wrapper" class="flex-grow-1">

            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
                <div class="container-fluid">
                    <h5 class="mb-0 fw-bold text-dark">Dashboard Overview</h5>
                    
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="adminMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-4 me-1"></i> <?php echo htmlspecialchars($admin_username); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminMenu">
                            <li><a class="dropdown-item" href="api/admin_logout.php">Log Out</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4 admin-dashboard">
                <h1 class="mt-4 mb-4 fw-bold">Admin Panel</h1>

                <div class="row g-4">
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="card dashboard-card admin-card-green shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">Total Bookings</h5>
                                <h1 class="display-4 fw-bold" id="totalBookingsCount">...</h1> <p class="card-text small">Since last month</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="card dashboard-card admin-card-purple shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">Active Routes</h5>
                                <h1 class="display-4 fw-bold" id="activeRoutesCount">...</h1> <p class="card-text small">Routes scheduled today or later</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="card dashboard-card text-white admin-card-yellow shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">Future Tickets</h5>
                                <h1 class="display-4 fw-bold" id="pendingTicketsCount">...</h1> <p class="card-text small">Confirmed trips in the future</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="card dashboard-card text-dark admin-card-blue shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">New Users</h5>
                                <h1 class="display-4 fw-bold" id="newUsersCount">...</h1> <p class="card-text small">Registered this week</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h2 class="mt-5 mb-4 fw-bold" id="reports">Route Booking Summary</h2>
                <div class="card shadow-sm mb-4 dashboard-card">
                    <div class="card-body">
                        <canvas id="routeBookingChart" style="height: 300px;"></canvas> 
                    </div>
                </div>
                
                <h2 class="mt-5 mb-4 fw-bold">Recent Bookings</h2>
                
                <div class="card shadow-sm mb-4 dashboard-card">
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Customer</th>
                                    <th>Route</th>
                                    <th>Departure Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="recentBookingsBody">
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Loading recent booking data from database...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <h2 class="mt-5 mb-4 fw-bold">Quick Actions</h2>
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <a href="manage_routes.php" class="btn btn-dark w-100 p-3 fw-semibold dashboard-card admin-btn-dark">
                            <i class="bi bi-plus-circle me-2"></i> Add New Route
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="manage_routes.php" class="btn btn-outline-dark w-100 p-3 fw-semibold dashboard-card">
                            <i class="bi bi-geo-alt me-2"></i> Manage Routes
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="#reports" class="btn btn-outline-dark w-100 p-3 fw-semibold dashboard-card">
                            <i class="bi bi-clipboard-data me-2"></i> View Full Report
                        </a>
                    </div>
                </div>

            </div>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const API_BASE_URL = 'http://localhost/WAIP_PROJECT'; 
        const dashboardCards = {
            totalBookingsCount: document.getElementById('totalBookingsCount'),
            activeRoutesCount: document.getElementById('activeRoutesCount'),
            pendingTicketsCount: document.getElementById('pendingTicketsCount'),
            newUsersCount: document.getElementById('newUsersCount')
        };
        const recentBookingsBody = document.getElementById('recentBookingsBody');


        document.addEventListener('DOMContentLoaded', function() {
            fetchDashboardData();
            // Optional: Fetch recent booking list (placeholder, requires a separate API for the table)
            // fetchRecentBookings(); 
        });

        async function fetchDashboardData() {
            try {
                // Call the Admin Reports API
                const response = await fetch(`${API_BASE_URL}/api/get_admin_reports.php`);
                
                // Handle unauthorized access (if session expired)
                if (response.status === 401) {
                     window.location.href = 'admin_login.php';
                     return;
                }

                const data = await response.json();

                if (data.success && data.data) {
                    const reportData = data.data;

                    // 1. Update Cards
                    dashboardCards.totalBookingsCount.textContent = reportData.totalBookingsCount;
                    dashboardCards.activeRoutesCount.textContent = reportData.activeRoutesCount;
                    dashboardCards.pendingTicketsCount.textContent = reportData.pendingTicketsCount;
                    dashboardCards.newUsersCount.textContent = reportData.newUsersCount;

                    // 2. Prepare Chart Data (Result Aggregation)
                    const chartLabels = reportData.routeSummary.map(r => r.route_name);
                    // Ensure the data is numeric for the chart
                    const chartCounts = reportData.routeSummary.map(r => parseInt(r.total_seats_booked));

                    renderChart(chartLabels, chartCounts);
                } else {
                     console.error("API Error:", data.message);
                     alert("Failed to load dashboard data.");
                }

            } catch (error) {
                console.error('Fetch Error:', error);
                // Set card status to indicate failure
                Object.values(dashboardCards).forEach(el => el.textContent = 'Err');
            }
        }
        
        // --- Chart Initialization Function ---
        function renderChart(labels, counts) {
            const ctx = document.getElementById('routeBookingChart');
            
            // Bar Colour
            const chartColors = [
                '#d7820b', 
                '#5c359d', 
                'rgb(60, 152, 147)', 
                'cornflowerblue', 
                '#FF00FF'
            ];
            
            // Destroy any existing chart instance before creating a new one
            if (window.myChart) {
                window.myChart.destroy();
            }

            window.myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels, // Routes
                    datasets: [{
                        label: 'Total Seats Booked',
                        data: counts, 
                        backgroundColor: chartColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Top 5 Routes by Total Seats Booked'
                        }
                    }
                }
            });
        }
        
        // --- Placeholder for Recent Bookings Table (Requires separate API) ---
        function fetchRecentBookings() {
             // This function should call an API (e.g., /api/get_recent_bookings.php)
             // to fetch the latest bookings and populate the 'recentBookingsBody' table.
             // Since this wasn't explicitly developed, we leave the placeholder status.
             recentBookingsBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">API required for recent bookings list.</td></tr>';
        }

    </script>
</body>
</html>