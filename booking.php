<?php
session_start();
// Assuming a successful CUSTOMER login sets these session variables
if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
    // Redirect to the customer login page if not logged in
    header('Location: user_login.html'); 
    exit;
}

$customer_id = $_SESSION['customer_id'];
$route_id = $_GET['route_id'] ?? null; // Get the route_id from the URL query parameter
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BTB | Book Ticket</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container d-flex justify-content-between align-items-center">
                <a class="navbar-logo" href="main.php">
                    <img src="assets/img/btb_logo.png" alt="BTB Logo" class="banner-logo" style="height: 40px;">
                </a>

                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="main.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="routes.php">Routes</a></li>
                        <li class="nav-item"><a class="nav-link active" href="booking.php">Book Ticket</a></li>
                    </ul>
                </div>

                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="accountMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-4 me-1"></i> Account
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountMenu">
                        <li><a class="dropdown-item" href="booking_history.php">Booking History</a></li>
                        <li><a class="dropdown-item" href="user_login.php">Log Out</a></li>
                    </ul>
                </div>
                
                <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>
    </header>

    <main class="container my-5">
        <h1 class="fw-bold mb-3" style="font-family: 'Montserrat', sans-serif;">Book Your Trip</h1>
        
        <div class="card p-3 mb-4 shadow-sm text-white" style="background-color: #5c359d;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1 fw-semibold text-white" id="routeDisplay">Kuala Lumpur <i class="bi bi-arrow-right-short"></i> Johor Bahru</h3>
                    <p class="small mb-0 opacity-75">Departure Date: 2025-11-20 | Bus Operator: ManaMana Bus</p>
                </div>
                <button class="btn btn-outline-light btn-sm" onclick="history.back()">Change Route</button>
            </div>
        </div>

        <div class="row">
            
            <div class="col-lg-8">
                <div class="card p-4 shadow-sm mb-4">
                    <h4 class="mb-4 fw-bold">Available Schedules</h4>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Departure Time</th>
                                <th>Price per Seat</th>
                                <th>Seats Left</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="scheduleTableBody">
                            <tr>
                                <td>08:00 AM</td>
                                <td class="fw-semibold">RM 35.00</td>
                                <td><span class="badge text-bg-secondary">25</span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning text-white book-btn" data-schedule-id="S-001" data-time="08:00 AM" data-price="35.00">Select</button>
                                </td>
                            </tr>
                            <tr>
                                <td>11:30 AM</td>
                                <td class="fw-semibold">RM 37.00</td>
                                <td><span class="badge text-bg-secondary">8</span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning text-white book-btn" data-schedule-id="S-002" data-time="11:30 AM" data-price="37.00">Select</button>
                                </td>
                            </tr>
                            <tr>
                                <td>04:00 PM</td>
                                <td class="fw-semibold">RM 35.00</td>
                                <td><span class="badge text-bg-secondary">3</span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning text-white book-btn" data-schedule-id="S-003" data-time="04:00 PM" data-price="35.00">Select</button>
                                </td>
                            </tr>
                            </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card p-4 shadow-sm sticky-top" style="top: 20px;">
                    <h4 class="mb-4 fw-bold">Booking Summary</h4>
                    
                    <form onsubmit="showSimpleAlert();" action="booking_history.php" method="POST" id="bookingForm">
                        <input type="hidden" name="schedule_id" id="selectedScheduleId" value="S-001">
                        
                        <div class="mb-3">
                            <label for="scheduleTime" class="form-label small fw-semibold">Selected Departure</label>
                            <input type="text" id="scheduleTime" class="form-control" value="08:00 AM" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="seatCount" class="form-label small fw-semibold">Number of Seats</label>
                            <select class="form-select" id="seatCount" name="seatCount" required>
                                <option value="1" selected>1 Seat</option>
                                <option value="2">2 Seats</option>
                                <option value="3">3 Seats</option>
                                <option value="4">4 Seats</option>
                            </select>
                        </div>
                        
                        <div class="d-flex justify-content-between pt-2 border-top">
                            <span class="fw-semibold fs-5">Total Price:</span>
                            <span class="fw-bold fs-4" style="color: #d7820b;" id="totalPriceDisplay">RM 35.00</span>
                        </div>

                        <p class="small text-muted mt-3 mb-4">By clicking 'Confirm Booking', you agree to the Terms & Conditions.</p>

                        <button type="submit" class="btn btn-warning w-100 text-white fw-semibold">
                            <i class="bi bi-credit-card me-2"></i> Confirm Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer text-dark mt-5">
        <div class="container py-4">
            <div class="text-center small">© 2025 BTB Team. All rights reserved.</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const API_BASE_URL = 'http://localhost/WAIP_PROJECT';
    const scheduleTableBody = document.getElementById('scheduleTableBody');
    const routeDisplayHeader = document.getElementById('routeDisplay');
    const totalPriceDisplay = document.getElementById('totalPriceDisplay');
    const scheduleTimeInput = document.getElementById('scheduleTime');
    const selectedScheduleIdInput = document.getElementById('selectedScheduleId');
    const seatCountSelect = document.getElementById('seatCount');
    const bookingForm = document.getElementById('bookingForm');
    
    // Get Route ID from PHP environment variable/URL (passed via PHP)
    const routeId = '<?php echo $route_id; ?>'; 
    let currentBasePrice = 0.00; 

    // Helper to update total price display
    function updateTotalPrice() {
        const seatCount = parseInt(seatCountSelect.value);
        const totalPrice = currentBasePrice * seatCount;
        totalPriceDisplay.textContent = `RM ${totalPrice.toFixed(2)}`;
    }

    // Function to handle schedule selection
    function handleScheduleSelection(button) {
        const row = button.closest('tr');
        const time = button.getAttribute('data-time');
        const priceValue = button.getAttribute('data-price');
        const scheduleId = button.getAttribute('data-schedule-id');
        
        currentBasePrice = parseFloat(priceValue); 
        scheduleTimeInput.value = time;
        selectedScheduleIdInput.value = scheduleId;

        // Reset seat count to 1 and update price
        seatCountSelect.value = 1; 
        updateTotalPrice();

        // Highlight selected row
        document.querySelectorAll('#scheduleTableBody tr').forEach(r => r.classList.remove('table-primary'));
        row.classList.add('table-primary');
    }

    // Function to render the schedules table
    function renderSchedules(schedules) {
        scheduleTableBody.innerHTML = '';
        if (schedules.length === 0) {
            scheduleTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">No schedules available for this route.</td></tr>`;
            return;
        }

        schedules.forEach((schedule, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${schedule.depart_time}</td>
                <td class="fw-semibold">RM ${schedule.price}</td>
                <td><span class="badge text-bg-${schedule.available_seats > 10 ? 'success' : schedule.available_seats > 3 ? 'warning' : 'danger'}">${schedule.available_seats}</span></td>
                <td>
                    <button class="btn btn-sm btn-warning text-white book-btn" 
                        data-schedule-id="${schedule.schedule_id}" 
                        data-time="${schedule.depart_time}" 
                        data-price="${schedule.price}"
                        ${schedule.available_seats == 0 ? 'disabled' : ''}>
                        ${schedule.available_seats == 0 ? 'Sold Out' : 'Select'}
                    </button>
                </td>
            `;
            scheduleTableBody.appendChild(row);

            // Automatically select and highlight the first available schedule
            if (index === 0) { 
                row.classList.add('table-primary');
                // Use the data from the first schedule to initialize the summary
                handleScheduleSelection(row.querySelector('.book-btn')); 
            }
        });
        
        // Add event listener delegation for select buttons
        scheduleTableBody.querySelectorAll('.book-btn').forEach(button => {
            button.addEventListener('click', function() {
                handleScheduleSelection(this);
            });
        });
    }

    // Fetch schedules API call
    async function fetchSchedules() {
        scheduleTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-info">Loading schedules...</td></tr>`;

        try {
            const response = await fetch(`${API_BASE_URL}/api/get_schedules.php?route_id=${routeId}`);
            const data = await response.json();

            if (data.success) {
                // Update header
                routeDisplayHeader.innerHTML = `${data.route_name} | Date: ${new Date().toISOString().slice(0, 10)}`; 
                renderSchedules(data.schedules);
            } else {
                throw new Error(data.message || 'Failed to load schedules.');
            }
        } catch (error) {
            console.error('Error fetching schedules:', error);
            scheduleTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Failed to load schedules: ${error.message}</td></tr>`;
        }
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        if (routeId) {
            fetchSchedules();
        } else {
            routeTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">No Route ID specified. Please select a route first.</td></tr>`;
        }
        seatCountSelect.addEventListener('change', updateTotalPrice);
        
        // TODO: Attach the booking submission logic here
        bookingForm.addEventListener('submit', handleBookingSubmission);
    });

    // --- FINAL Booking Submission Logic (in booking.php script block) ---
async function handleBookingSubmission(e) {
    e.preventDefault();
    
    // Ensure the user has selected a schedule and seat count > 0
    if (!selectedScheduleIdInput.value || parseInt(seatCountSelect.value) <= 0) {
        alert("Please select a schedule and the number of seats.");
        return;
    }

    const bookingForm = document.getElementById('bookingForm');
    const formData = new FormData(bookingForm); 
    
    // Manually append the total price to send it for verification/storage
    const totalPrice = totalPriceDisplay.textContent.replace('RM ', '');
    formData.append('total_price', totalPrice);

    try {
        const response = await fetch(`${API_BASE_URL}/api/create_booking.php`, {
            method: 'POST',
            body: formData // Sends the schedule_id, seatCount, and total_price
        });

        const data = await response.json();

        if (response.ok && data.success) {
            alert(`Booking successful! ID: BK${data.booking_id}. Redirecting to history.`);
            window.location.href = 'booking_history.php'; // Redirect to the final history page
        } else if (data.message.includes("seats remaining")) {
            // Specific error handling for sold out/availability
            alert(`Booking failed: ${data.message}`);
            // Re-fetch schedules to update availability status on the table
            fetchSchedules(); 
        } else {
            alert(`Booking failed: ${data.message || 'An unknown error occurred on the server.'}`);
        }
    } catch (error) {
        alert('Network error. Could not connect to the booking API.');
        console.error('Fetch error:', error);
    }
}
</script>
</body>
</html>