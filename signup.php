<?php
session_start();
require_once 'config/database.php'; // DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST["name"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = 'employee'; // Default role for signup

    $check = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $_SESSION['error'] = "Email already exists.";
        header("Location: login.php");
        exit();
    }

    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Signup successful. You can login now.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Signup failed. Please try again.";
        header("Location: login.php");
        exit();
    }
}
?>
