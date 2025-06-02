<?php
header("Content-Type: application/json");

// Connect to database
$conn = new mysqli("localhost", "root", "", "project");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Read raw JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate the data
if (!isset($data['farmerName'], $data['product'], $data['quantity'], $data['destination'])) {
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
    exit();
}

// Prepare SQL insert
$stmt = $conn->prepare("INSERT INTO orders (farmer_name, product, quantity, destination) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssis", $data['farmerName'], $data['product'], $data['quantity'], $data['destination']);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to insert order."]);
}

$stmt->close();
$conn->close();
?>
