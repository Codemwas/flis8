<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "project";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$track_id = $_GET['track_id'];

$sql = "SELECT * FROM orders WHERE tracking_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $track_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<style>
body { font-family: Arial; text-align: center; background: #f0fff4; padding: 30px; }
.result { margin: auto; max-width: 500px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 5px #ccc; }
h2 { color: #2e8b57; }
</style>";

if ($result->num_rows > 0) {
  $order = $result->fetch_assoc();
  echo "<div class='result'>";
  echo "<h2>Tracking ID: " . $order['tracking_id'] . "</h2>";
  echo "<p><strong>Product:</strong> " . $order['product'] . "</p>";
  echo "<p><strong>Quantity:</strong> " . $order['quantity'] . "</p>";
  echo "<p><strong>Destination:</strong> " . $order['destination'] . "</p>";
  echo "<p><strong>Status:</strong> " . $order['status'] . "</p>";
  echo "<p><strong>Current Location:</strong> " . $order['current_location'] . "</p>";
  echo "<p><strong>Order Date:</strong> " . $order['order_date'] . "</p>";
  echo "</div>";
} else {
  echo "<p style='color:red;'>❌ Tracking ID not found. Please check and try again.</p>";
}

$stmt->close();
$conn->close();
?>
