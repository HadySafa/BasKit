<?php

require_once './Backend/Controller/Controller.php';
$controller = new Controller();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    header('Content-Type: application/json'); 

    switch ($_GET['action']) {
        case 'addToCart':
            $controller->addToCart();
            exit();

        case 'saveShoppingList':
            $controller->saveShoppingListFromInput();
            exit();

        default:
            echo json_encode(['status' => 'error', 'message' => 'Action not recognized for POST']);
            exit();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json'); 

    switch ($_GET['action']) {
        case 'getSuggestions':
            $controller->getSuggestions();
            exit();

        case 'getShoppingList':
            $controller->getShoppingList();
            exit();


        case 'getListSuggestions':
            $controller->getListSuggestions();
            exit();

        case 'getProductDetails':
            if (isset($_GET['productId'])) {
                $productId = $_GET['productId'];
                $productDetails = $controller->getProductDetails($productId);
                echo json_encode($productDetails);
                exit();
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Product ID is required'
                ]);
                exit();
            }

        case 'getMultipleProductDetails':
            if (isset($_GET['ids'])) {
                $productIds = explode(',', $_GET['ids']);
                $controller->getMultipleProductDetails($productIds);
        
                exit();
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Product ID is required'
                ]);
                exit();
            }
            
            
        case 'getBestsellers':
            $controller->getBestsellingProducts(); 
            exit();

        case 'getSearchSuggestions':
            $controller->getSearchSuggestions();
            exit();

        default:
            echo json_encode(['status' => 'error', 'message' => 'Action not recognized']);
            exit();
    }
}


$route = $_GET['route'] ?? 'home';
$route = filter_var($route, FILTER_UNSAFE_RAW);

switch ($route) {
  
      case 'removeFromCart':
        $controller->removeFromCart();
        break;

    case 'updateCart':
        $controller->updateCart();
        break;

    case 'Products':
        $searchQuery = $_GET['quary'] ?? '';
        $category = $_GET['category'] ?? '';
        $price = $_GET['price'] ?? '';
        if (!empty($searchQuery) || !empty($category) || !empty($price)) {
            $controller->showFilterResults();
        } else {
            $controller->getProducts();
        }
        break;

    default:
        http_response_code(404);
        echo "Page not found.";
        break;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>


</body>
</html>