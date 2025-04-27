<?php

require_once './Backend/Controller/Controller.php';
$controller = new Controller();


$links = ["Home" => "", "User Profile" => "./User.php","Admin Profile" => "./Admin.php"];
$activeLink = "Home";
$showButton = true;


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php include './Header.php'; ?>
    <h1>Homepage</h1>
</body>

</html>