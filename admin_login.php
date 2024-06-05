<?php
session_start();
include 'admindb.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM admin_users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin['username'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Invalid username or password.";
        }
    } else {
        echo "Invalid username or password.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <!-- Include your custom styles -->
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Additional CSS for admin login page */
        body {
            background-color: #E3E7E8;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            overflow-x: hidden;
        }

        .container {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-btn {
            margin-top: 1rem;
        }

        .btn {
            padding: 0.75rem;
        }

        .col-lg-6 {
            padding: 0 1rem;
        }

        .img-fluid {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center" style="height: 100vh;">
            <div class="col-lg-6">
                <a href="index.php">
                    <img src="logo.png" alt="Description of image" class="img-fluid d-block mx-auto mb-4">
                </a>
            </div>
            <div class="col-lg-6">
                <form action="admin_login.php" method="POST">
                    <h2 class="text-center mb-4">Admin Login</h2>
                    <div class="form-group">
                        <input type="text" placeholder="Enter Your Username" name="username" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="password" placeholder="Enter Your Password" name="password" class="form-control">
                    </div>
                    <div class="form-btn">
                        <input type="submit" class="btn btn-danger btn-block w-100" value="Login">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

