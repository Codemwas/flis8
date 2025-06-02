<?php
// Set headers for proper response
header('Content-Type: text/html; charset=UTF-8');

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate required fields
    $required_fields = ['fullname', 'email', 'subject', 'message'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty(trim($_POST[$field] ?? ''))) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        http_response_code(400);
        die(json_encode([
            'status' => 'error',
            'message' => 'Missing required fields: ' . implode(', ', $missing_fields)
        ]));
    }

    // Sanitize inputs
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        die(json_encode([
            'status' => 'error',
            'message' => 'Invalid email format'
        ]));
    }

    // Database connection
    try {
        $conn = new mysqli("localhost", "root", "", "project");
        
        if ($conn->connect_error) {
            throw new Exception("Database connection failed: " . $conn->connect_error);
        }

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO contact_submissions (fullname, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullname, $email, $subject, $message);
        
        if ($stmt->execute()) {
            // Success response with HTML echo
            echo '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Message Received - Farmers Logistics System</title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="stylesheet" href="styles.css">
            </head>
            <body>
                <header>
                    <h1>📞 Message Received</h1>
                </header>
                <main>
                    <section class="success-message">
                        <h2>Thank You, ' . $fullname . '!</h2>
                        <p>We have received your message regarding <strong>"' . $subject . '"</strong> and will respond to you at <strong>' . $email . '</strong> within 24 hours.</p>
                        <p>Your submission reference: #' . $stmt->insert_id . '</p>
                        <a href="contact.html" class="button">← Back to Contact Page</a>
                    </section>
                </main>
                <footer>
                    <p>&copy; ' . date('Y') . ' Farmers Logistics System</p>
                </footer>
            </body>
            </html>';
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        http_response_code(500);
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Error</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                .error { color: #d9534f; }
            </style>
        </head>
        <body>
            <div class="error">
                <h1>⚠️ Submission Error</h1>
                <p>' . htmlspecialchars($e->getMessage()) . '</p>
                <p>Please try again later or contact support directly.</p>
                <a href="contact.html">← Back to Contact Page</a>
            </div>
        </body>
        </html>';
    }
} else {
    http_response_code(405);
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Invalid Request</title>
    </head>
    <body>
        <h1>Invalid Request Method</h1>
        <p>Please submit the form properly.</p>
        <a href="contact.html">← Back to Contact Page</a>
    </body>
    </html>';
}
?> 