<?php
session_start();
require __DIR__ . '/db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Process payment form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $driver_id = filter_input(INPUT_POST, 'driver_id', FILTER_SANITIZE_NUMBER_INT);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $payment_date = filter_input(INPUT_POST, 'payment_date', FILTER_SANITIZE_STRING);
    $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);
    $reference = filter_input(INPUT_POST, 'reference', FILTER_SANITIZE_STRING);

    // Validate inputs
    if ($driver_id && $amount && $payment_date && $payment_method) {
        $stmt = $conn->prepare("INSERT INTO driver_payments 
                               (driver_id, amount, payment_date, payment_method, reference, status) 
                               VALUES (?, ?, ?, ?, ?, 'completed')");
        $stmt->bind_param("idsss", $driver_id, $amount, $payment_date, $payment_method, $reference);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Payment processed successfully!";
        } else {
            $_SESSION['error'] = "Error processing payment: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Please fill all required fields";
    }
    header("Location: payment_drivers.php");
    exit();
}

// Fetch drivers and payment history
$drivers = $conn->query("SELECT * FROM drivers ORDER BY name");
$payments = $conn->query("SELECT p.*, d.name as driver_name 
                         FROM driver_payments p
                         JOIN drivers d ON p.driver_id = d.id
                         ORDER BY p.payment_date DESC LIMIT 50");
$unpaid_trips = $conn->query("SELECT t.driver_id, d.name, COUNT(*) as trip_count, 
                             SUM(t.distance * t.rate_per_km) as total_amount
                             FROM driver_trips t
                             JOIN drivers d ON t.driver_id = d.id
                             WHERE t.status = 'completed'
                             AND NOT EXISTS (
                                 SELECT 1 FROM driver_payments 
                                 WHERE driver_id = t.driver_id 
                                 AND payment_date >= t.trip_date
                             )
                             GROUP BY t.driver_id, d.name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Payments - Farmers Logistics</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        .payment-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .unpaid-trip {
            background-color: #fff8e1;
            cursor: pointer;
        }
        .unpaid-trip:hover {
            background-color: #ffecb3;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <?php include('admin_sidebar.php'); ?>
                </div>
            </nav>

            <!-- Main Content -->
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <h2 class="my-4">Driver Payment System</h2>
                
                <!-- Success/Error Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <div class="row">
                    <!-- Payment Form -->
                    <div class="col-md-6">
                        <div class="card payment-card">
                            <div class="card-header bg-primary text-white">
                                <h5>Process New Payment</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="driver_id" class="form-label">Driver</label>
                                        <select class="form-select" id="driver_id" name="driver_id" required>
                                            <option value="">Select Driver</option>
                                            <?php while ($driver = $drivers->fetch_assoc()): ?>
                                                <option value="<?= $driver['id'] ?>">
                                                    <?= htmlspecialchars($driver['name']) ?> 
                                                    (<?= $driver['payment_method'] ?? 'No method' ?>)
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">Amount</label>
                                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="payment_date" class="form-label">Payment Date</label>
                                        <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                               value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="payment_method" class="form-label">Payment Method</label>
                                        <select class="form-select" id="payment_method" name="payment_method" required>
                                            <option value="bank">Bank Transfer</option>
                                            <option value="mobile_money">Mobile Money</option>
                                            <option value="cash">Cash</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reference" class="form-label">Reference/Note</label>
                                        <input type="text" class="form-control" id="reference" name="reference">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Process Payment</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Unpaid Trips -->
                    <div class="col-md-6">
                        <div class="card payment-card">
                            <div class="card-header bg-warning text-dark">
                                <h5>Unpaid Completed Trips</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($unpaid_trips->num_rows > 0): ?>
                                    <div class="list-group">
                                        <?php while ($trip = $unpaid_trips->fetch_assoc()): ?>
                                            <div class="list-group-item unpaid-trip" 
                                                 onclick="fillPaymentForm(<?= $trip['driver_id'] ?>, <?= $trip['total_amount'] ?>)">
                                                <strong><?= htmlspecialchars($trip['name']) ?></strong>
                                                <span class="float-end"><?= $trip['trip_count'] ?> trips</span>
                                                <div>Amount: <?= number_format($trip['total_amount'], 2) ?></div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">No unpaid trips found</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="card payment-card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5>Recent Payment History</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Driver</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($payments->num_rows > 0): ?>
                                        <?php while ($payment = $payments->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= date('M j, Y', strtotime($payment['payment_date'])) ?></td>
                                                <td><?= htmlspecialchars($payment['driver_name']) ?></td>
                                                <td><?= number_format($payment['amount'], 2) ?></td>
                                                <td><?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?></td>
                                                <td><?= htmlspecialchars($payment['reference']) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No payment records found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function fillPaymentForm(driverId, amount) {
            document.getElementById('driver_id').value = driverId;
            document.getElementById('amount').value = amount.toFixed(2);
            document.getElementById('payment_method').focus();
        }
        
        // Auto-fill today's date if empty
        document.getElementById('payment_date').addEventListener('focus', function() {
            if (!this.value) {
                this.value = new Date().toISOString().substr(0, 10);
            }
        });
    </script>
</body>
</html>