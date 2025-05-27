

<?php
require_once './Backend/Controller/Controller.php';
$controller = new Controller();

$buttonText = $controller->isCustomerLogin() ? "Logout" : "Login";
$homeLink = $controller->isAdminLogin() ? './Admin.php' : './LandingPage.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['Status'])) {
    if ($_POST['Status'] == "Logout") {
        $msg = $controller->logout();
    } else {
        $controller->goToLogin();
    }
}

function generateButtonHTML($text) {
    return "<form method='post'>
                <input type='hidden' name='Status' value='$text' />
                <button type='submit' class='header-button'>$text</button>
            </form>";
}
?>
