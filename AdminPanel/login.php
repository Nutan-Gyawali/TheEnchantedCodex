<?php
session_start();
require '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userOrEmail = trim($_POST['userOrEmail']);
    $password = $_POST['password'];

    // Fetch user from database
    $stmt = $conn->prepare("SELECT id, username, email, role, password FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $userOrEmail, $userOrEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $email, $role, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;

            if ($role === 'admin') {
                header("Location: index.php"); // Redirect admin to discount page
                exit();
            } else {
                echo "<script>alert('Access denied! Only admins can log in.'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('Invalid username/email or password!'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('User not found!'); window.history.back();</script>";
        exit();
    }
}
