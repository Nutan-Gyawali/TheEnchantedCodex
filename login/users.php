<?php
session_start(); // Start session at the beginning

// Database connection details
$host = 'localhost';
$dbname = 'ecommerce';
$dbuser = 'root';
$dbpassword = '';

// Create connection
$conn = new mysqli($host, $dbuser, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    // Collect user input
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Validate input
    if (empty($username) || empty($password) || empty($email) || empty($firstname) || empty($lastname) || empty($phone) || empty($address)) {
        echo "<p>All fields are required!</p>";
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<p>Invalid email format!</p>";
        } else if (!preg_match('/^[0-9]{10}$/', $phone)) {
            echo "<p>Phone number must be a 10-digit number!</p>";
        } else {
            // Hash password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert data into the database
            $stmt = $conn->prepare("INSERT INTO users (username, password, email, firstname, lastname, phone, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $username, $hashed_password, $email, $firstname, $lastname, $phone, $address);

            if ($stmt->execute()) {
                // Get the user ID of the newly registered user
                $user_id = $stmt->insert_id;

                // Set session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['firstname'] = $firstname;
                $_SESSION['lastname'] = $lastname;

                // Close statement before redirecting
                $stmt->close();

                // Close database connection
                $conn->close();

                // Redirect to welcome page
                header("Location:../Landing/index.php ");
                exit();
            } else {
                echo "<p>Error: " . $stmt->error . "</p>";
                $stmt->close();
            }
        }
    }
}

// if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ViewAll'])) {
//     showUsers($conn);
// }

// Close connection
$conn->close();
?>
</body>

</html>