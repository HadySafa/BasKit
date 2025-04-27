<?php

// This is done by Hady

require_once './Backend/Controller/Controller.php';

$controller = new Controller();

// modify header
$links = ["Home" => "./index.php", "Profile" => "./Admin.php"];
$activeLink = "";
$showButton = true;

// check if the manager is logged in
$controller->checkManagerLogin();

// get user Id
$id = $controller->getUserId();

// get pending orders
$pendingOrders = $controller->getPendingOrders();

// get completed orders
$completedOrders = $controller->getCompletedOrders();

function displayOrders($orders)
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
                <p><img class='icon' src='./Icons/right.svg' alt='Icon'></p>
            </a>
        ";
    }
    return $html . "</div>";
}
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
            <button>Manage Order</button>
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
    <title>Orders History</title>
    <link rel="stylesheet" href="./Style/elements.css">
    <link rel="stylesheet" href="./Style/layout.css">
    <link rel="stylesheet" href="./Style/forms.css">
    <link rel="stylesheet" href="./Style/orders.css">
</head>

<body>

    <?php include './Header.php'; ?>
    
    <section class="container-40 container">

        <div class="order-section">

            <h2>Pending <img class="icon" src="./Icons/check.svg" alt=""></h2>

            <?php
            if (is_array($pendingOrders)) {
                echo displayPendingOrders($pendingOrders);
            } else {
                echo $pendingOrders;
            }
            ?>

        </div>

        <div class="order-section">

            <h2>Delivered <img class="icon" src="./Icons/double-check.svg" alt=""></h2>

            <?php
            if (is_array($completedOrders)) {
                echo displayOrders($completedOrders);
            } else {
                echo $completedOrders;
            }
            ?>

        </div>

    </section>

</body>

</html>