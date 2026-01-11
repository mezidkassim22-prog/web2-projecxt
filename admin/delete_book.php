<?php
session_start();
include "../config/db.php";

// Admin access only
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get book ID from query
if (!isset($_GET['id'])) {
    echo "Book ID missing.";
    exit();
}

$book_id = $_GET['id'];

// Delete book from database
$result = mysqli_query($conn, "DELETE FROM books WHERE id=$book_id");

if ($result) {
    // Redirect back to Manage Books
    header("Location: manage_books.php");
    exit();
} else {
    echo "Error deleting book: " . mysqli_error($conn);
}
?>
