<!DOCTYPE html>
<html>
<head>
    <title>Library Management System</title>
    <link rel="stylesheet" href="/library_management_system/assets/css/style.css">
</head>
<body>

<header class="main-header">
    <h1>📚 Library Management System</h1>

    <nav>
        <a href="/library management system/">Home</a>
        <a href="/library management system/user/dashboard.php">Dashboard</a>
        <a href="/library management system/pages/about.php">About</a>
        <a href="/library management system/pages/contact.php">Contact</a>

        <?php if (isset($_SESSION['user'])): ?>
            <a href="/library management system/auth/logout.php">Logout</a>
        <?php else: ?>
            <a href="/library management system/auth/login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<main class="content">
