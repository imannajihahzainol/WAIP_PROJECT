<?php
session_start();

// Check if the admin is logged in (using the session variable set during admin_login)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php'); 
    exit; 
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin User'; 
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
                                    </tr>
                            </thead>
                            <tbody id="recentBookingsBody">
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Loading recent booking data from database...</td>
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
        const BOOKING_DETAIL_PAGE = 'booking_details_admin.php'; 

        document.addEventListener('DOMContentLoaded', function() {
            fetchDashboardData();
            fetchRecentBookings(); 
        });
        async function fetchDashboardData() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/get_admin_reports.php`);
                
                if (response.status === 401) {
                     window.location.href = 'admin_login.php';
                     return;
                }

                const data = await response.json();

                if (data.success && data.data) {
                    const reportData = data.data;

                    dashboardCards.totalBookingsCount.textContent = reportData.totalBookingsCount;
                    dashboardCards.activeRoutesCount.textContent = reportData.activeRoutesCount;
                    dashboardCards.pendingTicketsCount.textContent = reportData.pendingTicketsCount;
                    dashboardCards.newUsersCount.textContent = reportData.newUsersCount;

                    // The API now sends combined labels like 'Route Name (Time)'
                    const chartLabels = reportData.routeSummary.map(r => r.route_name);
                    const chartCounts = reportData.routeSummary.map(r => parseInt(r.total_seats_booked));

                    renderChart(chartLabels, chartCounts);
                } else {
                     console.error("API Error:", data.message);
                     Object.values(dashboardCards).forEach(el => el.textContent = 'Err');
                }

            } catch (error) {
                console.error('Fetch Error:', error);
                Object.values(dashboardCards).forEach(el => el.textContent = 'Fail');
            }
        }
        
        async function fetchRecentBookings() {
            recentBookingsBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted"><span class="spinner-border spinner-border-sm me-2"></span> Loading...</td></tr>';
            
            try {
                const response = await fetch(`${API_BASE_URL}/api/get_recent_bookings.php`);
                
                if (response.status === 401) {
                     window.location.href = 'admin_login.php';
                     return;
                }

                const data = await response.json();

                if (data.success && data.data) {
                    renderRecentBookingsTable(data.data);
                } else {
                    recentBookingsBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Failed to load bookings. ${data.message || ''}</td></tr>`;
                }

            } catch (error) {
                console.error('Recent Bookings Fetch Error:', error);
                recentBookingsBody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Network Error loading recent bookings.</td></tr>';
            }
        }
        
        //recent bookings table 
        function renderRecentBookingsTable(bookings) {
            if (bookings.length === 0) {
                recentBookingsBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No recent bookings found.</td></tr>';
                return;
            }
            recentBookingsBody.innerHTML = ''; 
            bookings.forEach(booking => {
                const statusBadge = getStatusBadge(booking.booking_status);
                
                const row = `
                    <tr>
                        <td>${booking.display_id}</td>
                        <td>${booking.customer_name}</td>
                        <td>${booking.route_name}</td>
                        <td>${booking.depart_date}</td>
                        <td>${statusBadge}</td>
                        </tr>
                `;
                recentBookingsBody.insertAdjacentHTML('beforeend', row);
            });
        }
        function getStatusBadge(status) {
            status = status.toUpperCase();
            let color = 'secondary';
            if (status === 'CONFIRMED') color = 'success';
            else if (status === 'COMPLETED') color = 'primary';
            else if (status === 'CANCELLED') color = 'danger';
            return `<span class="badge text-bg-${color} fw-semibold">${status}</span>`;
        }
        
        //chart
        function renderChart(labels, counts) {
            const ctx = document.getElementById('routeBookingChart');
            
            const chartColors = [
                '#d7820b', 
                '#5c359d', 
                'rgb(60, 152, 147)', 
                'cornflowerblue', 
                '#FF00FF'
            ];
            
            if (window.myChart) {
                window.myChart.destroy();
            }

            window.myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels, 
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
                            // TITLE IS UPDATED TO REFLECT SCHEDULE FOCUS
                            text: 'Top Schedules by Seats Booked'
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>