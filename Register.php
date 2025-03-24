<?php

require_once './Backend/Controller/Controller.php';

$controller = new Controller();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['Name']) && isset($_POST['Email']) && isset($_POST['Phone']) && isset($_POST['Password'])) {
        $controller->addUser($_POST);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./Style/elements.css">
    <link rel="stylesheet" href="./Style/forms.css">
    <script src="./Script/app.js"></script>
</head>

<body>

    <div class="form-container">

        <form id="RegisterForm" class="form" method="post">

            <div class="form-group">
                <label for="Name">Full Name</label>
                <input
                    type="text"
                    id="Name"
                    name="Name"
                    class="input"
                    autofocus>
                <div id="nameError" class="error-message"></div>
            </div>

            <div class="form-group">
                <label for="Email">Email Address</label>
                <input
                    type="text"
                    id="Email"
                    name="Email"
                    class="input"
                    placeholder="example@example.com">
                <div id="emailError" class="error-message"></div>
            </div>

            <div class="form-group">
                <label for="Number">Phone Number</label>
                <input
                    type="number"
                    id="Phone"
                    name="Phone"
                    class="input"
                    placeholder="70860816">
                <div id="numberError" class="error-message"></div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="Password"
                    name="Password"
                    class="input"
                    placeholder="********">
                <div id="passwordError" class="error-message"> </div>
            </div>

            <button class="submit" type="submit">Register</button>

            <div class="link">
                <p>Already have an account?<a href="./Login.php">
                        <h4> Login</h4>
                    </a></p>
            </div>

        </form>

    </div>


</body>

</html>