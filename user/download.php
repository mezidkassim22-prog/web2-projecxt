<?php
session_start();
include "../config/db.php";

// Check if user is logged in and book_id is provided
if (!isset($_SESSION['user']) || !isset($_GET['book_id'])) {
    die("Access denied.");
}

$user_id = $_SESSION['user']['id'];
$book_id = intval($_GET['book_id']);

// Verify the user has actually been issued this book
$query = "
    SELECT b.file_path
    FROM issued_books ib
    JOIN books b ON ib.book_id = b.id
    WHERE ib.book_id = $book_id
      AND ib.user_id = $user_id
      AND ib.status = 'issued'
";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data || empty($data['file_path'])) {
    die("You are not allowed to download this book.");
}

$file = "../" . $data['file_path'];

if (!file_exists($file)) {
    die("File not found.");
}

// Serve the file for viewing or download
header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"" . basename($file) . "\"");
readfile($file);
exit();
?>
