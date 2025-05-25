<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    header('Location: login.php');
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Profile - Student Portal</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f8fa;
            margin: 0; padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 30px 40px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
            border-radius: 8px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .profile-info p {
            font-size: 16px;
            margin: 8px 0;
            color: #555;
            text-align: left;
        }
        .button, .btn-edit {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s ease;
            cursor: pointer;
            border: none;
        }
        .button:hover, .btn-edit:hover {
            background: #0056b3;
        }
        .btn-logout {
            background: #dc3545;
        }
        .btn-logout:hover {
            background: #a71d2a;
        }
        .profile-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .btn-edit img {
            vertical-align: middle;
            margin-right: 8px;
            width: 18px;
            height: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Top Profile Icon -->
        <img src="profileIcon.png" alt="Profile Icon" class="profile-icon">

        <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>

        <div class="profile-info">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'Not Provided'); ?></p>
        </div>

        <!-- Edit Profile Button with Icon -->
        <a href="edit_profile.php" class="btn-edit">
            <img src="profileIcon.png" alt="Edit Icon">
            Edit Profile
        </a>

        <!-- Logout Button -->
        <form action="logout.php" method="post" style="display:inline;">
            <button type="submit" class="button btn-logout">Logout</button>
        </form>
    </div>
</body>
</html>
