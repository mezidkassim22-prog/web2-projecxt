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
if (!isset($_GET['book_id'])) {
    echo "Book ID missing.";
    exit();
}

$book_id = intval($_GET['book_id']);

// Fetch book info
$book_result = mysqli_query($conn, "SELECT * FROM books WHERE id=$book_id");
$book = mysqli_fetch_assoc($book_result);

if (!$book) {
    echo "Book not found.";
    exit();
}

// Fetch all users (exclude admin if you want)
$user_result = mysqli_query($conn, "SELECT * FROM users ORDER BY name ASC");

// Handle form submission
if (isset($_POST['issue'])) {

    $user_id = intval($_POST['user_id']);
    $issue_date = date('Y-m-d');

    // Check book availability
    if ($book['quantity'] <= 0) {
        echo "Book is out of stock.";
        exit();
    }

    // Insert issued record
    mysqli_query($conn, "
        INSERT INTO issued_books (book_id, user_id, issue_date, status)
        VALUES ($book_id, $user_id, '$issue_date', 'issued')
    ");

    // Reduce quantity
    mysqli_query($conn, "
        UPDATE books SET quantity = quantity - 1 WHERE id=$book_id
    ");

    header("Location: manage_books.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Issue Book</title>
</head>
<body>

<h2>Issue Book</h2>

<p>
<strong>Title:</strong> <?php echo htmlspecialchars($book['title']); ?><br>
<strong>Available:</strong> <?php echo $book['quantity']; ?>
</p>

<form method="POST">
    <label>Select User:</label><br>
    <select name="user_id" required>
        <option value="">-- Select User --</option>
        <?php while ($user = mysqli_fetch_assoc($user_result)): ?>
            <option value="<?php echo $user['id']; ?>">
                <?php echo htmlspecialchars($user['name']); ?> (<?php echo $user['role']; ?>)
            </option>
        <?php endwhile; ?>
    </select>
    <br><br>

    <button type="submit" name="issue">Issue Book</button>
</form>

<br>
<a href="manage_books.php">← Back to Manage Books</a>

</body>
</html>
            