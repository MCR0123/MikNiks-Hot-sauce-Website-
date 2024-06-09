<?php
include 'admindb.php';
$username = 'MikNiks';
$password = password_hash('HotSauce@123', PASSWORD_DEFAULT); // Hash the password

// Insert the new admin user into the admin_users table
$query = "INSERT INTO admin_users (username, password) VALUES (?, ?)";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->close();

    echo "Admin user created successfully.";
} else {
    echo "Error: " . $conn->error;
}

// Close the database connection
mysqli_close($conn);
?>
