<?php

require_once __DIR__ . "\../Backend/Controller/Controller.php";
$controller = new Controller();

if ($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_GET['Basket-Id'])) {
    echo $controller->returnSessionId($_GET['Basket-Id']);
}

?>
