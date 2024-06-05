<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="regstyle.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center" style="height: 100vh;">
            <div class="col-lg-6">
                <a href="index.php">
                    <img src="logo.png" alt="image of Hot souce" class="img-fluid d-block mx-auto mb-4">
            </a>
            </div>
            <form action="send-password-reset.php" method="post">
                    <h2 class="text-center mb-4">Forgot Password</h2>
                    <div class="form-group">
                        <input type="email" placeholder="Enter Your Email" name="email" class="form-control">
                    </div>
            <button>Send</button>

        </form>

</body>
</html>