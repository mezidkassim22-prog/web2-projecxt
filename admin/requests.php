<?php
session_start();
include "../config/db.php";

// Admin access only
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
include "../includes/header.php";
// --------------------------
// Approve a pending request
// --------------------------
if (isset($_GET['approve']) && isset($_GET['id'])) {
    $issued_id = intval($_GET['id']);

    if ($issued_id <= 0) {
        echo "Issued Book ID missing or invalid.";
        exit();
    }

    // Update issued_books: set status 'issued', return_date=NULL
    mysqli_query($conn, "UPDATE issued_books SET status='issued', return_date=NULL WHERE id=$issued_id");

    // Reduce book quantity
    $book_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT book_id FROM issued_books WHERE id=$issued_id"));
    if ($book_row) {
        $book_id = $book_row['book_id'];
        mysqli_query($conn, "UPDATE books SET quantity = quantity - 1 WHERE id=$book_id");
    }

    header("Location: requests.php");
    exit();
}

// --------------------------
// Mark book as returned
// --------------------------
if (isset($_GET['return']) && isset($_GET['id'])) {
    $issued_id = intval($_GET['id']);

    if ($issued_id <= 0) {
        echo "Issued Book ID missing or invalid.";
        exit();
    }

    $return_date = date('Y-m-d');

    // Update issued_books: status 'returned', set return_date
    mysqli_query($conn, "UPDATE issued_books SET status='returned', return_date='$return_date' WHERE id=$issued_id");

    // Increase book quantity
    $book_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT book_id FROM issued_books WHERE id=$issued_id"));
    if ($book_row) {
        $book_id = $book_row['book_id'];
        mysqli_query($conn, "UPDATE books SET quantity = quantity + 1 WHERE id=$book_id");
    }

    header("Location: requests.php");
    exit();
}

// --------------------------
// Fetch all issued books
// --------------------------
$result = mysqli_query($conn, "
    SELECT ib.id, b.title, u.name, ib.issue_date, ib.return_date, ib.status
    FROM issued_books ib
    JOIN books b ON ib.book_id = b.id
    JOIN users u ON ib.user_id = u.id
    ORDER BY ib.id DESC
");
?>

<h2>User Book Requests / Issued Books</h2>
<a href="dashboard.php">← Back to Dashboard</a><br><br>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Book</th>
    <th>User</th>
    <th>Issue Date</th>
    <th>Return Date</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo htmlspecialchars($row['title']); ?></td>
    <td><?php echo htmlspecialchars($row['name']); ?></td>
    <td><?php echo $row['issue_date']; ?></td>
    <td><?php echo $row['return_date'] ?? '-'; ?></td>
    <td><?php echo ucfirst($row['status']); ?></td>
    <td>
        <?php if ($row['status'] == 'pending'): ?>
            <a href="requests.php?approve=1&id=<?php echo $row['id']; ?>">Approve Request</a>
        <?php elseif ($row['status'] == 'issued'): ?>
            <a href="requests.php?return=1&id=<?php echo $row['id']; ?>">Mark as Returned</a>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
