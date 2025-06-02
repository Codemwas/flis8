<?php
session_start();
if (!isset($_SESSION['tracking_id'])) {
    header("Location: create_shipment_form.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shipment Created</title>
    <style>
        .tracking-id {
            font-size: 24px;
            font-weight: bold;
            color: #2ecc71;
            margin: 20px 0;
            padding: 15px;
            background: #f0f9f0;
            border-radius: 5px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <h1>Shipment Created Successfully</h1>
    <p>Your shipment has been registered with the following tracking ID:</p>
    <div class="tracking-id"><?php echo htmlspecialchars($_SESSION['tracking_id']); ?></div>
    <p>You can use this ID to track your shipment at any time.</p>
    <a href="tracking.php?trackingID=<?php echo urlencode($_SESSION['tracking_id']); ?>">Track this shipment now</a>
    
    <?php 
    // Clear the session variable
    unset($_SESSION['tracking_id']);
    ?>
</body>
</html>