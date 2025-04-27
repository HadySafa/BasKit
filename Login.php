<?php

// This is done by Hady

require_once './Backend/Controller/Controller.php';

$controller = new Controller();

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['Email']) && isset($_POST['Password'])) {
        $msg = $controller->validateUser($_POST);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./Style/elements.css">
    <link rel="stylesheet" href="./Style/layout.css">
    <link rel="stylesheet" href="./Style/forms.css">
    <script src="./Script/app.js"></script>
</head>

<body>

    <div class="container-40">

        <form id="LoginForm" class="form" method="post">

            <div class="form-group">
                <label for="Email">Email Address</label>
                <input 
                    type="text" 
                    id="Email" 
                    name="Email" 
                    class="input"
                    placeholder="example@example.com"
                    autofocus>
                <div id="emailError" class="error-message"></div>
            </div>

            <div class="form-group">
                <label for="Password">Password</label>
                <input 
                    type="password" 
                    id="Password" 
                    name="Password" 
                    class="input"
                    placeholder="********">
                <div id="passwordError" class="error-message"> </div>
            </div>

            <button class="submit" type="submit">Sign In</button>

            <?php if($msg) echo "<script>alert('$msg')</script>"; ?>

            <div class="link">
                <p>New to BasKit?<a href="./Register.php"><h4> Register an Acccount</h4></a></p>
            </div>

        </form>

    </div>


</body>

</html>