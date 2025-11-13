<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BTB | User Sign Up</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* Placeholder for your external CSS login-page styling */
        .login-page {
            background-color: #5B21B6; /* Deep Purple, inferred from screenshot */
        }
        .login-card {
            max-width: 400px;
            border-radius: 1rem;
            border: none;
        }
        .custom-icon {
            color: #d7820b; /* Orange color for icons */
        }
        .btn-warning {
            background-color: #d7820b !important;
            border-color: #d7820b !important;
        }
        .btn-warning:hover {
            background-color: #c0730a !important;
            border-color: #c0730a !important;
        }
    </style>
</head>
<body class="login-page d-flex justify-content-center align-items-center vh-100">

    <div class="container">
        <div class="login-card p-4 mx-auto shadow-lg bg-white">
            
            <div class="text-center mb-4">
                <img src="assets/img/btb_logo.png" alt="BTB Logo" class="mb-2 login-logo" style="height: 50px;">
                <h4 class="fw-bold">Create Your Account</h4>
                <p class="text-muted small">Join us and start booking your bus tickets!</p>
            </div>
            
            <form id="signupForm" onsubmit="handleSignup(event)"> 
                
                <div class="mb-3">
                    <label for="fullName" class="form-label visually-hidden">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-person-badge custom-icon"></i></span>
                        <input type="text" class="form-control border-start-0" id="fullName" name="username" placeholder="Full Name (Username)" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label visually-hidden">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-envelope custom-icon"></i></span>
                        <input type="email" class="form-control border-start-0" id="email" name="email" placeholder="Email Address" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label visually-hidden">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock custom-icon"></i></span>
                        <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="Password" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="confirmPassword" class="form-label visually-hidden">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-check-circle custom-icon"></i></span>
                        <input type="password" class="form-control border-start-0" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
                    </div>
                </div>
                
                <div id="messageContainer" class="mb-3 text-center small fw-semibold" style="height: 20px;"></div>

                <button type="submit" id="signupButton" class="btn btn-warning w-100 text-white fw-semibold mb-3">
                    <span id="buttonText"><i class="bi bi-person-plus-fill me-2"></i>Sign Up</span>
                    <span id="loadingSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </form>

            <div class="text-center mt-3 small">
                <p class="small text-secondary">
                    Already have an account? <a href="user_login.php" class="text-decoration-none fw-semibold" style="color: #d7820b;">Login here</a>
                </p>
            </div>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // --- REVISED: Use XAMPP path ---
        const API_BASE_URL = 'http://localhost/WAIP_PROJECT'; 
        const form = document.getElementById('signupForm');
        const messageContainer = document.getElementById('messageContainer');
        const signupButton = document.getElementById('signupButton');
        const buttonText = document.getElementById('buttonText');
        const loadingSpinner = document.getElementById('loadingSpinner');

        function showLoading(isLoading) {
            signupButton.disabled = isLoading;
            buttonText.classList.toggle('d-none', isLoading);
            loadingSpinner.classList.toggle('d-none', !isLoading);
        }

        function showMessage(text, isError = false) {
            messageContainer.textContent = text;
            messageContainer.className = 'mb-3 text-center small fw-semibold';
            messageContainer.classList.add(isError ? 'text-danger' : 'text-success');
        }

        async function handleSignup(event) {
            event.preventDefault();
            messageContainer.textContent = ''; 
            
            const username = form.username.value.trim();
            const email = form.email.value.trim();
            const password = form.password.value;
            const confirmPassword = form.confirmPassword.value;

            // 1. Client-Side Validation
            if (password !== confirmPassword) {
                return showMessage("Passwords do not match.", true);
            }
            if (password.length < 5) {
                return showMessage("Password must be at least 5 characters.", true);
            }

            // 2. Prepare Payload
            const payload = {
                username: username,
                email: email,
                password: password,
                // Role is set implicitly by using the customer table
            };
            
            showLoading(true);

            // 3. API Call to Backend
            try {
                // --- REVISED: Use correct PHP endpoint ---
                const response = await fetch(`${API_BASE_URL}/api/register_user.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    // Send data as JSON, which the PHP script is configured to read
                    body: JSON.stringify(payload) 
                });
                
                const data = await response.json();

                if (response.ok && data.success) { // Check both response status and custom 'success' flag
                    showMessage("Registration successful! Redirecting to login...", false);
                    
                    setTimeout(() => {
                        window.location.href = 'user_login.php';
                    }, 1500);

                } else {
                    // Handle errors from the PHP script
                    const errorMessage = data.message || 'Registration failed due to server error.';
                    showMessage(errorMessage, true);
                }

            } catch (error) {
                console.error('Network Error:', error);
                showMessage('Could not connect to the server. Check your XAMPP server.', true);
            } finally {
                showLoading(false);
            }
        }
    </script>
</body>
</html>