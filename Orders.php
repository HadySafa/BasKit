<?php

// This is done by Hady

require_once './Backend/Controller/Controller.php';
$controller = new Controller();

// modify header
$links = ["Home" => "./LandingPage.php", "Profile" => "./User.php"];
$activeLink = "";
$showButton = true;

// check if the customer is logged in
$controller->checkCustomerLogin();

// get user Id
$id = $controller->getUserId();

// get pending orders
$pendingOrders = $controller->getUserOrders($id, 'Pending');

// get completed orders
$completedOrders = $controller->getUserOrders($id, 'Completed');

// functions to render HTML
function displayOrders($orders)
{
    $html = "<div class='nested-orders'>";
    foreach ($orders as $order) {
        $orderId = $order['Id'];
        $orderTime = formatDate($order['Timestamp']);
        $html = $html . "
            <a href='./Order-Details.php?Id=$orderId' class='order'>
                <div>
                    <p class='time'>$orderTime</p>
                    <p>Order # <span class='bold-text'>$orderId</span></p>
                </div>
                <p><img class='icon' src='./Icons/right.svg' alt='Icon'></p>
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
                echo displayOrders($pendingOrders);
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