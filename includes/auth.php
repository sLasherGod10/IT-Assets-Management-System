<?php
function login($email, $password) {
    global $conn;
    
    $email = $conn->real_escape_string($email);
    
    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = $conn->query($query);
    
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // For debugging: print the password hash
        error_log("Stored hash: " . $user['password']);
        error_log("Input password: " . $password);
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];
            return true;
        }
    }
    return false;
}

function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function logout() {
    session_destroy();
    header('Location: login.php');
    exit();
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: index.php?error=unauthorized');
        exit();
    }
}
?>
