<?php
// PHP Security Check - MUST be at the very top
session_start();
// Check if user is logged in
if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
    // If not logged in, redirect them to the login page
    header('Location: user_login.php'); 
    exit;
}

$customer_id = $_SESSION['customer_id'];
$is_logged_in = true; // Confirmed logged in
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BTB | Booking History</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-custom shadow-sm">
            <div class="container d-flex justify-content-between align-items-center">
                <a class="navbar-logo" href="main.php"> <img src="assets/img/btb_logo.png" alt="BTB Logo" class="banner-logo" style="height: 40px;">
                </a>

                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="main.php">Home</a></li> <li class="nav-item"><a class="nav-link" href="routes.php">Routes</a></li> <li class="nav-item"><a class="nav-link" href="booking.php">Book Ticket</a></li> </ul>
                </div>

                <div class="dropdown" id="accountDropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="accountMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-4 me-1"></i> 
                        My Account
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountMenu">
                        <li><a class="dropdown-item active" href="booking_history.php">Booking History</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="api/user_logout.php">Log Out</a></li> 
                    </ul>
                </div>
                
                <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>
    </header>

    <main class="container my-5">
        
        <div class="d-flex justify-content-between align-items-end mb-4">
            <h1 class="fw-bold" style="font-family: 'Montserrat', sans-serif; color: #5c359d;">My Booking History</h1>
            <p class="lead text-muted mb-0">Review your past and upcoming trips.</p>
        </div>
        
        <div class="card shadow-sm p-4">
            <div class="mb-3">
                <p class="fw-semibold mb-2">Filters:</p>
                <div class="d-flex gap-3">
                    <button class="btn btn-warning text-white btn-sm" data-status="CONFIRMED">Upcoming</button>
                    <button class="btn btn-outline-secondary btn-sm" data-status="COMPLETED">Completed</button>
                    <button class="btn btn-outline-danger btn-sm" data-status="CANCELLED">Cancelled</button>
                    <button class="btn btn-outline-secondary btn-sm" data-status="">All</button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Route</th>
                            <th>Departure Date</th>
                            <th>Time</th>
                            <th>Seats</th>
                            <th>Status</th>
                            <th>Total Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="bookingHistoryTable">
                        <tr>
                            <td colspan="8" class="text-center text-muted">Loading booking data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
        </div>
        
    </main>

    <footer class="footer text-dark mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h6>About Us</h6>
                    <p>BTB is your trusted online bus ticket booking platform offering routes across Malaysia. Fast, reliable, and easy to use.</p>
                </div>

                <div class="col-md-2 mb-3">
                    <h6>Contact</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-phone-fill me-2"></i><span>+60 11 234 5678</span></li> 
                        <li><a href="mailto:btb2@gmail.com"><i class="bi bi-envelope-fill me-2"></i>Contact Email</a></li>
                    </ul>
                </div>

                <div class="col-md-2 mb-3">
                    <h6>Info</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>

                <div class="col-md-2 mb-3">
                    <h6>Partners</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">ManaMana Bus</a></li>
                        <li><a href="#">Gogo Hotels</a></li>
                    </ul>
                </div>

                <div class="col-md-2 mb-3">
                    <h6>Follow Us</h6>
                    <div class="d-flex gap-3">
                        <a href="https://www.facebook.com/"><i class="bi bi-facebook"></i></a>
                        <a href="https://x.com/i/flow/login?lang=en"><i class="bi bi-twitter"></i></a>
                        <a href="https://www.instagram.com/"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center small">© 2025 BTB Team. All rights reserved.</div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API_BASE_URL = 'http://localhost/WAIP_PROJECT';
        const bookingTableBody = document.getElementById('bookingHistoryTable');
        const filterButtons = document.querySelectorAll('.d-flex.gap-3 button');
        
        let currentFilter = 'CONFIRMED'; 

        document.addEventListener('DOMContentLoaded', function() {
            fetchBookings(currentFilter); 

            // Filter button logic
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    let newFilter = this.getAttribute('data-status');

                    // Update active button styling
                    filterButtons.forEach(btn => btn.classList.replace('btn-warning', 'btn-outline-secondary'));
                    filterButtons.forEach(btn => btn.classList.remove('text-white'));
                    
                    this.classList.replace('btn-outline-secondary', 'btn-warning');
                    if (this.classList.contains('btn-warning')) {
                        this.classList.add('text-white');
                    } else if (newFilter === 'CANCELLED') {
                        // Ensure Cancelled filter button uses danger style when not active
                        this.classList.replace('btn-warning', 'btn-outline-danger');
                    }
                    
                    currentFilter = newFilter;
                    fetchBookings(currentFilter);
                });
            });

            // --- CANCELLATION EVENT LISTENER ---
            bookingTableBody.addEventListener('click', function(e) {
                const cancelBtn = e.target.closest('.cancel-btn');
                if (cancelBtn) {
                    const bookingId = cancelBtn.dataset.id;
                    const bookingCode = `BK${bookingId}`;
                    
                    if (confirm(`Are you sure you want to cancel booking ${bookingCode}? This action cannot be undone and may incur cancellation fees.`)) {
                        cancelBooking(bookingId);
                    }
                }
            });
        });

        // --- Function to send cancellation request ---
        async function cancelBooking(bookingId) {
            try {
                const response = await fetch(`${API_BASE_URL}/api/cancel_booking.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ booking_id: bookingId }) 
                });

                const data = await response.json();

                if (data.success) {
                    alert(`Success: Booking BK${bookingId} has been successfully cancelled.`);
                    // Refresh the current list to show the updated status
                    fetchBookings(currentFilter); 
                } else {
                    alert(`Error cancelling booking: ${data.message}`);
                }

            } catch (error) {
                console.error('Cancellation error:', error);
                alert('A network error occurred while trying to cancel the booking.');
            }
        }


        // Helper to get status badge styling
        function getStatusBadge(status) {
            status = status.toUpperCase();
            let color = 'secondary';
            if (status === 'CONFIRMED') color = 'success';
            else if (status === 'COMPLETED') color = 'primary';
            else if (status === 'CANCELLED') color = 'danger';
            return `<span class="badge text-bg-${color} fw-semibold">${status}</span>`;
        }

        // Function to render the booking history table
        function renderBookings(bookings) {
            bookingTableBody.innerHTML = '';

            // Updated colspan count to 7 (since 'Action' column remains)
            if (bookings.length === 0) {
                bookingTableBody.innerHTML = `<tr><td colspan="8" class="text-center text-muted">No ${currentFilter ? currentFilter.toLowerCase() : 'active'} bookings found.</td></tr>`;
                return;
            }

            bookings.forEach(booking => {
                const statusBadge = getStatusBadge(booking.booking_status);
                const routeParts = booking.route_name ? booking.route_name.split(' to ') : [booking.route_name];
                const routeDisplay = routeParts.join(' <i class="bi bi-arrow-right"></i> ');
                
                let actionButtons = '';
                const departDate = new Date(booking.depart_date);
                const today = new Date();
                today.setHours(0,0,0,0); 

                // Logic to show/hide the Cancel button
                if (booking.booking_status === 'CONFIRMED' && departDate >= today) {
                    // REVISION: Removed "View Ticket" button
                    actionButtons = `
                        <button class="btn btn-sm btn-outline-danger cancel-btn" data-id="${booking.booking_id}">Cancel</button>
                    `;
                } else if (booking.booking_status === 'COMPLETED' || (booking.booking_status === 'CONFIRMED' && departDate < today)) {
                    // REVISION: Removed "View Ticket" button and kept only disabled Review button
                    actionButtons = `
                        <button class="btn btn-sm btn-outline-secondary" disabled>Review</button>
                    `;
                } else {
                    actionButtons = `<button class="btn btn-sm btn-outline-dark" disabled>N/A</button>`;
                }

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>BK${booking.booking_id}</td>
                    <td>${routeDisplay}</td>
                    <td>${booking.depart_date}</td>
                    <td>${booking.depart_time}</td>
                    <td>${booking.seat_num.replace(' seat(s)', '')}</td>
                    <td>${statusBadge}</td>
                    <td>RM ${parseFloat(booking.total_price).toFixed(2)}</td>
                    <td>${actionButtons}</td>
                `;
                bookingTableBody.appendChild(row);
            });
        }

        // Fetch API call (now includes logic to handle session expiry implicitly)
        async function fetchBookings(filter) {
            bookingTableBody.innerHTML = `<tr><td colspan="8" class="text-center text-info py-3"><span class="spinner-border spinner-border-sm me-2"></span> Loading ${filter ? filter.toLowerCase() : 'all'} bookings...</td></tr>`;

            try {
                const url = `${API_BASE_URL}/api/get_bookings.php${filter ? '?status=' + filter : ''}`;
                const response = await fetch(url);
                const data = await response.json();

                if (data.success) {
                    renderBookings(data.bookings);
                } else if (response.status === 401) {
                    // Redirect if unauthorized (session expired - handled by get_bookings.php)
                    window.location.href = 'user_login.php';
                } else {
                    throw new Error(data.message || 'Failed to load booking history.');
                }
            } catch (error) {
                console.error('Error fetching bookings:', error);
                bookingTableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Failed to load history: ${error.message}</td></tr>`;
            }
        }
    </script>
</body>
</html>