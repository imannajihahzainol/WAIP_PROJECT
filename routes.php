<?php
// PHP Session Start and Status Check (if needed, though this file focuses on routes)
session_start();
$is_logged_in = isset($_SESSION['customer_logged_in']) && $_SESSION['customer_logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BTB | Routes</title>

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
                        <li class="nav-item"><a class="nav-link active" href="routes.php">Routes</a></li>
                        <li class="nav-item"><a class="nav-link" href="booking.php">Book Ticket</a></li>
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
        <h1 class="fw-bold mb-4" style="font-family: 'Montserrat', sans-serif; color: #5c359d;">Explore Available Routes</h1>
        <p class="lead text-secondary">A complete list of destinations and routes available for booking across Malaysia.</p>

        <div class="row">
            
            <div class="col-lg-3">
                <div class="card p-3 shadow-sm mb-4">
                    <h5 class="fw-bold mb-3" style="color: #333;">Filter Routes</h5>
                    <form id="filterForm">
                        <div class="mb-3">
                            <label for="fromFilter" class="form-label small fw-semibold">Departure City</label>
                            <select class="form-select" id="fromFilter">
                                <option selected>Select City</option>
                                <option>Kuala Lumpur</option>
                                <option>Johor Bahru</option>
                                <option>Georgetown</option>
                                <option>Seremban</option>
                                <option>Alor Gajah</option>
                                <option>Ipoh</option>
                                <option>Kl Sentral</option>
                                <option>Kuala Terengganu</option>
                                <option>Kuantan</option>
                                <option>Sg Petani</option>
                                <option>Pasir Mas</option>
                                <option>Seri Iskandar</option>
                                <option>Putrajaya</option>
                                <option>Pendang</option>
                                <option>Bentong</option>
                                <option>Port Dickson</option>
                                <option>Kangar</option>
                                <option>Alor Setar</option>
                                <option>Setiu</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="toFilter" class="form-label small fw-semibold">Arrival City</label>
                            <select class="form-select" id="toFilter">
                                <option selected>Select City</option>
                                <option>Kuala Lumpur</option>
                                <option>Johor Bahru</option>
                                <option>Georgetown</option>
                                <option>Seremban</option>
                                <option>Alor Gajah</option>
                                <option>Ipoh</option>
                                <option>Kl Sentral</option>
                                <option>Kuala Terengganu</option>
                                <option>Kuantan</option>
                                <option>Sg Petani</option>
                                <option>Pasir Mas</option>
                                <option>Seri Iskandar</option>
                                <option>Putrajaya</option>
                                <option>Pendang</option>
                                <option>Bentong</option>
                                <option>Port Dickson</option>
                                <option>Kangar</option>
                                <option>Alor Setar</option>
                                <option>Setiu</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-purple w-100 fw-semibold mt-2">
                            Apply Filters
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="row g-4" id="routeListContainer">
                    </div>
            
                <nav class="mt-5">
                    <ul class="pagination justify-content-center">
                        </ul>
                </nav>
            </div>
        </div>
    </main>

  <footer class="footer text-dark mt-5">
    <div class="container py-4">
      <div class="row">
        <div class="col-md-4 mb-3">
          <h6>About Us</h6>
          <ul class="list-unstyled">
          <p>BTB is your trusted online bus ticket booking platform offering routes across Malaysia. Fast, reliable, and easy to use.</p>
        </div>

        <div class="col-md-2 mb-3">
          <h6>Contact</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><i class="bi bi-phone-fill me-2"></i><span>+60 11 234 5678</span></li> 
          <li><a href="mailto:btb2@gmail.com"><i class="bi bi-envelope-fill me-2"></i>btb2@gmail.com</a></li>
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
    const routeListContainer = document.getElementById('routeListContainer');
    const paginationContainer = document.querySelector('.pagination'); 
    
    // Select filter elements
    const filterForm = document.getElementById('filterForm');
    const fromFilter = document.getElementById('fromFilter');
    const toFilter = document.getElementById('toFilter');
    
    // Get URL parameters sent from main.php
    const urlParams = new URLSearchParams(window.location.search);
    const initialFrom = urlParams.get('from') || '';
    const initialTo = urlParams.get('to') || '';

    // --- INITIAL LOAD & FORM SUBMISSION ---

    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Pre-fill filters from URL parameters
        // This checks if the URL parameter matches an available option before setting
        if (initialFrom && Array.from(fromFilter.options).some(opt => opt.value === initialFrom)) {
            fromFilter.value = initialFrom;
        }
        if (initialTo && Array.from(toFilter.options).some(opt => opt.value === initialTo)) {
            toFilter.value = initialTo;
        }

        // 2. Initial load of routes (Will automatically use the pre-filled values)
        fetchRoutes(1); 
        
        // 3. Add event listener for the filter form submission
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault(); 
            // When filters are applied (or re-applied), always start from page 1
            fetchRoutes(1); 
        });
    });

    // --- CORE PAGINATION AND FETCH FUNCTION ---

    async function fetchRoutes(page = 1) {
        routeListContainer.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading routes...</p>
            </div>
        `;
        paginationContainer.innerHTML = ''; 
        
        // Get current filter values (either pre-filled from URL or manually selected)
        const fromCity = fromFilter.value !== 'Select City' ? fromFilter.value : '';
        const toCity = toFilter.value !== 'Select City' ? toFilter.value : '';
        
        // Build query string with pagination and filter parameters
        const params = new URLSearchParams({
            page: page,
            from: fromCity,
            to: toCity
        });
        
        try {
            const url = `${API_BASE_URL}/api/get_routes.php?${params.toString()}`;
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                renderRouteCards(data.routes);
                // CRITICAL: Ensure data.total_pages and data.current_page exist
                renderPagination(data.current_page, data.total_pages); 
            } else {
                throw new Error(data.message || 'Failed to retrieve route data.');
            }

        } catch (error) {
            console.error('Error fetching routes:', error);
            routeListContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger text-center" role="alert">
                        Failed to load routes: ${error.message}. Please check your XAMPP server.
                    </div>
                </div>
            `;
            paginationContainer.innerHTML = ''; 
        }
    }

    // --- HELPER FUNCTIONS ---

    function renderRouteCards(routes) {
        routeListContainer.innerHTML = ''; 

        if (routes.length === 0) {
            routeListContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                        No routes found matching your criteria.
                    </div>
                </div>
            `;
            return;
        }

        routes.forEach(route => {
            const nameParts = route.route_name.split(' to ');
            const title = nameParts.length > 1 
                ? `${nameParts[0]} <i class="bi bi-arrow-right"></i> ${nameParts[1]}` 
                : route.route_name;
            
            const cardHtml = `
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h4 class="card-title fw-bold" style="color: #d7820b;">${title}</h4>
                            <p class="card-text text-muted small mt-1 mb-2">Route ID: R-${route.route_id}</p>
                            <p class="small mb-3">${route.route_desc}</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold fs-5" style="color: #5c359d;">From RM ${route.min_price}</span> 
                            </div>
                            <a href="booking.php?route_id=${route.route_id}" class="btn btn-outline-dark fw-semibold w-100">
                                View Schedules & Book
                            </a>
                        </div>
                    </div>
                </div>
            `;
            routeListContainer.innerHTML += cardHtml;
        });
    }

    function renderPagination(currentPage, totalPages) {
        paginationContainer.innerHTML = ''; 
        
        if (totalPages <= 1) return; 

        const createPageItem = (text, pageNumber, isActive = false, isDisabled = false) => {
            const li = document.createElement('li');
            li.className = `page-item ${isActive ? 'active' : ''} ${isDisabled ? 'disabled' : ''}`;
            
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.innerHTML = text;
            
            if (isActive) {
                li.style.zIndex = '1';
                a.style.backgroundColor = '#d7820b';
                a.style.borderColor = '#d7820b';
                a.style.color = '#fff'; 
            } else {
                a.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (!isDisabled) {
                        fetchRoutes(pageNumber); 
                    }
                });
            }
            
            li.appendChild(a);
            return li;
        };

        const prevPage = currentPage > 1 ? currentPage - 1 : 1;
        paginationContainer.appendChild(createPageItem('Previous', prevPage, false, currentPage === 1));

        for (let i = 1; i <= totalPages; i++) {
            paginationContainer.appendChild(createPageItem(i, i, i === currentPage));
        }
        
        const nextPage = currentPage < totalPages ? currentPage + 1 : totalPages;
        paginationContainer.appendChild(createPageItem('Next', nextPage, false, currentPage === totalPages));
    }
</script>
</body>
</html>