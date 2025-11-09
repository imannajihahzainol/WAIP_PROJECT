// src/admin.js

const db = require('./db');

// --- Helper Middleware for RBAC (Role-Based Access Control) ---

/**
 * Ensures the authenticated user has the role 'admin'.
 */
function requireAdmin(req, res, next) {
    if (req.user && req.user.role === 'admin') {
        next(); // User is an admin, continue
    } else {
        res.status(403).json({ message: 'Forbidden: Admin access required.' });
    }
}

// --- Route Management Handlers (Feature 2) ---

/**
 * [ROUTE]: POST /api/admin/routes
 * Creates a new bus route and related schedules in a single transaction.
 */
async function createRouteAndSchedules(req, res) {
    const { routeName, routeDesc, schedules } = req.body;
    const adminId = req.user.id;
    
    if (!routeName || !schedules || schedules.length === 0) {
        return res.status(400).json({ message: 'Route name and schedule details are required.' });
    }

    const connection = await db.getConnection();
    await connection.beginTransaction();

    try {
        // 1. Create Route
        const routeSql = `INSERT INTO ROUTES (route_name, route_desc, created_by, created_at) VALUES (?, ?, ?, NOW())`;
        const [routeResult] = await connection.query(routeSql, [routeName, routeDesc, adminId]);
        const routeId = routeResult.insertId;
        
        // 2. Create Schedules
        // Note: Uses the newly added 'depart_date' column.
        const scheduleSql = `
            INSERT INTO SCHEDULES 
            (route_id, depart_date, depart_time, price, max_seats, available_seats, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        `;
        
        for (const slot of schedules) {
            // Basic validation
            if (!slot.depart_date || !slot.depart_time || typeof slot.max_seats !== 'number' || slot.max_seats <= 0) {
                await connection.rollback();
                return res.status(400).json({ message: 'Invalid schedule slot data provided.' });
            }

            await connection.query(scheduleSql, [
                routeId,
                slot.depart_date, 
                slot.depart_time,
                slot.price,
                slot.max_seats,
                slot.max_seats // initial available_seats equals max_seats
            ]);
        }

        await connection.commit();
        res.status(201).json({
            message: 'Route and schedules created successfully.',
            route_id: routeId,
            schedules_count: schedules.length
        });

    } catch (error) {
        await connection.rollback();
        console.error('Transaction failed during route/schedule creation:', error);
        
        if (error.code === 'ER_DUP_ENTRY') {
             return res.status(409).json({ message: 'Route name already exists.' });
        }
        res.status(500).json({ message: 'Server error during schedule creation.' });
    } finally {
        connection.release();
    }
}

/**
 * [ROUTE]: DELETE /api/admin/routes/:routeId
 * Deletes a route and all related schedules and bookings (Feature 2).
 */
async function deleteRoute(req, res) {
    const { routeId } = req.params;
    
    const connection = await db.getConnection();
    await connection.beginTransaction();

    try {
        // 1. Find all schedules for this route
        const [schedules] = await connection.query(`SELECT schedule_id FROM SCHEDULES WHERE route_id = ?`, [routeId]);
        const scheduleIds = schedules.map(s => s.schedule_id);

        if (scheduleIds.length > 0) {
            // 2. Delete all bookings related to those schedules
            const placeholders = scheduleIds.map(() => '?').join(',');
            await connection.query(`DELETE FROM BOOKINGS WHERE schedule_id IN (${placeholders})`, scheduleIds);
            
            // 3. Delete all schedules for this route
            await connection.query(`DELETE FROM SCHEDULES WHERE route_id = ?`, [routeId]);
        }

        // 4. Delete the route itself
        const [routeResult] = await connection.query(`DELETE FROM ROUTES WHERE route_id = ?`, [routeId]);

        if (routeResult.affectedRows === 0) {
            await connection.rollback();
            return res.status(404).json({ message: 'Route not found or already deleted.' });
        }
        
        await connection.commit();
        res.json({ message: 'Route, schedules, and associated bookings deleted successfully.' });

    } catch (error) {
        await connection.rollback();
        console.error('Transaction failed during route deletion:', error);
        res.status(500).json({ message: 'Server error during deletion.' });
    } finally {
        connection.release();
    }
}

/**
 * [ROUTE]: GET /api/admin/reports/summary
 * Generates a complete report dataset for the Admin Dashboard (Feature 4).
 */
async function generateReportSummary(req, res) {
    try {
        // --- 1. Total Tickets Booked per Route & Most Preferred Time ---
        const [bookingSummary] = await db.query(`
            SELECT 
                r.route_name,
                r.route_id,
                COUNT(b.booking_id) AS total_bookings,
                s.depart_date,
                s.depart_time,
                COUNT(b.booking_id) AS slot_bookings
            FROM BOOKINGS b
            JOIN SCHEDULES s ON b.schedule_id = s.schedule_id
            JOIN ROUTES r ON s.route_id = r.route_id
            GROUP BY r.route_id, r.route_name, s.depart_date, s.depart_time
            ORDER BY total_bookings DESC
        `);

        // --- 2. List of Users Who Booked a Specific Route (Example Detail) ---
        const topRouteId = bookingSummary.length > 0 ? bookingSummary[0].route_id : null;
        let topRouteUsers = [];

        if (topRouteId) {
            [topRouteUsers] = await db.query(`
                SELECT 
                    c.customer_username, 
                    c.customer_email,
                    r.route_name
                FROM BOOKINGS b
                JOIN CUSTOMER c ON b.user_id = c.customer_id
                JOIN SCHEDULES s ON b.schedule_id = s.schedule_id
                JOIN ROUTES r ON s.route_id = r.route_id
                WHERE r.route_id = ?
                GROUP BY c.customer_id, r.route_name
            `, [topRouteId]);
        }
        
        // --- 3. Aggregate Total Stats for Dashboard Cards ---
        const [totalBookings] = await db.query(`SELECT COUNT(*) AS total FROM BOOKINGS`);
        const [totalRoutes] = await db.query(`SELECT COUNT(*) AS total FROM ROUTES`);
        const [totalCustomers] = await db.query(`SELECT COUNT(*) AS total FROM CUSTOMER`);


        // --- 4. Find the Most Preferred Time Slot (Business Logic) ---
        let mostPreferredTimeSlot = null;
        if (bookingSummary.length > 0) {
            mostPreferredTimeSlot = bookingSummary.reduce((maxSlot, currentSlot) => {
                return currentSlot.slot_bookings > maxSlot.slot_bookings ? currentSlot : maxSlot;
            }, bookingSummary[0]);
        }

        res.json({
            // Card Data
            totals: {
                total_bookings: totalBookings[0].total,
                active_routes: totalRoutes[0].total,
                new_users: totalCustomers[0].total // Simplified metric
            },
            // Graph/Visualization Data (Tickets Booked Per Route)
            route_summary: bookingSummary,
            // Highlight Data
            most_preferred_time: mostPreferredTimeSlot ? 
                `${mostPreferredTimeSlot.route_name} on ${mostPreferredTimeSlot.depart_date} at ${mostPreferredTimeSlot.depart_time}` : 
                'No bookings yet.',
            // User List Data
            top_route_users: topRouteUsers
        });

    } catch (error) {
        console.error('Database Error during report generation:', error);
        res.status(500).json({ message: 'Server error generating reports.' });
    }
}


module.exports = {
    requireAdmin,
    createRouteAndSchedules,
    deleteRoute,
    generateReportSummary,
};
