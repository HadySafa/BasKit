<?php

// This is done by Hady

require_once './Backend/Controller/Controller.php';

$controller = new Controller();

// check that products and quantites are submitted
$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['Location']) && !empty($_POST['PaymentMethod'])) {
        $msg = $controller->submitOrder($_POST);
    }
}

// define dropdown info -- can be adjusted to be set by the admin
$locations = ["Beirut", "Mount Lebanon", "North Lebanon", "Akkar", "Bekaa", "Baalbek-Hermel", "South Lebanon", "Nabatieh"];
$paymentMethods = ["Credit Card", "Cash"];

// functions to render HTML
function displayOptions($options, $text)
{
    $html = "<option value=''>$text</option>";
    foreach ($options as $option) {
        $html = $html . "<option value='$option'>$option</option>";
    }
    return $html;
}

// modify header
$links = ["Home" => "./index.php", "Profile" => "./User.php"];
$activeLink = "";
$showButton = false;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./Style/elements.css">
    <link rel="stylesheet" href="./Style/layout.css">
    <link rel="stylesheet" href="./Style/forms.css">
</head>

<body>

    <?php include './Header.php'; ?>

    <div class="container-40">

        <form class="form" method="post">

            <h2>Checkout <img class="icon" src="./Icons/signout.svg" alt="icon"></h2>

            <div class="form-group">
                <select class="input" name="Location" id="Location">
                    <?php echo displayOptions($locations, '- - Choose Location - -'); ?>
                </select>
            </div>

            <div class="form-group">
                <select class="input" name="PaymentMethod" id="PaymentMethod">
                    <?php echo displayOptions($paymentMethods, '- - Choose Payment Method - -'); ?>
                </select>
            </div>

            <button class="submit" type="submit">Checkout</button>

        </form>

    </div>


</body>

</html>