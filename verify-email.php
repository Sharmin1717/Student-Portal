<?php
// Include your database connection file
require 'db.php';

if (isset($_GET['code'])) {
    $verification_code = $_GET['code'];

    // Sanitize the input to prevent SQL injection
    $verification_code = $conn->real_escape_string($verification_code);

    // Check if there's a user with this verification code and not yet verified
    $sql = "SELECT * FROM users WHERE verification_code = '$verification_code' AND verified = 0 LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        // Update the user to mark as verified and clear verification code
        $update = "UPDATE users SET verified = 1, verification_code = NULL WHERE verification_code = '$verification_code'";

        if ($conn->query($update) === TRUE) {
            echo "<h2>Email Verified!</h2>";
            echo "<p>Your email has been verified successfully! You can now <a href='login.php'>login</a>.</p>";
        } else {
            echo "<h2>Error</h2>";
            echo "<p>There was an error updating your verification status. Please try again later.</p>";
        }
    } else {
        echo "<h2>Invalid or Expired Code</h2>";
        echo "<p>The verification link is invalid or your email has already been verified.</p>";
    }
} else {
    echo "<h2>No Verification Code</h2>";
    echo "<p>No verification code was provided in the URL.</p>";
}
?>
