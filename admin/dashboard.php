<?php
session_start();
include "../config/db.php";

// Access control: only admin
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}
include "../includes/header.php";

// Fetch total users
$user_count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$total_users = mysqli_fetch_assoc($user_count_query)['total'];

// Fetch total books
$book_count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM books");
$total_books = mysqli_fetch_assoc($book_count_query)['total'];

// Fetch total issued books
$issued_count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM issued_books");
$total_issued = mysqli_fetch_assoc($issued_count_query)['total'];

// Fetch recent books
$recent_books_query = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .btn { padding: 8px 15px; background: #007BFF; color: white; text-decoration: none; border-radius: 5px; margin-right: 5px; }
        .btn:hover { background: #0056b3; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?php echo $_SESSION['user']['name']; ?> (Role: <?php echo $_SESSION['user']['role']; ?>)</p>

    <nav>
        <a href="add_book.php" class="btn">Add Book</a>
        <a href="manage_books.php" class="btn">Manage Books</a>
        <a href="../auth/logout.php" class="btn">Logout</a>
    </nav>
    <hr>
<?php include "../includes/footer.php"; ?>
    <h2>Statistics</h2>
    <p>Total Users: <?php echo $total_users; ?></p>
    <p>Total Books: <?php echo $total_books; ?></p>
    <p>Total Issued Books: <?php echo $total_issued; ?></p>

    <h2>Recent Books</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Action</th>
        </tr>
        <?php while($book = mysqli_fetch_assoc($recent_books_query)): ?>
        <tr>
            <td><?php echo $book['id']; ?></td>
            <td><?php echo $book['title']; ?></td>
            <td><?php echo $book['author']; ?></td>
            <td><?php echo $book['category']; ?></td>
            <td><?php echo $book['quantity']; ?></td>
            <td>
                <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn">Edit</a>
                <a href="manage_books.php?delete_id=<?php echo $book['id']; ?>" class="btn" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
