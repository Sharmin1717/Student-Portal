<?php
session_start();
require 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "Please enter both email and password.";
    } else {
        $email = $conn->real_escape_string($email);
        $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
        $result = $conn->query($query);

        if ($result && $result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if ($user['verified'] == 0) {
                $message = "Please verify your email before logging in.";
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['name'];
                header('Location: home.php');
                exit();
            } else {
                $message = "Incorrect password.";
            }
        } else {
            $message = "No account found with that email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8" />
    <title>Login - Student Portal</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if ($message) { echo "<p class='message'>$message</p>"; } ?>
        <form method="POST" action="login.php" id="loginForm">
            <label>Email</label><br />
            <input type="email" name="email" required /><br />
            
            <label>Password</label><br />
            <input type="password" name="password" required /><br />
            
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>

    <!-- JavaScript Validation -->
    <script>
    document.getElementById('loginForm').addEventListener('submit', function (e) {
        const email = document.querySelector('input[name="email"]').value.trim();
        const password = document.querySelector('input[name="password"]').value;

        const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
        let message = '';

        if (!email || !password) {
            message = 'Both fields are required.';
        } else if (!emailPattern.test(email)) {
            message = 'Invalid email format.';
        }

        if (message) {
            e.preventDefault();
            alert(message);
        }
    });
    </script>
</body>
</html>
