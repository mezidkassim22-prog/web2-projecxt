<?php
// Show all errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include "config/db.php";

// Test connection
if ($conn) {
    echo "Database connected successfully!";
} else {
    echo "Connection failed: " . mysqli_connect_error();
}
?>
