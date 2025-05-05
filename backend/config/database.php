<?php
// Database configuration
$host = "127.0.0.1";
$db_name = "booknew";
$username = "root";
$password = "Euqificap12.";
$charset = "utf8mb4";


// DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Connect to database
try {
    $conn = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Display error message in development
    die("Connection failed: " . $e->getMessage());
    
    // In production, we would use a more generic error message
    // die("Sorry, there was a problem connecting to the database. Please try again later.");
}

// Create necessary tables if they don't exist
require_once __DIR__ . "/create_tables.php";


