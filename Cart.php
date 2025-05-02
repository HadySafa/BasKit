<?php 

    require_once './Backend/Controller/Controller.php';
    $controller = new Controller();

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $controller->addProductToBasket('MILK-001',1);
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
</head>
<body>
    <h2>Cart</h2>
    <?php 
        if(isset($_SESSION['Id'])){
            print_r($_SESSION);
        }
        echo "<br/>";
        echo "<br/>";
        if(isset($_SESSION['Products'])){
            print_r($_SESSION["Products"]);
        }
        echo "<br/>";
        echo "<br/>";
    ?>
    <form method='post'>
        <button type="submit">add product</button>
    </form>
</body>
</html>