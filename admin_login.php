<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BTB | Admin Login</title>

    <!-- Bootsrap Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Bootsrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">

    <!-- External CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
</head>
<body class="login-page d-flex justify-content-center align-items-center vh-100">

    <div class="container">
        <div class="login-card p-4 mx-auto shadow-lg bg-white">
            
            <div class="text-center mb-4">
                <img src="assets/img/btb_logo.png" alt="BTB Logo" class="mb-2 login-logo" style="height: 50px;">
                <h4 class="fw-bold">Administrator Access</h4>
                <p class="text-muted small">Log in to manage routes and bookings.</p>
            </div>
            
            <!-- Error Message Placeholder -->
            <div id="loginErrorMessage" class="alert alert-danger d-none" role="alert"></div>
            <form action="/api/auth/login" method="POST" id="adminLoginForm">
                
                <div class="mb-3">
                    <label for="adminUsername" class="form-label visually-hidden">Admin Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-person-gear custom-icon"></i></span>
                        <input type="text" class="form-control border-start-0" id="adminUsername" name="username" placeholder="Admin Username" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="adminPassword" class="form-label visually-hidden">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock custom-icon"></i></span>
                        <input type="password" class="form-control border-start-0" id="adminPassword" name="password" placeholder="Password" required>
                    </div>
                </div>

                <button type="submit" id="loginButton" class="btn btn-warning w-100 text-white fw-semibold mb-3">
                    <span id="buttonText"><i class="bi bi-shield-lock me-2"></i>Admin Login</span>
                    <span id="loadingSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </form>

            <div class="text-center mt-3 small">
                <p class="mb-0 text-secondary">
                    Not an admin? <a href="user_login.php" class="text-decoration-none fw-semibold" style="color: #d7820b;">Return to user login.</a>
                </p>
            </div>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const API_BASE_URL = 'http://localhost/WAIP_PROJECT'; 
    
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('adminLoginForm');
        const errorDiv = document.getElementById('loginErrorMessage');
        const loginButton = document.getElementById('loginButton');
        const buttonText = document.getElementById('buttonText');
        const loadingSpinner = document.getElementById('loadingSpinner');

        function showLoading(isLoading) {
            loginButton.disabled = isLoading;
            buttonText.classList.toggle('d-none', isLoading);
            loadingSpinner.classList.toggle('d-none', !isLoading);
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault(); 
            errorDiv.classList.add('d-none');
            errorDiv.textContent = '';
            showLoading(true);

            const username = form.username.value;
            const password = form.password.value;
            const payload = { username, password };

            try {
                const response = await fetch(`${API_BASE_URL}/api/admin_login.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload) 
                });

                const data = await response.json();
                if (data.success) {
                    window.location.href = 'admin.php';
                } else {
                    throw new Error(data.message || 'Login failed. Please check credentials.');
                }
            } catch (error) {
                errorDiv.textContent = error.message.includes('fetch') ? 'Could not connect to server or API path is wrong.' : error.message;
                errorDiv.classList.remove('d-none');
            } finally {
                showLoading(false);
            }
        });
    });
</script>
</body>
</html>