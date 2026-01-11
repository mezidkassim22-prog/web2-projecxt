<?php
session_start();
include "../config/db.php";

// Admin access check
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
include "../includes/header.php";
// Fetch all books
$result = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Books</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h2>Manage Books</h2>

<a href="dashboard.php">← Back to Dashboard</a>
<br><br>

<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Author</th>
        <th>Category</th>
        <th>Quantity</th>
        <th>Actions</th>
    </tr>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($book = mysqli_fetch_assoc($result)): ?>
        <tr>
    <td><?php echo $book['id']; ?></td>

    <td>
        <?php echo htmlspecialchars($book['title']); ?><br>
        <?php if (!empty($book['file_path'])): ?>
            <a href="../<?php echo $book['file_path']; ?>" target="_blank">
                View / Download
            </a>
        <?php else: ?>
            No file
        <?php endif; ?>
    </td>

    <td><?php echo htmlspecialchars($book['author']); ?></td>
    <td><?php echo htmlspecialchars($book['category']); ?></td>
    <td><?php echo $book['quantity']; ?></td>

    <td>
        <?php if ($book['quantity'] > 0): ?>
            <a href="issue_book.php?book_id=<?php echo $book['id']; ?>&user_id=1">
                Issue
            </a>
        <?php else: ?>
            Out of stock
        <?php endif; ?>

        | <a href="edit_book.php?id=<?php echo $book['id']; ?>">Edit</a>
        | <a href="delete_book.php?id=<?php echo $book['id']; ?>"
             onclick="return confirm('Are you sure you want to delete this book?');">
             Delete
          </a>
    </td>
</tr>

        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">No books found.</td>
        </tr>
    <?php endif; ?>

</table>

</body>
</html>
