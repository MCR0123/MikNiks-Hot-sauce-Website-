<?php
include 'productsdb.php';

$query = "SELECT * FROM products";
$result = $conn->query($query);

$products = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode($products);
$conn->close();
?>
