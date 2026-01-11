<?php
session_start();
include "../config/db.php";

// Admin only
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
include "../includes/header.php";
// Get book ID
if (!isset($_GET['id'])) {
    echo "Book ID missing.";
    exit();
}
$book_id = $_GET['id'];

// Fetch book details
$result = mysqli_query($conn, "SELECT * FROM books WHERE id=$book_id");
$book = mysqli_fetch_assoc($result);

if (!$book) {
    echo "Book not found.";
    exit();
}

// Handle form submission
if (isset($_POST['update_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $isbn = $_POST['isbn'];
    $quantity = $_POST['quantity'];

    mysqli_query($conn, "
        UPDATE books 
        SET title='$title', author='$author', category='$category', isbn='$isbn', quantity=$quantity
        WHERE id=$book_id
    ");

    echo "Book updated successfully! <a href='manage_books.php'>Back to Manage Books</a>";
    exit();
}
?>

<h2>Edit Book: <?php echo htmlspecialchars($book['title']); ?></h2>

<form method="POST">
    <input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required><br><br>
    <input type="text" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required><br><br>
    <input type="text" name="category" value="<?php echo htmlspecialchars($book['category']); ?>" required><br><br>
    <input type="text" name="isbn" value="<?php echo htmlspecialchars($book['isbn']); ?>" required><br><br>
    <input type="number" name="quantity" value="<?php echo $book['quantity']; ?>" required min="1"><br><br>
    <button name="update_book">Update Book</button>
</form>

<br>
<a href="manage_books.php">← Back to Manage Books</a>
