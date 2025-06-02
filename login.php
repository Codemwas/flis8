<?php
session_start();

// Connect to DB
$conn = new mysqli("localhost", "root", "", "project");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get form data
$email = $_POST['email'];
$password = $_POST['password'];

// Find farmer
$sql = "SELECT * FROM farmers WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $farmer = $result->fetch_assoc();

  if (password_verify($password, $farmer['password'])) {
    $_SESSION['farmer_id'] = $farmer['id'];
    $_SESSION['farmer_name'] = $farmer['name'];
    echo "<script>alert('Login successful!'); window.location.href='home.html';</script>";
	$_SESSION['farmer_name'] = $farmer['name'];

  } else {
    echo "<script>alert('Incorrect password.'); window.location.href='login.html';</script>";
  }
} else {
  echo "<script>alert('No farmer found with that email.'); window.location.href='login.html';</script>";
}

$stmt->close();
$conn->close();
?>
