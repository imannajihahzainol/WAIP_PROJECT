// server.js
const express = require('express');
const cors = require('cors');
const dotenv = require('dotenv');

// Load environment variables from .env file
dotenv.config();

// Import Database Connection (db)
const db = require('./src/db'); 

// Import all API Modules
const { verifyToken, register, login } = require('./src/auth');
const { requireAdmin, createRouteAndSchedules, deleteRoute, generateReportSummary } = require('./src/admin');
const { bookTicket, getBookingHistory, getBookingDetail } = require('./src/booking');

// --- APP SETUP ---
const app = express();
const PORT = 3000;

// Middleware
app.use(cors()); // Fixes the CORS issue for frontend testing
app.use(express.json()); // Allows parsing JSON body from requests

// --- PUBLIC ROUTES (No Auth Required) ---

// Test Route: Check database connection status
app.get('/api/test-db', async (req, res) => {
    try {
        await db.query('SELECT 1 + 1 AS solution'); 
        res.json({ message: 'Connected to MySQL successfully!' });
    } catch (error) {
        console.error('Database connection error:', error);
        res.status(500).json({ message: 'Database connection failed. Check your MySQL server status and .env file.', error: error.message });
    }
});

// User/Admin Registration and Login (Feature 1)
app.post('/api/auth/register', register);
app.post('/api/auth/login', login);

// Public Schedules (for Explore Routes Page)
// Uses existing logic in src/admin.js (getRoutes) since it queries all routes.
app.get('/api/public/routes', (req, res) => require('./src/admin').getRoutes(req, res)); // Using direct function import
app.get('/api/public/schedules/:routeId', (req, res) => require('./src/booking').getSchedulesByRoute(req, res)); // Using logic from booking module


// --- PROTECTED ROUTES (Admin Access Required) ---

// Route & Schedule Management (Feature 2)
app.post('/api/admin/routes', verifyToken, requireAdmin, createRouteAndSchedules);
app.delete('/api/admin/routes/:routeId', verifyToken, requireAdmin, deleteRoute);

// Reporting (Feature 4)
app.get('/api/admin/reports/summary', verifyToken, requireAdmin, generateReportSummary);


// --- PROTECTED ROUTES (Customer/User Access Required) ---

// Booking (Feature 3)
app.post('/api/user/book', verifyToken, bookTicket);

// Booking History
app.get('/api/user/history', verifyToken, getBookingHistory);
app.get('/api/user/booking/:bookingId', verifyToken, getBookingDetail);


// --- SERVER START ---
app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
    console.log(`Test the database connection at: http://localhost:${PORT}/api/test-db`);
});
// List users who booked a specific route
app.get('/api/admin/reports/route-users/:routeId', verifyToken, requireAdmin, (req, res) => require('./src/admin').getUsersByRoute(req, res));