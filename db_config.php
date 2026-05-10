<?php
/**
 * Database Configuration File
 * This file handles the connection between PHP and the MySQL database.
 */

$host = "localhost";      // Usually 'localhost' for XAMPP
$username = "root";       // Default XAMPP MySQL username
$password = "";           // Default XAMPP MySQL password is empty
$dbname = "student_management"; // The name of the database we created

// Create a new connection object using the MySQLi library
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    // If connection fails, stop the script and show the error
    die("Connection failed: " . $conn->connect_error);
}
?>
