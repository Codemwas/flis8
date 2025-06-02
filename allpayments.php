<?php
$conn = new mysqli("localhost", "root", "", "project");

$result = $conn->query("SELECT * FROM payments ORDER BY payment_date DESC");

echo "<h2>📄 Payment Records</h2>";
echo "<table border='1' cellpadding='8'>
  <tr>
    <th>ID</th><th>Receiver</th><th>Type</th><th>Order ID</th><th>Amount</th><th>Reason</th><th>Date</th>
  </tr>";

while ($row = $result->fetch_assoc()) {
  echo "<tr>
    <td>{$row['id']}</td>
    <td>{$row['receiver_name']}</td>
    <td>{$row['receiver_type']}</td>
    <td>{$row['related_order_id']}</td>
    <td>Ksh {$row['amount']}</td>
    <td>{$row['paid_for']}</td>
    <td>{$row['payment_date']}</td>
  </tr>";
}

echo "</table>";
$conn->close();
?>
