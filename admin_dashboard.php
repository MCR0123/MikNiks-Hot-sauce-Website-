<?php
session_start();
include 'admindb.php'; // Connect to admin database
include 'database.php'; // Connect to user database
include 'productsdb.php'; // Connect to products database

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}

// Redirect to login if not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle delete item action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_item'])) {
    $item_id = $_POST['item_id'];

    $query = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle add item action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_item'])) {
    $name = $_POST['name'];
    $image = $_POST['image'];
    $price = $_POST['price'];

    $query = "INSERT INTO products (name, image, price) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ssd", $name, $image, $price);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle edit item action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_item'])) {
    $item_id = $_POST['item_id'];
    $name = $_POST['name'];
    $image = $_POST['image'];
    $price = $_POST['price'];

    $query = "UPDATE products SET name = ?, image = ?, price = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ssdi", $name, $image, $price, $item_id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch existing items
$query = "SELECT * FROM products";
$result = $conn->query($query);

// Handle delete user action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    // Ensure the correct connection is used
    include 'database.php';
    $query = "DELETE FROM usernames WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Check for database connection errors
if (!$result) {
    echo "Error: " . $conn->error;
    exit();
}

// Fetch users
include 'database.php';
$userQuery = "SELECT * FROM usernames";
$userResult = $conn->query($userQuery);

// Check for database connection errors
if (!$userResult) {
    echo "Error: " . $conn->error;
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <a href="admin_dashboard.php?logout=true" class="logout-link">Logout</a>
    <link rel="stylesheet" href="styles.css">
    <style>
       body {
    background-color: #f4f4f4;
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    color: #333;
}

h1, h2 {
    color: #198d07;
    margin-bottom: 20px;
}

form {
    margin-bottom: 30px;
}

label {
    display: block;
    margin-bottom: 5px;
}

input[type="text"],
input[type="number"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

button[type="submit"] {
    padding: 10px 20px;
    background-color: #198d07;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button[type="submit"]:hover {
    background-color: #147305;
}

ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

li {
    margin-bottom: 20px;
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

li img {
    display: block;
    width: 100%;
    height: auto;
    border-radius: 5px;
    margin-bottom: 10px;
}

li button {
    padding: 10px 20px;
    background-color: #b12704;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

li button:hover {
    background-color: #9a2104;
}

.logout-link {
    display: block;
    margin-top: 30px;
    text-decoration: none;
    color: #198d07;
}

.logout-link:hover {
    color: #147305;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 8px;
    text-align: left;
}

th {
    background-color: #198d07;
    color: white;
}

    </style>
</head>
<body>
    <h1>Welcome, Admin!</h1>
    <form action="admin_dashboard.php" method="POST">
        <h2>Add New Item</h2>
        <label for="name">Item Name:</label>
        <input type="text" id="name" name="name" required>
        <label for="image">Item Image URL:</label>
        <input type="text" id="image" name="image">
        <label for="price">Price:</label>
        <input type="number" step="0.01" id="price" name="price" required>
        <button type="submit" name="add_item">Add Item</button>
    </form>

    <h2>Existing Items</h2>
    <ul>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<li>
                    <img src='{$row['image']}' alt='{$row['name']}'>
                    <div>{$row['name']} - R{$row['price']}</div>
                    <form action='admin_dashboard.php' method='POST'>
                        <input type='hidden' name='item_id' value='{$row['id']}'>
                        <button type='submit' name='delete_item'>Delete</button>
                    </form>
                    <form action='admin_dashboard.php' method='POST'>
                        <input type='hidden' name='item_id' value='{$row['id']}'>
                        <label for='name'>Item Name:</label>
                        <input type='text' id='name' name='name' value='{$row['name']}' required>
                        <label for='image'>Item Image URL:</label>
                        <input type='text' id='image' name='image' value='{$row['image']}'>
                        <label for='price'>Price:</label>
                        <input type='number' step='0.01' id='price' name='price' value='{$row['price']}' required>
                        <button type='submit' name='edit_item'>Edit Item</button>
                    </form>
                </li>";
        }
        $result->close();
        ?>
    </ul>

    <h2>Registered Users</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($userRow = $userResult->fetch_assoc()) {
                echo "<tr>
                        <td>{$userRow['id']}</td>
                        <td>{$userRow['full_name']}</td>
                        <td>{$userRow['email']}</td>
                        <td>
                            <form action='admin_dashboard.php' method='POST' onsubmit='return confirm(\"Are you sure you want to delete this user?\");'>
                                <input type='hidden' name='user_id' value='{$userRow['id']}'>
                                <button type='submit' name='delete_user'>Delete</button>
                            </form>
                        </td>
                    </tr>";
            }
            $userResult->close();
            ?>
        </tbody>
    </table>

</body>
</html>
