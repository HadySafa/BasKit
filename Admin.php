<!--This is done by hady-->

<?php

require_once './Backend/Controller/Controller.php';
$controller = new Controller();

// check if the customer is logged in
$controller->checkAdminLogin();

// check for logout
if (!empty($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') $controller->logout();

// modify header
$links = ["Home" => "./index.php", "Profile" => "./Admin.php"];
$activeLink = "Profile";
$showButton = false;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="./Style/layout.css">
    <link rel="stylesheet" href="./Style/dashboard.css">
    <link rel="stylesheet" href="./Style/elements.css">
</head>

<body>

    <?php include './Header.php'; ?>

    <div class="container-40 container">

        <section class="links">
            <a href="" class="link-button">
                <img class="icon" src="./Icons/users.svg" alt="icon" />
                <h4>Users Management</h4>
            </a>
            <a href="" class="link-button">
                <img class="icon" src="./Icons/settings.svg" alt="icon" />
                <h4>Products Management</h4>
            </a>
            <a href="./Manage-Orders.php" class="link-button">
                <img class="icon" src="./Icons/clipboard.svg" alt="icon" />
                <h4>Orders Management</h4>
            </a>
            <a href="" class="link-button">
                <img class="icon" src="./Icons/plus.svg" alt="icon" />
                <h4>Stock Management</h4>
            </a>
        </section>

        <section class="signout">
            <form method="post">
                <button class="link-button" type="submit">
                    <img class="icon" src="./Icons/signout.svg" alt="icon" />
                    <h4>Sign Out</h4>
                </button>
            </form>
        </section>

    </div>

</body>

</html>