<?php

// This is done by Hady

// Done

require_once './Backend/Controller/Controller.php';

$controller = new Controller();

// modify header
$links = ["Home" => "./Cashier.php", "Settings" => "./Manage-Account.php"];
$activeLink = "Home";
$showButton = true;

// check if the manager is logged in
$controller->checkCashierLogin();

// get in-store pending orders
$pendingOrders = $controller->getInStoreOrders();

function displayPendingOrders($orders)
{
    $html = "<div class='nested-orders'>";
    foreach ($orders as $order) {
        $orderId = $order['Id'];
        $orderTime = formatDate($order['Timestamp']);
        $html = $html . "
        <a href='./Order-Details.php?Id=$orderId' class='order'>
            <div>
                <p>Order # <span class='bold-text'>$orderId</span></p>
                <p class='time'>$orderTime</p>
            </div>
            <button>Deliver Order</button>
        </a>
    ";
    }
    return $html . "</div>";
}

function formatDate($date)
{
    $date = new DateTime($date);
    return $date->format('l, F j, Y \a\t g:i A');
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier</title>
    <link rel="stylesheet" href="./Style/elements.css">
    <link rel="stylesheet" href="./Style/layout.css">
    <link rel="stylesheet" href="./Style/forms.css">
    <link rel="stylesheet" href="./Style/orders.css">
</head>

<body>

    <?php include './Header.php'; ?>
    
    <section class="container-40 container">

        <div class="order-section">

            <h2>Pending Orders <img class="icon" src="./Icons/check.svg" alt=""></h2>

            <?php
            if (is_array($pendingOrders)) {
                echo displayPendingOrders($pendingOrders);
            } else {
                echo $pendingOrders;
            }
            ?>

        </div>

    </section>

</body>

</html>