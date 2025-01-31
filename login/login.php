<?php
// Database connection
$host = 'localhost';
$dbname = 'ecommerce';
$dbuser = 'root';
$dbpassword = '';

$conn = new mysqli($host, $dbuser, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if (isset($_POST['submit'])) {
    $userOrEmail = $_POST['userOrEmail'];
    $password = $_POST['password'];

    // Query to check if the user exists by username or email
    $query = "SELECT * FROM users WHERE (username = ? OR email = ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $userOrEmail, $userOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Start session and display login options
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo "<script>
                   
                    document.location = '../Landing/explore.php';
                  </script>";
        } else {
            echo "<script>alert('Invalid password!'); history.back();</script>";
        }
    } else {
        echo "<script>alert('No user found with that username or email!'); history.back();</script>";
    }

    $stmt->close();
}

$conn->close();
