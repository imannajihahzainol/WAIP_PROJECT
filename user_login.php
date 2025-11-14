<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BTB | User Login</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page d-flex justify-content-center align-items-center vh-100">

    <div class="container">
        <div class="login-card p-4 mx-auto shadow-lg">
            
            <div class="text-center mb-4">
                <img src="assets/img/btb_logo.png" alt="BTB Logo" style="height: 50px;" class="mb-2 login-logo">
                <h4 class="fw-bold">User Login</h4>
                <p class="text-muted small">Access your Bus Ticket Booking account</p>
            </div>

            <div id="loginMessage" class="alert d-none mt-3" role="alert"></div>

            <form id="userLoginForm" onsubmit="handleLogin(event)">
                
                <div class="mb-3">
                    <label for="username" class="form-label visually-hidden">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-person custom-icon"></i></span>
                        <input type="text" class="form-control border-start-0" id="username" name="username" placeholder="Enter your username or email" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label visually-hidden">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock custom-icon"></i></span>
                        <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                </div>

                <button type="submit" id="loginButton" class="btn btn-warning w-100 text-white fw-semibold mb-3">
                    <span id="buttonText"><i class="bi bi-box-arrow-in-right me-2"></i>Login</span>
                    <span id="loadingSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </form>

            <div class="text-center mt-3 small">
                <p class="mb-1 text-secondary">
                    New user? <a href="user_signup.php" class="text-decoration-none fw-semibold" style="color: #d7820b;">Create your account.</a>
                </p>
                <p class="mb-0 text-secondary">
                    Admin? <a href="admin_login.php" class="text-decoration-none fw-semibold" style="color: #d7820b;">Log in here.</a>
                </p>
            </div>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API_BASE_URL = 'http://localhost/WAIP_PROJECT'; 
        const form = document.getElementById('userLoginForm');
        const messageContainer = document.getElementById('loginMessage');
        const loginButton = document.getElementById('loginButton');
        const buttonText = document.getElementById('buttonText');
        const loadingSpinner = document.getElementById('loadingSpinner');

        function showLoading(isLoading) {
            loginButton.disabled = isLoading;
            buttonText.classList.toggle('d-none', isLoading);
            loadingSpinner.classList.toggle('d-none', !isLoading);
        }

        function showMessage(text, isError = false) {
            messageContainer.textContent = text;
            messageContainer.classList.remove('d-none', 'alert-success', 'alert-danger');
            messageContainer.classList.add(isError ? 'alert-danger' : 'alert-success');
        }

        async function handleLogin(event) {
            event.preventDefault();
            messageContainer.classList.add('d-none'); 
            showLoading(true);
            const username = form.username.value;
            const password = form.password.value;
            const payload = { username, password };

            try {
                const response = await fetch(`${API_BASE_URL}/api/user_login.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showMessage("Login successful! Redirecting to homepage...", false);
                    setTimeout(() => {
                        window.location.href = 'main.php'; 
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Login failed. Check your credentials.');
                }
            } catch (error) {
                showMessage(error.message.includes('fetch') ? 'Could not connect to the server.' : error.message, true);
            } finally {
                showLoading(false);
            }
        }
    </script>
</body>
</html>