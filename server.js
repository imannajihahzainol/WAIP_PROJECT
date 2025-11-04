// server.js

require('dotenv').config(); // Load environment variables from .env file
const express = require('express');
const db = require('./src/db'); // MySQL connection pool
const auth = require('./src/auth'); // Authentication module (register, login, requireAdmin)

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware to parse JSON bodies
app.use(express.json());

// --- Authentication Routes (Feature 1) ---

// POST /api/auth/register: Registers a new user or admin
app.post('/api/auth/register', auth.register);

// POST /api/auth/login: Logs in user/admin and returns a JWT token
app.post('/api/auth/login', auth.login);


// --- Protected Route Example (Role-Based Access Control) ---

// Example Admin Protected Route: Only Admins can access this.
app.get('/api/admin/test-protected', auth.verifyToken, auth.requireAdmin, (req, res) => {
    // If the request reaches here, the user is guaranteed to be an Admin
    res.json({
        message: 'ADMIN ACCESS GRANTED: This endpoint is secured.',
        user: req.user // Contains user ID and role ('admin')
    });
});


// --- Health Check / Database Connection Test ---

app.get('/api/test-db', async (req, res) => {
    try {
        // Use a simple query to test the connection (e.g., getting the current time)
        await db.query('SELECT 1 + 1 AS solution');
        res.json({ message: 'Connected to MySQL successfully!' });
    } catch (error) {
        console.error('Database connection failed:', error.message);
        res.status(500).json({ 
            message: 'Database connection failed. Check your MySQL server status and .env file.', 
            error: error.message 
        });
    }
});


// --- Start Server ---

app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
    console.log(`Test the database connection at: http://localhost:${PORT}/api/test-db`);
});
