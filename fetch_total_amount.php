<?php
session_start();
header('Content-Type: application/json');

// Check if total_amount is set in the session
if (isset($_SESSION['total_amount'])) {
    // Retrieve total amount from the session
    $totalAmount = $_SESSION['total_amount'];
    
    // Return total amount as JSON response
    echo json_encode(['total_amount' => $totalAmount]);
} else {
    // If total_amount is not set, return an error message
    echo json_encode(['error' => 'Total amount not found in session']);
}
?>