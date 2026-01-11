<?php
include "../config/db.php";

if (isset($_POST['register'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // plain password
    $role = $_POST['role'];

    // 1️⃣ Check if email exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($check) > 0) {

        $user = mysqli_fetch_assoc($check);

        // 2️⃣ Check ALL fields match
        if (
            $user['name'] === $name &&
            $user['role'] === $role &&
            password_verify($password, $user['password'])
        ) {
            echo "You already have an account. <a href='login.php'>Please login</a>";
            exit();
        } else {
            echo "Email already exists, but details differ. Please use a different email.";
            exit();
        }
    }

    // 3️⃣ New user → hash password and insert
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password, role)
            VALUES ('$name','$email','$hashedPassword','$role')";

    if (mysqli_query($conn, $sql)) {
        echo "Registration successful! <a href='login.php'>Login here</a>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<h2>User Registration</h2>

<form method="POST">
    <input type="text" name="name" placeholder="Name" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>

    <select name="role" required>
        <option value="student">Student</option>
        <option value="teacher">Teacher</option>
        <option value="other">other</option>
    </select><br><br>

    <button name="register">Register</button>
</form>
