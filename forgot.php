<?php
$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$token = bin2hex(random_bytes(16));
$expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

// Check if farmer exists
$stmt = $conn->prepare("SELECT * FROM farmers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  // Update token
  $update = $conn->prepare("UPDATE farmers SET reset_token = ?, token_expiry = ? WHERE email = ?");
  $update->bind_param("sss", $token, $expiry, $email);
  $update->execute();

  // You can send this via email in production
  echo "Reset link: <a href='reset.php?token=$token'>Click here to reset your password</a>";
} else {
  echo "No farmer found with that email.";
}
?>
