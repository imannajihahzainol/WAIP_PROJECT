// src/booking.js

const db = require('./db');

// --- Helper Functions ---

/**
 * Executes the core booking logic using a transaction to prevent race conditions (overselling).
 * This function is critical for Feature 3.
 * @param {number} scheduleId
 * @param {number} userId
 * @param {number} numSeats - Number of seats being booked.
 */
async function executeBookingTransaction(scheduleId, userId, numSeats = 1) {
    const connection = await db.getConnection(); // Get a connection from the pool
    await connection.beginTransaction(); // Start the transaction

    try {
        // 1. Lock the SCHEDULES row and check seats
        // FOR UPDATE ensures no other transaction can modify this row until this one commits or rolls back
        const [scheduleRows] = await connection.query(
            `SELECT available_seats, route_id FROM SCHEDULES WHERE schedule_id = ? FOR UPDATE`,
            [scheduleId]
        );

        if (scheduleRows.length === 0) {
            await connection.rollback();
            return { success: false, message: 'Schedule not found.' };
        }

        const schedule = scheduleRows[0];
        if (schedule.available_seats < numSeats) {
            await connection.rollback();
            return { success: false, message: 'Not enough available seats.' };
        }

        // 2. Decrease available seats
        const newAvailableSeats = schedule.available_seats - numSeats;
        await connection.query(
            `UPDATE SCHEDULES SET available_seats = ? WHERE schedule_id = ?`,
            [newAvailableSeats, scheduleId]
        );

        // 3. Create the booking record (Feature 3)
        const [bookingResult] = await connection.query(
            `INSERT INTO BOOKINGS (user_id, schedule_id, booking_time, seat_num, booking_status) VALUES (?, ?, NOW(), ?, 'CONFIRMED')`,
            [userId, scheduleId, numSeats] // seat_num here refers to the number of seats booked
        );

        await connection.commit(); // Commit all changes if successful
        return {
            success: true,
            message: 'Ticket successfully booked.',
            bookingId: bookingResult.insertId
        };

    } catch (error) {
        await connection.rollback(); // Revert all changes on error
        console.error("Transaction failed:", error);
        return { success: false, message: 'Booking failed due to a server error or conflict.' };
    } finally {
        connection.release(); // Release the connection back to the pool
    }
}


// --- API Handlers (Protected - Requires User Login) ---

/**
 * [ROUTE]: POST /api/user/book
 * Handles the actual ticket booking process (Feature 3).
 */
async function bookTicket(req, res) {
    // req.user.id is attached by verifyToken middleware (from CUSTOMER table)
    const userId = req.user.id; 
    // Assuming the frontend sends scheduleId and number of seats
    const { schedule_id, num_seats } = req.body; 

    if (!schedule_id || typeof num_seats !== 'number' || num_seats <= 0) {
        return res.status(400).json({ message: 'Missing schedule or invalid seat count.' });
    }

    // Call the transaction executor
    const result = await executeBookingTransaction(schedule_id, userId, num_seats);

    if (result.success) {
        res.status(201).json(result);
    } else {
        res.status(409).json(result); // 409 Conflict for business logic errors (e.g., sold out)
    }
}

/**
 * [ROUTE]: GET /api/user/history
 * Retrieves the booking history for the logged-in user (for Booking History Page).
 */
async function getBookingHistory(req, res) {
    const userId = req.user.id; // Customer ID attached by verifyToken

    // Join Bookings with Schedules and Routes for clear history display
    const sql = `
        SELECT
            b.booking_id,
            b.booking_time,
            b.seat_num,
            b.booking_status,
            s.depart_time,
            r.route_name
        FROM BOOKINGS b
        JOIN SCHEDULES s ON b.schedule_id = s.schedule_id
        JOIN ROUTES r ON s.route_id = r.route_id
        WHERE b.user_id = ?
        ORDER BY b.booking_time DESC
    `;

    try {
        const [rows] = await db.query(sql, [userId]);
        res.json(rows);
    } catch (error) {
        console.error('Database Error fetching history:', error);
        res.status(500).json({ message: 'Server error fetching booking history.' });
    }
}

/**
 * [ROUTE]: GET /api/user/booking/:bookingId
 * Retrieves a specific booking detail.
 */
async function getBookingDetail(req, res) {
    const userId = req.user.id;
    const { bookingId } = req.params;

    const sql = `
        SELECT *
        FROM BOOKINGS b
        WHERE b.booking_id = ? AND b.user_id = ?
    `;

    try {
        const [rows] = await db.query(sql, [bookingId, userId]);
        if (rows.length === 0) {
            return res.status(404).json({ message: 'Booking not found.' });
        }
        res.json(rows[0]);
    } catch (error) {
        console.error('Database Error fetching booking detail:', error);
        res.status(500).json({ message: 'Server error.' });
    }
}


module.exports = {
    bookTicket,
    getBookingHistory,
    getBookingDetail
};
