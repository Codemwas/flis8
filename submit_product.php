<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "project";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'];
$category = $_POST['category'];
$description = $_POST['description'];
$price = $_POST['price'];

$sql = "INSERT INTO products (name, category, description, price)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssd", $name, $category, $description, $price);

if ($stmt->execute()) {
  echo "<script>alert('✅ Product added successfully!'); window.location.href='product.html';</script>";
} else {
  echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
