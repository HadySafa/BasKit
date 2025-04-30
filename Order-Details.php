<?php

// This is done by Hady

require_once './Backend/Controller/Controller.php';

$controller = new Controller();

// Check if user requesting the page is logged in
$controller->checkLoggedIn();

// Deliver order - done by the admin
$msg = '';
if ($controller->isAdmin() && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $msg = $controller->deliverOrder($_POST['Id']);
    if (!$msg) {
        header('Location: ./Order-Details.php?Id=' . $_POST['Id']);
        exit();
    } else {
        header('Location: ./Manage-Orders.php');
        exit();
    }
}

// Get the order id requested (by customer or admin)
$submittedId = '';
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['Id'])) {
    $submittedId = $_GET['Id'];
}
// If Id is not numeric or Id is a negative number
if (!is_numeric($submittedId) || $submittedId < 0) {
    // we can rediret to another page
    exit();
}

// modify header
$links = ["Home" => "./index.php", "Profile" => "./User.php"];
$activeLink = "";
$showButton = false;


// temporary code, the order object must contain an array of products object
$order = '';
$products = '';
if ($controller->isCustomer()) {
    // customer
    $order = $controller->getOrderByIdUserId($submittedId);
    $products = $controller->getOrdersProducts($submittedId);
} elseif ($controller->isAdmin()) {
    // admin
    $order = $controller->getOrder($submittedId);
    $products = $controller->getOrdersProducts($submittedId);
}

// display error messages
if (!is_array($order)) {
    echo "<script>alert('$order')</script>";
    exit();
}
if (!is_array($products)) {
    echo "<script>alert('$products')</script>";
    exit();
}
if($msg){
    echo "<script>alert('$msg')</script>";
}


// functions to render HTML
function formatDate($date)
{
    $date = new DateTime($date);
    return $date->format('l, F j, Y \a\t g:i A');
}
function displayProduct($product)
{
    $name = $product["PRODUCTNAME"];
    $quantity = $product["Quantity"];
    $price = $product["SellingPrice"];
    return "
        <div class='section-item-layout'>
            <div>
                <div>$name</div>
                <div>$quantity</div>
            </div>
            <div>$price $</div>
        </div>
    ";
}
function calculateTotal($products)
{
    $total = 0;
    foreach ($products as $product) {
        $total += $product['SellingPrice'];
    }
    return $total;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order</title>
    <link rel="stylesheet" href="./Style/elements.css">
    <link rel="stylesheet" href="./Style/layout.css">
    <link rel="stylesheet" href="./Style/forms.css">
    <link rel="stylesheet" href="./Style/dashboard.css">
</head>

<body>

    <?php include './Header.php'; ?>

    <div class="container-40">

        <div class="orderMainContainer">

            <!--Order info based on role-->
            <div class="section">
                <div class="section-item-layout section-header">
                    <?php if ($controller->isAdmin()): ?><h4>User Id #<?php echo $order["UserId"]; ?></h4><?php endif; ?>
                    <?php if ($controller->isCustomer()): ?><h4>Your Order</h4><?php endif; ?>
                    <div><?php echo $order["Status"]; ?></div>
                </div>
                <p><img class="icon" src="./Icons/info.svg" alt="icon" /> Order Id <span class="bold-text"><?php echo $order["Id"] . ", " . $order["OrderType"]; ?></span></p>
                <p><img class="icon" src="./Icons/calendar.svg" alt="icon" /> Delivered on<span class="bold-text"> <?php echo formatDate($order["Timestamp"]); ?></span></p>
                <p><img class="icon" src="./Icons/location.svg" alt="icon" /> <?php echo $order["Location"]; ?></p>
            </div>

            <!--Products section-->
            <div class="section">
                <div class="section-item-layout section-header">
                    <div>Payment Method</div>
                    <div><?php echo $order["PaymentMethod"]; ?></div>
                </div>

                <?php
                foreach ($products as $product) {
                    echo displayProduct($product);
                }
                ?>

                <div class="section-item-layout section-footer bold-text">
                    <div>Total</div>
                    <div><?php echo calculateTotal($products); ?> $</div>
                </div>
            </div>

            <!--Render buttons based on the role and order status-->
            <?php if ($controller->isCustomer()): ?>

                <div class='buttons-container'>
                    <a href="<?php echo './Receipt/index.php?Id=' . $order['Id']; ?>" class="button">Digital Receipt</a>
                    <a href="./Feedback.php" class="submit">Give Feedback</a>
                </div>

            <?php endif; ?>

            <?php if ($controller->isAdmin() && $controller->isPending($order)): ?>

                <div class='buttons-container'>
                    <form method="post" action="">
                        <input type="hidden" name="Id" value="<?php echo $order['Id']; ?>">
                        <button type="submit" class="submit">Deliver Order</button>
                    </form>
                </div>

            <?php endif; ?>


        </div>

    </div>

</body>

</html>