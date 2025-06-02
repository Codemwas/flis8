<?php
session_start();
include 'db_connection.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$tracking_id = '';
$tracking_data = null;
$error = '';

// Process tracking request
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['trackingID'])) {
    $tracking_id = trim($_GET['trackingID']);
    
    if (!empty($tracking_id)) {
        // Use prepared statement for security
        $stmt = $conn->prepare("SELECT * FROM shipments WHERE tracking_id = ?");
        $stmt->bind_param("s", $tracking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $tracking_data = $result->fetch_assoc();
        } else {
            $error = "No shipment found with tracking ID: " . htmlspecialchars($tracking_id);
        }
        $stmt->close();
    } else {
        $error = "Please enter a tracking ID";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Product - Farmers Logistics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        header {
            background: #2ecc71;
            color: white;
            padding: 20px 0;
            margin-bottom: 20px;
        }
        nav {
            background: #27ae60;
            padding: 10px;
            margin-bottom: 20px;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
        }
        .tracking-form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .tracking-form input[type="text"] {
            padding: 8px;
            width: 200px;
        }
        .tracking-form button {
            padding: 8px 15px;
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .tracking-results {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .status {
            font-weight: bold;
        }
        .status-processing { color: #f39c12; }
        .status-in-transit { color: #3498db; }
        .status-delivered { color: #2ecc71; }
        .error {
            color: #e74c3c;
            padding: 10px;
            background: #ffebee;
            border-radius: 4px;
        }
        footer {
            margin-top: 40px;
            text-align: center;
            color: #7f8c8d;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Farmers Logistics System</h1>
        </header>
        
        <nav>
            <a href="home.html">Home</a>
            <a href="tracking.php">Track Product</a>
            <a href="contact.html">Contact</a>
            <a href="login.html">Login</a>
        </nav>
        
        <main>
            <section class="tracking-form">
                <h2>Track Your Shipment</h2>
                <p>Enter your tracking ID below to view the current status of your shipment.</p>
                <form action="tracking.php" method="GET">
                    <input type="text" id="trackingID" name="trackingID" 
                           value="<?php echo htmlspecialchars($tracking_id); ?>" 
                           placeholder="e.g. TRK123456" required>
                    <button type="submit">Track</button>
                </form>
            </section>

            <?php if ($error): ?>
                <div class="error">
                    <p><?php echo $error; ?></p>
                    <p>Need help? <a href="contact.html">Contact our support team</a></p>
                </div>
            <?php endif; ?>

            <?php if ($tracking_data): ?>
                <section class="tracking-results">
                    <h3>Shipment Details</h3>
                    <p><strong>Tracking Number:</strong> <?php echo htmlspecialchars($tracking_data['tracking_id']); ?></p>
                    <p><strong>Status:</strong> 
                        <span class="status status-<?php echo strtolower(str_replace(' ', '-', $tracking_data['status'])); ?>">
                            <?php echo htmlspecialchars($tracking_data['status']); ?>
                        </span>
                    </p>
                    <p><strong>Product:</strong> <?php echo htmlspecialchars($tracking_data['product_name']); ?></p>
                    <p><strong>Origin:</strong> <?php echo htmlspecialchars($tracking_data['origin']); ?></p>
                    <p><strong>Destination:</strong> <?php echo htmlspecialchars($tracking_data['destination']); ?></p>
                    <p><strong>Last Update:</strong> <?php echo date('M j, Y g:i A', strtotime($tracking_data['last_update'])); ?></p>
                    <p><strong>Estimated Delivery:</strong> <?php echo date('M j, Y g:i A', strtotime($tracking_data['estimated_delivery'])); ?></p>
                </section>
            <?php endif; ?>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Farmers Logistics System | Developed by Mwashi</p>
        </footer>
    </div>
</body>
</html>
<?php
$conn->close();
?>