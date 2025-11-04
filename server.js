const express = require('express');
const cors = require('cors'); // Required to allow HTML file to talk to the server
const dotenv = require('dotenv');
const db = require('./src/db'); // MySQL connection pool
const { register, login, verifyToken } = require('./src/auth'); // Authentication logic
const { createRoute, getRoutes, createSchedule, requireAdmin } = require('./src/admin'); // Admin logic

// Load environment variables from .env file
dotenv.config();

const app = express();
const PORT = 3000;

// Middleware setup
// 1. CORS: Allows requests from the local file system (for testing)
app.use(cors());

// 2. Body Parser: Allows Express to read JSON data from requests
app.use(express.json());

// --- PUBLIC ROUTES ---

// POST /api/auth/register: Registers a new user or admin (Feature 1)
app.post('/api/auth/register', register);

// POST /api/auth/login: Logs in a user/admin and issues a JWT token
app.post('/api/auth/login', login);

// GET /api/test-db: Checks connection to the database
app.get('/api/test-db', async (req, res) => {
    try {
        // Simple query to verify database connection and credentials
        await db.query('SELECT 1 + 1 AS solution');
        res.json({ message: 'Connected to MySQL successfully!' });
    } catch (error) {
        console.error('Database connection failed:', error);
        res.status(500).json({ 
            message: 'Database connection failed. Check your MySQL server status and .env file.',
            error: error.message 
        });
    }
});

// --- PROTECTED ADMIN ROUTES (Require verifyToken and requireAdmin middleware) ---

// Route Protection Middleware: All routes below this line require authentication
// The verifyToken middleware attaches req.user (id, role)
app.use(verifyToken); 

// POST /api/admin/routes: Creates a new route (e.g., KL to Ipoh)
app.post('/api/admin/routes', requireAdmin, createRoute);

// GET /api/admin/routes: Retrieves list of all routes (for Admin dashboard display)
app.get('/api/admin/routes', requireAdmin, getRoutes);

// POST /api/admin/schedules: Creates a new schedule for an existing route (Core Feature 2)
app.post('/api/admin/schedules', requireAdmin, createSchedule);


// --- Server Initialization ---

app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
    console.log(`Test the database connection at: http://localhost:${PORT}/api/test-db`);
});
