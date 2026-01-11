<?php
session_start();
include "../config/db.php";

// Admin access only
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Issued Book ID missing.";
    exit();
}

$issued_id = $_GET['id'];
$return_date = date('Y-m-d');

// Update issued_books
mysqli_query($conn, "UPDATE issued_books 
                     SET status='returned', return_date='$return_date'
                     WHERE id=$issued_id");

// Increase book quantity
$book_id = mysqli_fetch_assoc(mysqli_query($conn, "SELECT book_id FROM issued_books WHERE id=$issued_id"))['book_id'];
mysqli_query($conn, "UPDATE books SET quantity = quantity + 1 WHERE id=$book_id");

header("Location: issued_books.php");
exit();
?>
