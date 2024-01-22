<?php

$login = 0;
$invalid = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    include "connect.php";
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "select * from `users` where username = '$username' and user_password = '$password'";
    $result = mysqli_query($conn, $sql);
    if($result){
        $num = mysqli_num_rows($result);
        if ($num > 0){
            $login = 1;
            session_start();
            $_SESSION["username"] = $username;
            header ("location: home.php");
        }
        else{
            $invalid = 1;
        }
    }
}    
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap css-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>Login page</title>
</head>

<body>
    <?php
    if($invalid){
        echo '<div class="alert alert-danger text-center" role="alert"><strong>Error</strong> Invalid username or password!</div>';
    }
    ?>
    <h1 class=" text-center mt-5">Login page</h1>
    <div class="container mt-5">
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" placeholder="Enter your username" class="form-control"
                    autocomplete="off">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" placeholder="Enter your password" class="form-control">
            </div>
            <button type="submit" name="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <div class="container mt-5">
            <p>Dont have an account?</p>
            <a href="signup.php" class="btn btn-success">Signup</a>
        </div>
    </div>
</body>

</html>