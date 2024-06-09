<?php
session_start();
require_once "database.php";
require_once "mailer.php"; 

if (isset($_POST["submit"])) {
    $fullName = $_POST["fullname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passwordRepeat = $_POST["repeat_password"];

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $errors = array();

    if (empty($fullName) OR empty($email) OR empty($password) OR empty($passwordRepeat)) {
        array_push($errors, "Please ensure all fields are filled in");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Enter a Valid Email Address");
    }
    if (strlen($password) < 8) {
        array_push($errors, "Password must be at least 8 characters long");
    }
    if ($password !== $passwordRepeat) {
        array_push($errors, "Your Passwords do not match");
    }

    $sql = "SELECT * FROM usernames WHERE email = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        array_push($errors, "SQL error");
    } else {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            array_push($errors, "The Email already exists!");
        }
    }

    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    } else {
        $sql = "INSERT INTO usernames (full_name, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $fullName, $email, $passwordHash);
            mysqli_stmt_execute($stmt);
            
            // Generate a session token
            $sessionToken = bin2hex(random_bytes(16)); // Generate a random 32-character hexadecimal string
            
            // Update the session token in the database
            $updateQuery = "UPDATE usernames SET session_token = ? WHERE email = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("ss", $sessionToken, $email);
            $updateStmt->execute();
            $updateStmt->close();
            
            // Store user data and session token in session variables
            $_SESSION['user'] = $email;
            $_SESSION['session_token'] = $sessionToken;

            echo "<div class='alert alert-success'>You are registered successfully.</div>";
            echo "<script>setTimeout(function() { window.location.href = 'login.php'; }, 2000);</script>";

            // Send confirmation email
            $mail = require 'mailer.php';

            try {
                $mail->setFrom('mikniks.hotsauce@gmail.com', 'MikNiks HotSauce');
                $mail->addAddress($email, $fullName);
                $mail->Subject = 'Registration Successful';
                
                // Include signature with contact details
                $body = '<h1>Thank you for registering!</h1><p>You have successfully registered on our website.</p>';
                $body .= '<br><br>--<br>';
                $body .= "<table style='width: 100%;'>";
                $body .= "<tr>";
                $body .= "<td style='width: 50px;'><img src='https://mikniks.great-site.net/MikNiks/logo.png' alt='Company Logo' style='width: 100px; height: auto;'></td>";
                $body .= "<td style='padding-left: 10px;'>
                            <p style='margin: 0; font-size: 14px;'><strong>MikNiks Hot Sauce</strong></p>
                            <p style='margin: 0; font-size: 14px;'>Contact us:</p>
                            <p style='margin: 0; font-size: 14px;'>Phone: 0836072706</p>
                            <p style='margin: 0; font-size: 14px;'>Email: mikniks.hotsauce@gmail.com</p>
                            <p style='margin: 0; font-size: 14px;'>Address: 123 New Road, Midrand, Gauteng, South Africa</p>
                          </td>";
                $body .= "</tr>";
                $body .= "</table>";
                
                $mail->Body    = $body;
                $mail->isHTML(true);

                $mail->send();
                echo "<div class='alert alert-success'>A confirmation email has been sent to your email address.</div>";
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>There was an error sending the confirmation email: {$mail->ErrorInfo}</div>";
            }
        } else {
            die("Something went wrong");
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
    <title>LoginMikNiks</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="regstyle.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-6">
                <a href="index.php">
                    <img src="logo.png" alt="Description of image" class="img-fluid d-block mx-auto mb-4">
                </a>
            </div>
            <div class="col-lg-6">
                <form action="registration.php" method="post">
                    <h2 class="text-center mb-4">Create Account</h2>
                    <div class="form-group">
                        <input type="text" class="form-control" name="fullname" placeholder="Full Name">
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="email" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" placeholder="Your Password">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Your Password">
                    </div>
                    <div class="form-btn">
                        <input type="submit" class="btn btn-danger btn-block w-100" value="Register" name="submit">
                    </div>
                </form>
                <div>
                    <p class="text-left mt-3">Already Registered? <a href="login.php" class="text-green">Login Here</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
