<!--This is done by hady-->

<?php

require_once './Backend/Controller/Controller.php';
$controller = new Controller();

// check if the customer is logged in
$controller->checkCustomerLogin();


// get info user info from the SESSION
list($name, $balance) = $controller->getSessionInfo();
$name = $controller->cleanInput($name);
$balance = $controller->cleanInput($balance);

// check for logout
if (!empty($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') $controller->logout();


// modify header

$links = ["Home" => "./index.php", "Profile" => "./User.php"];
$activeLink = "Profile";
$showButton = false;


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User</title>
    <link rel="stylesheet" href="./Style/dashboard.css">
    <link rel="stylesheet" href="./Style/layout.css">
    <link rel="stylesheet" href="./Style/elements.css">
</head>

<body>

    <?php include './Header.php'; ?>

    <div class="container-40 container">

        <section class="info">
            <h2 class="name"><?php echo htmlspecialchars($name) ?></h2>
            <div class="balance">
                <div>
                    <img class="icon" src="./Icons/wallet.svg" alt="icon" />
                    <h4>Balance</h4>
                </div>
                <div>
                    <p>$ <?php echo $balance ?></p>
                </div>
            </div>
        </section>

        <section>
            <a href="./Manage-Account.php" class="link-button">
                <img class="icon" src="./Icons/settings.svg" alt="icon" />
                <h4>Profile</h4>
            </a>
            <a href="./Orders.php" class="link-button">
                <img class="icon" src="./Icons/list.svg" alt="icon" />
                <h4>Orders</h4>
            </a>
            <a href="" class="link-button">
                <img class="icon" src="./Icons/clipboard.svg" alt="icon" />
                <h4>Purchase List</h4>
            </a>
        </section>

        <section>
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