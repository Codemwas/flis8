<?php
$token = $_GET['token'] ?? '';
?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" href="styles.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
<head>
  <title>Reset Password</title>
</head>
<body>
  <h2>🔒 Reset Password</h2>
  <form action="update.php" method="POST">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
    <label>New Password:</label><br>
    <input type="password" name="password" required><br><br>
    <button type="submit">Update Password</button>
  </form>
</body>
</html>
