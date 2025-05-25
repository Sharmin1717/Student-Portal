<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = '';

// Fetch current user data
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Trim and sanitize inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!empty($phone) && !preg_match('/^\+?[0-9]{7,15}$/', $phone)) {
        $errors[] = "Phone number is invalid. Only digits and optional leading + allowed.";
    }

    // Check if email already taken by another user
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email is already taken by another user.";
        }
        $stmt->close();
    }

    // Update user info
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
            $user['name'] = $name;
            $user['email'] = $email;
            $user['phone'] = $phone;
        } else {
            $errors[] = "Failed to update profile. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Profile - Student Portal</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f8fa;
            margin: 0; padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 40px auto;
            background: white;
            padding: 30px 40px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        form label {
            display: block;
            margin: 12px 0 6px;
            font-weight: bold;
        }
        form input[type="text"],
        form input[type="email"],
        form input[type="tel"] {
            width: 100%;
            padding: 8px 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .button {
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .button:hover {
            background: #0056b3;
        }
        .errors {
            background: #f8d7da;
            border: 1px solid #f5c2c7;
            padding: 10px 15px;
            color: #842029;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background: #d1e7dd;
            border: 1px solid #badbcc;
            padding: 10px 15px;
            color: #0f5132;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .actions {
            margin-top: 20px;
        }
        .actions a {
            margin-right: 15px;
            color: #555;
            text-decoration: none;
        }
        .actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>

        <?php if ($errors): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form action="edit_profile.php" method="post" novalidate>
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required />

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required />

            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+1234567890" />

            <button type="submit" class="button">Save Changes</button>
        </form>

        <div class="actions">
            <a href="profile.php">&larr; Back to Profile</a>
        </div>
    </div>
</body>
</html>
