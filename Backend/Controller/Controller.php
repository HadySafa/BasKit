<?php

use FontLib\Table\Type\head;

require_once __DIR__ . '/databaseAccess.php';
require_once __DIR__ . '\../Model/User.php';
require_once __DIR__ . '\../Model/Feedback.php';
require_once __DIR__ . '\../Model/OrderProduct.php';

class Controller
{

    private $databaseAccess;

    public function __construct()
    {
        $this->databaseAccess = new databaseAccess();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Hady's Part  

    // Functions

    public function startSession($user)
    {
        $_SESSION["Id"] = $user->getId();
        $_SESSION["Name"] = $user->getName();
        $_SESSION["Balance"] = $user->getBalance();
        $_SESSION["Role"] = $user->getRole();
        $_SESSION["Products"] = [];
    }

    public function getSessionInfo()
    {
        return [$_SESSION["Name"], $_SESSION["Balance"]];
    }

    public function getUserName()
    {
        return $_SESSION["Name"] ?? null;
    }

    public function getUserId()
    {
        return $_SESSION["Id"];
    }

    public function getBasketId()
    {
        return $_SESSION["BasketId"];
    }

    public function isAdmin()
    {

        if (isset($_SESSION["Role"])) {
            return $_SESSION["Role"] == 'Admin';
        }
    }

    public function isPending($status)
    {
        return $status['Status'] == 'Pending';
    }

    public function isCustomer()
    {
        if (isset($_SESSION["Role"])) {
            return $_SESSION["Role"] == 'Customer';
        }
    }

    public function isCashier()
    {
        if (isset($_SESSION["Role"])) {
            return $_SESSION["Role"] == 'Cashier';
        }
    }

    public function checkCustomerLogin()
    {
        if (!isset($_SESSION['Role']) || $_SESSION['Role'] != "Customer") {
            header("Location: ./Login.php");
        }
    }

    public function isCustomerLogin()
    {
        if (!isset($_SESSION['Id'])) {
            return false;
        }
        return true;
    }

    public function isAdminLogin()
    {
        return isset($_SESSION['Role']) && $_SESSION['Role'] === 'Admin';
    }


    public function checkLoggedIn()
    {
        if (empty($_SESSION['Id'])) {
            header("Location: ./Login.php");
            exit();
        }
    }

    public function checkManagerLogin()
    {
        if (!isset($_SESSION['Role']) || $_SESSION['Role'] != "Admin") {
            header("Location: ./Login.php");
            exit();
        }
    }

    public function checkCashierLogin()
    {
        if (!isset($_SESSION['Role']) || $_SESSION['Role'] != "Cashier") {
            header("Location: ./Login.php");
            exit();
        }
    }

    public function checkAdminLogin()
    {
        if (!isset($_SESSION['Role']) || $_SESSION['Role'] != "Admin") {
            if ($_SESSION['Role'] == "Customer") {
                header("Location: User.php");
                return;
            }
            header("Location: Login.php");
            exit();
        }
    }

    public function logout()
    {
        // remove all session variables and destroy the session
        session_unset();
        session_destroy();
        header("Location: Login.php");
        exit();
    }

    public function cleanInput($data)
    {
        $data = strip_tags($data); // Remove all HTML tags
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // Convert special chars
    }

    public function goToLogin()
    {
        header('Location: ./Login.php');
    }

    private function generateCurrentTimestamp()
    {
        $date = new DateTime("now", new DateTimeZone("Asia/Beirut"));
        return $date->format(DateTime::ATOM);
    }


    public function redirectToDashboard()
    {
        if (!isset($_SESSION['Role'])) {
            header('Location: Login.php');
            exit();
        }

        switch ($_SESSION['Role']) {
            case 'Admin':
                header('Location: ./Admin.php');
                break;
            case 'Cashier':
                header('Location: ./Cashier.php');
                break;
            case 'Customer':
                header('Location: ./LandingPage.php');
                break;
            default:
                header('Location: ./Login.php');
                break;
        }
        exit();
    }


    /////////////////////////////////////////////////////////

    // User

    public function addUser($submittedInfo)
    {
        if (!$this->isUnique('Email', $submittedInfo['Email'])) {
            return "This email is already registered. Please use a different email address.";
        } elseif (!$this->isUnique('Phone', $submittedInfo['Phone'])) {
            return "This phone number is already associated with an account. Please use a different number.";
        } else {
            // unique
            $tempUser = new User($submittedInfo['Name'], $submittedInfo['Email'], $submittedInfo['Phone'], 'Customer', $submittedInfo['Password'], 0.00);
            $rowCount = $this->databaseAccess->addUser($tempUser);
            if ($rowCount > 0) header('Location: ./Login.php');
            else header('Location: ./Register.php');
            return '';
        }
    }

    private function isUnique($type, $parameter)
    {
        if ($type == 'Email') {
            $tempUser = $this->databaseAccess->getUserByEmail($parameter);
            if (is_array($tempUser) && count($tempUser) == 0) {
                return true;
            }
            return false;
        } elseif ($type == 'Phone') {
            $tempUser = $this->databaseAccess->getUserByPhone($parameter);
            if (is_array($tempUser) && count($tempUser) == 0) {
                return true;
            }
            return false;
        }
    }

    public function validateUser($submittedInfo, $info)
    {
        $tempUser = $this->databaseAccess->getUserByEmail($submittedInfo['Email']);
        if ($tempUser) {
            if ($tempUser->isActive() && password_verify($submittedInfo['Password'], $tempUser->getPassword())) {
                // user validated
                if (is_array($info)) {
                    $this->startPhysicalSession($tempUser, $info);
                    header('Location: ./BasKit.php');
                } else {
                    $this->startSession($tempUser);
                    $this->redirectToDashboard();
                }
                return '';
            } else return "Wrong credentials.";
        } else {
            if (is_array($tempUser) && count($tempUser) == 0) return "Wrong credentials.";
            return "Try again later.";
        }
    }





    private function validateOldPassword($id, $password)
    {
        $tempUser = $this->databaseAccess->getUserById($id);
        if ($tempUser) {
            if ($tempUser->isActive() && password_verify($password, $tempUser->getPassword())) {
                return '';
            } else return "Wrong credentials.";
        } else {
            if (is_array($tempUser) && count($tempUser) == 0) return "Wrong credentials.";
            return "Try again later.";
        }
    }

    private function updatePassword($id, $password)
    {
        $rowCount = $this->databaseAccess->updateUserPassword($id, $password);
        if (is_numeric($rowCount) && $rowCount > 0) {
            // successfully changed
            return '';
        } else return 'Action Failed';
    }

    public function changePassword($submittedInfo)
    {
        $response = self::validateOldPassword($_SESSION["Id"], $submittedInfo['OldPassword']);
        if (!$response) {
            // old password verified
            $response2 = self::updatePassword($_SESSION["Id"], $submittedInfo['NewPassword']);
            if (!$response2) {
                // password changed
                header('Location: ./Login.php');
                return '';
            }
            return $response2;
        } else {
            return $response;
        }
    }

    /////////////////////////////////////////////////////////

    // Order

    public function submitOrder($array)
    {
        $tempOrder = new Order(self::getUserId(), self::generateCurrentTimestamp(), $array['PaymentMethod'], "Online", $array['Location'], "Pending");
        $orderId = $this->databaseAccess->addOrder($tempOrder);
        if ($orderId > 0) {
            foreach ($_SESSION['cart'] as $productquantity) {
                $productId = $productquantity['productId'];
                $quantity = $productquantity['quantity'];
                $price = $productquantity['productPrice'];
                $this->addProductToOrder($productId, $orderId, $quantity, $price);
            }
            header('Location: ./Orders.php');
            return '';
            exit();
        }
        return "Failed to perform action, try again later.";
    }

    public function addProductToOrder($productId, $orderId, $quantity, $price)
    {
        $tempOrder = new OrderProduct($orderId, $productId, $quantity, $price);
        $response = $this->databaseAccess->addOrderProduct($tempOrder);
        return $response;
    }

    public function getUserOrders($id, $status)
    {
        $orders = $this->databaseAccess->getUserOrders($id);
        if (is_array($orders)) {
            if (count($orders) > 0) {
                $filteredArray = [];
                foreach ($orders as $order) {
                    if ($order->getStatus() == $status) $filteredArray[] = $order->toArray();
                }
                return $filteredArray;
            }
            return "No $status order yet.";
        }
        return 'Failed to get orders, try again later.';
    }

    public function getPendingOrders()
    {
        $orders = $this->databaseAccess->getAllOrders('Pending');
        if (is_array($orders)) {
            if (count($orders) > 0) {
                $filteredArray = [];
                foreach ($orders as $order) {
                    $filteredArray[] = $order->toArray();
                }
                return $filteredArray;
            }
            return "No pending order yet.";
        }
        return 'Failed to get orders, try again later.';
    }


    public function getInStoreOrders()
    {
        $orders = $this->databaseAccess->getAllOrders('Pending');
        if (is_array($orders)) {
            if (count($orders) > 0) {
                $filteredArray = [];
                foreach ($orders as $order) {
                    if ($order->getOrderType() == 'In-Store') {
                        $filteredArray[] = $order->toArray();
                    }
                }
                return $filteredArray;
            }
            return "No pending orders yet.";
        }
        return 'Failed to get orders, try again later.';
    }

    public function checkEmptyCart(){
        if(!isset($_SESSION['cart']) || count($_SESSION['cart']) <= 0){
            header('Location: ./Cart.php');
        }
    }

    public function getCompletedOrders()
    {
        $orders = $this->databaseAccess->getAllOrders('Completed');
        if (is_array($orders)) {
            if (count($orders) > 0) {
                $filteredArray = [];
                foreach ($orders as $order) {
                    $filteredArray[] = $order->toArray();
                }
                return $filteredArray;
            }
            return "No completed order yet.";
        }
        return 'Failed to get orders, try again later.';
    }

    public function getOrder($id)
    {
        $order = $this->databaseAccess->getOrderById($id);
        if (is_array($order)) {
            if (count($order) > 0) {
                return $order[0]->toArray();
            }
            return "No order found.";
        }
        return 'Failed to get order, try again later.';
    }

    public function getOrdersProducts($id)
    {
        $products = $this->databaseAccess->getOrdersProducts($id);
        if (is_array($products)) {
            if (count($products) > 0) {
                return $products;
            }
            return "No order found.";
        }
        return 'Failed to get order, try again later.';
    }

    public function getOrderByIdUserId($id)
    {
        $order = $this->databaseAccess->getOrderByIdUserId($id, self::getUserId());
        if (is_array($order)) {
            if (count($order) > 0) {
                return $order[0]->toArray();
            }
            return "No order found.";
        }
        return 'Failed to get order, try again later.';
    }

    public function deliverOrder($id)
    {
        $rowCount = $this->databaseAccess->updateOrderStatus($id, 'Completed');
        if (is_numeric($rowCount) && $rowCount > 0) {
            // successfully changed
            return 'succesfully  changed';
        } else return 'Action Failed';
    }

    /////////////////////////////////////////////////////////

    // Feedback

    public function submitFeedback($description)
    {
        $tempFeedback = new Feedback(self::getUserId(), $description, 1, self::generateCurrentTimestamp());
        $rowCount = $this->databaseAccess->addFeedback($tempFeedback);
        if ($rowCount > 0) {
            return 'Message Sent';
            exit();
        }
        return "Failed to perform action, try again later.";
    }

    /////////////////////////////////////////////////////////

    // Hardware part

    public function addProductToBasket($barcode) // this is dealing with an assoc array for now, fix it for later
    {
        // check basket id

        $product = $this->databaseAccess->getProductIdByBarcode($barcode);
        $productId = $product['Id'];

        if ($productId > 0) { // valid product id & no errors, add it to session
            $this->addProductToSession($product);
            return true;
        } else {
            return false;
        }
    }

    // change this to match mr amir's code
    private function addProductToSession($product)
    {
        if (array_key_exists($product["Id"], $_SESSION["cart"])) { // product exists, increment the quantity
            $_SESSION['cart'][$product["Id"]]['quantity']++;
        } else { // product doesn't exists, add it
            $_SESSION['cart'][] = [
                'productId' => $product['Id'],
                'productName' => $product["Name"],
                'productPrice' => $product['Price'],
                'productImage' => $product['ImageURL'],
                'productDescription' => $product['Description'],
                'quantity' => 1,
            ];
        }
    }

    public function startPhysicalSession($user, $info)
    {
        $_SESSION["Id"] = $user->getId();
        $_SESSION["Name"] = $user->getName();
        $_SESSION["Balance"] = $user->getBalance();
        $_SESSION["Role"] = $user->getRole();
        $_SESSION["OrderType"] = $info["Type"];
        $_SESSION["BasketId"] = $info["Basket"];
        $this->saveSession($info["Basket"]);
    }

    // save session in the database
    private function saveSession($basketId)
    {
        $rowCount = $this->databaseAccess->addToSession($this->getUserId(), $basketId, $this->getSessionId());
        if ($rowCount > 0) { // added to session
            return true;
        }
        return false;
    }

    public function returnSessionId($basketId)
    {
        $sessionId = $this->databaseAccess->getSessionId($basketId);
        if ($sessionId) return $sessionId;
    }

    private function getSessionId()
    {
        if (isset($_SESSION['Id'])) {
            return session_id();
        } else {
            return "";
        }
    }


    /////////////////////////////////////////////////////////


    ///////////////////  Amir's Part  ///////////////////////



    public function getAllUsers()
    {
        return $this->databaseAccess->getAllUsers();
    }

    public function getUserById($userId)
    {
        return $this->databaseAccess->getUserById($userId);
    }

    public function getCategories()
    {
        return $this->databaseAccess->getAllCategories();
    }

    public function getProducts()
    {
        return $this->databaseAccess->getAllProducts();
    }

    public function showProductById($productId)
    {
        return $this->databaseAccess->getProductById($productId);
    }

    public function searchProducts($searchedTerm, $categoryID = '', $price = '')
    {
        return $this->databaseAccess->getProductsBySearch($searchedTerm, $categoryID, $price);
    }

    public function getSearchSuggestions()
    {
        $results = $this->databaseAccess->searchSuggestions();
        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }

    public function filterProducts($category = '', $price = '')
    {
        return $this->databaseAccess->getProductsByFilter($category, $price);
    }

    public function showFilterResults()
    {

        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $price = isset($_GET['price']) ? $_GET['price'] : '';

        if (empty($category) && empty($price)) {
            // Show all products if no filter is applied
            return $this->getProducts();
        } else {
            // Filtered products
            return $this->filterProducts($category, $price);
        }
    }

    public function addToCart()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['Id'])) {
            // Not logged in — respond immediately and exit
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'You must be logged in to add items to your cart.'
            ]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'addToCart') {
            $productId = $_POST['productId'];
            $productName = $_POST['productName'];
            $productPrice = $_POST['productPrice'];
            $productImage = $_POST['productImage'];
            $productDescription = $_POST['productDescription'];
            $quantity = (int) $_POST['quantity'];

            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            $found = false;
            foreach ($_SESSION['cart'] as $cartItem) {
                if ($cartItem['productId'] === $productId) {
                    $cartItem['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
            unset($cartItem);

            if (!$found) {
                $_SESSION['cart'][] = [
                    'productId' => $productId,
                    'productName' => $productName,
                    'productPrice' => $productPrice,
                    'productImage' => $productImage,
                    'productDescription' => $productDescription,
                    'quantity' => $quantity,
                ];
            }

            $distinctCount = count($_SESSION['cart']);

            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'message' => 'Product added to cart!',
                'isNewProduct' => !$found,
                'distinctCount' => $distinctCount
            ]);
            exit;
        }
    }


    public function removeFromCart()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['productId'];


            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {

                foreach ($_SESSION['cart'] as $key => $cartItem) {
                    if ($cartItem['productId'] === $productId) {
                        // Remove the product from the cart
                        unset($_SESSION['cart'][$key]);
                        // Reindex the array after removal
                        $_SESSION['cart'] = array_values($_SESSION['cart']);
                        break;
                    }
                }
            }

            // Count distinct products in the cart after removal
            $distinctCount = count($_SESSION['cart']);

            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'message' => 'Product removed from cart!',
                'distinctCount' => $distinctCount
            ]);
            exit;
        }
    }

    public function updateCart()
    {
        // Check if the POST request contains the necessary parameters
        if (isset($_POST['productId']) && isset($_POST['quantity'])) {
            $productId = (int)$_POST['productId'];
            $quantity = (int)$_POST['quantity'];

            // Validate quantity (should be at least 1)
            if ($quantity < 1) {
                echo json_encode(['status' => 'error', 'message' => 'Quantity cannot be less than 1.']);
                exit;
            }


            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;


                $productPrice = $_SESSION['cart'][$productId]['price'];
                $totalPrice = $productPrice * $quantity;


                echo json_encode([
                    'status' => 'success',
                    'message' => 'Cart updated successfully.',
                    'totalPrice' => number_format($totalPrice, 2),
                ]);
            } else {
                // If the product doesn't exist in the cart
                echo json_encode(['status' => 'error', 'message' => 'Product not found in the cart.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
        }
        exit;
    }

    // Add Product
    public function addProduct($submittedInfo, $fileInfo)
    {
        if (!isset($submittedInfo['Name'], $submittedInfo['Price'], $submittedInfo['Stock'])) {
            return false;
        }

        // handle image 
        $fileExtension = pathinfo($fileInfo['Image']['name'], PATHINFO_EXTENSION); // get file extension

        return $fileExtension;

        $targetFile  = "Uploads/" . $submittedInfo["Name"] . '.' . $fileExtension;

        if (move_uploaded_file($fileInfo['Image']['tmp_name'], $targetFile)) {
            echo "succeeded";
        } else {
            echo "Failed to upload the image.";
        }



        $tempProduct = new Product(
            $submittedInfo['Name'],
            $submittedInfo['Price'],
            $submittedInfo['Stock'],
            $submittedInfo['Description'],
            $submittedInfo['Origin'],
            $submittedInfo['Barcode'],
            $targetFile,
            $submittedInfo['Weight']
        );

        $tempProduct->setActive(true);
        $rowCount = $this->databaseAccess->addProduct($tempProduct);

        return $rowCount > 0;
    }

    public function updateProduct($submittedInfo)
    {

        $tempProduct = new Product(
            $submittedInfo['Name'],
            $submittedInfo['Price'],
            $submittedInfo['Stock'],
            $submittedInfo['Description'],
            $submittedInfo['Origin'],
            $submittedInfo['Barcode'],
            $imageUrl = null, // to be changed 
            $submittedInfo['Weight']
        );


        $tempProduct->setId($submittedInfo['Id']);
        $tempProduct->setActive(true);

        // call updateProduct()
        $rowCount = $this->databaseAccess->updateProduct($tempProduct);
    }


    public function deleteProduct($submittedInfo)
    {
        $id = $submittedInfo['Id'];
        return $this->databaseAccess->deleteProduct($id);
    }



    // UpdateUser

    public function updateUser($submittedInfo)
    {

        $tempUser = new User(

            $submittedInfo['Name'],
            $submittedInfo['Email'],
            $submittedInfo['Phone'],
            $submittedInfo['Role'],
            $submittedInfo['Password'],
            $submittedInfo['Balance'],
            $submittedInfo['Active']
        );


        $tempUser->setId($submittedInfo['Id']);
        $rowCount = $this->databaseAccess->updateUser($tempUser);
    }

    // delete user

    public function deleteUser($submittedInfo)
    {
        $id = $submittedInfo['Id'];
        return $this->databaseAccess->deleteUser($id);
    }



    // WhishList 
    public function getAvailableProducts()
    {

        return $this->databaseAccess->getProductsName();
    }

    public function getUserCartItems()
    {

        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            return array_map(function ($item) {
                return [
                    'id' => $item['productId'] ?? null,
                    'name' => $item['productName'] ?? null
                ];
            }, $_SESSION['cart']);
        }

        return [];
    }

    // Get product details by ID for the Add to Cart form
    public function getProductDetails($productId)
    {
        $product = $this->showProductById($productId);

        if ($product !== null) {
            // Return the product details as a JSON response for frontend
            echo json_encode([
                'status' => 'success',
                'product' => [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'description' => $product->getDescription(),
                    'imageUrl' => $product->getImageUrl(),
                    'stock' => $product->getStock(),
                    'weight' => $product->getWeight(),
                ]
            ]);
            exit();
        } else {
            // Return an error message if the product isn't found
            echo json_encode([
                'status' => 'error',
                'message' => 'Product not found'
            ]);
            exit();
        }
    }


    public function getMultipleProductDetails($idsParam)
    {
        if (is_array($idsParam)) {
            $idsParam = implode(',', $idsParam);
        }

        $ids = explode(',', $idsParam);

        $ids = array_map('intval', $ids);

        $products = [];

        foreach ($ids as $id) {
            $product = $this->showProductById($id);

            if ($product !== null) {
                $products[] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'description' => $product->getDescription(),
                    'imageUrl' => $product->getImageUrl(),
                    'stock' => $product->getStock(),
                    'weight' => $product->getWeight(),
                ];
            }
        }

        // Return the product details as a JSON response
        echo json_encode([
            'status' => 'success',
            'products' => $products
        ]);
        exit();
    }


    // WishList
    public function getSuggestions()
    {


        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getSuggestions') {

            $cartItems = $this->getUserCartItems();  // get items in the cart

            $availableProducts = $this->getAvailableProducts(); // available products in the shop

            $cartNames = array_map(fn($item) => $item['name'], $cartItems); // get names of the items in the cart
            $availableNames = array_map(fn($item) => $item['name'], $availableProducts); // get names of the available products in the shop

            // combine into a string seperated by \n
            $cartProductList = implode("\n", $cartNames);  
            $availableProductList = implode("\n", $availableNames);

            // cart is empty -> error
            if (empty($cartProductList)) {
                echo json_encode(['status' => 'error', 'message' => 'Cart is empty.']);
                exit;
            }

            if (empty($availableProductList)) {
                echo json_encode(['status' => 'error', 'message' => 'No available products.']);
                exit;
            }

            $prompt = "A user has the following items in their cart:\n" .
                "$cartProductList\n\n" .
                "Here is the list of available products in the shop:\n" .
                "$availableProductList\n\n" .
                "Based on the cart items, suggest 5 complementary products from the available products. " .
                "Do not repeat items already in the cart. " .
                "For each suggested product, write its name followed by a short reason why it complements the cart item. " .
                "Format each suggestion on a separate line like this: Product Name - Short Description.";


            // Get raw suggestions from the model
            $suggestedNames = $this->fetchOpenAISuggestions($prompt);

            $matchedSuggestions = [];
            foreach ($suggestedNames as $suggestedLine) {

                $line = trim($suggestedLine);

                // Split at the first '-' to separate name from description
                $parts = explode(" - ", $line, 2);

                // If there's no description part, treat the whole line as a name
                if (count($parts) < 2) {
                    $suggestedName = $line;
                    $description = "No description available.";
                } else {
                    $suggestedName = $parts[0];
                    $description = $parts[1];
                }


                foreach ($availableProducts as $product) {
                    if (strcasecmp($product['name'], $suggestedName) === 0) {
                        $matchedSuggestions[] = [
                            'id' => $product['id'],
                            'name' => $product['name'],
                            'description' => $description // Store the description
                        ];
                        break;
                    }
                }
            }



            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'suggestions' => $matchedSuggestions,
                'debug' => [
                    'cartItems' => $cartItems,
                    'availableProducts' => $availableProducts,
                    'suggestedLines' => $suggestedNames
                ]
            ]);
            exit;
        }
    }


    private function fetchOpenAISuggestions($prompt)
    {
        require_once './Backend/Config/api_config.php';

        $data = [
            'model' => 'deepseek/deepseek-r1:free',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful shopping assistant.'],
                ['role' => 'user', 'content' => $prompt]
            ]
        ];

        $ch = curl_init("https://openrouter.ai/api/v1/chat/completions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: Bearer " . API_KEY
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo json_encode(['status' => 'error', 'message' => 'cURL Error: ' . curl_error($ch)]);
            curl_close($ch);
            exit;
        }

        curl_close($ch);
        $decoded = json_decode($result, true);

        if (isset($decoded['error'])) {
            echo json_encode(['status' => 'error', 'message' => "Error from API: " . $decoded['error']['message']]);
            exit;
        }

        if (!isset($decoded['choices'][0]['message']['content'])) {
            echo json_encode(['status' => 'error', 'message' => "No suggestions returned."]);
            exit;
        }

        $text = $decoded['choices'][0]['message']['content'];

        return explode("\n", trim($text)); // Return suggestions line-by-line
    }


    public function getBestsellingProducts()
    {
        $orderProducts = $this->databaseAccess->getBestsellingProducts();
        echo json_encode($orderProducts);
    }


    //Shopping List 
    public function getUserShoppingListItems(){
        return $this->databaseAccess->getUserShoppingListItems($this->getUserId());
    }


    public function getListSuggestions()
    {
        $debug = [];

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $debug[] = "Request method not GET";
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method', 'debug' => $debug]);
            exit;
        }

        if (!isset($_GET['action']) || $_GET['action'] !== 'getListSuggestions') {
            $debug[] = "Action parameter missing or incorrect";
            echo json_encode(['status' => 'error', 'message' => 'Invalid action', 'debug' => $debug]);
            exit;
        }

        $debug[] = "Fetching available products";
        $availableProducts = $this->getAvailableProducts();
        $debug[] = 'Available products: ' . json_encode($availableProducts);

        $debug[] = "Fetching user shopping list items";
        $userItems = $this->getUserShoppingListItems();
        $debug[] = 'User items: ' . json_encode($userItems);

        if (empty($userItems)) {
            $debug[] = "User items empty";
            echo json_encode(['status' => 'error', 'message' => 'Shopping list is empty.', 'debug' => $debug]);
            exit;
        }

        $availableNames = array_map(fn($item) => $item['name'], $availableProducts);

        if (empty($availableNames)) {
            $debug[] = "No available products";
            echo json_encode(['status' => 'error', 'message' => 'No available products.', 'debug' => $debug]);
            exit;
        }

        $prompt = "The user wants to buy the following items: " . json_encode($userItems) . "\n\n" .
            "The store has the following available products: " . json_encode($availableNames) . "\n\n" .
            "For each user item, suggest the most appropriate or similar product from the store. " .
            "Respond ONLY with a JSON array of objects, where each object contains 'userItem' and 'suggestedProduct'.";


        $debug[] = "Prompt for AI: " . $prompt;

        $rawSuggestions = $this->fetchOpenAISuggestions($prompt);
        $debug[] = 'Raw AI suggestions: ' . json_encode($rawSuggestions);

        // If AI returned multiple lines as an array of strings, join and decode
        if (is_array($rawSuggestions) && count($rawSuggestions) > 1 && is_string($rawSuggestions[0])) {
            $jsonString = implode("", $rawSuggestions);
            $decoded = json_decode($jsonString, true);
            if (is_array($decoded)) {
                $rawSuggestions = $decoded;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to decode AI suggestions', 'debug' => $debug]);
                exit;
            }
        }

        if (!is_array($rawSuggestions)) {
            $debug[] = 'Invalid AI suggestions format';
            echo json_encode(['status' => 'error', 'message' => 'Invalid response from AI.', 'debug' => $debug]);
            exit;
        }

        $matchedSuggestions = [];
        foreach ($rawSuggestions as $pair) {
            if (!isset($pair['userItem'], $pair['suggestedProduct'])) continue;

            $userItem = $pair['userItem'];
            $suggestedName = $pair['suggestedProduct'];

            foreach ($availableProducts as $product) {
                if (strcasecmp($product['name'], $suggestedName) === 0) {
                    $matchedSuggestions[] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'description' => $product['description'] ?? '',
                        'matchedTerm' => $userItem
                    ];
                    break;
                }
            }
        }
        echo json_encode([
            'status' => 'success',
            'suggestions' => $matchedSuggestions,
            'debug' => $debug
        ]);
        exit;
    }


    public function saveShoppingListFromInput()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get the raw input and decode JSON
            $input = json_decode(file_get_contents("php://input"), true);

            // Validate and save to session
            if (!empty($input['items']) && is_array($input['items'])) {
                $_SESSION['shoppingList'] = $input['items'];


                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid input. Expecting JSON with "items" array.'
                ]);
            }

            exit;
        }
    }

    public function addItemToShoppingList($description){

        if($this->databaseAccess->addItemToShoppingList($this->getUserId(),$description)  > 0){
            return [
                'status' => 'success',
                'message' => 'Added to database successfully.'
            ];
        }
        else{
            return [
                'status' => 'error',
                'message' => 'Invalid input. Expecting JSON with "items" array.'
            ];
        }
        
    }

    /*
    public function getShoppingList()
    {
        header('Content-Type: application/json');
        if (isset($_SESSION['shoppingList']) && is_array($_SESSION['shoppingList'])) {
            echo json_encode(['status' => 'success', 'items' => $_SESSION['shoppingList']]);
        } else {
            echo json_encode(['status' => 'success', 'items' => []]);
        }
        exit;
    }
    */
    
    public function getShoppingList()
    {
        return $items = $this->databaseAccess->getShoppingList($this->getUserId());
        exit;
    }

    public function clearShoppingList(){

        return $this->databaseAccess->deleteShoppingList($this->getUserId());
    }
    
}
