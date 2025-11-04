// src/db.js

const mysql = require('mysql2');
const path = require('path');

// Ensure dotenv config is loaded globally
require('dotenv').config({ path: path.resolve(__dirname, '..', '.env') });

// Configuration uses environment variables loaded from .env
const pool = mysql.createPool({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE,
    port: process.env.DB_PORT,
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

// IMPORTANT FIX: Export the promise-wrapped pool.
// This allows you to use `await db.query(...)` everywhere.
module.exports = pool.promise();
