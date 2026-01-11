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

// Fetch total books available
$total_books_query = mysqli_query($conn, "SELECT SUM(quantity) as total FROM books");
$total_books = mysqli_fetch_assoc($total_books_query)['total'] ?? 0;

// Fetch issued books for this user
$issued_books_query = mysqli_query($conn, "
    SELECT b.title, b.author, b.category, ib.issue_date, ib.return_date, ib.status
    FROM issued_books ib
    JOIN books b ON ib.book_id = b.id
    WHERE ib.user_id = $user_id
    ORDER BY ib.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h1>User Dashboard</h1>
<p>
Welcome to the library system.<br>
Here you can view your issued books and download them.<br>
Please return books on time.
</p>
<p>Welcome, <?php echo $_SESSION['user']['name']; ?> (Role: <?php echo $_SESSION['user']['role']; ?>)</p>
<nav>
    <a href="search_books.php">Search Books</a> |
    <a href="../auth/logout.php">Logout</a>
    <a href="my_issued_books.php">My Issued Books</a>

</nav>
<hr>
<?php include "../includes/footer.php"; ?>
<h2>Library Info</h2>
<p>Total Books Available: <?php echo $total_books; ?></p>

<h2>My Issued Books</h2>
<table border="1" cellpadding="10">
    <tr>
        <th>Book</th>
        <th>Author</th>
        <th>Category</th>
        <th>Issue Date</th>
        <th>Return Date</th>
        <th>Status</th>
    </tr>

    <?php if (mysqli_num_rows($issued_books_query) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($issued_books_query)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['author']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo $row['issue_date']; ?></td>
            <td><?php echo $row['return_date'] ?? '-'; ?></td>
            <td><?php echo $row['status']; ?></td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">You have no issued books.</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>
