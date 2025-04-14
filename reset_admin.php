<?php
require_once 'config/database.php';

// Create users table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee') DEFAULT 'employee',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($create_table) === FALSE) {
    die("Error creating table: " . $conn->error);
}

// Delete existing admin user if exists
$conn->query("DELETE FROM users WHERE email = 'admin@itams.com'");

// Insert fresh admin user
$name = "Admin User";
$email = "admin@itams.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$role = "admin";

$query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssss", $name, $email, $password, $role);

if ($stmt->execute()) {
    echo "Admin user created successfully!<br>";
    echo "Email: admin@itams.com<br>";
    echo "Password: admin123";
} else {
    echo "Error creating admin user: " . $stmt->error;
}
?>
