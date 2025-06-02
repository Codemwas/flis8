<?php
// Database connection
$db = new mysqli('localhost', 'root', '', 'project');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get statistics
$farmers_count = $db->query("SELECT COUNT(*) as count FROM farmers")->fetch_assoc()['count'];
$orders_count = $db->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$delivered_count = $db->query("SELECT COUNT(*) as count FROM orders WHERE status = 'Delivered'")->fetch_assoc()['count'];
$pending_count = $db->query("SELECT COUNT(*) as count FROM orders WHERE status = 'Pending'")->fetch_assoc()['count'];

// Get farmers list
$farmers = $db->query("SELECT * FROM farmers ORDER BY name LIMIT 10");

// Get orders list
$orders = $db->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>System Info & Reports - Farmers Logistics</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <style>
        .report-box {
            white-space: pre-wrap;
            margin-top: 20px;
            background: #f0f9f0;
            border: 1px solid #cceccc;
            padding: 20px;
            border-radius: 8px;
            font-family: monospace;
        }
        .stats-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            display: inline-block;
            width: 48%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>
    <section class="reveal">
        <h2>Why Farmers Logistics?</h2>
        <p>We simplify transport, tracking, and payments.</p>
    </section>

    <header>
        <h1>📊 System Information & Reports</h1>
        <nav>
            <ul>
                <li><a href="home.php">🏠 Home</a></li>
                <li><a href="register.php">🧑‍🌾 Register</a></li>
                <li><a href="products.php">🧺 Products</a></li>
                <li><a href="orders.php">🛒 Orders</a></li>
                <li><a href="tracking.php">🚚 Track</a></li>
                <li><a href="dashboard.php" class="active">📊 Reports</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>🌱 Summary of the System</h2>
            <div class="stats-card">
                <p><strong>Registered Farmers:</strong> <?= $farmers_count ?></p>
                <p><strong>Total Orders Made:</strong> <?= $orders_count ?></p>
            </div>
            <div class="stats-card">
                <p><strong>Successful Deliveries:</strong> <?= $delivered_count ?></p>
                <p><strong>Pending Orders:</strong> <?= $pending_count ?></p>
            </div>
        </section>

        <section>
            <h2>📋 Farmer List</h2>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Product</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($farmer = $farmers->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($farmer['name']) ?></td>
                        <td><?= htmlspecialchars($farmer['product_type']) ?></td>
                        <td><?= htmlspecialchars($farmer['location']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>📦 Order Records</h2>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Destination</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_id']) ?></td>
                        <td><?= htmlspecialchars($order['product_name']) ?></td>
                        <td><?= htmlspecialchars($order['quantity']) ?></td>
                        <td><?= htmlspecialchars($order['destination']) ?></td>
                        <td><?= $order['status'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>📝 Generate Simple Report</h2>
            <p>Click the button to get a summary report you can print or download.</p>
            <button onclick="generateReport()">Generate Report</button>

            <div id="report-output" class="report-box"></div>
        </section>
    </main>

    <footer>
        <p>&copy; <span id="year"></span> Farmers Logistics System | Developed by Mwashi Victor</p>
    </footer>

    <script>
        // Year update
        document.getElementById("year").textContent = new Date().getFullYear();

        // Generate Report Function
        function generateReport() {
            const report = `
=== FARMERS LOGISTICS REPORT ===

➤ Total Registered Farmers: <?= $farmers_count ?>
➤ Orders Made: <?= $orders_count ?>
➤ Delivered Orders: <?= $delivered_count ?>
➤ Pending Deliveries: <?= $pending_count ?>

➤ Report Generated On: ${new Date().toLocaleString()}
`;
            document.getElementById("report-output").innerText = report;
        }
    </script>
</body>
</html>

<?php
$db->close();
?>
