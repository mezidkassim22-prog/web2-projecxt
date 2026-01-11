<?php
session_start();
include "../config/db.php";

// User access check
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}
include "../includes/header.php";
$user_id = $_SESSION['user']['id'];

// Handle search
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM books 
              WHERE title LIKE '%$search%' OR author LIKE '%$search%' OR category LIKE '%$search%'";
} else {
    $query = "SELECT * FROM books";
}

$result = mysqli_query($conn, $query);

// Handle borrow request
if (isset($_GET['request']) && isset($_GET['book_id'])) {
    $book_id = intval($_GET['book_id']);

    // Check if quantity > 0
    $book_check = mysqli_query($conn, "SELECT quantity FROM books WHERE id=$book_id");
    $book_data = mysqli_fetch_assoc($book_check);

    if ($book_data['quantity'] > 0) {
        $issue_date = date('Y-m-d');

        // Insert into issued_books
        mysqli_query($conn, "INSERT INTO issued_books (book_id, user_id, issue_date) VALUES ($book_id, $user_id, '$issue_date')");

        // Reduce quantity
        mysqli_query($conn, "UPDATE books SET quantity = quantity - 1 WHERE id=$book_id");

        echo "Book requested successfully!";
    } else {
        echo "Sorry, this book is out of stock.";
    }
}
?>

<h2>Search & Browse Books</h2>

<form method="GET">
    <input type="text" name="search" placeholder="Search by title, author, category" value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<br>

<table border="1" cellpadding="10">
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Category</th>
        <th>Quantity</th>
        <th>Action</th>
    </tr>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($book = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($book['title']); ?></td>
            <td><?php echo htmlspecialchars($book['author']); ?></td>
            <td><?php echo htmlspecialchars($book['category']); ?></td>
            <td><?php echo $book['quantity']; ?></td>
            <td>
                <?php if ($book['quantity'] > 0): ?>
                    <a href="?request=1&book_id=<?php echo $book['id']; ?>">Request to Borrow</a>
                <?php else: ?>
                    Out of stock
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">No books found.</td>
        </tr>
    <?php endif; ?>
</table>

<br>
<a href="dashboard.php">← Back to Dashboard</a>
