<?php
session_start();
include "../config/db.php";

// User must be logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}
include "../includes/header.php";
$user_id = $_SESSION['user']['id'];

// Fetch issued books for this user
$query = "
    SELECT 
        ib.id AS issued_id,
        ib.book_id,
        b.title,
        b.author,
        b.category,
        ib.issue_date,
        ib.return_date,
        ib.status
    FROM issued_books ib
    JOIN books b ON ib.book_id = b.id
    WHERE ib.user_id = $user_id
    ORDER BY ib.issue_date DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Issued Books</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h2>My Issued Books</h2>

<a href="dashboard.php">← Back to Dashboard</a>
<br><br>

<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Category</th>
        <th>Issue Date</th>
        <th>Return Date</th>
        <th>Status</th>
    </tr>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
                <td>
        <?php echo htmlspecialchars($row['title']); ?><br>

        <?php if ($row['status'] === 'issued' && !empty($row['book_id'])): ?>
            <a href="download.php?book_id=<?php echo $row['book_id']; ?>" target="_blank">
                View / Download
            </a>
        <?php else: ?>
            Returned
        <?php endif; ?>
    </td>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['author']; ?></td>
            <td><?php echo $row['category']; ?></td>
            <td><?php echo $row['issue_date']; ?></td>
            <td><?php echo $row['return_date'] ?? '—'; ?></td>
            <td><?php echo ucfirst($row['status']); ?></td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">No books issued to you.</td>
        </tr>
    <?php endif; ?>
</table>
</body>
</html>
