<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require 'db.php';        // DB connection
require 'functions.php'; // sendVerificationEmail() function

$message = '';

// Check DB connection
if (!$conn) {
    die('Database connection not established.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        $email = $conn->real_escape_string($email);
        $query = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $message = "Email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $verification_code = bin2hex(random_bytes(16));

            $sql = "INSERT INTO users (name, email, password, verification_code, verified) 
                    VALUES (?, ?, ?, ?, 0)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('ssss', $name, $email, $hashed_password, $verification_code);

                if ($stmt->execute()) {
                    if (sendVerificationEmail($email, $verification_code)) {
                        $message = "Registration successful! Please check your email to verify your account.";
                    } else {
                        $message = "Failed to send verification email.";
                    }
                } else {
                    $message = "Database error: Could not register user.";
                }
                $stmt->close();
            } else {
                $message = "Database error: Failed to prepare statement.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Register - Student Portal</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if ($message) { echo "<p class='message'>$message</p>"; } ?>
        <form method="POST" action="register.php" id="registerForm">
            <label>Username</label><br />
            <input type="text" name="username" required /><br />
            
            <label>Email</label><br />
            <input type="email" name="email" required /><br />
            
            <label>Password</label><br />
            <input type="password" name="password" required /><br />
            
            <label>Confirm Password</label><br />
            <input type="password" name="confirm_password" required /><br />
            
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>

    <!-- JavaScript Validation -->
    <script>
    document.getElementById('registerForm').addEventListener('submit', function (e) {
        const username = document.querySelector('input[name="username"]').value.trim();
        const email = document.querySelector('input[name="email"]').value.trim();
        const password = document.querySelector('input[name="password"]').value;
        const confirmPassword = document.querySelector('input[name="confirm_password"]').value;

        const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
        let message = '';

        if (!username || !email || !password || !confirmPassword) {
            message = 'All fields are required.';
        } else if (!emailPattern.test(email)) {
            message = 'Invalid email format.';
        } else if (password.length < 6) {
            message = 'Password must be at least 6 characters.';
        } else if (password !== confirmPassword) {
            message = 'Passwords do not match.';
        }

        if (message) {
            e.preventDefault();
            alert(message);
        }
    });
    </script>
</body>
</html>
