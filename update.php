<?php
$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$token = $_POST['token'];
$newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Check token validity
$stmt = $conn->prepare("SELECT * FROM farmers WHERE reset_token = ? AND token_expiry > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  // Update password
  $update = $conn->prepare("UPDATE farmers SET password = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?");
  $update->bind_param("ss", $newPassword, $token);
  $update->execute();

  echo "✅ Password updated successfully. You can now <a href='login.html'>login</a>.";
} else {
  echo "❌ Invalid or expired token.";
}
?>
