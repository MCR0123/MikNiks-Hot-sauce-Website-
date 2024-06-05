<?php
session_start();
header('Content-Type: application/json');
$totalAmount = isset($_SESSION['total_amount']) ? $_SESSION['total_amount'] : 0;
echo json_encode(['total_amount' => $totalAmount]);
?>
