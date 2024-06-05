// set_total.php
<?php
session_start();
$data = json_decode(file_get_contents('php://input'), true);
$_SESSION['total_amount'] = isset($data['total_amount']) ? $data['total_amount'] : 0;
echo json_encode(['success' => true]);
?>
