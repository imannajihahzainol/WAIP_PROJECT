// src/auth.js

const db = require('./db');
const crypto = require('crypto');
const jwt = require('jsonwebtoken');

// WARNING: In a real-world app, use a secret key from .env
const JWT_SECRET = 'your_super_secret_key'; 

/**
 * Hashes a password using SHA-256 for storage in the database.
 * NOTE: For production, use bcrypt/scrypt. Using crypto.createHash for simplicity.
 */
function hashPassword(password) {
    // This is the function used to hash the password for comparison/storage
    return crypto.createHash('sha256').update(password).digest('hex');
}

/**
 * Generates a JSON Web Token (JWT) for authentication.
 */
function generateToken(id, role) {
    return jwt.sign({ id, role }, JWT_SECRET, { expiresIn: '1h' });
}

/**
 * Middleware to verify JWT and attach user info to the request.
 * This protects all our Admin and User endpoints.
 */
function verifyToken(req, res, next) {
    const authHeader = req.headers.authorization;

    if (!authHeader || !authHeader.startsWith('Bearer ')) {
        return res.status(401).json({ message: 'Access Denied. No token provided.' });
    }

    const token = authHeader.split(' ')[1];

    try {
        const decoded = jwt.verify(token, JWT_SECRET);
        req.user = decoded; // Attach user info (id, role) to the request
        next();
    } catch (ex) {
        res.status(400).json({ message: 'Invalid token.' });
    }
}


// --- API Handlers ---

/**
 * Handles User/Admin Registration (Feature 1).
 * Inserts user into ADMIN or CUSTOMER table based on role provided in body.
 */
async function register(req, res) {
    const { username, email, password, role } = req.body;
    const hashedPassword = hashPassword(password);
    
    // Determine target table and column names based on role
    const table = role === 'admin' ? 'ADMIN' : 'CUSTOMER';
    const usernameField = role === 'admin' ? 'admin_username' : 'customer_username';
    const emailField = role === 'admin' ? 'admin_email' : 'customer_email';
    const passwordField = role === 'admin' ? 'admin_password' : 'customer_password';

    if (!username || !email || !password || !['admin', 'customer'].includes(role)) {
        return res.status(400).json({ message: 'Missing required fields or invalid role.' });
    }

    const sql = `INSERT INTO ${table} (${usernameField}, ${emailField}, ${passwordField}, created_at) VALUES (?, ?, ?, NOW())`;

    try {
        const [result] = await db.query(sql, [username, email, hashedPassword]);
        
        // Return success
        res.status(201).json({ 
            message: `${role} registered successfully.`, 
            id: result.insertId 
        });
    } catch (error) {
        console.error(`Registration Error for ${role}:`, error);
        if (error.code === 'ER_DUP_ENTRY') {
            return res.status(409).json({ message: 'Username or email already exists.' });
        }
        res.status(500).json({ message: 'Server error during registration.' });
    }
}

/**
 * Handles User/Admin Login (Feature 1).
 * Verifies credentials and issues a JWT token.
 */
async function login(req, res) {
    const { username, password } = req.body;
    const hashedPassword = hashPassword(password);

    // Try finding the user in both ADMIN and CUSTOMER tables
    const sqlAdmin = `SELECT admin_id AS id, admin_username AS username, admin_password AS password FROM ADMIN WHERE admin_username = ? AND admin_password = ?`;
    const sqlCustomer = `SELECT customer_id AS id, customer_username AS username, customer_password AS password FROM CUSTOMER WHERE customer_username = ? AND customer_password = ?`;
    
    let user = null;
    let role = null;

    try {
        // 1. Check Admin Table
        const [adminRows] = await db.query(sqlAdmin, [username, hashedPassword]);
        if (adminRows.length > 0) {
            user = adminRows[0];
            role = 'admin';
        }

        // 2. Check Customer Table (only if not found in Admin)
        if (!user) {
            const [customerRows] = await db.query(sqlCustomer, [username, hashedPassword]);
            if (customerRows.length > 0) {
                user = customerRows[0];
                role = 'customer';
            }
        }
        
        if (!user) {
            return res.status(401).json({ message: 'Invalid username or password.' });
        }

        // 3. Issue Token
        const token = generateToken(user.id, role);

        res.json({ 
            message: `Login successful.`, 
            token, 
            role, 
            id: user.id 
        });

    } catch (error) {
        console.error('Login Error:', error);
        res.status(500).json({ message: 'Server error during login.' });
    }
}

module.exports = {
    register,
    login,
    verifyToken,
};