<?php
session_start();
include 'db_connection.php'; // Your database connection file

function generateTrackingID() {
    // Generate a unique tracking ID with prefix 'TRK' followed by 6 alphanumeric chars
    return 'TRK' . strtoupper(substr(md5(uniqid()), 0, 6));
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input data first
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_NUMBER_INT);
    $product_name = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING);
    // Add other fields as needed
    
    // Generate tracking ID
    $tracking_id = generateTrackingID();
    
    // Log the generation
    error_log("Generated new tracking ID: " . $tracking_id . " for order: " . $order_id);
    
    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO shipments (tracking_id, order_id, product_name, status, created_at) 
                           VALUES (?, ?, ?, 'Processing', NOW())");
    $stmt->bind_param("sis", $tracking_id, $order_id, $product_name);
    
    if ($stmt->execute()) {
        // Log successful creation
        error_log("Successfully created shipment with tracking ID: " . $tracking_id);
        
        // Redirect to success page or display tracking ID to user
        $_SESSION['tracking_id'] = $tracking_id;
        header("Location: shipment_created.php");
        exit();
    } else {
        // Log the error
        error_log("Error creating shipment: " . $stmt->error);
        
        // Handle error
        $_SESSION['error'] = "Error creating shipment. Please try again.";
        header("Location: create_shipment_form.php");
        exit();
    }
}
?>