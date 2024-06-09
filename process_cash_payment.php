<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Start session and retrieve user email
session_start();
if (!isset($_SESSION['user']['email'])) {
    echo json_encode(['success' => false, 'message' => 'Client email not found in session.']);
    exit();
}
$clientEmail = $_SESSION['user']['email'];

// Retrieve order details from the request body
$orderDetails = json_decode(file_get_contents("php://input"), true);

// Check if order details are valid
if (!empty($orderDetails['items']) && isset($orderDetails['totalPrice'])) {
    try {
        // Create a PHPMailer instance
        $mail = new PHPMailer(true);

        // Enable SMTP debugging
        $mail->SMTPDebug = 0;
        // SMTP configuration
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Username = "mikniks.hotsauce@gmail.com";
        $mail->Password = "ditdbdmtfaibipke";

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            )
        );

        // Set From and Reply-To addresses
        $mail->setFrom('mikniks.hotsauce@gmail.com', 'MikNiks Hot Sauce');
        $mail->addReplyTo('mikniks.hotsauce@gmail.com', 'MikNiks Hot Sauce');

        // Add recipient (client)
        $mail->addAddress($clientEmail);

        // Add recipient (admin)
        $adminEmail = 'mikniks.hotsauce@gmail.com';
        $mail->addAddress($adminEmail);

        // Generate invoice number
        $invoiceNumber = 'INV-' . strtoupper(uniqid());

        // Email subject
        $mail->Subject = 'Order Details - Cash Payment';

        // Email body content
        $message = "<div style='font-family: Arial, sans-serif;'>";
        $message .= "<div style='text-align: center;'>";
        $message .= "<img src='https://mikniks.great-site.net/MikNiks/logo.png' alt='Company Logo' style='width: 150px; height: auto;'><br><br>";
        $message .= "</div>";
        $message .= "<div style='display: flex; justify-content: space-between; align-items: center; margin-top: 20px;'>";
        $message .= "<h3 style='flex: 1;'>Order Details</h3>";
        $message .= "<h3 style='flex: 1; text-align: right; margin-left: 780px;'>Invoice #: {$invoiceNumber}</h3>";
        $message .= "</div>";
        $message .= "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
        $message .= "<thead><tr><th>Item Name</th><th>Quantity</th><th>Price</th><th>Total</th></tr></thead><tbody>";
        foreach ($orderDetails['items'] as $item) {
            $message .= "<tr>
                            <td>{$item['name']}</td>
                            <td>{$item['quantity']}</td>
                            <td>R{$item['price']}</td>
                            <td>R{$item['total']}</td>
                         </tr>";
        }
        $message .= "</tbody></table>";
        $message .= "<br>Total Price: R{$orderDetails['totalPrice']}<br><br>";
        $message .= "Payment method: Cash<br><br>";
        $message .= "We will contact you soon to confirm your order.<br><br>";
        $message .= "Thank you for shopping with us!<br><br>";
        $message .= "<div style='border-top: 1px solid #ccc; padding-top: 10px;'></div>";

        $message .= "<br><br>--<br>";
        $message .= "<table style='width: 100%;'>";
        $message .= "<tr>";
        $message .= "<td style='width: 50px;'><img src='https://mikniks.great-site.net/MikNiks/logo.png' alt='Company Logo' style='width: 100px; height: auto;'></td>";
        $message .= "<td style='padding-left: 10px;'>
                        <p style='margin: 0; font-size: 14px;'><strong>MikNiks Hot Sauce</strong></p>
                        <p style='margin: 0; font-size: 14px;'>Phone: 0836072706</p>
                        <p style='margin: 0; font-size: 14px;'>Email: mikniks.hotsauce@gmail.com</p>
                        <p style='margin: 0; font-size: 14px;'>Address: 123 New Road, Midrand, Gauteng, South Africa</p>
                      </td>";
        $message .= "</tr>";
        $message .= "</table>";

        $message .= "</div>";

        $mail->Body = $message;

        // Set email format to HTML
        $mail->isHTML(true);

        $mail->send();

        unset($_SESSION['cart']);

        // Return success response
        echo json_encode(['success' => true, 'message' => 'Order details sent successfully and cart cleared.']);
    } catch (Exception $e) {
        // Log the error
        error_log('PHPMailer Exception: ' . $e->getMessage());

        // Return error response if email sending fails
        echo json_encode(['success' => false, 'message' => 'Failed to send order details: ' . $e->getMessage()]);
    }
} else {
    // Log the error for invalid order details
    error_log('Invalid order details received.');

    // Return error response for invalid order details
    echo json_encode(['success' => false, 'message' => 'Invalid order details.']);
}
?>
