<?php

require_once './Backend/Controller/Controller.php';
$controller = new Controller();

// change the text in the header if user is already logged in
$buttonText = "";
if ($controller->isCustomerLogin()) {
    $buttonText = "Logout";
} else {
    $buttonText = "Login";
}

// handle logout request
$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['Status'])) {
        if($_POST['Status'] == "Logout"){
            $msg = $controller->logout();
        }
        else{
            $controller->goToLogin();
        }
    }
}

// display dropdown links
function displayLinks($links, $activeLink)
{
    $html = '';
    foreach ($links as $linkText => $link) {
        $classes = $activeLink == $linkText ? "link active-link" : "link";
        $html = $html . "<a class='$classes' href='$link'>$linkText</a>";
    }
    return $html;
}

// button HTML code
function generateButtonHTML($text)
{
    return
        "<form method='post'>
        <input type='hidden' name='Status' value='$text' />
            <button type='submit' class='header-button'>$text</button>
        </form>
    ";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" href="./Style/header.css">
    <script src="./Script/app.js"></script>
</head>

<body>
    <nav class="navbar">

        <div class="parent">

            <div class="nested-div-1">

                <a href="./index.php" class="section1">
                    <img src="https://flowbite.com/docs/images/logo.svg" alt="">
                    <h2>BasKit</h2>
                </a>

            </div>

            <div class="nested-div-2">

                <div class="section2">

                    <!--Button-->
                    <?php if (isset($showButton))
                        echo $showButton ? generateButtonHTML($buttonText) :  null;
                    ?>

                    <!--Bars Button-->
                    <button class="bars-btn" id="bars-btn">
                        <img class="header-icon" src="./Icons/bars.svg" alt="icon">
                    </button>

                    <!--Dropdown-->
                    <div id="dropdown-menu" class="dropdown-menu">
                        <?php if (isset($links) && isset($activeLink)) echo displayLinks($links, $activeLink); ?>
                    </div>

                </div>

            </div>

        </div>

    </nav>

</body>

</html>