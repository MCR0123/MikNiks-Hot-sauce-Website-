<?php

$token = $_GET["token"];

$token_hash = hash("sha256", $token);

$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT * FROM usernames
        WHERE reset_token = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
    die("The token not found");
}

if (strtotime($user["reset_token_expire"]) <= time()) {
    die("The token has expired");
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
                <form action="process-reset-password.php" method="post">
                    <h2 class="text-center mb-4">Reset Password</h2>
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" placeholder="New Password">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password">
                    </div>
                    <div class="form-btn">
                        <input type="submit" class="btn btn-danger btn-block w-100" value="Reset Password">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
