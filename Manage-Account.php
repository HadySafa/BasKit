<?php

// This is done by Hady

require_once './Backend/Controller/Controller.php';

$controller = new Controller();

// check if logged in
$controller->checkLoggedIn();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['OldPassword']) && isset($_POST['NewPassword'])) {
        $msg = $controller->changePassword($_POST);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Account</title>
    <link rel="stylesheet" href="./Style/elements.css">
    <link rel="stylesheet" href="./Style/layout.css">
    <link rel="stylesheet" href="./Style/forms.css">
    <link rel="stylesheet" href="./Style/dashboard.css">
    <script src="./Script/app.js"></script>
</head>

<body>

    <div class="container-40">

        <form id="ChangePasswordForm" class="form" method="post">

            <h2>Change Password <img class="icon" src="./Icons/edit.svg" alt="icon" /></h2>

            <div class="form-group">
                <h4>Old Password</h4>
                <input
                    type="password"
                    id="OldPassword"
                    name="OldPassword"
                    class="input"
                    placeholder="********">
                <div id="oldPasswordError" class="error-message"> </div>
            </div>

            <div class="form-group">
                <h4>New Password</h4>
                <input
                    type="password"
                    id="NewPassword"
                    name="NewPassword"
                    class="input"
                    placeholder="********">
                <div id="newPasswordError" class="error-message"> </div>
            </div>

            <?php if($msg) echo "<script>alert('$msg')</script>"; ?>

            <button class="submit" type="submit">Submit</button>

        </form>

    </div>


</body>

</html>