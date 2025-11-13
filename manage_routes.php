<?php
// PHP Security Check - MUST be at the top of the file
session_start();

// Check if the admin is logged in (using the session variable set during login)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.html'); // Redirect to your login page
    exit; 
}
// Admin is logged in, continue execution.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BTB | Manage Routes</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">

    <div class="d-flex" id="wrapper">
        
        <div class="admin-sidebar" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom p-3 text-center" style="background-color: #ebd9ff;">
                <img src="assets/img/btb_logo.png" alt="BTB Logo" style="height: 40px; filter: drop-shadow(0 0 1px #fff);"> 
            </div>
            
            <div class="list-group list-group-flush admin-sidebar-nav">
                <a class="list-group-item list-group-item-action p-3" href="admin.php">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
                <a class="list-group-item list-group-item-action p-3 active" href="manage_routes.php">
                    <i class="bi bi-geo-alt me-2"></i>Manage Routes
                </a>
                <a class="list-group-item list-group-item-action p-3" href="admin.php#reporting">
                    <i class="bi bi-clipboard-data me-2"></i>Reports
                </a>
            </div>
        </div>
        
        <div id="page-content-wrapper" class="flex-grow-1">

            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
                <div class="container-fluid">
                    <h5 class="mb-0 fw-bold text-dark">Route Management</h5>
                    
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="adminMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-4 me-1"></i> Admin User
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminMenu">
                            <li><a class="dropdown-item" href="api/admin_logout.php" id="adminLogoutBtn">Log Out</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4 admin-dashboard">
                <h1 class="mt-4 mb-4 fw-bold">Create New Route</h1>
                
                <div id="routeMessage" class="alert d-none"></div>

                <div class="card shadow-sm mb-5 p-4 dashboard-card">
                    <form action="api/create_route_schedules.php" method="POST" id="createRouteForm">
                        <h4 class="mb-3" style="color: #5c359d;">Route Details</h4>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="routeName" class="form-label fw-semibold">Route Name (e.g., KL to JB)</label>
                                <input type="text" class="form-control" id="routeName" name="routeName" required>
                            </div>
                            <div class="col-md-6">
                                <label for="routeDate" class="form-label fw-semibold">Departure Date (Default for all slots)</label>
                                <input type="date" class="form-control" id="routeDate" name="depart_date" required>
                            </div>
                            <div class="col-12">
                                <label for="routeDesc" class="form-label fw-semibold">Route Description</label>
                                <textarea class="form-control" id="routeDesc" name="routeDesc" rows="2" required></textarea>
                            </div>
                        </div>

                        <h4 class="mb-3 mt-4" style="color: #5c359d;">Schedule Time Slots</h4>

                        <div id="scheduleSlots">
                            <div class="row g-3 schedule-slot border-bottom pb-3 mb-3" data-slot-id="1">
                                <div class="col-md-3">
                                    <label for="departTime1" class="form-label small fw-semibold">Departure Time</label>
                                    <input type="time" class="form-control" id="departTime1" data-field="departTime" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="price1" class="form-label small fw-semibold">Price (RM)</label>
                                    <input type="number" class="form-control" id="price1" data-field="price" step="0.01" required value="30.00">
                                </div>
                                <div class="col-md-3">
                                    <label for="maxSeats1" class="form-label small fw-semibold">Max Seats</label>
                                    <input type="number" class="form-control" id="maxSeats1" data-field="maxSeats" required value="40">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger w-100 remove-slot-btn"><i class="bi bi-trash"></i> Remove</button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" id="addSlotBtn" class="btn btn-outline-secondary btn-sm mb-4">
                            <i class="bi bi-clock-history me-1"></i> Add Another Time Slot
                        </button>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" id="saveRouteBtn" class="btn btn-warning text-white fw-bold p-2">
                                <span id="buttonText"><i class="bi bi-save me-2"></i> Save Route & Schedules</span>
                                <span id="loadingSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
                
                <h1 class="mt-5 mb-4 fw-bold">Existing Routes</h1>

                <div class="card shadow-sm mb-4 dashboard-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Route ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Schedules</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="existingRoutesTable">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Loading existing routes...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const API_BASE_URL = 'http://localhost/WAIP_PROJECT'; 
        const routeForm = document.getElementById('createRouteForm');
        const scheduleSlotsContainer = document.getElementById('scheduleSlots');
        const addSlotBtn = document.getElementById('addSlotBtn');
        const routesTableBody = document.getElementById('existingRoutesTable');
        const routeMessage = document.getElementById('routeMessage');
        
        // Store the initial slot HTML for resetting the form cleanly
        const initialSlotHTML = scheduleSlotsContainer.innerHTML;

        let currentSlotId = 1;

        document.getElementById('adminLogoutBtn').addEventListener('click', function(e) {
            e.preventDefault();
            // Redirect to the PHP logout script
            window.location.href = 'api/admin_logout.php'; 
        });

        function showLoading(isLoading) {
            const saveRouteBtn = document.getElementById('saveRouteBtn');
            const buttonText = document.getElementById('buttonText');
            const loadingSpinner = document.getElementById('loadingSpinner');
            saveRouteBtn.disabled = isLoading;
            buttonText.classList.toggle('d-none', isLoading);
            loadingSpinner.classList.toggle('d-none', !isLoading);
        }
        
        function showFeedback(message, isError = false) {
            routeMessage.textContent = message;
            routeMessage.classList.remove('d-none', 'alert-success', 'alert-danger');
            routeMessage.classList.add(isError ? 'alert-danger' : 'alert-success');
        }

        function addSlot() {
            currentSlotId++;
            const newSlot = document.createElement('div');
            newSlot.className = 'row g-3 schedule-slot border-bottom pb-3 mb-3';
            newSlot.setAttribute('data-slot-id', currentSlotId);
            newSlot.innerHTML = `
                <div class="col-md-3">
                    <label for="departTime${currentSlotId}" class="form-label small fw-semibold">Departure Time</label>
                    <input type="time" class="form-control" id="departTime${currentSlotId}" data-field="departTime" required>
                </div>
                <div class="col-md-3">
                    <label for="price${currentSlotId}" class="form-label small fw-semibold">Price (RM)</label>
                    <input type="number" class="form-control" id="price${currentSlotId}" data-field="price" step="0.01" required value="30.00">
                </div>
                <div class="col-md-3">
                    <label for="maxSeats${currentSlotId}" class="form-label small fw-semibold">Max Seats</label>
                    <input type="number" class="form-control" id="maxSeats${currentSlotId}" data-field="maxSeats" required value="40">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-outline-danger w-100 remove-slot-btn"><i class="bi bi-trash"></i> Remove</button>
                </div>
            `;
            scheduleSlotsContainer.appendChild(newSlot);
        }

        // Attach event listener delegation for removing slots
        scheduleSlotsContainer.addEventListener('click', function(event) {
            const button = event.target.closest('.remove-slot-btn');
            if (button) {
                if (scheduleSlotsContainer.children.length > 1) {
                    button.closest('.schedule-slot').remove();
                } else {
                    showFeedback("You must have at least one time slot.", true);
                }
            }
        });

        // --- AJAX Form Submission (Create Route & Schedules) ---
        routeForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            routeMessage.classList.add('d-none');
            showLoading(true);

            // 1. Collect Route Details
            const routeName = document.getElementById('routeName').value;
            const routeDesc = document.getElementById('routeDesc').value;
            const departDate = document.getElementById('routeDate').value; 
            
            // 2. Collect all Schedule Slots
            const schedules = [];
            const slotElements = scheduleSlotsContainer.querySelectorAll('.schedule-slot');

            slotElements.forEach(slot => {
                schedules.push({
                    depart_date: departDate, 
                    depart_time: slot.querySelector('[data-field="departTime"]').value,
                    price: parseFloat(slot.querySelector('[data-field="price"]').value),
                    max_seats: parseInt(slot.querySelector('[data-field="maxSeats"]').value),
                });
            });

            // 3. Construct Payload
            const payload = { routeName, routeDesc, schedules };

            // 4. Send to PHP API for creation
            try {
                const response = await fetch(`${API_BASE_URL}/api/create_route_schedules.php`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload)
                });
                
                const data = await response.json();

                if (response.ok && data.success) {
                    showFeedback(`Route created successfully! ID: R-${data.route_id}`, false);
                    routeForm.reset();
                    // Reset slots to the initial single slot
                    scheduleSlotsContainer.innerHTML = initialSlotHTML; 
                    currentSlotId = 1; 
                    fetchExistingRoutes(); // Refresh the list
                } else {
                    // Handle server-side errors
                    throw new Error(data.message || 'Route creation failed on the server.');
                }
            } catch (error) {
                showFeedback(error.message.includes('fetch') ? 'Could not connect to API. Check XAMPP/PHP.' : error.message, true);
            } finally {
                showLoading(false);
            }
        });

        // --- AJAX Route Listing (GET) ---

        async function fetchExistingRoutes() {
            routesTableBody.innerHTML = `<tr><td colspan="5" class="text-center text-info py-3"><span class="spinner-border spinner-border-sm me-2"></span> Loading routes...</td></tr>`;

            try {
                // Call the API endpoint for fetching routes
                const response = await fetch(`${API_BASE_URL}/api/get_routes.php`); 
                
                const data = await response.json();
                
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to load routes from the server.');
                }
                
                const routes = data.routes; 

                routesTableBody.innerHTML = ''; 

                if (routes.length === 0) {
                    routesTableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No existing routes found.</td></tr>';
                    return;
                }

                routes.forEach(route => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${route.route_id}</td>
                        <td>${route.route_name}</td>
                        <td>${route.route_desc}</td>
                        <td>${route.schedule_count} Slots</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-2 edit-btn" data-id="${route.route_id}" disabled>Edit</button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${route.route_id}">Delete</button>
                        </td>
                    `;
                    routesTableBody.appendChild(row);
                });

            } catch (error) {
                console.error('Error fetching existing routes:', error);
                routesTableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Failed to load routes: ${error.message}</td></tr>`;
            }
        }
        
        // --- AJAX Route Deletion (DELETE) ---
        routesTableBody.addEventListener('click', async function(e) {
            const deleteBtn = e.target.closest('.delete-btn');
            if (deleteBtn) {
                const routeId = deleteBtn.dataset.id;
                
                if (!confirm(`Are you sure you want to delete Route ID ${routeId}? This will remove ALL associated bookings and schedules permanently.`)) {
                    return;
                }
                
                deleteBtn.disabled = true;
                
                try {
                    // Call the DELETE API endpoint
                    const response = await fetch(`${API_BASE_URL}/api/delete_route.php?id=${routeId}`, {
                        method: 'DELETE',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    
                    const data = await response.json();

                    if (response.ok && data.success) {
                        showFeedback(`Route ${routeId} deleted successfully.`, false);
                        fetchExistingRoutes(); // Refresh the list
                    } else {
                        throw new Error(data.message || 'Deletion failed.');
                    }
                } catch (error) {
                    deleteBtn.disabled = false;
                    showFeedback(`Error deleting route: ${error.message}`, true);
                }
            }
        });


        // Initial Load Setup
        document.addEventListener('DOMContentLoaded', function() {
            addSlotBtn.addEventListener('click', addSlot);
            // Ensure initial slot removal button is managed
            const initialRemoveBtn = scheduleSlotsContainer.querySelector('.remove-slot-btn');
            if (initialRemoveBtn) {
                initialRemoveBtn.disabled = scheduleSlotsContainer.children.length <= 1;
            }
            
            fetchExistingRoutes(); // Call the function on page load
        });
    </script>
</body>
</html>