<?php
session_start();
include 'db_connection.php'; // Adjust the path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? '');

    // Validate inputs
    if (empty($name) || empty($username) || empty($email) || empty($password) || empty($role)) {
        die("All fields are required.");
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        if ($role === 'admin') {
            $stmt = $conn->prepare("INSERT INTO admins (name, username, email, password) VALUES (?, ?, ?, ?)");
        } elseif ($role === 'farmer') {
            $stmt = $conn->prepare("INSERT INTO farmers (name, username, email, password) VALUES (?, ?, ?, ?)");
        } elseif ($role === 'user') {
            die("Invalid role selected.");
        }

        $stmt->bind_param("ssss", $name, $username, $email, $hashedPassword);
        $stmt->execute();

        echo "<script>alert('Registration successful! You can now log in.'); window.location.href='login.html';</script>";
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    header("Location: register.html");
    exit();
}
?>
