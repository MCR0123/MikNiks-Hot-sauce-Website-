<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logging out...</title>
    <script>
        // Clear cart data on logout
        console.log('Clearing cart...');
        localStorage.removeItem('cart');
        console.log('Cart cleared successfully.');

        // Redirect to index.php
        setTimeout(function() {
            window.location.href = "index.php";
        }, 500);
    </script>
</head>
<body>
    <p>Logging out...</p>

    <?php
    // Destroy the session
    session_destroy();
    ?>
</body>
</html>
