<?php
// PHP Session Start and Status Check - MUST be at the very top
session_start();
// Check if user is logged in
$is_logged_in = isset($_SESSION['customer_logged_in']) && $_SESSION['customer_logged_in'] === true;

$customer_id = $_SESSION['customer_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BTB | Home Page</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <header>
    <nav class="navbar navbar-expand-lg navbar-light bg-custom shadow-sm">
      <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-logo" href="main.php">
          <img src="assets/img/btb_logo.png" alt="BTB Logo" class="banner-logo">
        </a>

        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link active" href="main.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="routes.php">Routes</a></li>
            <li class="nav-item"><a class="nav-link" href="booking.php">Book Ticket</a></li>
          </ul>
        </div>

        <div class="dropdown" id="accountDropdown">
            <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="accountMenu" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-4 me-1"></i> 
                <?php echo $is_logged_in ? 'My Account' : 'Guest'; ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountMenu">
                <?php if ($is_logged_in): ?>
                    <li><a class="dropdown-item" href="booking_history.php">Booking History</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="api/user_logout.php">Log Out</a></li> 
                <?php else: ?>
                    <li><a class="dropdown-item" href="user_login.php">Log In / Sign Up</a></li> 
                <?php endif; ?>
            </ul>
        </div>
        <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>
    </nav>
  </header>

  <main>
    <section class="banner d-flex align-items-center text-center text-white">
      <div class="container position-relative">
        <div class="banner-content">
          <h1 class="fw-bold">Book Your Bus Tickets Easily</h1>
          <p class="lead">Find and book the best routes in Malaysia</p>

          <div class="search-bar mx-auto mt-4 p-3 rounded-4 shadow">
            <form id="searchForm"> 
              <div class="row g-2 align-items-center">
                
                <div class="col-md-3">
                  <input type="text" class="form-control" name="from" placeholder="From (e.g., KL Sentral)" list="from-locations">
                  <datalist id="from-locations">
                    <option value="Johor Bahru"></option>
                    <option value="Seremban"></option>
                    <option value="Kuala Lumpur"></option>
                    <option value="Alor Gajah"></option>
                    <option value="Georgetown"></option>
                    <option value="Ipoh"></option>
                    <option value="Kl Sentral"></option>
                    <option value="Kuala Terengganu"></option>
                    <option value="Kuantan"></option>
                    <option value="Sg Petani"></option>
                    <option value="Pasir Mas"></option>
                    <option value="Seri Iskandar"></option>
                    <option value="Putrajaya"></option>
                    <option value="Pendang"></option>
                    <option value="Bentong"></option>
                    <option value="Port Dickson"></option>
                    <option value="Kangar"></option>
                    <option value="Alor Setar"></option>
                    <option value="Setiu"></option>
                  </datalist>
                </div>
                
                <div class="col-md-3">
                  <input type="text" class="form-control" name="to" id="toInput" placeholder="To (e.g., Johor Bahru)" list="to-locations">
                  <datalist id="to-locations">
                    <option value="Kuala Lumpur"></option>
                    <option value="Johor Bahru"></option>
                    <option value="Seremban"></option>
                    <option value="Ipoh"></option>
                    <option value="Alor Gajah"></option>
                    <option value="Georgetown"></option>
                    <option value="Kl Sentral"></option>
                    <option value="Kuala Terengganu"></option>
                    <option value="Kuantan"></option>
                    <option value="Sg Petani"></option>
                    <option value="Pasir Mas"></option>
                    <option value="Seri Iskandar"></option>
                    <option value="Putrajaya"></option>
                    <option value="Pendang"></option>
                    <option value="Bentong"></option>
                    <option value="Port Dickson"></option>
                    <option value="Kangar"></option>
                    <option value="Alor Setar"></option>
                    <option value="Setiu"></option>
                  </datalist>
                </div>
                
                <div class="col-md-2">
                  <input type="date" class="form-control" name="date" id="departDate" title="Departure Date">
                </div>
                <div class="col-md-2">
                  <input type="date" class="form-control" name="return_date" title="Return (optional)">
                </div>
                <div class="col-md-2">
                  <button type="submit" class="btn btn-warning w-100 text-white fw-semibold">
                    <i class="bi bi-search me-1"></i> Search Buses
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
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
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.getElementById('searchForm');
        const departDateInput = document.getElementById('departDate');
        const toInput = document.getElementById('toInput'); // Use ID for reliable access
        
        // --- Date Validation ---
        const today = new Date().toISOString().split('T')[0];
        if (departDateInput) {
            departDateInput.setAttribute('min', today);
        }
        
        // --- Search Submission Logic ---
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // 1. Validation: Ensure the 'To' field is filled
            if (toInput.value.trim() === '') {
                alert('Please enter a destination (To) to search for buses.');
                toInput.focus();
                return; // Stop the function if validation fails
            }

            const formData = new FormData(searchForm);
            
            const params = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                // Only include non-empty fields in the URL (excluding return date)
                if (value.trim() !== '' && key !== 'return_date') {
                    params.append(key, value.trim());
                }
            }

            // Redirect to the routes page with search parameters
            if (params.toString()) {
                window.location.href = `routes.php?${params.toString()}`;
            } else {
                window.location.href = `routes.php`;
            }
        });
    });
  </script>
</body>
</html>