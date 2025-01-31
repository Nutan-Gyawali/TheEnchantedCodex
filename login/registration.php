<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="register.css">

</head>
<script>
    function toggleUsers() {
        var tableContainer = document.getElementById('userTableContainer');
        var toggleBtn = document.getElementById('toggleUsersBtn');

        if (tableContainer.style.display === 'none') {
            tableContainer.style.display = 'block';
            toggleBtn.textContent = 'Hide Users';
        } else {
            tableContainer.style.display = 'none';
            toggleBtn.textContent = 'Show Users';
        }
    }
</script>

<body>
    <div class="form-container">

        <h2>Register User</h2>
        <form action="users.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" required><br>

            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" required><br>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required><br>

            <label for="address">Address:</label>
            <textarea id="address" name="address" required></textarea><br>

            <input type="submit" name="submit" value="Register">
        </form>

        <p>Already Have an Account?</p>
        <button onclick="location.href='login.html'">Login</button>

    </div>

</body>

</html>