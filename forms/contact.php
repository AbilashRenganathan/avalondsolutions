<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for AJAX requests
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $name    = htmlspecialchars(trim($_POST["name"]));
    $email   = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST["subject"]));
    $message = htmlspecialchars(trim($_POST["message"]));

    // Validate required fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        http_response_code(400);
        echo "Please fill in all required fields.";
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Please enter a valid email address.";
        exit;
    }

    // Set the recipient email
    $to = "avalondsolutions@gmail.com"; // 

    // Set the email headers
    $headers = "From: $name <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Compose the message
    $email_content = "You have received a new message from your website contact form:\n\n";
    $email_content .= "Name: $name\n";
    $email_content .= "Email: $email\n";
    $email_content .= "Subject: $subject\n";
    $email_content .= "Message:\n$message\n";

    // For local testing, you can log the email content instead of sending
    if ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
        // Local testing - log to file instead of sending email
        $log_file = 'email_log.txt';
        $log_entry = date('Y-m-d H:i:s') . " - Email would be sent to: $to\n";
        $log_entry .= "Subject: $subject\n";
        $log_entry .= "From: $name <$email>\n";
        $log_entry .= "Message: $message\n";
        $log_entry .= "----------------------------------------\n";
        file_put_contents($log_file, $log_entry, FILE_APPEND);
        echo "OK";
    } else {
        // Production - try sending the email
        if (mail($to, $subject, $email_content, $headers)) {
            echo "OK";
        } else {
            http_response_code(500);
            echo "Failed to send email. Please check your server's mail configuration.";
        }
    }
} else {
    // Not a POST request
    http_response_code(403);
    echo "Forbidden";
}
?>
