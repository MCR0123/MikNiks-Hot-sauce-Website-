<?php
session_start();
if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    require_once "database.php";
    $sql = "SELECT * FROM usernames WHERE email = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "<div class='alert alert-danger'>SQL statement failed</div>";
    } else {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $passwordCheck = password_verify($password, $row['password']);
            if ($passwordCheck == false) {
                echo "<div class='alert alert-danger'>Wrong password</div>";
            } elseif ($passwordCheck == true) {
                // Set user session data including full name
                $_SESSION['user'] = [
                    'email' => $email,
                    'full_name' => $row['full_name']
                ];
                
                header("Location: index.php");
                exit();
            }
        } else {
            echo "<div class='alert alert-danger'>No user found with this email</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="regstyle.css">
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
                <form action="login.php" method="post">
                    <h2 class="text-center mb-4">Login to Account</h2>
                    <div class="form-group">
                        <input type="email" placeholder="Enter Your Email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <input type="password" placeholder="Enter Your Password" name="password" class="form-control" required>
                    </div>
                    <div class="form-btn">
                        <input type="submit" class="btn btn-danger btn-block w-100" value="Login" name="login">
                    </div>
                </form>
                <div class="mt-3"><p class="text-left">Not registered yet? <a href="registration.php" class="text-green">Register Here</a></p></div>
                <div class="mt-3"><p class="text-left">Forgot Password? <a href="forgot_password.php" class="text-green">Click Here</a></p></div>
                <div class="mt-3"><p class="text-left">Admin <a href="admin_login.php" class="text-green">Click Here</a></p></div>
            </div>
        </div>
    </div>
</body>
</html>
