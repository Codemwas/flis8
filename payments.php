<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "project";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$receiver_type = $_POST['receiver_type'];
$receiver_name = $_POST['receiver_name'];
$order_id = $_POST['related_order_id'] ?: null;  // Optional
$amount = $_POST['amount'];
$paid_for = $_POST['paid_for'];

$sql = "INSERT INTO payments (receiver_type, receiver_name, related_order_id, amount, paid_for)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssids", $receiver_type, $receiver_name, $order_id, $amount, $paid_for);

if ($stmt->execute()) {
  echo "<script>alert('✅ Payment recorded successfully!'); window.location.href='payments.html';</script>";
} else {
  echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
