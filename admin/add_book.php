<?php
session_start();
include "../config/db.php";
include "../includes/header.php";
// Admin access only
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $isbn = $_POST['isbn'];
    $quantity = $_POST['quantity'];

    // Handle file upload
    if (isset($_FILES['book_file']) && $_FILES['book_file']['error'] === 0) {
        $filename = time() . "_" . $_FILES['book_file']['name'];
        $target = "../uploads/books/" . $filename;

        if (move_uploaded_file($_FILES['book_file']['tmp_name'], $target)) {
            $file_path = "uploads/books/" . $filename; // Save relative path
        } else {
            echo "File upload failed!";
            exit();
        }
    } else {
        $file_path = NULL; // No file uploaded
    }

    // Insert book into database
    $sql = "INSERT INTO books (title, author, category, isbn, quantity, file_path)
            VALUES ('$title', '$author', '$category', '$isbn', $quantity, '$file_path')";

    if (mysqli_query($conn, $sql)) {
        echo "Book added successfully! <a href='manage_books.php'>Back to Manage Books</a>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h2>Add New Book</h2>
<a href="dashboard.php">← Back to Dashboard</a>
<br><br>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Book Title" required><br><br>
    <input type="text" name="author" placeholder="Author" required><br><br>
    <input type="text" name="category" placeholder="Category" required><br><br>
    <input type="text" name="isbn" placeholder="ISBN" required><br><br>
    <input type="number" name="quantity" placeholder="Quantity" required min="1"><br><br>
    <input type="file" name="book_file" accept=".pdf"><br><br>
    <button type="submit" name="add_book">Add Book</button>
</form>

</body>
</html>
