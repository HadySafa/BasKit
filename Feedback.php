<?php

// This is done by Hady

require_once './Backend/Controller/Controller.php';

$controller = new Controller();

// check if the customer is logged in
$controller->checkCustomerLogin();

$links = ["Home" => "./LandingPage.php"];
$activeLink = "Home";
$showButton = false;


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['Description'])) {
        $msg = $controller->submitFeedback($_POST['Description']);
        echo "<script>alert('$msg')</script>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="stylesheet" href="./Style/elements.css">
    <link rel="stylesheet" href="./Style/layout.css">
    <link rel="stylesheet" href="./Style/forms.css">
    <link rel="stylesheet" href="./Style/dashboard.css">
</head>

<body>


    <?php include "./Header.php"?>
    
    <div class="container-40">

        <form class="form" method="post">

            <h2>Give Feedback <img class="icon" src="./Icons/comment.svg" alt="icon" /></h2>

            <input
                type="text"
                name="Description"
                autofocus
                class="input"
                placeholder="">

            <button class="submit" type="submit">Submit</button>

        </form>

    </div>


</body>

</html>