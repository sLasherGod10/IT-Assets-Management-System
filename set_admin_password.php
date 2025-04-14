<?php
require_once 'config/database.php';

$email = 'admin@example.com';
$newPassword = 'admin123';
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$query = "UPDATE users SET password = '$hashedPassword' WHERE email = '$email'";
if ($conn->query($query)) {
    echo "Password updated successfully for $email";
} else {
    echo "Error updating password: " . $conn->error;
}
?>
