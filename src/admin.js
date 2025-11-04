const db = require('./db');

// --- Helper Middleware for RBAC ---

/**
 * Ensures the authenticated user has the 'admin' role.
 * Assumes req.user (containing user role) has been attached by verifyToken.
 */
function requireAdmin(req, res, next) {
    if (req.user && req.user.role === 'admin') {
        next(); // User is admin, proceed
    } else {
        res.status(403).json({ message: 'Forbidden: Admin access required.' });
    }
}

// --- Route Management Handlers ---

/**
 * [ROUTE]: POST /api/admin/routes
 * Creates a new bus route (e.g., KL to Penang).
 */
async function createRoute(req, res) {
    const { route_name, route_desc } = req.body;
    const admin_id = req.user.id; // User ID attached by verifyToken
    
    if (!route_name) {
        return res.status(400).json({ message: 'Route name is required.' });
    }

    const sql = `INSERT INTO ROUTES (route_name, route_desc, created_by, created_at) VALUES (?, ?, ?, NOW())`;

    try {
        const [result] = await db.query(sql, [route_name, route_desc, admin_id]);
        res.status(201).json({
            message: 'Route created successfully.',
            route: {
                route_id: result.insertId,
                route_name,
            }
        });
    } catch (error) {
        console.error('Database Error during route creation:', error);
        if (error.code === 'ER_DUP_ENTRY') {
             return res.status(409).json({ message: 'Route name already exists.' });
        }
        res.status(500).json({ message: 'Server error during route creation.' });
    }
}

/**
 * [ROUTE]: GET /api/admin/routes
 * Retrieves all available routes for admin view.
 */
async function getRoutes(req, res) {
    // Selects route information and the admin username who created it
    const sql = `
        SELECT 
            r.route_id, 
            r.route_name, 
            r.route_desc, 
            r.created_at,
            a.admin_username AS created_by_admin
        FROM ROUTES r
        JOIN ADMIN a ON r.created_by = a.admin_id
        ORDER BY r.route_name
    `;

    try {
        const [rows] = await db.query(sql);
        res.json(rows);
    } catch (error) {
        console.error('Database Error fetching routes:', error);
        res.status(500).json({ message: 'Server error fetching routes.' });
    }
}

// --- Schedule Management Handlers (Core Feature 2) ---

/**
 * [ROUTE]: POST /api/admin/schedules
 * Creates a new schedule for an existing route.
 * Note: available_seats is initially set equal to max_seats.
 */
async function createSchedule(req, res) {
    // Collects all data needed for the SCHEDULES table
    const { route_id, depart_time, max_seats } = req.body;

    // Validation
    if (!route_id || !depart_time || typeof max_seats !== 'number' || max_seats <= 0) {
        return res.status(400).json({ message: 'Missing or invalid route_id, depart_time, or max_seats.' });
    }

    // Ensure depart_time is a valid DATETIME string (e.g., 'YYYY-MM-DD HH:MM:SS')
    // We set available_seats = max_seats at creation.
    const sql = `
        INSERT INTO SCHEDULES 
        (route_id, depart_time, max_seats, available_seats, created_at) 
        VALUES (?, ?, ?, ?, NOW())
    `;

    try {
        // Use a SQL function or the provided value for initial available seats
        const [result] = await db.query(sql, [route_id, depart_time, max_seats, max_seats]);
        
        res.status(201).json({
            message: 'Schedule created successfully.',
            schedule: {
                schedule_id: result.insertId,
                route_id,
                depart_time,
                max_seats,
            }
        });
    } catch (error) {
        console.error('Database Error during schedule creation:', error);
        res.status(500).json({ message: 'Server error during schedule creation.' });
    }
}

// --- Module Export ---

module.exports = {
    requireAdmin,
    createRoute,
    getRoutes,
    createSchedule,
};
